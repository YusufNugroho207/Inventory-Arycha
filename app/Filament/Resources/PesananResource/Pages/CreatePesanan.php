<?php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use Filament\Actions;
use App\Models\Barang;
use Filament\Resources\Pages\CreateRecord;

class CreatePesanan extends CreateRecord
{
    protected static string $resource = PesananResource::class;

    protected function afterCreate(): void
    {
        $pesanan = $this->record;
        $detailPesanan = $pesanan->detailPesanan;

        foreach ($detailPesanan as $detail) {
            $barang = Barang::find($detail->barang_id);

            if ($barang) {
                $barang->stok -= $detail->jumlah;
                $barang->save();
                $subTotal = $barang->harga * $detail->jumlah;
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
