<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    /** @use HasFactory<\Database\Factories\DosenFactory> */
    use HasFactory;

    protected $table = 'dosen';

    protected $primaryKey = 'id_dosen';
    protected $fillable = ['nama'];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'id_dosen');
    }

    public function absensi()
    {
        return $this->hasMany(Absen::class, 'id_dosen');
    }
}
