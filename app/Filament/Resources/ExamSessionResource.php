<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamSessionResource\Pages;
use App\Models\ExamSession;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Forms\ComponentContainer;
use Filament\Notifications\Notification;

class ExamSessionResource extends Resource
{
    protected static ?string $model = ExamSession::class;
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $navigationLabel = 'Monitoring Ujian';
    protected static ?string $navigationGroup = 'Management dan Monitoring Ujian';

    protected static ?string $modelLabel = 'Monitoring Ujian Calon Mahasiswa Baru';
    protected static ?string $pluralModelLabel = 'Monitoring Ujian Calon Mahasiswa Baru';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            // 1. TAMBAHKAN GROUPING DI SINI
            ->groups([
                Tables\Grouping\Group::make('candidate.user.name')
                    ->label('Nama Peserta')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(
                        fn($record) =>
                        // Format: Nama - Pangkat Korps (NRP: xxx)
                        $record->candidate->user->name . ' — ' .
                            $record->candidate->pangkat . ' ' .
                            $record->candidate->korps . ' (NRP: ' .
                            $record->candidate->nrp . ')'
                    ),
            ])
            ->defaultGroup('candidate.user.name') // Grouping aktif secara default
            // -----------------------------

            ->columns([
                // 2. HAPUS/SEMBUNYIKAN KOLOM NAMA PESERTA
                // (Karena namanya sudah muncul di Header Group, kita sembunyikan kolom ini agar tidak duplikat)
                TextColumn::make('candidate.user.name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan by default

                TextColumn::make('examPacket.title')
                    ->label('Paket Ujian')
                    ->weight('bold')
                    ->color('primary')
                    ->limit(30),

                // KOLOM STATUS
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        0 => 'Belum Mulai',
                        1 => 'Sedang Mengerjakan',
                        2 => 'Menunggu Koreksi',
                        3 => 'Selesai',
                    })
                    ->color(fn($state) => match ($state) {
                        1 => 'warning',
                        2 => 'danger',
                        3 => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('score_tpa_aggregate')
                    ->label('Nilai PG')
                    ->numeric(2)
                    ->alignCenter(),

                TextColumn::make('score_essay_aggregate')
                    ->label('Nilai Essay')
                    ->numeric(2)
                    ->placeholder('-')
                    ->alignCenter(),

                TextColumn::make('total_score')
                    ->label('Total')
                    ->numeric(2)
                    ->weight('black')
                    ->color('success')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        0 => 'Belum Mulai',
                        1 => 'Sedang Mengerjakan',
                        2 => 'Menunggu Koreksi',
                        3 => 'Selesai Final',
                    ])
            ])
            ->actions([
                // TOMBOL KOREKSI TETAP SAMA
                Tables\Actions\Action::make('grade_essay')
                    ->label('Koreksi')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->button() // Ubah jadi button agar lebih terlihat
                    ->size('xs')
                    ->visible(fn($record) => in_array($record->status, [2, 3]))
                    ->mountUsing(function (ComponentContainer $form, $record) {
                        // ... (Logika mountUsing Anda tetap sama, copy paste di sini) ...
                        // Saya singkat biar tidak kepanjangan
                        $answers = $record->answers()
                            ->whereHas('question', fn($q) => $q->where('type', 'essay'))
                            ->with('question')
                            ->get()
                            ->map(fn($answer) => [
                                'answer_id' => $answer->id,
                                'question_text' => $answer->question->content,
                                'student_answer' => $answer->answer ?? '-',
                                'question_weight' => $answer->question->weight ?? 0,
                                'question_reference' => $answer->question->correct_answer,
                                'score' => $answer->score,
                            ])->toArray();
                        $form->fill(['essays' => $answers]);
                    })
                    ->form([
                        // ... (Logika Form Repeater Anda tetap sama) ...
                        Repeater::make('essays')
                            ->label('Koreksi Jawaban Essay')
                            ->addable(false)
                            ->deletable(false)
                            ->schema([
                                Placeholder::make('display')
                                    ->content(fn($get) => new HtmlString("
                                    <div class='p-3 border rounded bg-gray-50 mb-2 text-sm'>
                                        <b>Soal (Bobot: {$get('question_weight')})</b><br>{$get('question_text')}<br>
                                        <div class='mt-2 bg-white p-2 border text-blue-900'><b>Jawab:</b> {$get('student_answer')}</div>
                                    </div>
                                ")),
                                Hidden::make('answer_id'),
                                Hidden::make('question_weight'),
                                Radio::make('grading_level')
                                    ->label('Penilaian')
                                    ->inline()
                                    ->options(fn($get) => [
                                        '100' => 'Benar (' . $get('question_weight') . ')',
                                        '50'  => 'Setengah (' . ($get('question_weight') * 0.5) . ')',
                                        '0'   => 'Salah (0)',
                                    ])
                                    ->required()
                                    ->afterStateHydrated(function (Radio $component, $get) {
                                        $s = $get('score');
                                        $w = $get('question_weight');
                                        if ($s !== null && $w > 0) {
                                            $p = ($s / $w) * 100;
                                            $component->state($p >= 100 ? '100' : ($p >= 50 ? '50' : '0'));
                                        }
                                    })
                            ])
                    ])
                    ->action(function (array $data, $record) {
                        // ... (Logika Action Save Anda tetap sama) ...
                        $totalEssay = 0;
                        foreach ($data['essays'] as $item) {
                            $nilai = ((int)$item['grading_level'] / 100) * (int)$item['question_weight'];
                            \App\Models\ExamAnswer::where('id', $item['answer_id'])->update(['score' => $nilai]);
                            $totalEssay += $nilai;
                        }
                        $record->refresh();
                        $nilaiPG = $record->score_tpa_aggregate ?? 0;
                        $record->update([
                            'score_essay_aggregate' => $totalEssay,
                            'total_score' => $nilaiPG + $totalEssay,
                            'status' => 3
                        ]);
                        Notification::make()->title('Nilai Disimpan')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListExamSessions::route('/')];
    }
    public static function getRelations(): array
    {
        return [];
    }
}
