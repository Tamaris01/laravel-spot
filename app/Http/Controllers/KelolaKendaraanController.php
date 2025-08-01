<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\PenggunaParkir; // Menggunakan model PenggunaParkir
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\KendaraanRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class KelolaKendaraanController extends Controller
{
    protected $jenisArray;
    protected $warnaArray;

    public function __construct()
    {
        // Inisialisasi nilai enum jenis kendaraan dan warna kendaraan
        $this->jenisArray = $this->getEnumValues('kendaraan', 'jenis');
        $this->warnaArray = $this->getEnumValues('kendaraan', 'warna');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('rows', 5);

        // Ambil enum jenis kendaraan dan warna kendaraan
        $this->jenisArray = $this->getEnumValues('kendaraan', 'jenis');
        $this->warnaArray = $this->getEnumValues('kendaraan', 'warna');

        // Ambil kendaraan dengan status pengguna parkir aktif
        $kendaraan = Kendaraan::with('penggunaParkir')
            ->whereHas('penggunaParkir', function ($query) {
                $query->where('status', 'aktif');  // Filter berdasarkan status aktif
            })
            ->paginate($perPage);

        // Ambil plat nomor kendaraan jika ada di request
        $platNomor = $request->input('plat_nomor');

        // Tentukan kendaraan terkait berdasarkan plat nomor
        $kendaraanTerkait = null;
        if ($platNomor) {
            $kendaraanTerkait = Kendaraan::where('plat_nomor', $platNomor)->first();
        }

        // Tentukan pengguna terkait dengan kendaraan tersebut
        $penggunaTerkait = null;
        if ($kendaraanTerkait) {
            $penggunaTerkait = $kendaraanTerkait->penggunaParkir;
        }

        // Tentukan pengguna yang belum memiliki kendaraan
        $penggunaTanpaKendaraan = PenggunaParkir::doesntHave('kendaraan')->get();

        // Gabungkan pengguna terkait dengan pengguna tanpa kendaraan
        $penggunaDropdown = collect();

        if ($penggunaTerkait) {
            $penggunaDropdown->push($penggunaTerkait); // Masukkan pengguna yang terkait
        }

        $penggunaDropdown = $penggunaDropdown->merge($penggunaTanpaKendaraan);

        // Tentukan nilai id_penggunaTerkait (id pengguna yang sedang diedit)
        $idPenggunaTerkait = $penggunaTerkait ? $penggunaTerkait->id_pengguna : null;

        // Kirim ke view dengan data yang dibutuhkan
        return view('pengelola.kelola_kendaraan', [
            'kendaraan' => $kendaraan,
            'jenisArray' => $this->jenisArray,
            'warnaArray' => $this->warnaArray,
            'perPage' => $perPage,
            'penggunaDropdown' => $penggunaDropdown,
            'idPenggunaTerkait' => $idPenggunaTerkait,
        ]);
    }



    public function search(Request $request)
    {
        $perPage = $request->input('rows', 10);
        $query = $request->get('query');

        // Mencari kendaraan berdasarkan id_pengguna, plat_nomor, atau jenis
        $kendaraan = Kendaraan::where('id_pengguna', 'LIKE', "%$query%")
            ->orWhere('plat_nomor', 'LIKE', "%$query%")
            ->orWhere('jenis', 'LIKE', "%$query%")
            ->paginate($perPage);

        // Kembalikan hasil pencarian ke tampilan
        return view('pengelola.kelola_kendaraan', [
            'kendaraan' => $kendaraan,
            'query' => $query,
            'perPage' => $perPage,
            'jenisArray' => $this->jenisArray,
            'warnaArray' => $this->warnaArray,
        ]);
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        // Ambil hanya pengguna aktif yang belum memiliki kendaraan
        $penggunaParkir = PenggunaParkir::where('status', 'aktif')
            ->doesntHave('kendaraan')
            ->get();

        return view('pengelola.kelola_kendaraan.create', compact('penggunaParkir'));
    }


    /**
     * Store a newly created vehicle in the database.
     */
    public function store(KendaraanRequest $request)
    {
        try {
            // Validasi ID Pengguna dan cek apakah ID Pengguna ada di database
            if (is_null($request->id_pengguna)) {
                return redirect()->back()->with('error', 'ID Pengguna tidak boleh kosong.');
            }

            // Cek apakah pengguna sudah memiliki kendaraan
            $existingVehicle = Kendaraan::where('id_pengguna', $request->id_pengguna)->first();
            if ($existingVehicle) {
                return redirect()->back()->with('error', 'Pengguna ini sudah memiliki kendaraan, tidak dapat menambahkan lebih dari satu.');
            }

            // Validasi dan simpan detail kendaraan
            $kendaraan = new Kendaraan();
            $kendaraan->plat_nomor = $request->plat_nomor;
            $kendaraan->jenis = $request->jenis;
            $kendaraan->warna = ucwords(strtolower($request->warna));

            $kendaraan->id_pengguna = $request->id_pengguna;
            if ($request->hasFile('foto_kendaraan')) {
                $uploadResult = Cloudinary::upload(
                    $request->file('foto_kendaraan')->getRealPath(),
                    ['folder' => 'images/kendaraan', 'resource_type' => 'image']
                );
                $kendaraan->foto = $uploadResult->getSecurePath();
            }

            $kendaraan->save();

            // Redirect ke halaman kendaraan dengan pesan sukses
            return redirect()->route('pengelola.kelola_kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan');
        } catch (\Exception $e) {
            // Log error jika ada kegagalan
            Log::error('Failed to store vehicle: ' . $e->getMessage());

            // Berikan pesan kesalahan yang lebih informatif
            return redirect()->back()->with('error', 'Gagal menambahkan kendaraan. Silakan coba lagi atau hubungi admin jika kesalahan berlanjut.');
        }
    }


    public function edit($platNomor)
    {
        try {
            $platNomor = urldecode($platNomor); // tambahkan ini
            // Ambil data kendaraan beserta pengguna terkait
            $kendaraan = Kendaraan::with('penggunaParkir')->where('plat_nomor', $platNomor)->firstOrFail();

            // Ambil semua data pengguna untuk dropdown
            $penggunaParkir = PenggunaParkir::select('id_pengguna', 'nama')->get();

            // Return ke view dengan data
            return view('pengelola.kelola_kendaraan.edit', compact('kendaraan', 'penggunaParkir'));
        } catch (\Exception $e) {
            // Redirect jika data tidak ditemukan
            return redirect()->route('pengelola.kelola_kendaraan.index')->with('error', 'Kendaraan tidak ditemukan.');
        }
    }


    public function update(KendaraanRequest $request, $plat_nomor)
    {
        $plat_nomor = urldecode($plat_nomor); // penting!
        $kendaraan = Kendaraan::where('plat_nomor', $plat_nomor)->first();

        if (!$kendaraan) {
            return back()->with('error', 'Kendaraan tidak ditemukan.');
        }

        $oldFoto = $kendaraan->foto;
        $kendaraan->jenis = $request->jenis;
        $kendaraan->warna = ucwords(strtolower($request->warna));

        if ($request->id_pengguna != $kendaraan->id_pengguna) {
            if (Kendaraan::where('id_pengguna', $request->id_pengguna)->exists()) {
                return back()->with('error', 'Pengguna yang dipilih sudah memiliki kendaraan.');
            }
            $kendaraan->id_pengguna = $request->id_pengguna;
        }

        if ($request->hasFile('foto_kendaraan')) {
            // Hapus foto lama dari Cloudinary jika ada
            if ($oldFoto) {
                $parsedUrl = parse_url($oldFoto);
                $path = $parsedUrl['path'] ?? '';
                $publicId = pathinfo($path, PATHINFO_FILENAME);
                if ($publicId) {
                    Cloudinary::destroy("images/kendaraan/{$publicId}");
                }
            }

            // Upload foto baru
            $uploadResult = Cloudinary::upload(
                $request->file('foto_kendaraan')->getRealPath(),
                ['folder' => 'images/kendaraan', 'resource_type' => 'image']
            );
            $kendaraan->foto = $uploadResult->getSecurePath();
        }

        $kendaraan->save();

        return redirect()->route('pengelola.kelola_kendaraan.index')->with('success', 'Kendaraan berhasil diperbarui!');
    }



    /**
     * Delete the specified vehicle.
     */
    public function destroy($platNomor)
    {
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->firstOrFail();

        try {
            if ($kendaraan->foto) {
                $parsedUrl = parse_url($kendaraan->foto);
                $path = $parsedUrl['path'] ?? '';
                $publicId = pathinfo($path, PATHINFO_FILENAME);
                if ($publicId) {
                    Cloudinary::destroy("images/kendaraan/{$publicId}");
                }
            }

            $kendaraan->delete();
            return redirect()->route('pengelola.kelola_kendaraan.index')->with('success', 'Kendaraan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Failed to delete vehicle: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus kendaraan.');
        }
    }

    /**
     * Generate QR code for the vehicle.
     */

    /**
     * Get enum values from a given table and column.
     */
    protected function getEnumValues($table, $column)
    {
        $result = DB::select("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column]);
        if (count($result) > 0) {
            $type = $result[0]->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            $enum = [];

            foreach (explode(',', $matches[1]) as $value) {
                $enum[] = trim($value, "'");
            }

            return $enum;
        }

        return []; // Return an empty array if no result
    }
}
