<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamReportResource\Pages;
use App\Models\ExamSession; // Kita gunakan model ExamSession
use App\Models\Strata;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamReportResource extends Resource
{
    // Gunakan ExamSession sebagai model dasar laporan
    protected static ?string $model = ExamSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Laporan Penilaian';
    protected static ?string $navigationGroup = 'Management dan Monitoring Ujian';
    protected static ?string $slug = 'laporan-penilaian';

    // Nonaktifkan tombol Create di halaman laporan
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 3))
            ->defaultSort('total_score', 'desc')
            ->groups([
                Tables\Grouping\Group::make('candidate.strata.name')->label('Strata'),
                Tables\Grouping\Group::make('candidate.prodi1.name')->label('Prodi Pilihan 1'),
            ])
            ->defaultGroup('candidate.prodi1.name')
            ->columns([
                Tables\Columns\TextColumn::make('candidate.user.name')->label('Nama Peserta')->searchable(),
                Tables\Columns\TextColumn::make('candidate.nrp')->label('NRP'),
                Tables\Columns\TextColumn::make('score_tpa_aggregate')->label('Nilai PG')->alignCenter(),
                Tables\Columns\TextColumn::make('score_essay_aggregate')->label('Nilai Essay')->alignCenter(),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('Total Skor')
                    ->weight('black')
                    ->alignCenter()
                    ->color(function ($record) {
                        // Ambil skor dalam kelompok prodi yang sama
                        $prodiId = $record->candidate->prodi_1_id;
                        $scores = \App\Models\ExamSession::whereHas('candidate', fn($q) => $q->where('prodi_1_id', $prodiId))
                            ->where('status', 3)
                            ->pluck('total_score');

                        if ($scores->count() <= 1) return null;
                        if ($record->total_score == $scores->max()) return 'success'; // Hijau
                        if ($record->total_score == $scores->min()) return 'danger';  // Merah
                        return null;
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('downloadExcel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->action(fn() => \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExamReportExport, 'Laporan_Nilai_Ujian.xlsx')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamReports::route('/'),
        ];
    }
}
