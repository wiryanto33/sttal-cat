<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdiResource\Pages;
use App\Filament\Resources\ProdiResource\RelationManagers;
use App\Models\Prodi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProdiResource extends Resource
{
    protected static ?string $model = Prodi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Prodi';

    protected static ?string $modelLabel = 'Pogram Studi';
    protected static ?string $pluralModelLabel = 'Program Studi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Dropdown memilih Strata
                Forms\Components\Select::make('strata_id')
                    ->relationship('strata', 'name')
                    ->label('Jenjang Strata')
                    ->required()
                    ->preload()
                    ->searchable(),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Program Studi')
                    ->placeholder('Contoh: Teknik Hidrografi')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('code')
                    ->label('Kode Prodi')
                    ->placeholder('Contoh: HYD-01')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan Nama Strata dari relasi
                Tables\Columns\TextColumn::make('strata.name')
                    ->label('Strata')
                    ->sortable()
                    ->badge() // Tampil seperti badge warna
                    ->color('info'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Program Studi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
            ])
            ->filters([
                // Filter tabel berdasarkan Strata
                Tables\Filters\SelectFilter::make('strata')
                    ->relationship('strata', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProdis::route('/'),
            'create' => Pages\CreateProdi::route('/create'),
            'edit' => Pages\EditProdi::route('/{record}/edit'),
        ];
    }
}
