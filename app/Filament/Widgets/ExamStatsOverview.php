<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use App\Models\ExamSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExamStatsOverview extends BaseWidget
{
    // Mengatur agar widget melakukan auto-refresh setiap 5 detik
    protected static ?string $pollingInterval = '5s';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Peserta', Candidate::count())
                ->description('Semua calon mahasiswa')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Menunggu Verifikasi', Candidate::where('status', 'pending')->count())
                ->description('Butuh tindakan admin')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make('Peserta Aktif', ExamSession::where('status', 1)->count())
                ->description('Sedang mengerjakan ujian')
                ->descriptionIcon('heroicon-m-play')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]), // Contoh tren grafik sederhana

            Stat::make('Perlu Koreksi', ExamSession::where('status', 2)->count())
                ->description('Menunggu penilaian essay')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),

            Stat::make('Selesai', ExamSession::where('status', 3)->count())
                ->description('Nilai akhir sudah keluar')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Diskualifikasi', ExamSession::where('is_disqualified', true)->count())
                ->description('Pelanggaran batas proctoring')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
