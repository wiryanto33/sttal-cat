<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamSessionResource\Pages;
use App\Models\ExamSession;
use App\Models\ExamAnswer;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\HtmlString;
use Filament\Forms\ComponentContainer;
use Filament\Notifications\Notification;

class ExamSessionResource extends Resource
{
    protected static ?string $model = ExamSession::class;
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $navigationLabel = 'Monitoring Ujian';
    protected static ?string $navigationGroup = 'Management dan Monitoring Ujian';
    protected static ?string $modelLabel = 'Monitoring Ujian';
    protected static ?string $pluralModelLabel = 'Monitoring Ujian';

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
            ->poll('5s') // Auto-refresh setiap 5 detik untuk monitoring real-time
            ->groups([
                Group::make('candidate.user.name')
                    ->label('Nama Peserta')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(
                        fn($record) =>
                        "{$record->candidate->user->name} — {$record->candidate->pangkat} {$record->candidate->korps} (NRP: {$record->candidate->nrp})"
                    ),
            ])
            ->defaultGroup('candidate.user.name')
            ->columns([
                TextColumn::make('candidate.user.name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('examPacket.title')
                    ->label('Paket Ujian')
                    ->weight('bold')
                    ->color('primary')
                    ->limit(30),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        0 => 'Belum Mulai',
                        1 => 'Mengerjakan',
                        2 => 'Perlu Koreksi',
                        3 => 'Selesai',
                        default => 'Unknown',
                    })
                    ->color(fn($state) => match ($state) {
                        1 => 'info',
                        2 => 'warning',
                        3 => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('violations_count')
                    ->label('Pelanggaran')
                    ->counts('violations')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 4 => 'danger',
                        $state >= 1 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn($state) => $state . ' Log')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_disqualified')
                    ->label('Status Diskualifikasi')
                    // Jangan gunakan ->boolean() karena akan memaksa true = hijau
                    ->icon(fn($state): string => $state ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn($state) => ($state === true || $state === 1) ? 'danger' : 'success')
                    ->alignCenter()
                    ->tooltip(fn($record) => $record->disqualification_reason),

                TextColumn::make('total_score')
                    ->label('Total Nilai')
                    ->numeric(2)
                    ->weight('black')
                    ->color('success')
                    ->alignCenter(),
            ])
            ->actions([
                // 1. LIHAT LOG PELANGGARAN
                Tables\Actions\Action::make('view_logs')
                    ->label('Log')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->modalHeading('Kronologi Pelanggaran')
                    ->modalWidth('lg')
                    ->modalContent(fn(ExamSession $record) => view('filament.components.violation-logs', [
                        'logs' => $record->violations
                    ]))
                    ->modalSubmitAction(false),

                // 2. PEMULIHAN PESERTA (Reset Diskualifikasi)
                Tables\Actions\Action::make('reset_violations')
                    ->label('Pulihkan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Pulihkan Sesi Ujian')
                    ->modalDescription('Menghapus semua pelanggaran dan membatalkan diskualifikasi. Lanjutkan?')
                    ->action(function (ExamSession $record) {
                        $record->violations()->delete();
                        $record->update([
                            'is_disqualified' => false,
                            'disqualification_reason' => null,
                            'status' => 1,
                        ]);
                        Notification::make()->title('Peserta Berhasil Dipulihkan')->success()->send();
                    })
                    ->visible(fn(ExamSession $record) => $record->is_disqualified),

                // 3. KOREKSI ESSAY
                Tables\Actions\Action::make('grade_essay')
                    ->label('Koreksi')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->visible(fn($record) => in_array($record->status, [2, 3]) && !$record->is_disqualified)
                    ->mountUsing(function (ComponentContainer $form, $record) {
                        $essays = $record->answers()
                            ->whereHas('question', fn($q) => $q->where('type', 'essay'))
                            ->with('question')
                            ->get()
                            ->map(fn($answer) => [
                                'answer_id' => $answer->id,
                                'question_text' => $answer->question->content,
                                'student_answer' => $answer->answer ?? '-',
                                'question_weight' => $answer->question->weight ?? 0,
                                'score' => $answer->score,
                            ])->toArray();
                        $form->fill(['essays' => $essays]);
                    })
                    ->form([
                        Repeater::make('essays')
                            ->label('Daftar Jawaban Essay')
                            ->addable(false)
                            ->deletable(false)
                            ->schema([
                                Placeholder::make('display')
                                    ->content(fn($get) => new HtmlString("
                                        <div class='p-4 border rounded-lg bg-gray-50 mb-2 shadow-sm'>
                                            <div class='mb-2 text-gray-700 font-semibold'>Pertanyaan:</div>
                                            <div class='mb-4 text-gray-600 italic'>{$get('question_text')}</div>
                                            <div class='p-3 bg-blue-50 border-l-4 border-blue-500 rounded text-blue-900'>
                                                <div class='text-xs uppercase font-bold mb-1'>Jawaban Peserta:</div>
                                                <div class='text-md'>{$get('student_answer')}</div>
                                            </div>
                                            <div class='mt-2 text-right text-xs font-bold text-gray-500'>Bobot Soal: {$get('question_weight')}</div>
                                        </div>
                                    ")),
                                Hidden::make('answer_id'),
                                Hidden::make('question_weight'),
                                Radio::make('grading_level')
                                    ->label('Beri Penilaian')
                                    ->inline()
                                    ->options(fn($get) => [
                                        '100' => 'Benar (' . $get('question_weight') . ')',
                                        '50'  => 'Setengah (' . ($get('question_weight') * 0.5) . ')',
                                        '0'   => 'Salah (0)',
                                    ])
                                    ->required()
                                    ->afterStateHydrated(function (Radio $component, $get) {
                                        $s = (float) $get('score');
                                        $w = (float) $get('question_weight');
                                        if ($w > 0) {
                                            $p = ($s / $w) * 100;
                                            $component->state($p >= 100 ? '100' : ($p >= 50 ? '50' : '0'));
                                        }
                                    })
                            ])
                    ])
                    ->action(function (array $data, ExamSession $record) {
                        $totalEssay = 0;
                        foreach ($data['essays'] as $item) {
                            $nilai = ((float)$item['grading_level'] / 100) * (float)$item['question_weight'];
                            ExamAnswer::where('id', $item['answer_id'])->update(['score' => $nilai]);
                            $totalEssay += $nilai;
                        }

                        $record->refresh();
                        $nilaiPG = (float) $record->score_tpa_aggregate;

                        $record->update([
                            'score_essay_aggregate' => $totalEssay,
                            'total_score' => $nilaiPG + $totalEssay,
                            'status' => 3
                        ]);
                        Notification::make()->title('Penilaian Disimpan')->success()->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListExamSessions::route('/')];
    }
}
