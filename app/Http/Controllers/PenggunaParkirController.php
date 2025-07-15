<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PenggunaParkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Kendaraan;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class PenggunaParkirController extends Controller
{
    protected $kategoriArray;

    public function __construct()
    {
        // Inisialisasi nilai enum kategori
        $this->kategoriArray = $this->getEnumValues('pengguna_parkir', 'kategori');
    }

    public function index(Request $request)
    {
        // Mendapatkan jumlah item per halaman dari parameter 'rows' (default ke 10)
        $perPage = $request->input('rows', 5);

        // Mendapatkan data pengguna dengan status aktif dan paginasi
        $pengguna = PenggunaParkir::where('status', 'aktif')->paginate($perPage);

        // Mengirimkan data pengguna dan kategori ke view
        return view('pengelola.kelola_pengguna', [
            'pengguna' => $pengguna,
            'kategoriArray' => $this->kategoriArray,
            'perPage' => $perPage // Menyertakan perPage untuk form filter jumlah per halaman
        ]);
    }

    public function search(Request $request)
    {
        // Mengambil query dari input pencarian
        $perPage = $request->get('rows', 10); // Default 10 rows per page
        $query = $request->input('query');

        // Mencari data pengguna berdasarkan nama atau email
        $pengguna = PenggunaParkir::where('nama', 'LIKE', "%$query%")
            ->orWhere('email', 'LIKE', "%$query%")
            ->paginate($perPage);

        // Mengembalikan hasil pencarian ke view yang sama
        return view('pengelola.kelola_pengguna', compact('pengguna', 'query', 'perPage'));
    }

    protected function getEnumValues($table, $column)
    {
        // Ambil nilai enum dari kolom yang diberikan
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

        return []; // Kembalikan array kosong jika tidak ada hasil
    }

    public function create()
    {
        return view('pengelola.modal.edit_pengguna', [
            'kategoriArray' => $this->kategoriArray // Pass the kategoriArray for form options
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pengguna' => 'required_if:kategori,!Tamu|string|max:255|unique:pengguna_parkir,id_pengguna',
            'kategori' => 'required|string|in:' . implode(',', $this->getEnumValues('pengguna_parkir', 'kategori')),
            'nama' => 'required|string|regex:/^[A-Z][a-zA-Z\s]*$/|max:50',
            'email' => 'required|email|unique:pengguna_parkir,email|max:255',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $fotoUrl = null;
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $dimensions = getimagesize($foto);
                if ($dimensions[0] !== 472 || $dimensions[1] !== 472) {
                    return redirect()->back()->withErrors(['foto' => 'Dimensi foto harus 472x472 piksel.'])->withInput();
                }

                $fotoUrl = Cloudinary::upload($foto->getRealPath(), [
                    'folder' => 'images/profil'
                ])->getSecurePath();
            }

            $pengguna = new PenggunaParkir();
            $pengguna->id_pengguna = $request->kategori !== 'Tamu'
                ? $request->id_pengguna
                : 'Tamu_' . mt_rand(10000000, 99999999);
            $pengguna->nama = $request->nama;
            $pengguna->email = $request->email;
            $pengguna->password = $request->password;
            $pengguna->foto = $fotoUrl;
            $pengguna->kategori = $request->kategori;
            $pengguna->status = 'aktif';
            $pengguna->save();

            return redirect()->route('pengelola.kelola_pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error saving pengguna: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengguna.');
        }
    }

    public function edit($id_pengguna)
    {
        // Menggunakan find untuk mencari berdasarkan id_pengguna
        $pengguna = PenggunaParkir::findOrFail($id_pengguna);

        return response()->json([
            'view' => view('pengelola.kelola_pengguna.edit', compact('pengguna'))->render()
        ]);
    }

    public function update(Request $request, $id_pengguna)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|in:' . implode(',', $this->getEnumValues('pengguna_parkir', 'kategori')),
            'nama' => 'required|string|regex:/^[A-Z][a-zA-Z\s,\.]*$/|max:50',
            'email' => 'required|email|unique:pengguna_parkir,email,' . $id_pengguna . ',id_pengguna|max:255',
            'password' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $pengguna = PenggunaParkir::findOrFail($id_pengguna);
            $oldFoto = $pengguna->foto;

            // Upload ke Cloudinary jika ada file foto baru
            if ($request->hasFile('foto')) {
                // Hapus foto lama dari Cloudinary jika bukan default
                if ($oldFoto && !str_contains($oldFoto, 'default.jpg')) {
                    $publicId = pathinfo(basename($oldFoto), PATHINFO_FILENAME);
                    Cloudinary::destroy("images/profil/{$publicId}");
                    Log::info("Foto lama pengguna dengan ID {$id_pengguna} dihapus dari Cloudinary: {$publicId}");
                }

                // Upload foto baru
                $uploadedFileUrl = Cloudinary::upload($request->file('foto')->getRealPath(), [
                    'folder' => 'images/profil',
                ])->getSecurePath();

                $pengguna->foto = $uploadedFileUrl;
                Log::info("Foto baru pengguna dengan ID {$id_pengguna} diupload ke Cloudinary: {$uploadedFileUrl}");
            }

            if ($request->filled('password')) {
                $pengguna->password = $request->password;
            }

            $pengguna->update([
                'kategori' => $validated['kategori'],
                'nama' => $validated['nama'],
                'email' => $validated['email'],
            ]);

            return redirect()->route('pengelola.kelola_pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui pengguna: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui pengguna.');
        }
    }




    public function destroy($id_pengguna)
    {
        Log::info("Menghapus pengguna dengan ID: $id_pengguna");

        $pengguna = PenggunaParkir::find($id_pengguna);

        if (!$pengguna) {
            Log::error("Pengguna dengan ID $id_pengguna tidak ditemukan.");
            return redirect()->route('pengelola.kelola_pengguna.index')->with('error', 'Pengguna tidak ditemukan.');
        }

        try {
            // Hapus foto profil pengguna dari Cloudinary jika ada dan bukan default
            if ($pengguna->foto && !str_contains($pengguna->foto, 'default.jpg')) {
                $filename = basename($pengguna->foto);
                $publicId = pathinfo($filename, PATHINFO_FILENAME);
                $folderedPublicId = "images/profil/{$publicId}";

                Cloudinary::destroy($folderedPublicId);
                Log::info("Foto pengguna dengan ID $id_pengguna berhasil dihapus dari Cloudinary: {$folderedPublicId}");
            }

            // Cari kendaraan yang dimiliki pengguna ini
            $kendaraans = Kendaraan::where('id_pengguna', $id_pengguna)->get();

            foreach ($kendaraans as $kendaraan) {
                // Hapus foto kendaraan dari Cloudinary jika ada
                if ($kendaraan->foto && !str_contains($kendaraan->foto, 'default.jpg')) {
                    $filename = basename($kendaraan->foto);
                    $publicId = pathinfo($filename, PATHINFO_FILENAME);
                    $folderedPublicId = "images/kendaraan/{$publicId}";

                    Cloudinary::destroy($folderedPublicId);
                    Log::info("Foto kendaraan dengan plat {$kendaraan->plat_nomor} berhasil dihapus dari Cloudinary: {$folderedPublicId}");
                }

                // Hapus QR Code kendaraan dari Cloudinary jika ada
                if ($kendaraan->qr_code) {
                    $qrFilename = basename($kendaraan->qr_code);
                    $qrPublicId = pathinfo($qrFilename, PATHINFO_FILENAME);
                    $qrFolderedPublicId = "images/qrcodes/{$qrPublicId}";

                    Cloudinary::destroy($qrFolderedPublicId);
                    Log::info("QR Code kendaraan dengan plat {$kendaraan->plat_nomor} berhasil dihapus dari Cloudinary: {$qrFolderedPublicId}");
                }

                // Hapus kendaraan dari database
                $kendaraan->delete();
                Log::info("Kendaraan dengan plat {$kendaraan->plat_nomor} milik pengguna ID $id_pengguna berhasil dihapus dari database.");
            }

            // Hapus pengguna dari database
            $pengguna->delete();
            Log::info("Pengguna dengan ID $id_pengguna berhasil dihapus dari database.");

            return redirect()->route('pengelola.kelola_pengguna.index')->with('success', 'Pengguna dan kendaraan terkait berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error("Gagal menghapus pengguna dengan ID $id_pengguna: " . $e->getMessage());
            return redirect()->route('pengelola.kelola_pengguna.index')->with('error', 'Terjadi kesalahan saat menghapus pengguna.');
        }
    }
}
