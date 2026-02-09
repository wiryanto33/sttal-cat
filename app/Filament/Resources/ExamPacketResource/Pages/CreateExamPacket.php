<?php

namespace App\Filament\Resources\ExamPacketResource\Pages;

use App\Filament\Resources\ExamPacketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExamPacket extends CreateRecord
{
    protected static string $resource = ExamPacketResource::class;
    protected static bool $canCreateAnother = true;
    protected static ?string $title = 'Buat Paket Soal';

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
