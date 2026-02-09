<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestExamSessions extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Monitoring Progres Peserta (Realtime)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Candidate::query()
                    ->whereHas('examSessions')
                    ->with(['user', 'examSessions.examPacket'])
                    ->latest('updated_at')
                    ->limit(5)
            )
            ->columns([
                // 1. Identitas Peserta (DIPERBAIKI DI SINI)
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peserta')
                    // Menggabungkan Pangkat, Korps, dan NRP di bawah nama
                    ->description(fn($record) => "{$record->pangkat} {$record->korps} / NRP: {$record->nrp}")
                    ->weight('bold')
                    ->searchable(),

                // 2. Status Paket Ujian (Looping Badge)
                Tables\Columns\TextColumn::make('examSessions')
                    ->label('Status Paket Ujian')
                    ->formatStateUsing(function ($record) {
                        $sessions = $record->examSessions;

                        $html = '<div class="flex flex-wrap gap-2">';
                        foreach ($sessions as $sesi) {
                            $warna = match ($sesi->status) {
                                1 => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                2, 3 => 'bg-green-100 text-green-800 border-green-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };

                            $icon = match ($sesi->status) {
                                1 => '⏳',
                                2, 3 => '✅',
                                default => '⏸️'
                            };

                            $paket = $sesi->examPacket->title ?? 'Paket';

                            $html .= "<span class='px-2 py-1 text-xs rounded-md border $warna flex items-center gap-1'>
                                        $icon $paket
                                      </span>";
                        }
                        $html .= '</div>';

                        return new \Illuminate\Support\HtmlString($html);
                    }),
            ])
            ->paginated(false);
    }
}
