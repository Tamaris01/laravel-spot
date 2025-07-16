<?php

namespace App\Http\Controllers;

use App\Models\PenggunaParkir;
use App\Models\Kendaraan;
use App\Models\AktivitasPenggunaParkir;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;

class RegisterController extends Controller
{
    protected $kategoriArray = [];
    protected $jenisKendaraanArray = [];
    protected $warnaKendaraanArray = [];

    public function __construct()
    {
        $this->kategoriArray = $this->getEnumValues('pengguna_parkir', 'kategori');
        $this->jenisKendaraanArray = $this->getEnumValues('kendaraan', 'jenis');
        $this->warnaKendaraanArray = $this->getEnumValues('kendaraan', 'warna');
    }

    public function showRegistrationForm()
    {
        return view('auth.register', [
            'kategoriArray' => $this->kategoriArray,
            'jenisKendaraanArray' => $this->jenisKendaraanArray,
            'warnaKendaraanArray' => $this->warnaKendaraanArray,
        ]);
    }

    protected function getEnumValues($table, $column)
    {
        $result = DB::select("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column]);
        if (!empty($result)) {
            $type = $result[0]->Type;
            if (preg_match('/^enum\((.*)\)$/', $type, $matches)) {
                return array_map(fn($value) => trim($value, "'"), explode(',', $matches[1]));
            }
        }
        return [];
    }

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            // ✅ Validasi dan upload foto profil
            if (!$request->hasFile('foto') || !$request->file('foto')->isValid()) {
                return back()->withErrors(['foto' => 'Foto profil tidak valid atau belum diupload.'])->withInput();
            }

            [$width, $height] = getimagesize($request->file('foto')->getRealPath());
            if ($width != 472 || $height != 472) {
                return back()->withErrors(['foto' => 'Foto profil harus berukuran tepat 472 x 472 pixel.'])->withInput();
            }

            $fotoProfilUpload = Cloudinary::upload(
                $request->file('foto')->getRealPath(),
                [
                    'folder' => 'images/profil',
                    'resource_type' => 'image'
                ]
            );
            $fotoProfilUrl = $fotoProfilUpload->getSecurePath();

            // ✅ Validasi dan upload foto kendaraan
            if (!$request->hasFile('foto_kendaraan') || !$request->file('foto_kendaraan')->isValid()) {
                return back()->withErrors(['foto_kendaraan' => 'Foto kendaraan tidak valid atau belum diupload.'])->withInput();
            }

            [$widthKendaraan, $heightKendaraan] = getimagesize($request->file('foto_kendaraan')->getRealPath());
            if ($widthKendaraan != 472 || $heightKendaraan != 472) {
                return back()->withErrors(['foto_kendaraan' => 'Foto kendaraan harus berukuran tepat 472 x 472 pixel.'])->withInput();
            }

            $fotoKendaraanUpload = Cloudinary::upload(
                $request->file('foto_kendaraan')->getRealPath(),
                [
                    'folder' => 'images/kendaraan',
                    'resource_type' => 'image'
                ]
            );
            $fotoKendaraanUrl = $fotoKendaraanUpload->getSecurePath();

            // ✅ Simpan data pengguna
            $pengguna = PenggunaParkir::create([
                'id_pengguna' => $request->kategori !== 'Tamu'
                    ? $request->id_pengguna
                    : 'Tamu_' . mt_rand(10000000, 99999999),
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => $request->password,
                'foto' => $fotoProfilUrl,
                'kategori' => $request->kategori,
                'status' => 'nonaktif',
            ]);

            // ✅ Catat aktivitas register
            AktivitasPenggunaParkir::create([
                'id_pengguna' => $pengguna->id_pengguna,
                'aktivitas' => 'register',
                'keterangan' => 'User mendaftar akun parkir',
                'waktu_aktivitas' => Carbon::now(),
            ]);

            // ✅ Simpan kendaraan
            $kendaraan = new Kendaraan();
            $kendaraan->plat_nomor = $request->plat_nomor;
            $kendaraan->jenis = $request->jenis;
            $kendaraan->warna = ucwords(strtolower($request->warna));
            $kendaraan->foto = $fotoKendaraanUrl;
            $kendaraan->id_pengguna = $pengguna->id_pengguna;
            $kendaraan->save();

            DB::commit();

            return redirect()->route('login')->with('pendaftaran', 'Pendaftaran anda berhasil. Tunggu konfirmasi dari pengelola parkir.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during registration: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'])->withInput();
        }
    }
}
