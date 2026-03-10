<?php

namespace App\Filament\Resources\CategoriasDocumentos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\CategoriasDocumento;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class CategoriasDocumentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('clave')
                    ->required(),
                TextInput::make('nombre')
                    ->required(),
                Select::make('roles_permitidos')
                ->label('Roles permitidos')
                ->options(function () {
                    $roles = DB::table('categorias_documentos')
                    ->whereNotNull('roles_permitidos')
                    ->where('roles_permitidos', '!=', '')
                    ->pluck('roles_permitidos')
                    ->flatMap(function ($roles) {
                        return [$roles];
                    })
                    ->unique()
                    ->sort()
                    ->values()
                    ->mapWithKeys(function ($role) {
                        return [$role => $role];
                    })
                    ->toArray();
                    return $roles;
                })
                ->searchable()
                ->preload(),
            ]);
    }

}
