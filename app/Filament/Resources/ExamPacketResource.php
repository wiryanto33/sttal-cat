<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamPacketResource\Pages;
use App\Models\ExamPacket;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker; // Untuk Input Waktu
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle; // Untuk Switch ON/OFF
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn; // Untuk Switch di Tabel
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExamPacketResource extends Resource
{
    protected static ?string $model = ExamPacket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Paket Soal Ujian';
    protected static ?string $navigationGroup = 'Management dan Monitoring Ujian';

    protected static ?string $modelLabel = 'Paket Soal Ujian';
    protected static ?string $pluralModelLabel = 'Paket Soal Ujian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- BAGIAN 1: STATUS & JADWAL (BARU) ---
                Section::make('Status & Jadwal Ujian')
                    ->description('Atur kapan ujian ini bisa diakses oleh peserta.')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Publikasikan Paket Soal?')
                            ->helperText('Jika dimatikan, paket ini tidak akan terlihat sama sekali oleh peserta.')
                            ->default(false)
                            ->required()
                            ->columnSpanFull(),

                        DateTimePicker::make('start_time')
                            ->label('Waktu Mulai Akses')
                            ->placeholder('Pilih tanggal & jam...')
                            ->helperText('Peserta baru bisa menekan tombol "Mulai" setelah waktu ini.')
                            ->seconds(false),

                        DateTimePicker::make('end_time')
                            ->label('Waktu Selesai Akses (Opsional)')
                            ->placeholder('Pilih tanggal & jam...')
                            ->helperText('Lewat dari jam ini, paket soal akan hilang/ditutup.')
                            ->seconds(false)
                            ->afterOrEqual('start_time'), // Validasi: Tidak boleh sebelum waktu mulai
                    ])->columns(2),

                // --- BAGIAN 2: DETAIL PAKET ---
                Section::make('Detail Paket')
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Paket')
                            ->placeholder('Contoh: Matematika D3 Tahun 2025')
                            ->required(),

                        TextInput::make('duration_minutes')
                            ->label('Durasi Pengerjaan (Menit)')
                            ->numeric()
                            ->default(90)
                            ->helperText('Waktu hitung mundur saat peserta mengerjakan soal.')
                            ->required(),
                    ])->columns(2),

                // --- BAGIAN 3: KLASIFIKASI ---
                Section::make('Klasifikasi')
                    ->schema([
                        Select::make('exam_period_id')
                            ->relationship('examPeriod', 'name', modifyQueryUsing: fn(Builder $query) => $query->active())
                            ->label('Tahun / Periode')
                            ->required(),

                        Select::make('strata_id')
                            ->relationship('strata', 'name')
                            ->label('Strata / Level')
                            ->required(),

                        Select::make('exam_category_id')
                            ->relationship('examCategory', 'name')
                            ->label('Mata Uji')
                            ->required(),

                        // TAMBAHKAN INI
                        Select::make('prodi_id')
                            ->label('Khusus Program Studi')
                            ->relationship('prodi', 'code')
                            ->placeholder('Semua Prodi (Umum)')
                            ->helperText('Pilih jika paket ini hanya untuk pendaftar prodi tertentu.')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                // --- BAGIAN 4: BANK SOAL (REPEATER) ---
                Section::make('Bank Soal')
                    ->schema([
                        Repeater::make('questions')
                            ->relationship()
                            ->label('Daftar Pertanyaan')
                            ->itemLabel(fn(array $state): ?string => strip_tags($state['content'] ?? 'Soal Baru'))
                            ->collapsed()
                            ->cloneable()
                            ->schema([
                                Select::make('type')
                                    ->label('Tipe Soal')
                                    ->options([
                                        'multiple_choice' => 'Pilihan Ganda',
                                        'essay' => 'Esai / Uraian',
                                    ])
                                    ->default('multiple_choice')
                                    ->required()
                                    ->live(),

                                RichEditor::make('content')
                                    ->label('Pertanyaan')
                                    ->required()
                                    ->columnSpanFull()
                                    ->helperText(new HtmlString('
                    <div class="mt-1 text-sm text-gray-600 bg-blue-50 p-2 rounded border border-blue-100">
                        ℹ️ <strong>Tips Matematika:</strong>
                        Jika soal memerlukan simbol rumit (Akar, Pecahan, Integral),
                        buat rumusnya di
                        <a href="https://editor.codecogs.com/" target="_blank" class="text-blue-600 font-bold underline hover:text-blue-800">
                            Online LaTeX Editor ini 🔗
                        </a>,
                        lalu <strong>Copy</strong> kodenya dan <strong>Paste</strong> di sini.
                    </div>
                ')),
                                FileUpload::make('image_path')
                                    ->label('Gambar Pendukung')
                                    ->directory('soal')
                                    ->image(),

                                Section::make('Opsi & Kunci Jawaban')
                                    ->schema([
                                        KeyValue::make('options')
                                            ->label('Pilihan Jawaban (A, B, C, D)')
                                            ->keyLabel('Huruf')
                                            ->valueLabel('Isi Jawaban')
                                            ->required(),

                                        Select::make('correct_answer')
                                            ->label('Kunci Jawaban')
                                            ->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'])
                                            ->required(),
                                    ])
                                    ->visible(fn(Get $get) => $get('type') === 'multiple_choice'),

                                Section::make('Kunci Jawaban Essay')
                                    ->schema([
                                        Textarea::make('correct_answer')
                                            ->label('Jawaban Referensi')
                                            ->rows(4)
                                            ->required(),
                                    ])
                                    ->visible(fn(Get $get) => $get('type') === 'essay'),
                            ])
                            ->grid(1)
                            ->addActionLabel('Tambah Soal Baru'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Toggle Langsung di Tabel (Praktis!)
                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),

                Tables\Columns\TextColumn::make('examPeriod.year')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('strata.name')
                    ->label('Strata')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('examCategory.name')
                    ->label('Mata Uji'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Paket')
                    ->searchable(),

                // Tampilkan Waktu Mulai
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jadwal Mulai')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('Belum diatur'),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->suffix(' Menit'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_period_id')
                    ->relationship('examPeriod', 'name')
                    ->label('Filter Tahun'),

                Tables\Filters\Filter::make('is_active')
                    ->label('Hanya yang Aktif')
                    ->query(fn(Builder $query) => $query->where('is_active', true)),
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

    // ... getRelations, getPages sama seperti sebelumnya
    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamPackets::route('/'),
            'create' => Pages\CreateExamPacket::route('/create'),
            'edit' => Pages\EditExamPacket::route('/{record}/edit'),
        ];
    }
}
