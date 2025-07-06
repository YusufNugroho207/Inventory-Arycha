<?php

namespace App\Filament\Clusters\Laporan\Resources\LaporanPenjualanResource\Pages;

use App\Filament\Clusters\Laporan\Resources\LaporanPenjualanResource;
use App\Filament\Clusters\Laporan\Resources\LaporanResource\Widgets\laporanPenjualanChart;
use App\Filament\Clusters\Laporan\Resources\LaporanResource\Widgets\laporanPenjualanOverview;
use App\Filament\Clusters\Laporan\Resources\LaporanResource\Widgets\LaporanPenjualanMingguanChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanPenjualans extends ListRecords
{
    protected static string $resource = LaporanPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            laporanPenjualanOverview::class,
            laporanPenjualanChart::class,
            LaporanPenjualanMingguanChart::class,
        ];
    }
}
