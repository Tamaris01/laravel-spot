<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardPenggunaController extends Controller
{
    public function dashboard()
    {
        // Pastikan pengguna login
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $date = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');
        $today = Carbon::now()->toDateString();

        // Mengambil detail pengguna dari tabel pengguna_parkir
        $penggunaDetail = DB::table('pengguna_parkir')
            ->where('id_pengguna', $user->id_pengguna) // Ambil data pengguna berdasarkan id_pengguna
            ->select(['id_pengguna', 'nama', 'kategori', 'email', 'foto']) // Tentukan kolom yang diambil
            ->first();

        // Mengambil kendaraan yang terkait dengan pengguna
        $kendaraan = DB::table('kendaraan')
            ->where('id_pengguna', $user->id_pengguna) // Ambil kendaraan berdasarkan pengguna
            ->select(['plat_nomor']) // Ambil kolom plat_nomor
            ->first();

        // Buat path QR code jika kendaraan ada
        $qrCodePath = $kendaraan
            ? 'https://res.cloudinary.com/dusw72eit/image/upload/images/qrcodes/' . rawurlencode($kendaraan->plat_nomor) . '.svg'
            : null;


        // Jumlah pengguna unik dari tabel riwayat_parkir berdasarkan hari ini
        $jumlahPengguna = DB::table('riwayat_parkir')
            ->whereDate('waktu_masuk', $today)
            ->distinct('id_pengguna') // Hitung pengguna unik
            ->count('id_pengguna');

        // Jumlah parkir yang statusnya 'masuk' dan 'keluar' berdasarkan hari ini
        $jumlahParkirMasuk = DB::table('riwayat_parkir')
            ->whereDate('waktu_masuk', $today)
            ->where('status_parkir', 'masuk')
            ->count();

        $jumlahParkirKeluar = DB::table('riwayat_parkir')
            ->whereDate('waktu_keluar', $today)
            ->where('status_parkir', 'keluar')
            ->count();
        // Hitung total pengguna aktif untuk judul
        $jumlahPenggunaAktif = DB::table('session_penggunaparkir')
            ->whereNull('session_end')
            ->count();
        // Ambil 5 pengguna aktif terbaru saja
        $penggunaAktif = DB::table('session_penggunaparkir')
            ->join('pengguna_parkir', 'session_penggunaparkir.id_pengguna', '=', 'pengguna_parkir.id_pengguna')
            ->whereNull('session_penggunaparkir.session_end')
            ->select('pengguna_parkir.id_pengguna', 'pengguna_parkir.nama')
            ->orderBy('session_penggunaparkir.session_start', 'desc')
            ->limit(3)
            ->get();

        // Hitung jumlah pengguna aktif lainnya
        $jumlahPenggunaAktifLainnya = max($jumlahPenggunaAktif - 3, 0);



        // Kirim semua data ke tampilan
        return view('pengguna.dashboard', compact(
            'user',
            'date',
            'penggunaDetail',
            'kendaraan',
            'qrCodePath',
            'jumlahPengguna',
            'jumlahParkirMasuk',
            'jumlahParkirKeluar',
            'jumlahPenggunaAktif',
            'penggunaAktif',
            'jumlahPenggunaAktifLainnya'
        ));
    }
}
