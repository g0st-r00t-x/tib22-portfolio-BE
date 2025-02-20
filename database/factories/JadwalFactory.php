<?php

namespace Database\Factories;

use App\Models\Jadwal;
use App\Models\Dosen;
use App\Models\Pelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalFactory extends Factory
{
    protected $model = Jadwal::class;

    public function definition()
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $startHour = $this->faker->numberBetween(7, 15);
        $endHour = $startHour + 2;

        return [
            'hari' => $this->faker->randomElement($days),
            'jam_mulai' => sprintf('%02d:00:00', $startHour),
            'jam_selesai' => sprintf('%02d:00:00', $endHour),
            'ruangan' => 'R.' . $this->faker->bothify('##?'),
            'id_dosen' => Dosen::factory(),
            'id_pelajaran' => Pelajaran::factory(),
        ];
    }
}
