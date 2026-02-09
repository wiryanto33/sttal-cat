<?php

namespace App\Filament\Resources\LandingHeroResource\Pages;

use App\Filament\Resources\LandingHeroResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLandingHero extends CreateRecord
{
    protected static string $resource = LandingHeroResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
