<?php

namespace App\Filament\Resources\SubcategoriasDocumentos\Pages;

use App\Filament\Resources\SubcategoriasDocumentos\SubcategoriasDocumentosResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubcategoriasDocumentos extends EditRecord
{
    protected static string $resource = SubcategoriasDocumentosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
