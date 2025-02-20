<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    /** @use HasFactory<\Database\Factories\MahasiswaFactory> */
    use HasFactory;
    protected $table ='mahasiswa';
    protected $primaryKey = 'id_mahasiswa';
    protected $fillable = [
        'nim',
        'nama',
        'alamat',
        'email'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id_mahasiswa');
    }

    public function absensi()
    {
        return $this->hasMany(Absen::class, 'id_mahasiswa');
    }

    public function jadwal()
    {
        return $this->belongsToMany(Jadwal::class, 'absensi', 'id_mahasiswa', 'id_jadwal');
    }
}
