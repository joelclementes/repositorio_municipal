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
                // Select::make('rol_id')
                //     ->label('Rol')
                //     ->relationship('roles', 'name')   // << muestra nombres, guarda el id
                //     ->searchable()
                //     ->preload()
                //     ->required(),
                Select::make('rol_id')
                    ->label('Rol')
                    ->relationship(
                        name: 'roles', 
                        titleAttribute: 'name',
                        modifyQueryUsing: function ($query) {
                            $user = auth()->user();
                            // Si el usuario NO es SuperUsuario, ocultamos el rol SuperUsuario
                            if (!$user->hasRole('SuperUsuario')) {
                                $query->where('name', '!=', 'SuperUsuario');
                            }
                            return $query;
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, $fail) {
                                $user = auth()->user();
                                // Si el usuario no es SuperUsuario y trata de asignar el rol SuperUsuario
                                if (!$user->hasRole('SuperUsuario')) {
                                    $rolSuperUsuario = Role::where('name', 'SuperUsuario')->first();
                                    if ($rolSuperUsuario && $value == $rolSuperUsuario->id) {
                                        $fail('No tienes permiso para asignar el rol de SuperUsuario.');
                                    }
                                }
                            };
                        }
                    ]),
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
