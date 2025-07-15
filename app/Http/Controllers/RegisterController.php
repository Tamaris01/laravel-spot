<?php

namespace App\Http\Controllers;

use App\Models\PenggunaParkir;
use App\Models\Kendaraan;
use App\Models\AktivitasPenggunaParkir; // ✅ Tambah ini
use App\Http\Requests\RegisterRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; // ✅ Tambah ini

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
                return array_map(function ($value) {
                    return trim($value, "'");
                }, explode(',', $matches[1]));
            }
        }
        return [];
    }

    public function register(RegisterRequest $request)
    {
        try {
            // Validasi dan upload foto profil
            if (!$request->hasFile('foto') || !$request->file('foto')->isValid()) {
                return back()->withErrors(['foto' => 'Foto profil tidak valid atau belum diupload.'])->withInput();
            }

            $uploadedFotoProfil = Cloudinary::upload(
                $request->file('foto')->getRealPath(),
                ['folder' => 'images/profil', 'resource_type' => 'image']
            );
            $fotoProfilPublicId = $uploadedFotoProfil->getPublicId();

            // Validasi dan upload foto kendaraan
            if (!$request->hasFile('foto_kendaraan') || !$request->file('foto_kendaraan')->isValid()) {
                return back()->withErrors(['foto_kendaraan' => 'Foto kendaraan tidak valid atau belum diupload.'])->withInput();
            }

            $uploadedFotoKendaraan = Cloudinary::upload(
                $request->file('foto_kendaraan')->getRealPath(),
                ['folder' => 'images/kendaraan', 'resource_type' => 'image']
            );
            $fotoKendaraanPublicId = $uploadedFotoKendaraan->getPublicId();

            // Simpan data pengguna
            $pengguna = new PenggunaParkir();
            $pengguna->id_pengguna = $request->kategori !== 'Tamu'
                ? $request->id_pengguna
                : 'Tamu_' . mt_rand(10000000, 99999999);
            $pengguna->nama = $request->nama;
            $pengguna->email = $request->email;
            $pengguna->password = $request->password;
            $pengguna->foto = $fotoProfilPublicId;
            $pengguna->kategori = $request->kategori;
            $pengguna->status = 'nonaktif';
            $pengguna->save();

            // ✅ Catat aktivitas register
            AktivitasPenggunaParkir::create([
                'id_pengguna' => $pengguna->id_pengguna,
                'aktivitas' => 'register',
                'keterangan' => 'User mendaftar akun parkir',
                'waktu_aktivitas' => Carbon::now(),
            ]);

            // Simpan data kendaraan
            $kendaraan = new Kendaraan();
            $kendaraan->plat_nomor = $request->plat_nomor;
            $kendaraan->jenis = $request->jenis;
            $kendaraan->warna = ucwords(strtolower($request->warna));
            $kendaraan->foto = $fotoKendaraanPublicId;
            $kendaraan->id_pengguna = $pengguna->id_pengguna;

            // Generate dan upload QR Code
            $qrCodeContent = $kendaraan->plat_nomor;
            $tempPath = sys_get_temp_dir() . '/' . $qrCodeContent . '.png';
            QrCode::format('png')->size(300)->generate($qrCodeContent, $tempPath);

            $uploadedQrCode = Cloudinary::upload(
                $tempPath,
                ['folder' => 'images/qrcodes', 'resource_type' => 'image']
            );

            // Hapus file QR sementara
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            $kendaraan->qr_code = $uploadedQrCode->getPublicId();
            $kendaraan->save();

            return redirect()->route('login')->with('pendaftaran', 'Pendaftaran anda berhasil. Tunggu konfirmasi dari pengelola parkir.');
        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'])->withInput();
        }
    }
}
