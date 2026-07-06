<?php

namespace App\Filament\Resources\Usuarios\Tables;

use App\Filament\Resources\Usuarios\Actions\ExportPdfAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Ente;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsuariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
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
                TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->sortable()
                    ->action(function ($record) {
                        $record->is_active = !$record->is_active;
                        $record->save();
                    })
                    ->extraAttributes([
                        'style' => 'cursor: pointer;',
                    ]),
            ])
            ->filters([
                // Puedes agregar un filtro adicional para roles
                // pero ten cuidado de no exponer SuperUsuarios a Administradores
                SelectFilter::make('name')
                    ->label('Nombre')
                    ->options(function () {
                        return User::orderBy('name')
                            ->pluck('name', 'name')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccionar o buscar nombre'),

                SelectFilter::make('role')
                    ->label('Rol')
                    ->options(function () {
                        $user = auth()->user();
                        $roles = Role::query()
                            ->orderBy('name')
                            ->pluck('name', 'name')
                            ->toArray();
                        
                        if (!$user->hasRole('SuperUsuario')) {
                            unset($roles['SuperUsuario']);
                        }
                        
                        return $roles;
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccionar rol')
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('roles', function ($q) use ($data) {
                                $q->where('name', $data['value']);
                            });
                        }
                        return $query;
                    }),

                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos los estados')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                        blank: fn (Builder $query) => $query, 
                    ),

                SelectFilter::make('ente_id')
                    ->label('Ente')
                    ->options(function () {
                        return Ente::orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccionar ente (opcional)')
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->where('ente_id', $data['value']);
                        }
                        return $query;
                    }),
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
                ExportPdfAction::make()
                    ->visible(function () {
                        // Opcional: controlar quién puede exportar
                        return auth()->user()->hasAnyRole(['SuperUsuario', 'Administrador']);
                    }),
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