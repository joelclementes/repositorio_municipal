<?php

namespace App\Filament\Resources\SubcategoriasDocumentos\Pages;

use App\Filament\Resources\SubcategoriasDocumentos\SubcategoriasDocumentosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubcategoriasDocumentos extends ListRecords
{
    protected static string $resource = SubcategoriasDocumentosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
