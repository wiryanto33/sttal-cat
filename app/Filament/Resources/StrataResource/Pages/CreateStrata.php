<?php

namespace App\Filament\Resources\StrataResource\Pages;

use App\Filament\Resources\StrataResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStrata extends CreateRecord
{
    protected static string $resource = StrataResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
