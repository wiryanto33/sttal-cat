<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingHeroResource\Pages;
use App\Filament\Resources\LandingHeroResource\RelationManagers;
use App\Models\LandingHero;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LandingHeroResource extends Resource
{
    protected static ?string $model = LandingHero::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Setting Content Landing Page';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required()->label('Judul Besar'),
            Textarea::make('caption')->label('Sub Judul'),
            FileUpload::make('image_path')->image()->directory('hero')->required()->label('Background Image'),
            Toggle::make('is_active')->label('Aktifkan Banner Ini')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image_path'),
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
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
            'index' => Pages\ListLandingHeroes::route('/'),
            'create' => Pages\CreateLandingHero::route('/create'),
            'edit' => Pages\EditLandingHero::route('/{record}/edit'),
        ];
    }
}
