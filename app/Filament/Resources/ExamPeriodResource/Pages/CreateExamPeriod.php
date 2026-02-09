<?php

namespace App\Filament\Resources\ExamPeriodResource\Pages;

use App\Filament\Resources\ExamPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExamPeriod extends CreateRecord
{
    protected static string $resource = ExamPeriodResource::class;
    protected static bool $canCreateAnother = false;
    protected static ?string $title = 'Buat Periode';


    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
