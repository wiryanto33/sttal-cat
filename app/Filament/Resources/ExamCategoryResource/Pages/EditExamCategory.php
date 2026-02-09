<?php

namespace App\Filament\Resources\ExamCategoryResource\Pages;

use App\Filament\Resources\ExamCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamCategory extends EditRecord
{
    protected static string $resource = ExamCategoryResource::class;

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
