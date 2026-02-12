<?php

namespace App\Filament\Resources\ExamReportResource\Pages;

use App\Filament\Resources\ExamReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamReport extends EditRecord
{
    protected static string $resource = ExamReportResource::class;

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
