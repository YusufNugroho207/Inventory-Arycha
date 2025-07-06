<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangMasukResource\Pages;
use App\Filament\Resources\BarangMasukResource\RelationManagers;
use App\Models\BarangMasuk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\kelolaStok;
use App\Models\Barang;
use App\Models\Supplier;
use Filament\Tables\Actions\Action;

class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;

    // protected static ?string $cluster = kelolaStok::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'Barang Masuk';

    protected static ?string $navigationGroup = 'Manajemen Stok Barang';

    protected static ?string $pluralModelLabel = 'Barang Masuk';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('barang_id')
                    ->label('Barang')
                    ->options(Barang::query()->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::query()->pluck('supplier', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->placeholder('Masukkan jumlah barang')
                    ->disabled(fn ($record) => $record !== null)
                    ->numeric()
                    ->required(),

                Forms\Components\DatePicker::make('tanggalMasuk')
                    ->default(now())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barang.nama')
                    ->label('Barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Barang Masuk')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggalMasuk')
                    ->label('Tanggal Masuk')
                    ->date(),
            ])

            ->emptyStateHeading('Tidak ada data barang masuk')
            ->emptyStateDescription('Silahkan tambahkan data barang masuk terlebih dahulu.')
            ->emptyStateIcon('heroicon-o-no-symbol')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Tambahkan')
                    ->url(route('filament.admin.resources.barang-masuks.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])

            ->filters([
                Tables\Filters\Filter::make('tanggalMasuk')
                ->form([
                    Forms\Components\DatePicker::make('from'),
                    Forms\Components\DatePicker::make('until'),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when($data['from'], fn ($q) => $q->whereDate('tanggalMasuk', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('tanggalMasuk', '<=', $data['until']));
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListBarangMasuks::route('/'),
            'create' => Pages\CreateBarangMasuk::route('/create'),
            'edit' => Pages\EditBarangMasuk::route('/{record}/edit'),
        ];
    }
}
