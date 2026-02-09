<?php

namespace App\Filament\Resources\LandingGalleryResource\Pages;

use App\Filament\Resources\LandingGalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingGallery extends EditRecord
{
    protected static string $resource = LandingGalleryResource::class;

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
