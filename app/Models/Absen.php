<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    /** @use HasFactory<\Database\Factories\AbsenFactory> */
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    protected $fillable = [
        'id_dosen',
        'id_jadwal',
        'id_mahasiswa',
        'waktu_absen',
        'status'
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }
}
