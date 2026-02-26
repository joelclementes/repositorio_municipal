<?php

namespace App\Filament\Resources\CategoriasDocumentos\Pages;

use App\Filament\Resources\CategoriasDocumentos\CategoriasDocumentoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoriasDocumentos extends ListRecords
{
    protected static string $resource = CategoriasDocumentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
