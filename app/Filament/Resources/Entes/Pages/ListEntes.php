<?php

namespace App\Filament\Resources\Entes\Pages;

use App\Filament\Resources\Entes\EnteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEntes extends ListRecords
{
    protected static string $resource = EnteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
