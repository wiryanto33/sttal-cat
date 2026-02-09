<?php

namespace App\Filament\Resources\StrataResource\Pages;

use App\Filament\Resources\StrataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStratas extends ListRecords
{
    protected static string $resource = StrataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
