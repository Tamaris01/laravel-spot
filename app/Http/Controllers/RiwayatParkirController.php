<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RiwayatParkir;
use App\Models\Kendaraan;


class RiwayatParkirController extends Controller
{
    /**
     * Handle QR Code scanning for parking
     */
    public function scanQR(Request $request)
    {
        $platQR = strtoupper($request->input('plat_nomor'));
        $platDeteksi = null;

        try {
            // Step 1: Coba deteksi plat otomatis dari server Flask
            $response = Http::timeout(5)->get('https://alpu.web.id/server/result');

            if ($response->ok()) {
                $data = $response->json();
                $platDeteksi = strtoupper($data['plat_nomor'] ?? '');
            }
        } catch (\Exception $e) {
            // Log tapi tetap lanjut ke fallback QR
        }

        // Step 2: Gunakan hasil deteksi jika ada, jika tidak gunakan plat dari QR
        $platFinal = $platDeteksi && $platDeteksi !== '-' ? $platDeteksi : $platQR;

        if (!$platFinal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plat nomor tidak terdeteksi maupun tidak tersedia dari QR. Silakan coba lagi.'
            ], 400);
        }

        // Step 3: Cek kendaraan
        $kendaraan = Kendaraan::where('plat_nomor', $platFinal)->first();

        if (!$kendaraan) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Plat nomor tidak ditemukan dalam sistem. Silakan daftar terlebih dahulu.'
            ], 404);
        }

        $idPengguna = $kendaraan->penggunaParkir->id_pengguna ?? null;
        $waktuSekarang = Carbon::now();

        $riwayatParkir = RiwayatParkir::where('plat_nomor', $platFinal)
            ->where('status_parkir', 'masuk')
            ->first();

        if ($riwayatParkir) {
            $riwayatParkir->update([
                'waktu_keluar' => $waktuSekarang,
                'status_parkir' => 'keluar',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Kendaraan ditemukan, status keluar berhasil diperbarui.',
                'status_parkir' => 'keluar',
                'plat_nomor' => $platFinal,
                'metode' => $platDeteksi ? 'deteksi_kamera' : 'qr_code'
            ]);
        } else {
            RiwayatParkir::create([
                'id_pengguna' => $idPengguna,
                'plat_nomor' => $platFinal,
                'waktu_masuk' => $waktuSekarang,
                'status_parkir' => 'masuk',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Kendaraan berhasil dicatat sebagai masuk.',
                'status_parkir' => 'masuk',
                'plat_nomor' => $platFinal,
                'metode' => $platDeteksi ? 'deteksi_kamera' : 'qr_code'
            ]);
        }
    }

    /**
     * ESP32-A mengirim plat ke server untuk disimpan sementara
     */
    public function sendPlat(Request $request)
    {
        $platNomor = strtoupper($request->input('plat_nomor'));

        if (!$platNomor) {
            return response()->json([
                'status' => false,
                'message' => 'Plat nomor tidak boleh kosong.'
            ], 400);
        }

        // Simpan plat sementara ke cache selama 15 detik agar diambil oleh ESP32-B
        cache()->put('plat_scan_terbaru', $platNomor, 15);

        return response()->json([
            'status' => true,
            'message' => 'Plat nomor berhasil dikirim.',
            'plat_nomor' => $platNomor
        ]);
    }
    /**
     * ESP32-B mengambil plat terbaru yang dikirim oleh ESP32-A
     */
    public function getPlat()
    {
        $platNomor = cache()->get('plat_scan_terbaru');

        if (!$platNomor) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada plat nomor yang tersedia.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Plat nomor ditemukan.',
            'plat_nomor' => $platNomor
        ]);
    }

    /**
     * Display user parking history for today
     */
    public function riwayatParkir()
    {
        $user = auth()->user();
        $date = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');

        // Mendapatkan tanggal hari ini
        $today = Carbon::today();

        // Mengambil riwayat parkir pengguna berdasarkan ID pengguna dan hari ini
        $riwayatParkir = RiwayatParkir::whereHas('kendaraan', function ($query) use ($user) {
            $query->where('id_pengguna', $user->id_pengguna);
        })
            ->where(function ($query) use ($today) {
                $query->whereDate('waktu_masuk', $today)
                    ->orWhereDate('waktu_keluar', $today);
            })
            ->select('id_riwayat_parkir', 'plat_nomor', 'waktu_masuk', 'waktu_keluar')
            ->orderByRaw("CASE WHEN waktu_keluar IS NULL THEN 0 ELSE 1 END, id_riwayat_parkir DESC")
            ->get();

        return view('pengguna.riwayat_parkir', compact('riwayatParkir', 'date'));
    }
}
