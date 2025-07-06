<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesananResource\Pages;
use App\Filament\Resources\PesananResource\RelationManagers;
use App\Models\Barang;
use App\Models\Pesanan;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Pesanan';

    protected static ?string $navigationGroup = 'Pemesanan';

    protected static ?string $pluralModelLabel = 'Pesanan';
    
     protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Detail Pesanan')
                        ->schema([
                            Forms\Components\TextInput::make('nomorPesanan')
                                ->label('Nomor Pesanan')
                                ->default(fn() => 'OR-' . Carbon::now()->format('Ymd') . '-' . random_int(100000, 999999))
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->maxLength(32)
                                ->unique(Pesanan::class, 'nomorPesanan', ignoreRecord: true),

                            Forms\Components\DatePicker::make('tanggalPemesanan')
                                ->default(now())
                                ->required(),

                            Forms\Components\MarkdownEditor::make('note')
                                ->nullable(),
                        ]),

                    Forms\Components\Wizard\Step::make('Detail Barang')
                        ->schema([
                            Forms\Components\Repeater::make('detailPesanan')
                                ->relationship('detailPesanan')
                                ->schema([
                                    Forms\Components\Select::make('barang_id')
                                        ->label('Barang')
                                        ->options(function () {
                                            return Barang::query()
                                                ->where('stok', '>', 0)
                                                ->pluck('nama', 'id');
                                        })
                                        ->searchable()
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, Forms\Set $set) =>
                                        $set('subtotal', Barang::find($state)?->harga ?? 0)),

                                    Forms\Components\TextInput::make('jumlah')
                                        ->numeric()
                                        ->live()
                                        ->dehydrated()
                                        ->default(1)
                                        ->required()
                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                            $barangId = $get('barang_id');
                                            $barang = Barang::find($barangId);

                                            if ($barang) {
                                                if ($state > $barang->stok) {
                                                    $set('jumlah', $barang->stok);

                                                    Notification::make()
                                                        ->title('Stok Tidak Mencukupi')
                                                        ->body("Stok tersedia hanya {$barang->stok} item.")
                                                        ->danger()
                                                        ->persistent()
                                                        ->send();

                                                    $state = $barang->stok;
                                                }

                                                $harga = $barang->harga;
                                                $subtotal = $harga * (int) $state;
                                                $set('subtotal', $subtotal);
                                            }
                                        }),

                                    Forms\Components\TextInput::make('subtotal')
                                        ->label('Sub Total')
                                        ->prefix('Rp')
                                        ->disabled()
                                        ->reactive()
                                        ->dehydrated()
                                        ->numeric()
                                        ->required(),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Checkbox::make('retur')
                                                ->label('Barang Retur')
                                                ->default(false)
                                                ->reactive()
                                                ->helperText('Pilih jika barang yang diterima cacat, rusak, atau alasan lainnya.')
                                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                    $set('jumlah_retur', $state ? 0 : null);
                                                }),

                                            Forms\Components\Checkbox::make('penukaran')
                                                ->label('Penukaran Barang')
                                                ->default(false)
                                                ->reactive()
                                                ->helperText('Pilih jika ingin menukar barang yang sudah dibeli.')
                                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                    $set('jumlah_penukaran', $state ? 0 : null);
                                                }),
                                        ]),

                                    Forms\Components\TextInput::make('jumlahRetur')
                                        ->label('Jumlah Retur')
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(fn(Forms\Get $get) => $get('jumlah'))
                                        ->visible(fn(Forms\Get $get) => $get('retur'))
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                            if ($state < 1) {
                                                Notification::make()
                                                    ->title('Jumlah Retur Tidak Valid')
                                                    ->body('Jumlah retur minimal 1.')
                                                    ->danger()
                                                    ->send();
                                                $set('jumlahRetur', 1);
                                                return;
                                            }

                                            $jumlahAwal = $get('jumlah') + ($get('jumlah_retur') ?? 0);
                                            $jumlahRetur = $state;

                                            if ($jumlahRetur > $jumlahAwal) {
                                                Notification::make()
                                                    ->title('Jumlah Retur Terlalu Besar')
                                                    ->body('Jumlah retur tidak boleh melebihi jumlah yang dibeli.')
                                                    ->danger()
                                                    ->send();

                                                $jumlahRetur = $jumlahAwal;
                                                $set('jumlahRetur', $jumlahRetur);
                                            }

                                            $jumlahBaru = max($jumlahAwal - $jumlahRetur, 0);
                                            $set('jumlah', $jumlahBaru);

                                            $barang = Barang::find($get('barang_id'));
                                            if ($barang) {
                                                $set('subtotal', $barang->harga * $jumlahBaru);
                                            }
                                        }),


                                    Forms\Components\TextInput::make('jumlahPenukaran')
                                        ->label('Jumlah Penukaran')
                                        ->helperText('Untuk barang pengganti, harap buat pesanan baru.')
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(fn(Forms\Get $get) => $get('jumlah'))
                                        ->visible(fn(Forms\Get $get) => $get('penukaran'))
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                            if ($state < 1) {
                                                Notification::make()
                                                    ->title('Jumlah Penukaran Tidak Valid')
                                                    ->body('Jumlah penukaran minimal 1.')
                                                    ->danger()
                                                    ->send();
                                                $set('jumlahPenukaran', 1);
                                                return;
                                            }

                                            $jumlahAwal = $get('jumlah') + ($get('jumlah_penukaran') ?? 0);
                                            $jumlahPenukaran = $state;

                                            if ($jumlahPenukaran > $jumlahAwal) {
                                                Notification::make()
                                                    ->title('Jumlah Penukaran Terlalu Besar')
                                                    ->body('Jumlah penukaran tidak boleh melebihi jumlah yang dibeli.')
                                                    ->danger()
                                                    ->send();

                                                $jumlahPenukaran = $jumlahAwal;
                                                $set('jumlahPenukaran', $jumlahPenukaran);
                                            }

                                            $jumlahBaru = max($jumlahAwal - $jumlahPenukaran, 0);
                                            $set('jumlah', $jumlahBaru);

                                            $barang = Barang::find($get('barang_id'));
                                            if ($barang) {
                                                $set('subtotal', $barang->harga * $jumlahBaru);
                                            }
                                        }),
                                ]),

                            Forms\Components\TextInput::make('totalHarga')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->prefix('Rp')
                                ->reactive()
                                ->placeholder(function (Set $set, Get $get) {
                                    $detailPesanan = collect($get('detailPesanan'));

                                    $total = $detailPesanan
                                        ->pluck('subtotal')
                                        ->sum();

                                    $set('totalHarga', $total);
                                }),

                            Forms\Components\ToggleButtons::make('pembayaran')
                                ->label('Tipe Pembayaran')
                                ->options([
                                    'Tunai' => 'Tunai',
                                    'QRIS' => 'QRIS',
                                    'Transfer' => 'Transfer',
                                ])
                                ->inline()
                                ->required(),
                        ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomorPesanan')
                    ->label('Nomor Pesanan')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('totalHarga')
                    ->label('Total Harga')
                    ->sortable()
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('pembayaran')
                    ->label('Pembayaran')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggalPemesanan')
                    ->label('Tanggal Pemesanan')
                    ->sortable()
                    ->date(),

                Tables\Columns\BadgeColumn::make('statusBarang')
                    ->label('Status Pesanan')
                    ->getStateUsing(function ($record) {
                        $status = [];

                        if ($record->detailPesanan->where('retur', true)->isNotEmpty()) {
                            $status[] = 'Retur';
                        }

                        if ($record->detailPesanan->where('penukaran', true)->isNotEmpty()) {
                            $status[] = 'Penukaran';
                        }

                        return implode(', ', $status) ?: 'Normal';
                    })
                    ->colors([
                        'danger' => 'Retur',
                        'warning' => 'Penukaran',
                        'success' => 'Normal',
                    ]),
            ])

            ->emptyStateHeading('Tidak ada data pesanan')
            ->emptyStateDescription('Silahkan tambahkan data pesanan terlebih dahulu.')
            ->emptyStateIcon('heroicon-o-no-symbol')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Tambahkan')
                    ->url(route('filament.admin.resources.pesanans.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])

            ->filters([
                Tables\Filters\Filter::make('tanggalPemesanan')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('tanggalPemesanan', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('tanggalPemesanan', '<=', $data['until']));
                    }),
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
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereYear('tanggalPemesanan', now()->year)
            ->whereMonth('tanggalPemesanan', now()->month)
            ->count();
    }
}
