<?php

namespace App\Filament\Resources\CausaRechazos\Pages;

use App\Filament\Resources\CausaRechazos\CausaRechazoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCausaRechazo extends EditRecord
{
    protected static string $resource = CausaRechazoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
