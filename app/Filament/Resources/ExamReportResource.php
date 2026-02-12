<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamReportResource\Pages;
use App\Models\ExamSession;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class ExamReportResource extends Resource
{
    protected static ?string $model = ExamSession::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Laporan Penilaian';
    protected static ?string $navigationGroup = 'Management dan Monitoring Ujian';
    protected static ?string $slug = 'laporan-penilaian';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 3))
            ->defaultSort('total_score', 'desc')

            // STRUKTUR GROUPING: Strata -> Prodi -> Nama Kandidat
            ->groups([
                Tables\Grouping\Group::make('candidate.strata.name')
                    ->label('Strata')
                    ->collapsible(),
                Tables\Grouping\Group::make('candidate.prodi1.name')
                    ->label('Program Studi')
                    ->collapsible(),
                Tables\Grouping\Group::make('candidate.user.name')
                    ->label('Nama Peserta')
                    ->collapsible()
                    ->getTitleFromRecordUsing(
                        fn($record) =>
                        "{$record->candidate->user->name}  " .
                            "{$record->candidate->pangkat} {$record->candidate->korps} / NRP: {$record->candidate->nrp}"
                    ),
            ])
            // Default awal tampilkan pengelompokan berdasarkan Nama agar rapi
            ->defaultGroup('candidate.user.name')

            ->columns([
                Tables\Columns\TextColumn::make('examPacket.title')
                    ->label('Mata Ujian / Peserta')
                    ->weight('bold')
                    ->description(
                        fn($record) =>
                        strtoupper($record->candidate->user->name ?? '-') . " | " .
                            ($record->candidate->pangkat ?? '-') . " " . ($record->candidate->korps ?? '-') .
                            " (" . ($record->candidate->nrp ?? '-') . ") " .
                            "\n Diselesaikan pada: " . ($record->end_time?->format('d/m/Y H:i') ?? '-')
                    )
                    ->wrap()
                    /* PERBAIKAN SEARCHABLE:
       Gunakan prefix tabel jika perlu, atau pastikan path relasi benar.
       Filament akan menangani Join secara otomatis.
    */
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('candidate.user', function (Builder $q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('candidate', function (Builder $q) use ($search) {
                            $q->where('nrp', 'like', "%{$search}%")
                                ->orWhere('pangkat', 'like', "%{$search}%");
                        })->orWhereHas('examPacket', function (Builder $q) use ($search) {
                            $q->where('title', 'like', "%{$search}%");
                        });
                    }), // Agar tetap bisa dicari via kolom ini

                TextColumn::make('score_tpa_aggregate')
                    ->label('Skor PG')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                TextColumn::make('score_essay_aggregate')
                    ->label('Skor Essay')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('total_score')
                    ->label('Nilai Akhir')
                    ->weight('black')
                    ->alignCenter()
                    ->color(function ($record) {
                        // Logika warna Tertinggi/Terendah tetap per Prodi
                        $prodiId = $record->candidate->prodi_1_id;
                        $scores = ExamSession::whereHas('candidate', fn($q) => $q->where('prodi_1_id', $prodiId))
                            ->where('status', 3)
                            ->pluck('total_score');

                        if ($scores->count() <= 1) return null;
                        if ($record->total_score == $scores->max()) return 'success';
                        if ($record->total_score == $scores->min()) return 'danger';
                        return null;
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('downloadExcel')
                    ->label('Export Excel Rekap')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->action(fn() => \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExamReportExport, 'Rekap_Nilai_Peserta.xlsx')),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListExamReports::route('/')];
    }
}
