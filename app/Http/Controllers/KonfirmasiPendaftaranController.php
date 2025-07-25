<?php

namespace App\Http\Controllers;

use App\Models\PenggunaParkir;
use App\Models\Kendaraan;
use Illuminate\Http\Request;

class KonfirmasiPendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('rows', 5);

        $pendaftar = PenggunaParkir::where('status', 'nonaktif')
            ->paginate($perPage)
            ->withQueryString(); // agar perPage tetap saat pindah halaman

        $cloudName = env('CLOUDINARY_CLOUD_NAME');

        return view('pengelola.konfirmasi_pendaftaran', [
            'pendaftar' => $pendaftar,
            'cloudName' => $cloudName,
            'perPage' => $perPage
        ]);
    }


    // Fungsi pencarian pendaftar berdasarkan nama atau email
    public function search(Request $request)
    {
        // Mengambil input pencarian
        $query = $request->input('query');

        // Melakukan pencarian pengguna berdasarkan nama atau email
        $pendaftar = PenggunaParkir::where('status', 'nonaktif')
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('nama', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%");
            })
            ->paginate(10);

        // Mengembalikan tampilan dengan data pengguna yang difilter
        return view('pengelola.konfirmasi_pendaftaran', compact('pendaftar'));
    }

    // Mengubah status pengguna menjadi 'aktif'
    public function terima($id_pengguna)
    {
        // Mengubah status pengguna menjadi 'aktif'
        $pengguna = PenggunaParkir::findOrFail($id_pengguna);
        $pengguna->status = 'aktif';
        $pengguna->save();

        // Mengalihkan kembali dengan pesan sukses
        return redirect()->route('pengelola.konfirmasi_pendaftaran')->with('success', 'Pengguna telah diterima.');
    }

    // Menolak pendaftaran dan menghapus kendaraan serta data pengguna
    public function tolak($id_pengguna)
    {
        // Menghapus kendaraan terkait dan menghapus pengguna
        $pengguna = PenggunaParkir::findOrFail($id_pengguna);
        Kendaraan::where('id_pengguna', $pengguna->id)->delete();
        $pengguna->delete();

        // Mengalihkan kembali dengan pesan sukses
        return redirect()->route('pengelola.konfirmasi_pendaftaran')->with('success', 'Pengguna telah ditolak dan datanya dihapus.');
    }
}
