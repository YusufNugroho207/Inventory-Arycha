<?php

namespace App\Filament\Resources\BarangKeluarResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\BarangKeluar;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatusReturWidget extends BaseWidget
{
    public function getStats(): array
    {
        $menungguKonfirmasi = BarangKeluar::where('status', 'menunggu_konfirmasi')->count();
        $diajukan = BarangKeluar::where('status', 'diajukan')->count();
        $diproses = BarangKeluar::where('status', 'diproses')->count();
        $selesai = BarangKeluar::where('status', 'selesai')->count();

        return [
            Card::make('Menunggu Konfirmasi', $menungguKonfirmasi)->description('Jumlah barang dalam status menunggu konfirmasi')->color('danger'),
            Card::make('Diajukan', $diajukan)->description('Jumlah barang dalam status diajukan')->color('primary'),
            Card::make('Diproses', $diproses)->description('Jumlah barang dalam status diproses')->color('warning'),
            Card::make('Selesai', $selesai)->description('Jumlah barang dalam status selesai')->color('success'),
        ];
    }
}
