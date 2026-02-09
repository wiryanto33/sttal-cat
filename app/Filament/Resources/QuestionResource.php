<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    
    // --- TAMBAHKAN BARIS INI UNTUK MENYEMBUNYIKAN DARI SIDEBAR ---
    protected static bool $shouldRegisterNavigation = false;
    // -------------------------------------------------------------

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pilih Paket Soal (Wadah)
                Select::make('exam_packet_id')
                    ->label('Masuk ke Paket Soal?')
                    ->relationship('examPacket', 'title') // Menampilkan Nama Paket
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        // Fitur Quick Create Paket baru langsung dari sini (Opsional)
                        TextInput::make('title')->required(),
                        TextInput::make('duration_minutes')->numeric()->required(),
                        Select::make('exam_period_id')->relationship('examPeriod', 'name')->required(),
                        Select::make('strata_id')->relationship('strata', 'name')->required(),
                        Select::make('exam_category_id')->relationship('examCategory', 'name')->required(),
                    ]),

                // 1. Pilih Tipe Soal
                Select::make('type')
                    ->label('Tipe Soal')
                    ->options([
                        'multiple_choice' => 'Pilihan Ganda',
                        'essay' => 'Esai / Uraian',
                    ])
                    ->default('multiple_choice')
                    ->required()
                    ->live(),

                Section::make('Konten Soal')->schema([
                    RichEditor::make('content')->label('Pertanyaan')->required(),
                    FileUpload::make('image_path')->label('Gambar (Opsional)')->directory('soal'),

                    // Opsi Jawaban (Disimpan sbg JSON: Key=Huruf, Value=Teks)
                    KeyValue::make('options')
                        ->label('Pilihan Jawaban')
                        ->keyLabel('Huruf (A, B, C, D)')
                        ->valueLabel('Teks Jawaban')
                        ->addButtonLabel('Tambah Opsi')
                        ->required(),

                    Select::make('correct_answer')
                        ->label('Kunci Jawaban')
                        ->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'])
                        ->required(),
                ]) // HANYA MUNCUL JIKA TIPE = MULTIPLE_CHOICE
                    ->visible(fn(Get $get) => $get('type') === 'multiple_choice'),

                // --- BAGIAN KHUSUS ESSAY ---
                Section::make('Kunci Jawaban Essay')
                    ->schema([
                        Textarea::make('correct_answer')
                            ->label('Jawaban Referensi / Poin Penilaian')
                            ->helperText('Jawaban ini digunakan oleh korektor sebagai acuan nilai.')
                            ->rows(5)
                            ->required(), // Wajib jika tipe = essay
                    ])
                    // HANYA MUNCUL JIKA TIPE = ESSAY
                    ->visible(fn(Get $get) => $get('type') === 'essay'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tampilkan Info Paket
                TextColumn::make('examPacket.title')->label('Paket Soal')->searchable(),
                TextColumn::make('examPacket.strata.name')->label('Strata')->badge(), // Info Strata muncul disini
                TextColumn::make('examPacket.examCategory.name')->label('Mapel'),
                TextColumn::make('content')->html()->limit(50),
            ])
            ->filters([
                // Filter berdasarkan Paket
                SelectFilter::make('exam_packet_id')
                    ->relationship('examPacket', 'title')
                    ->label('Filter Paket'),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
