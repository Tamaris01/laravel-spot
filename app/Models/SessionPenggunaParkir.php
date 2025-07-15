<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionPenggunaParkir extends Model
{
    use HasFactory;

    protected $table = 'session_penggunaparkir';

    protected $fillable = [
        'id_pengguna',
        'session_id',
        'session_start',
        'session_end',
        'duration_seconds',
    ];

    public function pengguna()
    {
        return $this->belongsTo(PenggunaParkir::class, 'id_pengguna', 'id_pengguna');
    }
}
