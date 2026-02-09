<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StrataResource\Pages;
use App\Filament\Resources\StrataResource\RelationManagers;
use App\Models\Strata;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StrataResource extends Resource
{
    protected static ?string $model = Strata::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Strata';

    protected static ?string $modelLabel = 'Strata';
    protected static ?string $pluralModelLabel = 'Strata';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Strata')
                    ->placeholder('Contoh: S1, S2, D3')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->rows(3)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Strata')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50),
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
            'index' => Pages\ListStratas::route('/'),
            'create' => Pages\CreateStrata::route('/create'),
            'edit' => Pages\EditStrata::route('/{record}/edit'),
        ];
    }
}
