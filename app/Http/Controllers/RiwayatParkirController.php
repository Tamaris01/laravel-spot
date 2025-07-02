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

    public function perintahPalang()
    {
        // Perbaikan: gunakan orderBy dengan kolom yang ada
        $latest = RiwayatParkir::orderBy('id_riwayat_parkir', 'desc')->first();

        if ($latest) {
            if ($latest->status_parkir === 'masuk' && $latest->waktu_keluar === null) {
                return response()->json([
                    'perintah' => 'BUKA PALANG',
                    'status_parkir' => 'masuk',
                    'plat_nomor' => $latest->plat_nomor
                ]);
            }

            if ($latest->status_parkir === 'keluar') {
                return response()->json([
                    'perintah' => 'BUKA PALANG',
                    'status_parkir' => 'keluar',
                    'plat_nomor' => $latest->plat_nomor
                ]);
            }
        }

        return response()->json([
            'perintah' => 'DIAM',
            'status_parkir' => null,
            'plat_nomor' => null
        ]);
    }
}
