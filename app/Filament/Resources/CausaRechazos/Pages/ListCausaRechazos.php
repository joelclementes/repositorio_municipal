<?php

namespace App\Filament\Resources\CausaRechazos\Pages;

use App\Filament\Resources\CausaRechazos\CausaRechazoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCausaRechazos extends ListRecords
{
    protected static string $resource = CausaRechazoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
