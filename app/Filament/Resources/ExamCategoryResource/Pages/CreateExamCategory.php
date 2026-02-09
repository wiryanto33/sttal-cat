<?php

namespace App\Filament\Resources\ExamCategoryResource\Pages;

use App\Filament\Resources\ExamCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExamCategory extends CreateRecord
{
    protected static string $resource = ExamCategoryResource::class;
    protected static bool $canCreateAnother = true;
    protected static ?string $title = 'Buat Categori';
    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
