<?php

namespace App\Filament\Resources\StrataResource\Pages;

use App\Filament\Resources\StrataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStrata extends EditRecord
{
    protected static string $resource = StrataResource::class;

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
