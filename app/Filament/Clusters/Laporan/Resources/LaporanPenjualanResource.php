<?php

namespace App\Filament\Clusters\Laporan\Resources;

use App\Filament\Clusters\Laporan;
use App\Filament\Clusters\Laporan\Resources\LaporanPenjualanResource\Pages;
use App\Filament\Clusters\Laporan\Resources\LaporanPenjualanResource\RelationManagers;
use App\Models\DetailPesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

class LaporanPenjualanResource extends Resource
{
    protected static ?string $model = DetailPesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $cluster = Laporan::class;

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?string $pluralModelLabel = 'Laporan Penjualan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pesanan.nomorPesanan')
                    ->label('Nomor Pesanan')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('barang.nama')
                    ->label('Barang')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah Barang'),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Total')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('pesanan.pembayaran')
                    ->label('Pembayaran')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pesanan.tanggalPemesanan')
                    ->label('Tanggal Pemesanan')
                    ->sortable()
                    ->date(),
            ])
            ->filters([
                Tables\Filters\Filter::make('pesanan.tanggalPemesanan')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereHas('pesanan', fn($q2) => $q2->whereDate('tanggalPemesanan', '>=', $data['from'])))
                            ->when($data['until'], fn($q) => $q->whereHas('pesanan', fn($q2) => $q2->whereDate('tanggalPemesanan', '<=', $data['until'])));
                    }),
            ])

            ->emptyStateHeading('Tidak ada data Laporan Penjualan')
            ->emptyStateDescription('Data laporan penjualan tidak ditemukan.')
            ->emptyStateIcon('heroicon-o-no-symbol')

            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                ]),
            ])

            ->headerActions([
                ExportAction::make()
                    ->label('Export ke Excel'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanPenjualans::route('/'),
            'create' => Pages\CreateLaporanPenjualan::route('/create'),
        ];
    }
}
