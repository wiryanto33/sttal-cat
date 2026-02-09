<?php

namespace App\Filament\Resources\LandingGalleryResource\Pages;

use App\Filament\Resources\LandingGalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandingGalleries extends ListRecords
{
    protected static string $resource = LandingGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
