<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AktivitasPenggunaParkir extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_penggunaparkir';

    protected $fillable = [
        'id_pengguna',
        'aktivitas',
        'keterangan',
        'waktu_aktivitas',
    ];

    public $timestamps = false;

    public function pengguna()
    {
        return $this->belongsTo(PenggunaParkir::class, 'id_pengguna', 'id_pengguna');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('waktu_aktivitas', today());
    }

    public function scopeLast7Days($query)
    {
        return $query->where('waktu_aktivitas', '>=', now()->subDays(7));
    }

    public function scopeLast30Days($query)
    {
        return $query->where('waktu_aktivitas', '>=', now()->subDays(30));
    }
}
