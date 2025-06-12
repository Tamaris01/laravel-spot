<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\PenggunaParkir; // Menggunakan model PenggunaParkir
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\KendaraanRequest;
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
        // Get a list of all pengguna_parkir (assuming you have a PenggunaParkir model)
        $penggunaParkir = PenggunaParkir::all(); // You can add more filtering if necessary

        return view('pengelola.kelola_kendaraan.create', compact('penggunaParkir'));
    }

    /**
     * Store a newly created vehicle in the database.
     */
    public function store(KendaraanRequest $request)
    {
        try {
            if (is_null($request->id_pengguna)) {
                return redirect()->back()->with('error', 'ID Pengguna tidak boleh kosong.');
            }

            $existingVehicle = Kendaraan::where('id_pengguna', $request->id_pengguna)->first();
            if ($existingVehicle) {
                return redirect()->back()->with('error', 'Pengguna ini sudah memiliki kendaraan.');
            }

            $kendaraan = new Kendaraan();
            $kendaraan->plat_nomor = $request->plat_nomor;
            $kendaraan->jenis = $request->jenis;
            $kendaraan->warna = ucwords(strtolower($request->warna));
            $kendaraan->id_pengguna = $request->id_pengguna;

            if ($request->hasFile('foto')) {
                $request->validate([
                    'foto' => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);

                // Upload ke Cloudinary dengan folder "images/kendaraan"
                $uploadedFileUrl = Cloudinary::upload(
                    $request->file('foto')->getRealPath(),
                    ['folder' => 'images/kendaraan']
                )->getSecurePath();

                // Simpan URL ke database
                $kendaraan->foto = $uploadedFileUrl;
            }

            $kendaraan->save();

            return redirect()->route('pengelola.kelola_kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Failed to store vehicle: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan kendaraan.');
        }
    }


    public function edit($platNomor)
    {
        try {
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
        // Mencari kendaraan berdasarkan plat_nomor
        $kendaraan = Kendaraan::where('plat_nomor', $plat_nomor)->first();

        if (!$kendaraan) {
            Log::warning("Kendaraan dengan plat nomor {$plat_nomor} tidak ditemukan.");
            return redirect()->route('pengelola.kelola_kendaraan.index')->with('error', 'Kendaraan tidak ditemukan!');
        }

        // Mencatat log awal pembaruan
        Log::info("Memulai pembaruan kendaraan dengan plat nomor {$plat_nomor}");

        // Menyimpan data lama untuk log perubahan
        $oldJenis = $kendaraan->jenis;
        $oldWarna = $kendaraan->warna;
        $oldFoto = $kendaraan->foto;
        $oldIdPengguna = $kendaraan->id_pengguna;

        // Validasi id_pengguna jika diubah
        if ($request->id_pengguna && $request->id_pengguna != $oldIdPengguna) {
            $newIdPengguna = $request->id_pengguna;

            // Pastikan pengguna yang dipilih belum memiliki kendaraan
            $userHasVehicle = Kendaraan::where('id_pengguna', $newIdPengguna)->exists();
            if ($userHasVehicle) {
                Log::warning("Pengguna dengan ID {$newIdPengguna} sudah memiliki kendaraan. Pembaruan ditolak.");
                return redirect()->back()->with('error', 'Pengguna yang dipilih sudah memiliki kendaraan!');
            }

            // Update id_pengguna
            $kendaraan->id_pengguna = $newIdPengguna;
            Log::info("Id pengguna kendaraan dengan plat nomor {$plat_nomor} diubah menjadi {$newIdPengguna}");
        }

        // Update jenis dan warna kendaraan
        $kendaraan->jenis = $request->jenis;
        $kendaraan->warna = $request->warna;

        // Mengelola upload foto kendaraan jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama dari Cloudinary jika bukan default
            if ($oldFoto && !str_contains($oldFoto, 'default.jpg')) {
                $publicId = pathinfo(basename($oldFoto), PATHINFO_FILENAME);
                Cloudinary::destroy("images/kendaraan/{$publicId}");
                Log::info("Foto lama kendaraan dengan plat nomor {$plat_nomor} dihapus dari Cloudinary: {$publicId}");
            }

            // Upload foto baru ke Cloudinary
            $uploadedFileUrl = Cloudinary::upload(
                $request->file('foto')->getRealPath(),
                [
                    'folder' => 'images/kendaraan',
                ]
            )->getSecurePath();

            // Update path foto ke database
            $kendaraan->foto = $uploadedFileUrl;
            Log::info("Foto baru kendaraan dengan plat nomor {$plat_nomor} diupload ke Cloudinary: {$uploadedFileUrl}");
        }

        // Simpan perubahan ke database
        $kendaraan->save();

        // Catat perubahan yang dilakukan
        Log::info("Kendaraan dengan plat nomor {$plat_nomor} berhasil diperbarui. Perubahan: " .
            "Jenis dari {$oldJenis} menjadi {$kendaraan->jenis}, " .
            "Warna dari {$oldWarna} menjadi {$kendaraan->warna}, " .
            "Id Pengguna dari {$oldIdPengguna} menjadi {$kendaraan->id_pengguna}, " .
            "Foto dari {$oldFoto} menjadi {$kendaraan->foto}.");

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('pengelola.kelola_kendaraan.index')->with('success', 'Kendaraan berhasil diperbarui!');
    }



    /**
     * Delete the specified vehicle.
     */
    public function destroy($platNomor)
    {
        // Find the vehicle by plat nomor
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->firstOrFail();

        try {
            // Delete the vehicle's photo if it exists
            if ($kendaraan->foto) {
                Storage::disk('public')->delete($kendaraan->foto);
            }

            // Delete the QR code if it exists
            if ($kendaraan->qr_code_url) {
                Storage::disk('public')->delete($kendaraan->qr_code_url);
            }

            // Delete the vehicle record
            $kendaraan->delete();

            return redirect()->route('pengelola.kelola_kendaraan.index')->with('success', 'Kendaraan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Failed to delete vehicle: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus kendaraan');
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
