<?php

namespace App\Filament\Clusters\Laporan\Resources;

use App\Filament\Clusters\Laporan;
use App\Filament\Clusters\Laporan\Resources\LaporanStokBarangResource\Pages;
use App\Filament\Clusters\Laporan\Resources\LaporanStokBarangResource\RelationManagers;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\DetailPesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaporanStokBarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Laporan::class;

    protected static ?string $navigationLabel = 'Laporan Stok Barang';

    protected static ?string $pluralModelLabel = 'Laporan Stok Barang';

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
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Barang')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('kategori.kategori')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('stok_awal')
                    ->label('Stok Awal')
                    ->getStateUsing(function (Barang $record) {
                        $masuk = BarangMasuk::where('barang_id', $record->id)->sum('jumlah');
                        $keluar = BarangKeluar::where('barang_id', $record->id)->sum('jumlah');
                        return $record->stok + $keluar - $masuk;
                    }),

                Tables\Columns\TextColumn::make('masuk')
                    ->label('Barang Masuk')
                    ->getStateUsing(fn(Barang $record) => BarangMasuk::where('barang_id', $record->id)->sum('jumlah'))
                    ->color('success'),

                Tables\Columns\TextColumn::make('keluar')
                    ->label('Barang Keluar')
                    ->getStateUsing(fn(Barang $record) => BarangKeluar::where('barang_id', $record->id)->sum('jumlah'))
                    ->color('danger'),

                Tables\Columns\TextColumn::make('terjual')
                    ->label('Barang Terjual')
                    ->getStateUsing(fn(Barang $record) => DetailPesanan::where('barang_id', $record->id)->sum('jumlah'))
                    ->color('warning'),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Stok Akhir')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status_stok')
                    ->label('Status Stok')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function (Barang $record) {
                        if ($record->stok == 0) {
                            return 'Habis';
                        } elseif ($record->stok < 5) {
                            return 'Rendah';
                        } else {
                            return 'Tersedia';
                        }
                    })
                    ->colors([
                        'danger' => 'Habis',
                        'warning' => 'Rendah',
                        'success' => 'Tersedia',
                    ]),

            ])
            ->filters([
                //
            ])

            ->emptyStateHeading('Tidak ada data Laporan Stok barang')
            ->emptyStateDescription('Data laporan stok barang tidak ditemukan.')
            ->emptyStateIcon('heroicon-o-no-symbol')

            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
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
            'index' => Pages\ListLaporanStokBarangs::route('/'),
            'create' => Pages\CreateLaporanStokBarang::route('/create'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('viewAny', static::class);
    }
}
