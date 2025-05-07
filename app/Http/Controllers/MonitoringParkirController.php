<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatParkir;
use App\Models\Kendaraan;
use App\Models\PenggunaParkir;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MonitoringParkirController extends Controller
{
    /**
     * Menampilkan halaman monitoring parkir tanpa filter pencarian.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('rows', 10);
        $today = Carbon::today();

        // Data untuk halaman utama (pakai pagination)
        $riwayatParkir = RiwayatParkir::with(['pengguna', 'kendaraan'])
            ->whereDate('waktu_masuk', $today)
            ->orderBy('waktu_masuk', 'desc');

        // Jika request AJAX, kirim semua data (tanpa paginate)
        if ($request->ajax()) {
            $data = $riwayatParkir->get();
            return response()->json($data);
        }

        // Jika bukan AJAX, kirim ke view dengan paginate
        $riwayatParkir = $riwayatParkir->paginate($perPage);
        return view('pengelola.monitoring', compact('riwayatParkir', 'perPage'));
    }




    /**
     * Menampilkan hasil pencarian berdasarkan query.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', ''); // Dapatkan input pencarian
        $perPage = $request->get('rows', 10); // Default 10 rows per page

        // Menampilkan data yang sesuai dengan pencarian
        $riwayatParkir = RiwayatParkir::with(['pengguna', 'kendaraan'])
            ->where('plat_nomor', 'like', "%$query%")
            ->orWhere('id_pengguna', 'like', "%$query%")
            ->paginate($perPage);

        // Mengirim data ke view
        return view('pengelola.monitoring', compact('riwayatParkir', 'query', 'perPage'));
    }

    // Fungsi untuk cek apakah plat nomor ada di database
    public function checkPlate($platNomor)
    {
        // Mencari plat nomor di tabel kendaraan
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();

        // Jika ditemukan, kembalikan response success
        if ($kendaraan) {
            return response()->json([
                'exists' => true,
                'message' => "Plat nomor $platNomor ada di database."
            ]);
        } else {
            // Jika tidak ditemukan, kembalikan response not found
            return response()->json([
                'exists' => false,
                'message' => "Plat nomor $platNomor tidak ada di database."
            ], 404);
        }
    }
}
