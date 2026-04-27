<?php

namespace App\Filament\Resources\Documentos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use app\Models\Documento;

class DocumentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('clave')
                    ->required(),
                TextInput::make('nombre')
                    ->required(),
                Select::make('subcategoria_id')
                    ->label('Subcategoria de documento')
                    ->relationship('subcategoria', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
                // TextInput::make('fecha_inicio')
                //     ->numeric()
                //     ->required(),
                // TextInput::make('fecha_limite')
                //     ->numeric()
                //     ->required(),
                Select::make('regla_presentacion')
                    ->label('Regla de presentación')
                    ->options(config('documentos.reglas_presentacion', []))
                    ->required()
                    ->default('dia_1_mes')
                    ->searchable()
                    ->preload(),
                Select::make('formato')
                    ->label('Formato del documento')
                    ->options([
                        'PDF' => 'PDF',
                        'XLSX' => 'XLSX',
                        'PDF, XLSX' => 'PDF y XLSX',
                    ])
                    ->searchable()
                    ->preload(),
            ]);
    }
}
