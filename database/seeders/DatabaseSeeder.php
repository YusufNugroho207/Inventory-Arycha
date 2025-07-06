<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Kategori::factory(5)->create();
        \App\Models\Brand::factory(5)->create();
        \App\Models\Supplier::factory(5)->create();
        \App\Models\Barang::factory()->count(50)->create();
    }
}
