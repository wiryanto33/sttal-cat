<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingStatResource\Pages;
use App\Filament\Resources\LandingStatResource\RelationManagers;
use App\Models\LandingStat;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LandingStatResource extends Resource
{
    protected static ?string $model = LandingStat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Setting Content Landing Page';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('label')->required()->label('Judul (ex: Total Peserta)'),
            TextInput::make('value')->required()->label('Angka (ex: 1,200+)'),
            TextInput::make('description')->label('Keterangan Kecil'),

            Select::make('icon')
                ->options([
                    'heroicon-o-academic-cap' => 'Topi Toga',
                    'heroicon-o-users' => 'Orang/User',
                    'heroicon-o-clock' => 'Jam/Waktu',
                    'heroicon-o-check-circle' => 'Centang',
                    'heroicon-o-computer-desktop' => 'Komputer',
                ])
                ->searchable()
                ->required(),

            Select::make('color')
                ->options([
                    'blue' => 'Biru',
                    'yellow' => 'Kuning',
                    'red' => 'Merah',
                    'green' => 'Hijau',
                ])
                ->required(),

            TextInput::make('sort_order')->numeric()->default(0)->label('Urutan'),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('label'),
            Tables\Columns\TextColumn::make('value'),
            Tables\Columns\TextColumn::make('icon'),
        ])->defaultSort('sort_order')
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
            'index' => Pages\ListLandingStats::route('/'),
            'create' => Pages\CreateLandingStat::route('/create'),
            'edit' => Pages\EditLandingStat::route('/{record}/edit'),
        ];
    }
}
