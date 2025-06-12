<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';
    public $timestamps = false;
    protected $primaryKey = 'plat_nomor';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengguna',
        'jenis',
        'warna',
        'plat_nomor',
        'qr_code',
        'foto',
    ];

    // Boot method untuk generate QR dan upload ke Cloudinary
    public static function boot()
    {
        parent::boot();

        static::creating(function ($kendaraan) {
            if ($kendaraan->plat_nomor) {
                // Buat isi QR code
                $qrCodeContent = json_encode([
                    'plat_nomor' => $kendaraan->plat_nomor
                ]);

                // Generate QR code SVG
                $qrSvg = QrCode::format('svg')
                    ->size(200)
                    ->generate($qrCodeContent);

                // Simpan ke file sementara
                $tempFile = tempnam(sys_get_temp_dir(), 'qr_');
                file_put_contents($tempFile, $qrSvg);

                // Upload ke Cloudinary
                $uploadQr = Cloudinary::upload($tempFile, [
                    'folder' => 'images/qrcodes',
                    'public_id' => $kendaraan->plat_nomor,
                    'resource_type' => 'image',
                    'format' => 'svg',
                    'overwrite' => true,
                ]);

                // Ambil URL dan simpan ke atribut model
                $qrUrl = $uploadQr->getSecurePath();
                $kendaraan->qr_code = $qrUrl;

                // Hapus file temp
                unlink($tempFile);
            }
        });
    }

    // Relasi ke PenggunaParkir
    public function penggunaParkir()
    {
        return $this->belongsTo(PenggunaParkir::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi ke RiwayatParkir
    public function riwayatParkir()
    {
        return $this->hasMany(RiwayatParkir::class, 'plat_nomor', 'plat_nomor');
    }

    // Method untuk menyimpan kendaraan (bisa dipanggil dari Controller)
    public static function storeKendaraan(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_pengguna' => 'required|string|max:255',
            'jenis' => 'required|integer',
            'warna' => 'required|string|max:50',
            'plat_nomor' => 'required|string|max:255|unique:kendaraan,plat_nomor',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cek apakah pengguna sudah punya kendaraan
        if (self::where('id_pengguna', $request->id_pengguna)->exists()) {
            return back()->withErrors(['id_pengguna' => 'Pengguna sudah memiliki kendaraan terdaftar.'])->withInput();
        }

        // Simpan foto kendaraan
        $fotoKendaraanPath = $request->file('foto')->store('images/kendaraan', 'public');

        // Simpan data ke DB (boot akan otomatis generate QR)
        $kendaraan = new Kendaraan();
        $kendaraan->id_pengguna = $request->id_pengguna;
        $kendaraan->jenis = $request->jenis;
        $kendaraan->warna = $request->warna;
        $kendaraan->plat_nomor = $request->plat_nomor;
        $kendaraan->foto = $fotoKendaraanPath;
        $kendaraan->save();

        return back()->with('success', 'Kendaraan berhasil ditambahkan dan QR Code telah diunggah!');
    }
}
