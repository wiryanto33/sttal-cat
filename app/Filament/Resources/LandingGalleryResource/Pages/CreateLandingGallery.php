<?php

namespace App\Filament\Resources\LandingGalleryResource\Pages;

use App\Filament\Resources\LandingGalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLandingGallery extends CreateRecord
{
    protected static string $resource = LandingGalleryResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
