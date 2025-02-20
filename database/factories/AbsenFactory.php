<?php

namespace Database\Factories;

use App\Models\Absen;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsenFactory extends Factory
{
    protected $model = Absen::class;

    public function definition()
    {
        return [
            'id_dosen' => Dosen::factory(),
            'id_jadwal' => Jadwal::factory(),
            'id_mahasiswa' => Mahasiswa::factory(),
            'waktu_absen' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'status' => $this->faker->randomElement(['hadir', 'izin', 'sakit', 'alpa']),
        ];
    }
}