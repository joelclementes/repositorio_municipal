<?php

namespace App\Filament\Resources\SubcategoriasDocumentos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class SubcategoriasDocumentosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('clave')
                    ->required(),
                TextInput::make('nombre')
                    ->required(),
                Select ::make('categoria_documento_id')
                    ->label('Subcategoria de documento')
                    ->relationship('categoria', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(), 
            ]);
    }
}
