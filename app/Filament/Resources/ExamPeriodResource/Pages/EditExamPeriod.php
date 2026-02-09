<?php

namespace App\Filament\Resources\ExamPeriodResource\Pages;

use App\Filament\Resources\ExamPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamPeriod extends EditRecord
{
    protected static string $resource = ExamPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
