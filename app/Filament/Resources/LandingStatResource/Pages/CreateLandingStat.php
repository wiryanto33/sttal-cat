<?php

namespace App\Filament\Resources\LandingStatResource\Pages;

use App\Filament\Resources\LandingStatResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLandingStat extends CreateRecord
{
    protected static string $resource = LandingStatResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
