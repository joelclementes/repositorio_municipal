<?php

namespace App\Filament\Resources\Usuarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsuariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                
                // Si el usuario autenticado tiene rol de Administrador
                if ($user && $user->hasRole('Administrador')) {
                    // Excluir usuarios que tengan rol de SuperUsuario
                    $query->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'SuperUsuario');
                    });
                }
                
                // Los SuperUsuarios pueden ver todos los usuarios
                // Los Administradores no ven a los SuperUsuarios
                
                return $query;
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Rol')
                    ->searchable()
                    ->badge() // Opcional: muestra el rol como un badge
                    ->color(fn (string $state): string => match ($state) {
                        'SuperUsuario' => 'danger',
                        'Administrador' => 'warning',
                        default => 'success',
                    }),
                CheckboxColumn::make('is_active')
                    ->label('Activo')
                    ->sortable(),
            ])
            ->filters([
                // Puedes agregar un filtro adicional para roles
                // pero ten cuidado de no exponer SuperUsuarios a Administradores
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(function ($record) {
                        // Evitar que Administradores editen SuperUsuarios
                        $user = auth()->user();
                        if ($user && $user->hasRole('Administrador')) {
                            return !$record->hasRole('SuperUsuario');
                        }
                        return true;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(function () {
                            // Solo SuperUsuarios pueden eliminar usuarios
                            $user = auth()->user();
                            return $user && $user->hasRole('SuperUsuario');
                        }),
                ]),
            ]);
    }
}