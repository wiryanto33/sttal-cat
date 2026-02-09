<?php

namespace App\Filament\Resources\ExamPacketResource\Pages;

use App\Filament\Resources\ExamPacketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamPackets extends ListRecords
{
    protected static string $resource = ExamPacketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
