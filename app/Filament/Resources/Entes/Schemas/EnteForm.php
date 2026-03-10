<?php

namespace App\Filament\Resources\Entes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class EnteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Select::make('tipos_entes_id')
                    ->label('Tipo de ente')
                    ->relationship('tipoEnte', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
