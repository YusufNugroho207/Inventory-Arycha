<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangKeluarResource\Pages;
use App\Filament\Resources\BarangKeluarResource\RelationManagers;
use App\Models\BarangKeluar;
use App\Models\Supplier;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Group;
use App\Filament\Clusters\kelolaStok;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class BarangKeluarResource extends Resource
{
    protected static ?string $model = BarangKeluar::class;

    // protected static ?string $cluster = kelolaStok::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationGroup = 'Manajemen Stok Barang';

    protected static ?string $navigationLabel = 'Pengembalian Barang';

    protected static ?string $pluralModelLabel = 'Pengembalian Barang';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
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
                        ->required()
                        ->numeric()
                        ->disabled(fn ($record) => $record !== null),
                ])
                    ->columns(1) 
                    ->columnSpan(6),

                Group::make([
                    Forms\Components\ToggleButtons::make('penyebab')
                        ->label('Penyebab')
                        ->options([
                            'Barang Cacat' => 'Barang Cacat',
                            'Barang Tidak Sesuai Pesanan' => 'Barang Tidak Sesuai Pesanan',
                            'Lainnya' => 'Lainnya',
                        ])
                        ->inline()
                        ->required()
                        ->live(),

                    Forms\Components\TextInput::make('penyebab')
                        ->label('Tulis Penyebab Lainnya')
                        ->placeholder('Masukkan penyebab lainnya')
                        ->hidden(fn($get) => $get('penyebab') !== 'Lainnya')
                        ->required(fn($get) => $get('penyebab') === 'Lainnya'),

                    Forms\Components\Select::make('status')
                        ->options([
                            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                            'diajukan' => 'Diajukan',
                            'diproses' => 'Diproses',
                            'selesai' => 'Selesai',
                        ])
                        ->native(false)
                        ->default('menunggu_konfirmasi')
                        ->required(),

                    Forms\Components\DatePicker::make('tanggalKeluar')
                        ->default(now())
                        ->required(),
                ])
                    ->columns(1)
                    ->columnSpan(6),
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
                    ->label('Barang Keluar')
                    ->sortable(),

                Tables\Columns\TextColumn::make('penyebab')
                    ->label('Penyebab')
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Status Retur')
                    ->options([
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                    ])
                    ->disabled(fn($record) => $record->status === 'selesai')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggalKeluar')
                    ->label('Tanggal Keluar')
                    ->sortable()
                    ->date(),

            ])

            ->emptyStateHeading('Tidak ada data pengembalian barang')
            ->emptyStateDescription('Silahkan tambahkan data pengembalian barang terlebih dahulu.')
            ->emptyStateIcon('heroicon-o-no-symbol')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Tambahkan')
                    ->url(route('filament.admin.resources.barang-keluars.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])

            ->filters([
                Tables\Filters\Filter::make('tanggalKeluar')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('tanggalKeluar', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('tanggalKeluar', '<=', $data['until']));
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
            'index' => Pages\ListBarangKeluars::route('/'),
            'create' => Pages\CreateBarangKeluar::route('/create'),
            'edit' => Pages\EditBarangKeluar::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'menunggu_konfirmasi')->count();
    }
}
