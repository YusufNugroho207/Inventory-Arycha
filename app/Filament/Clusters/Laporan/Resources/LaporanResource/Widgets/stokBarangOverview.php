<?php

namespace App\Filament\Clusters\Laporan\Resources\LaporanResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Barang;
use App\Models\DetailPesanan;

class stokBarangOverview extends BaseWidget
{
     protected function getStats(): array
    {
        $lowStockThreshold = 5;

        return [
            Stat::make('Barang Tersedia', Barang::where('stok', '>', $lowStockThreshold)->count())
                ->description('Barang dengan stok aman')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Stok Rendah', Barang::whereBetween('stok', [1, $lowStockThreshold])->count())
                ->description("Barang dengan stok rendah (Kurang dari $lowStockThreshold)")
                ->icon('heroicon-o-exclamation-circle')
                ->color('warning'),

            Stat::make('Stok Habis', Barang::where('stok', 0)->count())
                ->description('Barang yang habis stok')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}