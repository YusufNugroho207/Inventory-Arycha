<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brand')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('supplier')->cascadeOnDelete();
            $table->string('panjangLebar')->nullable();
            $table->string('tinggi')->nullable();
            $table->string('bahan')->nullable();
            $table->string('kelengkapan')->nullable();
            $table->integer('harga');
            $table->integer('stok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
