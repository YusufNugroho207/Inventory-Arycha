<?php

namespace App\Filament\Clusters\Laporan\Resources\LaporanStokBarangResource\Pages;

use App\Filament\Clusters\Laporan\Resources\LaporanStokBarangResource;
use Filament\Actions;
use App\Filament\Clusters\Laporan\Resources\LaporanResource\Widgets\stokBarangOverview;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListLaporanStokBarangs extends ListRecords
{
    protected static string $resource = LaporanStokBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            stokBarangOverview::class,
        ];
    }


    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua'),
            'tersedia' => Tab::make('Stok Tersedia')
                ->query(fn($query) => $query->where('stok', '>', 5)),
            'rendah' => Tab::make('Stok Rendah')
                ->query(fn($query) => $query->where('stok', '>', 0)->where('stok', '<=', 5)),
            'habis' => Tab::make('Stok Habis')
                ->query(fn($query) => $query->where('stok', 0)),
        ];
    }
}
