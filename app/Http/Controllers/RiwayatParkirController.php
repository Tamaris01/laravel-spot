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
        $platNomor = $request->input('plat_nomor');

        // Validasi awal
        if (!$platNomor) {
            session(['scan_error' => ['message' => 'Plat nomor tidak ditemukan dalam permintaan.']]);
            return response()->json(['message' => 'Plat nomor tidak ditemukan dalam permintaan.'], 400);
        }

        // Cari data kendaraan
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();

        if (!$kendaraan) {
            session(['scan_error' => ['message' => 'Plat nomor tidak terdaftar.']]);
            return response()->json(['message' => 'Plat nomor yang anda pindai tidak terdaftar di sistem.'], 404);
        }

        try {
            // Coba hubungi server Flask untuk deteksi plat
            $response = Http::timeout(5)->get('https://alpu.web.id/server/result');

            if (!$response->ok()) {
                session(['scan_error' => ['message' => 'Gagal mendapatkan respons dari server deteksi plat.']]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mendapatkan respons dari server deteksi plat. Silakan coba lagi atau pindai QR.'
                ], 500);
            }

            $data = $response->json();
            $platDeteksi = strtoupper($data['plat_nomor'] ?? '');

            // Jika tidak terdeteksi atau deteksi tidak valid
            if (empty($platDeteksi) || $platDeteksi === '-') {
                session(['scan_error' => ['message' => 'Plat nomor tidak terdeteksi oleh sistem.']]);
                return response()->json([
                    'status' => 'not_detected',
                    'message' => 'Plat nomor tidak terdeteksi. Silakan coba lagi atau pindai QR code.'
                ]);
            }

            // Jika plat hasil deteksi tidak cocok dengan yang dari QR/input
            if (strtoupper($kendaraan->plat_nomor) !== $platDeteksi) {
                session(['scan_error' => ['message' => 'Plat nomor hasil deteksi tidak sesuai dengan QR.']]);
                return response()->json([
                    'status' => 'mismatch',
                    'message' => 'Plat nomor hasil deteksi tidak sesuai dengan QR. Silakan coba lagi atau pindai ulang QR.'
                ], 400);
            }

            // Proses lanjut: pencatatan riwayat parkir
            $idPengguna = $kendaraan->penggunaParkir->id_pengguna;
            $waktuSekarang = Carbon::now();

            $riwayatParkir = RiwayatParkir::where('plat_nomor', $platNomor)
                ->where('status_parkir', 'masuk')
                ->first();

            if ($riwayatParkir) {
                $riwayatParkir->waktu_keluar = $waktuSekarang;
                $riwayatParkir->status_parkir = 'keluar';
                $riwayatParkir->save();

                return response()->json([
                    'message' => 'Kendaraan ditemukan dan status diperbarui menjadi keluar. Silakan keluar.'
                ]);
            } else {
                RiwayatParkir::create([
                    'id_pengguna' => $idPengguna,
                    'plat_nomor' => $platNomor,
                    'waktu_masuk' => $waktuSekarang,
                    'status_parkir' => 'masuk',
                ]);

                return response()->json([
                    'message' => 'Kendaraan ditemukan dan status diperbarui menjadi masuk. Silakan masuk.'
                ]);
            }
        } catch (\Exception $e) {
            session(['scan_error' => ['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau gunakan QR sebagai alternatif.'
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
