<?php

namespace App\Filament\Resources\ExamPeriodResource\Pages;

use App\Filament\Resources\ExamPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamPeriods extends ListRecords
{
    protected static string $resource = ExamPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
