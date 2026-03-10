<?php

namespace App\Filament\Resources\Entes\Pages;

use App\Filament\Resources\Entes\EnteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEnte extends EditRecord
{
    protected static string $resource = EnteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
