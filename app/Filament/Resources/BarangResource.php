<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Barangs;
use App\Models\Brand;
use App\Models\Kategori;
use App\Models\Supplier;
use Filament\Tables\Actions\Action;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $cluster = Barangs::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Barang';

    protected static ?string $pluralModelLabel = 'Barang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Barang')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('kategori_id')
                    ->label('Kategori')
                    ->options(Kategori::query()->pluck('kategori', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('brand_id')
                    ->label('Brand')
                    ->nullable()
                    ->options(Brand::query()->pluck('brand', 'id'))
                    ->searchable()
                    ->default(''),

                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::query()->pluck('supplier', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('panjangLebar')
                    ->label('Panjang x Lebar')
                    ->options([
                        '30x70'    => '30 x 70 cm',
                        '50x100'   => '50 x 100 cm',
                        '70x140'   => '70 x 140 cm',
                        '80x150'   => '80 x 150 cm',
                        '90x200'   => '90 x 200 cm',
                        '100x200'  => '100 x 200 cm',
                        '115x155'  => '115 x 155 cm',
                        '120x150'  => '120 x 150 cm',
                        '120x200'  => '120 x 200 cm',
                        '140x200'  => '140 x 200 cm',
                        '150x190'  => '150 x 190 cm',
                        '150x200'  => '150 x 200 cm',
                        '160x200'  => '160 x 200 cm',
                        '160x210'  => '160 x 210 cm',
                        '180x200'  => '180 x 200 cm',
                        '190x230'  => '190 x 230 cm',
                        '190x260'  => '190 x 260 cm',
                        '200x200'  => '200 x 200 cm',
                        '200x210'  => '200 x 210 cm',
                        '200x220'  => '200 x 220 cm',
                        '210x310'  => '210 x 310 cm',
                        '230x310'  => '230 x 310 cm',
                    ])
                    ->native(false)
                    ->default('')
                    ->nullable(),

                Forms\Components\Select::make('tinggi')
                    ->label('Tinggi')
                    ->options([
                        '20' => '20 cm',
                        '30' => '30 cm',
                        '40' => '40 cm',
                    ])
                    ->default('')
                    ->native(false)
                    ->nullable(),

                Forms\Components\TextInput::make('bahan')
                    ->label('Bahan')
                    ->nullable()
                    ->default(''),

                Forms\Components\Select::make('kelengkapan')
                    ->label('Kelengkapan')
                    ->options([
                        '1 Bantal 1 Guling' => '1 Bantal 1 Guling',
                        '2 Bantal 2 Guling' => '2 Bantal 2 Guling',
                    ])
                    ->native(false)
                    ->default('')
                    ->nullable(),

                Forms\Components\TextInput::make('harga')
                    ->label('Harga')
                    ->prefix('Rp')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('stok')
                    ->label('Stok')
                    ->required()
                    ->numeric()
                    ->disabled(fn($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Barang')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('kategori.kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.brand')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('panjangLebar')
                    ->label('Panjang Lebar')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tinggi')
                    ->label('Tinggi')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('bahan')
                    ->label('Bahan')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('kelengkapan')
                    ->label('Kelengkapan')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('stok')
                    ->label('Stok')
                    ->colors([
                        'success' => fn($state) => $state > 5,
                        'warning' => fn($state) => $state > 0 && $state <= 5,
                        'danger' => fn($state) => $state == 0,
                    ])
                    ->formatStateUsing(function ($state) {
                        if ($state > 5) {
                            return '✅ Tersedia (' . $state . ')';
                        } elseif ($state > 0) {
                            return '⚠️ Low Stock (' . $state . ')';
                        } else {
                            return '❌ Habis';
                        }
                    })
                    ->sortable(),


                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->toggleable()
                    ->date(),
            ])

            ->emptyStateHeading('Tidak ada data barang')
            ->emptyStateDescription('Silahkan tambahkan data barang terlebih dahulu.')
            ->emptyStateIcon('heroicon-o-no-symbol')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Tambahkan')
                    ->url(route('filament.admin.barangs.resources.barangs.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
