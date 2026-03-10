<?php

namespace App\Filament\Resources\Usuarios\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Validation\Rules\Password;
use Filament\Schemas\Schema;

class UsuariosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Usuario')
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->rule(Password::default())
                    ->same('password_confirmation')
                    ->maxLength(255)
                    ->revealable(),
                TextInput::make('password_confirmation')
                    ->label('Confirmar Contraseña')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->revealable()
                    ->dehydrated(false),
                Select::make('rol_id')
                    ->label('Rol')
                    ->relationship('roles', 'name')   // << muestra nombres, guarda el id
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('ente_id')
                    ->label('Ente')
                    ->relationship('ente', 'nombre') 
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->placeholder('Seleccione un ente (opcional)'),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }
}
