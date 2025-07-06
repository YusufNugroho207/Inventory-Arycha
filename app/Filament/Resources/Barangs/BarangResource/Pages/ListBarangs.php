<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\Barangs\BarangResource\widgets\produkOverview;

class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            produkOverview::class,
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
