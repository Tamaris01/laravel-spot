<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // Import Cloudinary

class PenggunaParkir extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna_parkir';

    public $timestamps = false;
    protected $primaryKey = 'id_pengguna';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengguna',
        'nama',
        'email',
        'password',
        'foto',
        'kategori',
        'status',
    ];

    // Mutator untuk mengenkripsi password sebelum disimpan
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Mengembalikan nama kolom yang digunakan untuk autentikasi
    public function getAuthIdentifierName()
    {
        return 'id_pengguna';
    }

    // Mengembalikan password yang di-hash untuk pemeriksaan autentikasi
    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    // Relasi one-to-one dengan tabel kendaraan
    public function kendaraan()
    {
        return $this->hasOne(Kendaraan::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Mutator untuk menyimpan public_id foto yang diupload ke Cloudinary.
     */
    public function setFotoAttribute($value)
    {
        // Jika $value adalah string, anggap sudah public_id dan langsung simpan
        if (is_string($value)) {
            $this->attributes['foto'] = $value;
            return;
        }

        // Kalau bukan string, kemungkinan file upload, lakukan upload Cloudinary
        if (method_exists($value, 'getRealPath')) {
            $foto = Cloudinary::upload($value->getRealPath());
            $this->attributes['foto'] = $foto->getPublicId();
            return;
        }

        // Jika nilai lain, bisa set null atau error sesuai kebutuhan
        $this->attributes['foto'] = null;
    }


    /**
     * Menampilkan URL dari foto yang disimpan di Cloudinary.
     */
    public function getFotoUrlAttribute()
    {
        return Cloudinary::getUrl($this->foto); // Mendapatkan URL gambar berdasarkan public_id
    }
}
