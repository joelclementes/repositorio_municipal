<?php

namespace App\Filament\Resources\CategoriasDocumentos\Pages;

use App\Filament\Resources\CategoriasDocumentos\CategoriasDocumentoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoriasDocumento extends EditRecord
{
    protected static string $resource = CategoriasDocumentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
