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
                'status' => 'success',
                'message' => 'Kendaraan ditemukan, status keluar berhasil diperbarui.',
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
                'status' => 'success',
                'message' => 'Kendaraan berhasil dicatat sebagai masuk.',
                'metode' => $platDeteksi ? 'deteksi_kamera' : 'qr_code'
            ]);
        }
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
    public function getLatest()
    {
        // Cek apakah ada error scan di session
        if (session()->has('scan_error')) {
            $errorData = session('scan_error');
            session()->forget('scan_error'); // Hapus supaya tidak muncul terus-menerus

            return response()->json([
                'status' => 'error',
                'message' => $errorData['message'],
                'timestamp' => now()->timestamp // Tetap berikan timestamp untuk validasi JS
            ]);
        }

        // Ambil data scan terakhir dari database
        $latest = RiwayatParkir::orderByRaw('GREATEST(COALESCE(waktu_masuk, 0), COALESCE(waktu_keluar, 0)) DESC')->first();

        if (!$latest) {
            return response()->json([
                'message' => 'Belum ada data scan.',
                'status' => 'kosong',
                'timestamp' => now()->timestamp // Tambahkan timestamp juga untuk 'kosong'
            ]);
        }

        $timestamp = strtotime(
            $latest->status_parkir === 'masuk'
                ? $latest->waktu_masuk
                : $latest->waktu_keluar
        );

        return response()->json([
            'plat_nomor' => $latest->plat_nomor,
            'status' => $latest->status_parkir,
            'message' => $latest->status_parkir === 'masuk'
                ? 'QR Code berhasil dipindai dan kendaraan ditemukan, silakan masuk!'
                : 'QR Code berhasil dipindai dan kendaraan ditemukan, silakan keluar!',
            'waktu' => $latest->status_parkir === 'masuk'
                ? $latest->waktu_masuk
                : $latest->waktu_keluar,
            'timestamp' => $timestamp
        ]);
    }
    public function perintahPalang()
    {
        // Ambil log QR terbaru
        $latest = RiwayatParkir::latest()->first();

        if ($latest) {
            if ($latest->status_parkir === 'masuk' && $latest->waktu_keluar === null) {
                return response()->json(['perintah' => 'BUKA PALANG']);
            } else {
                return response()->json(['perintah' => 'DIAM']);
            }
        }

        return response()->json(['perintah' => 'DIAM']);
    }
}
