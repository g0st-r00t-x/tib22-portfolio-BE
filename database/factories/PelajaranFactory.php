<?php

namespace Database\Factories;

use App\Models\Pelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class PelajaranFactory extends Factory
{
    protected $model = Pelajaran::class;

    public function definition()
    {
        $subjects = ['Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Inggris', 'Bahasa Indonesia'];
        $randomSubject = $this->faker->unique()->randomElement($subjects);

        return [
            'nama_pelajaran' => $randomSubject,
            'kode_pelajaran' => $this->faker->unique()->bothify('MK###??'),
        ];
    }
}