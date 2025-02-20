<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    /** @use HasFactory<\Database\Factories\JadwalFactory> */
    use HasFactory;

    protected $table = 'jadwal';

    protected $primaryKey = 'id_jadwal';
    protected $fillable = [
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'id_dosen',
        'id_pelajaran'
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen');
    }

    public function pelajaran()
    {
        return $this->belongsTo(Pelajaran::class, 'id_pelajaran');
    }

    public function absensi()
    {
        return $this->hasMany(Absen::class, 'id_jadwal');
    }

    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 'absensi', 'id_jadwal', 'id_mahasiswa');
    }
}
