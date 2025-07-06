<?php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use Filament\Actions;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Barang;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Models\BarangKeluar;
use Carbon\Carbon;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function afterSave(): void
    {
        foreach ($this->record->detailPesanan as $detail) {
            if ($detail->retur) {
                $barang = Barang::find($detail->barang_id);

                if ($barang) {
                    BarangKeluar::create([
                        'barang_id' => $barang->id,
                        'supplier_id' => $barang->supplier_id,
                        'jumlah' => $detail->jumlahRetur,
                        'penyebab' => 'Barang Cacat',
                        'tanggalKeluar' => Carbon::now(),
                        'status' => 'menunggu_konfirmasi',
                    ]);

                    Notification::make()
                        ->title('Barang Retur Dicatat')
                        ->body("{$detail->jumlahRetur} barang '{$barang->nama}' dicatat sebagai barang keluar karena cacat.")
                        ->success()
                        ->send();
                }
            }

            if ($detail->penukaran) {
                $barang = Barang::find($detail->barang_id);

                $barang->increment('stok', $detail->jumlahPenukaran);

                Notification::make()
                    ->title('Barang Penukaran Dicatat')
                    ->body("{$detail->jumlahPenukaran} barang '{$barang->nama}' dikembalikan ke stok karena penukaran.")
                    ->success()
                    ->send();
            }
        }
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl();
    }
}
