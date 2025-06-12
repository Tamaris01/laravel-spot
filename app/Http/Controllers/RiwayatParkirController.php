<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RiwayatParkir;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\Http;

class RiwayatParkirController extends Controller
{
    /**
     * Handle QR Code scanning for parking
     */
    public function scanQR(Request $request)
    {
        // Ambil plat nomor dari request (QR code atau input plat nomor)
        $platNomor = $request->input('plat_nomor');

        // Validasi input plat nomor
        if (!$platNomor) {
            return response()->json(['message' => 'Plat nomor tidak ditemukan'], 400);
        }

        // Cari kendaraan berdasarkan plat nomor di tabel Kendaraan
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();

        if (!$kendaraan) {
            return response()->json(['message' => 'Plat nomor yang anda pindai tidak terdaftar di sistem'], 404);
        }

        // Ambil id_pengguna dari relasi kendaraan
        $idPengguna = $kendaraan->penggunaParkir->id_pengguna ?? null;

        if (!$idPengguna) {
            return response()->json(['message' => 'Data pengguna tidak ditemukan untuk kendaraan ini'], 404);
        }

        // Cek apakah kendaraan sudah masuk dan belum keluar
        $riwayatParkir = RiwayatParkir::where('plat_nomor', $platNomor)
            ->where('status_parkir', 'masuk')
            ->first();

        $waktuSekarang = Carbon::now();

        if ($riwayatParkir) {
            // Kendaraan keluar
            $riwayatParkir->waktu_keluar = $waktuSekarang;
            $riwayatParkir->status_parkir = 'keluar';
            $riwayatParkir->save();

            return response()->json([
                'message' => 'QR Code berhasil dipindai, kendaraan keluar.',
            ]);
        } else {
            // Kendaraan masuk
            RiwayatParkir::create([
                'id_pengguna' => $idPengguna,
                'plat_nomor' => $platNomor,
                'waktu_masuk' => $waktuSekarang,
                'status_parkir' => 'masuk',
            ]);

            return response()->json([
                'message' => 'QR Code berhasil dipindai, kendaraan masuk.',
            ]);
        }
    }


    public function CheckscanQR(Request $request)
    {
        // Ambil plat nomor dari request GET
        $platQR = $request->input('plat_nomor');

        // Validasi input
        if (!$platQR) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plat nomor dari QR tidak ditemukan.'
            ], 400);
        }

        // Cek apakah kendaraan QR terdaftar
        $kendaraan = Kendaraan::where('plat_nomor', $platQR)->first();

        if (!$kendaraan) {
            return response()->json([
                'status' => 'not_registered',
                'message' => 'Plat nomor dari QR tidak terdaftar.'
            ], 404);
        }

        // Panggil FastAPI untuk ambil hasil deteksi
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:5000/result');

            if (!$response->ok()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mendapatkan hasil deteksi dari server FastAPI.'
                ], 500);
            }

            $detectedPlat = strtoupper($response->json()['plat_nomor'] ?? '-');

            if ($detectedPlat === '-' || empty($detectedPlat)) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Plat nomor tidak terdeteksi oleh kamera.'
                ]);
            }

            // Bandingkan plat QR dan hasil deteksi
            if (strtoupper($platQR) !== $detectedPlat) {
                return response()->json([
                    'status' => 'mismatch',
                    'message' => 'Plat nomor hasil deteksi tidak sesuai dengan QR.'
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Plat nomor cocok dengan hasil deteksi.',
                'plat_nomor' => $platQR
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
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
}
