<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kategori>
 */
class KategoriFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kategori' => fake()->word(),
        ];
    }
}
