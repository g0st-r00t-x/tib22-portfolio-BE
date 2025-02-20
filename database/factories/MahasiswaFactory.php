<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class MahasiswaFactory extends Factory
{
    protected $model = Mahasiswa::class;

    public function definition()
    {
        return [
            'nim' => $this->faker->unique()->numerify('20########'),
            'nama' => $this->faker->name(),
            'alamat' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
