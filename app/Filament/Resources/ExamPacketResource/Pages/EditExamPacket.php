<?php

namespace App\Filament\Resources\ExamPacketResource\Pages;

use App\Filament\Resources\ExamPacketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamPacket extends EditRecord
{
    protected static string $resource = ExamPacketResource::class;

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
