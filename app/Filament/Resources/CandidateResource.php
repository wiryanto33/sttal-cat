<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateResource\Pages;
use App\Filament\Resources\CandidateResource\RelationManagers;
use App\Models\Candidate;
use App\Models\Prodi;
use Filament\Tables\Actions\Action; // ✅ Correct import for table actions
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'CAMABA';

    protected static ?string $modelLabel = 'Calon Mahasiswa Baru';
    protected static ?string $pluralModelLabel = 'Calon Mahasiswa Baru';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION 1: Data Akun & Pribadi
                Section::make('Identitas Dasar')
                    ->schema([
                        // Pilih User (Relasi ke tabel users)
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Akun User')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Pilih Periode Ujian
                        Select::make('exam_period_id')
                            // GANTI 'examPeriod' MENJADI 'period' (Sesuai nama fungsi di Model Candidate)
                            ->relationship('period', 'name', fn($query) => $query->active())
                            ->label('Periode Gelombang')
                            ->required(),

                        // Upload Foto
                        FileUpload::make('photo_path')
                            ->label('Foto Peserta')
                            ->image()
                            ->avatar() // Tampilan bulat
                            ->directory('candidates') // Disimpan di folder storage/app/public/candidates
                            ->columnSpanFull(),
                    ])->columns(2),

                // SECTION 2: Data Militer
                Section::make('Data Militer & Ujian')
                    ->description('Pastikan NRP dan Nomor Ujian sesuai data panitia.')
                    ->schema([
                        TextInput::make('nrp')
                            ->label('NRP')
                            ->required()
                            ->unique(ignoreRecord: true), // Cek unik kecuali data ini sendiri

                        TextInput::make('exam_number')
                            ->label('Nomor Ujian')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('pangkat')
                            ->label('Pangkat')
                            ->placeholder('Contoh: Lettu, Kapten')
                            ->required(),

                        TextInput::make('korps')
                            ->label('Korps')
                            ->placeholder('Contoh: Pelaut (P), Teknik (T)')
                            ->required(),

                        TextInput::make('satuan')
                            ->label('Satuan Asal')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                // SECTION 3: Pilihan Prodi (Dinamis)
                Section::make('Pilihan Akademik')
                    ->schema([
                        // 1. Pilih Strata Dulu
                        Select::make('strata_id')
                            ->relationship('strata', 'name')
                            ->label('Jenjang Pendidikan')
                            ->live() // Agar form refresh saat ini diganti
                            ->afterStateUpdated(fn(Set $set) => $set('prodi_1_id', null)) // Reset prodi jika strata ganti
                            ->required(),

                        // 2. Prodi 1 (Hanya muncul sesuai Strata yg dipilih)
                        Select::make('prodi_1_id')
                            ->label('Pilihan Prodi Utama')
                            ->options(
                                fn(Get $get) =>
                                Prodi::where('strata_id', $get('strata_id'))->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        // 3. Prodi 2 (Opsional & Filter Sesuai Strata)
                        Select::make('prodi_2_id')
                            ->label('Pilihan Prodi Kedua (Opsional)')
                            ->options(
                                fn(Get $get) =>
                                Prodi::where('strata_id', $get('strata_id'))->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload(),
                    ])->columns(3),

                // SECTION 4: Status Verifikasi
                Section::make('Status Administrasi')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Menunggu Verifikasi',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->native(false),

                        Textarea::make('admin_note')
                            ->label('Catatan Admin')
                            ->placeholder('Isi alasan jika ditolak...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_path')->circular()->label('Foto'),
                TextColumn::make('nrp')->searchable()->sortable(),
                TextColumn::make('user.name')->label('Nama'),
                TextColumn::make('exam_number')->label('No Ujian')->searchable(),
                TextColumn::make('pangkat'),
                TextColumn::make('korps'),

                // Kolom Status dengan Warna
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu Verifikasi',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                // Tombol Edit Bawaan
                Tables\Actions\EditAction::make(),

                // CUSTOM ACTION: APPROVE
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'approved']))
                    ->visible(fn($record) => $record->status !== 'approved'),

                // CUSTOM ACTION: REJECT
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('admin_note')->label('Alasan Penolakan')->required()
                    ])
                    ->action(fn($record, array $data) => $record->update([
                        'status' => 'rejected',
                        'admin_note' => $data['admin_note']
                    ]))
                    ->visible(fn($record) => $record->status !== 'rejected'),
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
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'edit' => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}
