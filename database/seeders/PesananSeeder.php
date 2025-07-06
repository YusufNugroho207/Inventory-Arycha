<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Pesanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = Barang::select('id', 'harga', 'stok')->get();

        if ($barangs->isEmpty()) {
            $this->command->warn('❌ Tidak ada data barang yang tersedia.');
            return;
        }

        for ($i = 0; $i < 150; $i++) {
            // Rentang tanggal dari 1 November 2024 sampai hari ini
            $tanggal = Carbon::create(2024, 11, 1)->addDays(rand(0, now()->diffInDays('2024-11-01')));

            $nomor = 'OR-' . $tanggal->format('Ymd') . '-' . rand(100000, 999999);
            $jumlahItem = rand(1, 3);
            $detailPesanan = [];
            $totalHarga = 0;

            $dipilih = $barangs->random($jumlahItem);

            foreach ($dipilih as $barang) {
                if ($barang->stok <= 0) continue;

                $maxJumlah = min(5, $barang->stok);
                $jumlah = rand(1, $maxJumlah);
                $subtotal = $barang->harga * $jumlah;
                $totalHarga += $subtotal;

                $detailPesanan[] = [
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                    'retur' => false,
                    'penukaran' => false,
                    'jumlahRetur' => 0,
                    'jumlahPenukaran' => 0,
                ];
            }

            if (count($detailPesanan) === 0) continue;

            $pesanan = Pesanan::create([
                'nomorPesanan' => $nomor,
                'tanggalPemesanan' => $tanggal,
                'note' => fake()->optional()->sentence(),
                'totalHarga' => $totalHarga,
                'pembayaran' => fake()->randomElement(['Tunai', 'QRIS', 'Transfer']),
            ]);

            $pesanan->detailPesanan()->createMany($detailPesanan);
        }

        $this->command->info('✅ Seeder pesanan (beserta detailnya) berhasil dibuat sebanyak 150 data.');
    }
}
