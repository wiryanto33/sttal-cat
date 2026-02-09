<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamPeriodResource\Pages;
use App\Filament\Resources\ExamPeriodResource\RelationManagers;
use App\Models\ExamPeriod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamPeriodResource extends Resource
{
    protected static ?string $model = ExamPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = ' Periode Ujian';

    protected static ?string $modelLabel = 'Periode Ujian CBT';
    protected static ?string $pluralModelLabel = 'Periode Ujian CBT';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year')
                    ->label('Tahun')
                    ->numeric()
                    ->required()
                    ->maxLength(4),
                Forms\Components\TextInput::make('name')
                    ->label('Deskripsi')
                    ->placeholder('Contoh: Seleksi Masuk STTAL 2026')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->helperText('Hanya satu periode yang boleh aktif dalam satu waktu.')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Gelombang')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(), // Menampilkan tanda centang/silang
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListExamPeriods::route('/'),
            'create' => Pages\CreateExamPeriod::route('/create'),
            'edit' => Pages\EditExamPeriod::route('/{record}/edit'),
        ];
    }
}
