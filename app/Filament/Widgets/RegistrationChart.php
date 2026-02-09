<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RegistrationChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendaftaran Peserta (7 Hari Terakhir)';
    protected static ?int $sort = 2; // Urutan tampilan di dashboard

    protected function getData(): array
    {
        $data = Trend::model(Candidate::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Pendaftar Baru',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6', // Biru
                    'fill' => 'start',
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
