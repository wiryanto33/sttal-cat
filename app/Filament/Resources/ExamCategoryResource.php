<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamCategoryResource\Pages;
use App\Filament\Resources\ExamCategoryResource\RelationManagers;
use App\Models\ExamCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamCategoryResource extends Resource
{
    protected static ?string $model = ExamCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Kategori Soal Ujian';

    protected static ?string $modelLabel = 'Kategori Soal Ujian';
    protected static ?string $pluralModelLabel = 'Kategori Soal Ujian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Mata Uji')
                    ->placeholder('Contoh: Matematika Dasar, Fisika Terapan')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->helperText('Jika non-aktif, kategori ini tidak akan muncul di input soal.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Mata Uji')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListExamCategories::route('/'),
            'create' => Pages\CreateExamCategory::route('/create'),
            'edit' => Pages\EditExamCategory::route('/{record}/edit'),
        ];
    }
}
