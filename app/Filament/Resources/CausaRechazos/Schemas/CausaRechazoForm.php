<?php

namespace App\Filament\Resources\CausaRechazos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CausaRechazoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('descripcion')
                    ->required(),
            ]);
    }
}
