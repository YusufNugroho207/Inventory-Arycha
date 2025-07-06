<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth;

class BarangLowStockList extends BaseWidget
{
    protected static ?string $heading = 'Daftar Barang Stok Rendah';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Barang::query()
                    ->where('stok', '<=', 5)
                    ->orderBy('stok', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategori.kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.brand')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Sisa Stok')
                    ->color(fn($state) => match (true) {
                        $state == 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    }),
            ])
            ->emptyStateHeading('Tidak ada data Barang Stok Rendah')
            ->emptyStateDescription('Data barang dengan stok rendah tidak ditemukan.')
            ->emptyStateIcon('heroicon-o-no-symbol');
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Manajer');
    }
}
