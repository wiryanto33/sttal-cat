<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Filament\Resources\SiteSettingResource\RelationManagers;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Setting Content Landing Page';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas Aplikasi')
                    ->schema([
                        TextInput::make('site_name')->required()->label('Nama Aplikasi'),
                        FileUpload::make('logo_path')
                            ->image()
                            ->directory('settings')
                            ->label('Logo Header (PNG/Transparan)'),
                        FileUpload::make('favicon_path')
                            ->image()
                            ->directory('settings')
                            ->label('Favicon (Icon Browser)'),
                    ])->columns(2),

                Section::make('Tampilan Halaman Auth')
                    ->schema([
                        FileUpload::make('login_image_path')
                            ->image()
                            ->directory('settings')
                            ->label('Gambar Samping Login'),
                        FileUpload::make('register_image_path')
                            ->image()
                            ->directory('settings')
                            ->label('Gambar Samping Register'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site_name')->label('Nama Aplikasi'),
                Tables\Columns\ImageColumn::make('logo_path')->label('Logo Header')->rounded(),
                Tables\Columns\ImageColumn::make('favicon_path')->label('Favicon')->rounded(),
                Tables\Columns\ImageColumn::make('login_image_path')->label('Gambar Login')->rounded(),
                Tables\Columns\ImageColumn::make('register_image_path')->label('Gambar Register')->rounded(),
                
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
            'index' => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSetting::route('/create'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
