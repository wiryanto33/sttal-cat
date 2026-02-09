<?php

namespace App\Filament\Resources\LandingHeroResource\Pages;

use App\Filament\Resources\LandingHeroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandingHeroes extends ListRecords
{
    protected static string $resource = LandingHeroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
