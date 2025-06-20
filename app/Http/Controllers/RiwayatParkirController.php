<?php

namespace App\Http\Controllers;

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
        // Validasi data QR Code
        $validated = $request->validate([
            'qr_code' => 'required|string|max:255|regex:/^[A-Za-z0-9]+$/',
        ]);

        $platNomor = $validated['qr_code'];
        $currentTime = Carbon::now();

        // Mencocokkan plat nomor dengan kendaraan
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();

        if (!$kendaraan) {
            return response()->json([
                'message' => 'Plat nomor tidak ditemukan',
            ], 404);
        }

        // Mengambil id_pengguna dari tabel kendaraan
        $idPengguna = $kendaraan->id_pengguna;

        // Logika untuk mencatat status parkir
        $riwayat = RiwayatParkir::where('plat_nomor', $platNomor)
            ->where(function ($query) {
                $query->whereNull('waktu_keluar')
                    ->orWhereDate('waktu_keluar', Carbon::today()->format('Y-m-d'));
            })
            ->first();

        if ($riwayat) {
            // Jika status sebelumnya 'masuk' dan belum keluar, maka update status ke keluar
            if ($riwayat->status_parkir === 'masuk' && is_null($riwayat->waktu_keluar)) {
                $riwayat->update([
                    'waktu_keluar' => $currentTime,
                    'status_parkir' => 'keluar',
                ]);

                return response()->json([
                    'message' => 'Status parkir diubah menjadi keluar',
                    'data' => $riwayat,
                ], 200);
            }
        } else {
            // Periksa duplikasi status masuk tanpa keluar
            $duplicateEntry = RiwayatParkir::where('plat_nomor', $platNomor)
                ->where('status_parkir', 'masuk')
                ->whereNull('waktu_keluar')
                ->exists();

            if ($duplicateEntry) {
                return response()->json([
                    'message' => 'Kendaraan ini sudah terdaftar sebagai masuk tanpa status keluar.',
                ], 409);
            }

            // Buat entri baru untuk masuk
            $riwayat = RiwayatParkir::create([
                'id_pengguna' => $idPengguna,
                'plat_nomor' => $platNomor,
                'waktu_masuk' => $currentTime,
                'status_parkir' => 'masuk',
            ]);

            return response()->json([
                'message' => 'Status parkir diubah menjadi masuk',
                'data' => $riwayat,
            ], 201);
        }

        return response()->json([
            'message' => 'Tidak ada perubahan status parkir.',
        ], 400);
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
