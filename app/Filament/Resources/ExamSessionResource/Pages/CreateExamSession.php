<?php

namespace App\Filament\Resources\ExamSessionResource\Pages;

use App\Filament\Resources\ExamSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExamSession extends CreateRecord
{
    protected static string $resource = ExamSessionResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
