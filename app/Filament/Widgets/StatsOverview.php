<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use App\Models\ExamSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Agar widget ini refresh otomatis setiap 15 detik (Realtime monitoring)
    protected static ?string $pollingInterval = '15s';

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
                ->color('warning'), // Kuning biar eye-catching

            Stat::make('Sedang Ujian', ExamSession::where('status', 1)->count())
                ->description('Peserta online saat ini')
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('success') // Hijau
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Dummy sparkline
            ,

            Stat::make('Perlu Koreksi Essay', ExamSession::where('status', 2)->where('score_essay_aggregate', 0)->count())
                ->description('Menunggu penilaian dosen')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('danger'),
        ];
    }
}
