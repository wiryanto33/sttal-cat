<?php

namespace App\Filament\Resources\ExamReportResource\Pages;

use App\Filament\Resources\ExamReportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExamReport extends CreateRecord
{
    protected static string $resource = ExamReportResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
