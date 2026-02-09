<?php

namespace App\Filament\Resources\LandingStatResource\Pages;

use App\Filament\Resources\LandingStatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingStat extends EditRecord
{
    protected static string $resource = LandingStatResource::class;

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
