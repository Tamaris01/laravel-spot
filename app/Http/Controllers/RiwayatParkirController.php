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
            session(['scan_error' => ['message' => 'Plat nomor tidak ditemukan']]);
            return response()->json(['message' => 'Plat nomor tidak ditemukan'], 400);
        }

        // Cari kendaraan berdasarkan plat nomor di tabel Kendaraan
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();

        // Jika kendaraan tidak ditemukan
        if (!$kendaraan) {
            session(['scan_error' => ['message' => 'Plat nomor yang anda pindai tidak terdaftar di sistem']]);
            return response()->json(['message' => 'Plat nomor yang anda pindai tidak terdaftar di sistem'], 404);
        }

        // Step 2: Hubungi server deteksi plat nomor (Flask)
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:5000/result');

            if (!$response->ok()) {
                session(['scan_error' => ['message' => 'Gagal mendapatkan respons dari server deteksi plat.']]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mendapatkan respons dari server deteksi plat.'
                ], 500);
            }

            $data = $response->json();
            $platDeteksi = strtoupper($data['plat_nomor'] ?? '');

            if (empty($platDeteksi) || $platDeteksi === '-') {
                session(['scan_error' => ['message' => 'Plat nomor tidak terdeteksi.']]);
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Plat nomor tidak terdeteksi.'
                ]);
            }

            // Step 3: Verifikasi plat hasil deteksi cocok dengan plat dari QR Code
            if (strtoupper($kendaraan->plat_nomor) !== $platDeteksi) {
                session(['scan_error' => ['message' => 'Plat nomor hasil deteksi tidak sesuai dengan QR.']]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Plat nomor hasil deteksi tidak sesuai dengan QR.'
                ], 400);
            }

            // Ambil id_pengguna dari kendaraan yang ditemukan (relasi satu-ke-satu dengan pengguna_parkir)
            $idPengguna = $kendaraan->penggunaParkir->id_pengguna;

            // Cek apakah kendaraan sudah terparkir dan masih status masuk
            $riwayatParkir = RiwayatParkir::where('plat_nomor', $platNomor)
                ->where('status_parkir', 'masuk')
                ->first();

            // Ambil waktu sekarang
            $waktuSekarang = Carbon::now();

            if ($riwayatParkir) {
                // Jika kendaraan ditemukan dan statusnya masih masuk, maka ubah menjadi keluar
                $riwayatParkir->waktu_keluar = $waktuSekarang;
                $riwayatParkir->status_parkir = 'keluar';
                $riwayatParkir->save();

                return response()->json([
                    'message' => 'QR Code berhasil dipindai dan kendaraan ditemukan, silakan keluar!',

                ]);
            } else {
                // Jika kendaraan belum terparkir (status masuk), maka simpan sebagai kendaraan masuk
                RiwayatParkir::create([
                    'id_pengguna' => $idPengguna,
                    'plat_nomor' => $platNomor,
                    'waktu_masuk' => $waktuSekarang,
                    'status_parkir' => 'masuk',
                ]);

                return response()->json([
                    'message' => 'QR Code berhasil dipindai dan kendaraan ditemukan, silakan masuk!',

                ]);
            }
        } catch (\Exception $e) {
            // Tangani error jika terjadi masalah dengan server deteksi
            session(['scan_error' => ['message' => 'Terjadi kesalahan: ' . $e->getMessage()]]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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
