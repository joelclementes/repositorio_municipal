<?php

namespace App\Filament\Resources\Documentos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

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
                    ->options([
                        'trimestral_ene_abr_jul_oct' => 'Trimestral (Ene/Abr/Jul/Oct)',
                        'dia_1_mes' => 'Día 1 de cada mes',
                        'dias_16_25_mes' => 'Días 16 al 25 de cada mes',
                        'enero_abril' => 'Enero a abril de cada año',
                        'septiembre_15_30' => '15 al 30 de septiembre',
                        'enero_1_a_marzo_31' => '1 de enero al 31 de marzo',
                        'enero_1_31' => '1 al 31 de enero',
                        'marzo_1_31' => '1 al 31 de marzo',
                        'abril_1_30' => '1 al 30 de abril',
                        'todo_el_anio' => 'Abierto',
                    ])
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
