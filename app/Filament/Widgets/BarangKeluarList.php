<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\Auth;

class BarangKeluarList extends BaseWidget
{
    protected static ?string $heading = 'Permintaan Pengembalian Barang (Menunggu Persetujuan)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BarangKeluar::query()
                    ->where('status', 'menunggu_konfirmasi')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('barang.nama')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggalKeluar')
                    ->label('Tanggal Keluar')
                    ->sortable()
                    ->date(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Status Pengajuan')
                    ->options([
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                    ])
                    ->disabled(fn($record) => $record->status === 'selesai')
                    ->sortable()
                    ->searchable(),
            ])
            ->emptyStateHeading('Tidak ada permintaan pengembalian barang')
            ->emptyStateDescription('Data permintaan pengembalian dengan status menunggu konfirmasi tidak ditemukan.')
            ->emptyStateIcon('heroicon-o-no-symbol');
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Manajer');
    }
}
