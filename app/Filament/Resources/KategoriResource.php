<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriResource\Pages;
use App\Filament\Resources\KategoriResource\RelationManagers;
use App\Models\Kategori;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Barangs;
use Filament\Tables\Actions\Action;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static ?string $cluster = Barangs::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kategori')
                    ->label('Kategori')
                    ->required()
                    ->unique()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created Date')
                    ->toggleable()
                    ->date(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->toggleable()
                    ->date(),
            ])

            ->emptyStateHeading('Tidak ada data kategori')
            ->emptyStateDescription('Silahkan tambahkan data kategori terlebih dahulu.')
            ->emptyStateIcon('heroicon-o-no-symbol')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Tambahkan')
                    ->url(route('filament.admin.barangs.resources.kategoris.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])

            ->filters([
                //
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
            'index' => Pages\ListKategoris::route('/'),
            'create' => Pages\CreateKategori::route('/create'),
            'edit' => Pages\EditKategori::route('/{record}/edit'),
        ];
    }
}
