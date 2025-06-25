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
        // Validasi input
        $validated = $request->validate([
            'plat_nomor' => 'required|string|max:255|regex:/^[A-Za-z0-9\s]+$/',
        ]);

        $platNomorInput = strtoupper(preg_replace('/\s+/', '', $validated['plat_nomor']));
        $currentTime = Carbon::now();

        // Cari kendaraan berdasarkan plat_nomor tanpa spasi dan huruf besar
        $kendaraan = Kendaraan::whereRaw("REPLACE(UPPER(plat_nomor), ' ', '') = ?", [$platNomorInput])->first();

        if (!$kendaraan) {
            return response()->json([
                'message' => 'Plat nomor tidak ditemukan',
            ], 404);
        }

        $idPengguna = $kendaraan->id_pengguna;
        $platNomor = $kendaraan->plat_nomor; // pakai format asli dari database

        // Ambil entri terakhir dari plat nomor ini
        $riwayat = RiwayatParkir::where('plat_nomor', $platNomor)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$riwayat) {
            // Belum ada data → buat status masuk
            $riwayatBaru = RiwayatParkir::create([
                'id_pengguna'   => $idPengguna,
                'plat_nomor'    => $platNomor,
                'waktu_masuk'   => $currentTime,
                'status_parkir' => 'masuk',
            ]);

            return response()->json([
                'message' => 'Status parkir diubah menjadi masuk',
                'data'    => $riwayatBaru,
            ], 201);
        }

        if ($riwayat->status_parkir === 'masuk' && $riwayat->waktu_keluar === null) {
            // Jika terakhir adalah masuk dan belum keluar → update jadi keluar
            $riwayat->update([
                'waktu_keluar'  => $currentTime,
                'status_parkir' => 'keluar',
            ]);

            return response()->json([
                'message' => 'Status parkir diubah menjadi keluar',
                'data'    => $riwayat,
            ], 200);
        }

        if ($riwayat->status_parkir === 'keluar') {
            // Terakhir keluar → buat status masuk baru
            $riwayatBaru = RiwayatParkir::create([
                'id_pengguna'   => $idPengguna,
                'plat_nomor'    => $platNomor,
                'waktu_masuk'   => $currentTime,
                'status_parkir' => 'masuk',
            ]);

            return response()->json([
                'message' => 'Status parkir diubah menjadi masuk',
                'data'    => $riwayatBaru,
            ], 201);
        }

        // Kondisi lain yang tidak dikenali
        return response()->json([
            'message' => 'Gagal memproses status parkir.',
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
