<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingGalleryResource\Pages;
use App\Filament\Resources\LandingGalleryResource\RelationManagers;
use App\Models\LandingGallery;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LandingGalleryResource extends Resource
{
    protected static ?string $model = LandingGallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Setting Content Landing Page';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required()->label('Nama Kegiatan'),
            DatePicker::make('event_date')->label('Tanggal Kegiatan'),
            FileUpload::make('image_path')->image()->directory('gallery')->required(),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image_path'),
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\TextColumn::make('event_date')->date(),
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
            'index' => Pages\ListLandingGalleries::route('/'),
            'create' => Pages\CreateLandingGallery::route('/create'),
            'edit' => Pages\EditLandingGallery::route('/{record}/edit'),
        ];
    }
}
