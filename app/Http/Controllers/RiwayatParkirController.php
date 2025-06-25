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
        // Validasi data input dari request
        $validated = $request->validate([
            'plat_nomor' => 'required|string|max:255|regex:/^[A-Za-z0-9\s]+$/',
        ]);

        // Bersihkan dan ubah plat nomor jadi huruf besar tanpa spasi
        $platNomorInput = strtoupper(preg_replace('/\s+/', '', $validated['plat_nomor']));
        $currentTime = Carbon::now();

        // Cari kendaraan dengan plat nomor yang cocok
        $kendaraan = Kendaraan::whereRaw("REPLACE(UPPER(plat_nomor), ' ', '') = ?", [$platNomorInput])->first();

        if (!$kendaraan) {
            return response()->json([
                'message' => 'Plat nomor tidak ditemukan',
            ], 404);
        }

        $idPengguna = $kendaraan->id_pengguna;
        $platNomor = $kendaraan->plat_nomor; // gunakan format asli dari database

        // Cek apakah ada riwayat yang belum keluar
        $riwayat = RiwayatParkir::where('plat_nomor', $platNomor)
            ->where(function ($query) {
                $query->whereNull('waktu_keluar')
                    ->orWhereDate('waktu_keluar', Carbon::today()->format('Y-m-d'));
            })
            ->first();

        if ($riwayat && $riwayat->status_parkir === 'masuk' && is_null($riwayat->waktu_keluar)) {
            // Update ke keluar
            $riwayat->update([
                'waktu_keluar' => $currentTime,
                'status_parkir' => 'keluar',
            ]);

            return response()->json([
                'message' => 'Status parkir diubah menjadi keluar',
                'data' => $riwayat,
            ], 200);
        }

        // Cek duplikasi status masuk
        $duplicateEntry = RiwayatParkir::where('plat_nomor', $platNomor)
            ->where('status_parkir', 'masuk')
            ->whereNull('waktu_keluar')
            ->exists();

        if ($duplicateEntry) {
            return response()->json([
                'message' => 'Kendaraan ini sudah terdaftar sebagai masuk tanpa status keluar.',
            ], 409);
        }

        // Catat sebagai masuk
        $riwayatBaru = RiwayatParkir::create([
            'id_pengguna'   => $idPengguna,
            'plat_nomor'    => $platNomor,
            'waktu_masuk'   => $currentTime,
            'status_parkir' => 'masuk',
        ]);

        return response()->json([
            'message' => 'Status parkir diubah menjadi masuk',
            'data' => $riwayatBaru,
        ], 201);
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
