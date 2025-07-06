<?php

namespace App\Filament\Clusters\Laporan\Resources\LaporanStokBarangResource\Pages;

use App\Filament\Clusters\Laporan\Resources\LaporanStokBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanStokBarang extends EditRecord
{
    protected static string $resource = LaporanStokBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
