<?php

namespace App\Filament\Resources\BarangMasukResource\Pages;

use App\Filament\Resources\BarangMasukResource;
use Filament\Actions;
use App\Models\Barang;
use App\Models\Supplier;
use Filament\Resources\Pages\CreateRecord;

class CreateBarangMasuk extends CreateRecord
{
    protected static string $resource = BarangMasukResource::class;

    protected function afterCreate(): void
    {
        $barangMasuk = $this->record;
        $barang = Barang::find($barangMasuk->barang_id);
    
        if ($barang) {
            $barang->increment('stok', $barangMasuk->jumlah);
            $barang->update(['supplier_id' => $barangMasuk->supplier_id]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
