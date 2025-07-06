<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kategori;
use App\Models\Brand;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang>
 */
class BarangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => fake()->words(2, true),
            'kategori_id' => Kategori::inRandomOrder()->first()->id ?? Kategori::factory(),
            'brand_id' => Brand::inRandomOrder()->first()->id ?? Brand::factory(),
            'supplier_id' => Supplier::inRandomOrder()->first()->id ?? Supplier::factory(),
            'panjangLebar' => fake()->randomElement([
                '90x200',
                '100x200',
                '120x200',
                '140x200',
                '160x200',
                '180x200',
                '200x200',
                '120x150',
                '150x200',
                '200x220',
            ]),
            'tinggi' => fake()->randomElement(['20', '30', '40']),
            'bahan' => fake()->randomElement(['Disperse', 'Katun cvc']),
            'kelengkapan' => fake()->randomElement(['1 Bantal 1 Guling', '2 Bantal 2 Guling']),
            'harga' => fake()->numberBetween(50000, 500000),
            'stok' => fake()->numberBetween(0, 100),
        ];
    }
}
