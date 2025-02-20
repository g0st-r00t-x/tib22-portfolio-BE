<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelajaran extends Model
{
    /** @use HasFactory<\Database\Factories\PelajaranFactory> */
    use HasFactory;
    protected $table = 'pelajaran';
    protected $primaryKey = 'id_pelajaran';
    protected $fillable = [
        'nama_pelajaran',
        'kode_pelajaran'
    ];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'id_pelajaran');
    }
}
