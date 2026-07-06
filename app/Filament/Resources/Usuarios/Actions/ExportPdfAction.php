<?php

namespace App\Filament\Resources\Usuarios\Actions;

use App\Models\Ente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExportPdfAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'export_pdf';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Exportar PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function () {
                Log::info('=== INICIO EXPORTACIÓN PDF ===');
                
                // Obtener el request
                $request = request();
                
                // Obtener el snapshot del request
                $snapshotData = $request->input('components.0.snapshot', '');
                
                $filtrosAplicados = [];
                $busquedaAplicada = '';
                
                // Decodificar el snapshot si es string
                if (is_string($snapshotData) && !empty($snapshotData)) {
                    try {
                        $decodedSnapshot = json_decode($snapshotData, true);
                        
                        // Buscar filtros en tableDeferredFilters
                        if (isset($decodedSnapshot['data']['tableDeferredFilters'])) {
                            $rawFilters = $decodedSnapshot['data']['tableDeferredFilters'];
                            
                            // Extraer filtros de la estructura compleja
                            $filtrosAplicados = $this->extractFiltersFromLivewireArray($rawFilters);
                            Log::info('FILTROS EXTRAÍDOS:', $filtrosAplicados);
                        }
                        
                        // Buscar búsqueda
                        if (isset($decodedSnapshot['data']['tableSearch'])) {
                            $busquedaAplicada = $decodedSnapshot['data']['tableSearch'];
                        }
                    } catch (\Exception $e) {
                        Log::error('Error decodificando snapshot:', ['error' => $e->getMessage()]);
                    }
                }
                
                // Si no se encontraron filtros, intentar con el request directo
                if (empty($filtrosAplicados)) {
                    $tableFilters = $request->input('tableFilters', []);
                    if (!empty($tableFilters)) {
                        $filtrosAplicados = $this->extractFiltersFromLivewireArray($tableFilters);
                    }
                }
                
                Log::info('FILTROS FINALES A APLICAR:', $filtrosAplicados);
                
                // === CONSTRUIR LA QUERY ===
                $query = User::query();
                
                // Aplicar la lógica de exclusión de SuperUsuario
                $user = auth()->user();
                if ($user && $user->hasRole('Administrador')) {
                    $query->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'SuperUsuario');
                    });
                }
                
                // === APLICAR FILTROS ===
                if (!empty($filtrosAplicados)) {
                    Log::info('APLICANDO FILTROS:', $filtrosAplicados);
                    
                    // Filtro de nombre
                    if (isset($filtrosAplicados['name']) && !empty($filtrosAplicados['name'])) {
                        $query->where('name', $filtrosAplicados['name']);
                        Log::info('Filtro nombre aplicado:', ['nombre' => $filtrosAplicados['name']]);
                    }
                    
                    // Filtro de rol
                    if (isset($filtrosAplicados['role']) && !empty($filtrosAplicados['role'])) {
                        $query->whereHas('roles', function ($q) use ($filtrosAplicados) {
                            $q->where('name', $filtrosAplicados['role']);
                        });
                        Log::info('Filtro rol aplicado:', ['rol' => $filtrosAplicados['role']]);
                    }
                    
                    // Filtro de estado - CORREGIDO
                    if (isset($filtrosAplicados['is_active'])) {
                        $state = $filtrosAplicados['is_active'];
                        
                        // Convertir a booleano independientemente del tipo
                        $boolState = null;
                        
                        // Si es string
                        if (is_string($state)) {
                            if ($state === '1' || $state === 'true' || strtolower($state) === 'activo') {
                                $boolState = true;
                            } elseif ($state === '0' || $state === 'false' || strtolower($state) === 'inactivo') {
                                $boolState = false;
                            }
                        } 
                        // Si es entero
                        elseif (is_int($state) || is_float($state)) {
                            $boolState = (bool) $state;
                        } 
                        // Si es booleano
                        elseif (is_bool($state)) {
                            $boolState = $state;
                        }
                        
                        // Aplicar el filtro solo si tenemos un valor booleano válido
                        if ($boolState !== null) {
                            $query->where('is_active', $boolState);
                            Log::info('Filtro estado aplicado:', ['estado' => $boolState ? 'Activo' : 'Inactivo', 'valor_original' => $state]);
                        } else {
                            Log::info('Estado no reconocido:', ['valor' => $state]);
                        }
                    }
                    
                    // Filtro de ente
                    if (isset($filtrosAplicados['ente_id']) && !empty($filtrosAplicados['ente_id'])) {
                        $query->where('ente_id', $filtrosAplicados['ente_id']);
                        Log::info('Filtro ente aplicado:', ['ente_id' => $filtrosAplicados['ente_id']]);
                    }
                }
                
                // Búsqueda
                if (!empty($busquedaAplicada)) {
                    $query->where('name', 'like', '%' . $busquedaAplicada . '%');
                    Log::info('Búsqueda aplicada:', ['search' => $busquedaAplicada]);
                }
                
                // === EJECUTAR QUERY ===
                $users = $query->get();
                Log::info('TOTAL USUARIOS ENCONTRADOS:', ['total' => $users->count()]);
                
                // Preparar filtros para mostrar
                $displayFilters = $this->prepararFiltrosParaMostrar($filtrosAplicados, $busquedaAplicada);
                Log::info('FILTROS PARA MOSTRAR EN PDF:', $displayFilters);
                
                // Generar nombre del archivo con los filtros
                $nombreArchivo = $this->generarNombreArchivo($displayFilters);
                
                return $this->generarPdf($users, $displayFilters, $nombreArchivo);
            })
            ->modalHeading('Exportar a PDF')
            ->modalDescription('¿Deseas exportar todos los usuarios visibles actualmente?')
            ->modalSubmitActionLabel('Exportar')
            ->closeModalByClickingAway(false);
    }

    /**
     * Genera el nombre del archivo basado en los filtros aplicados
     */
    private function generarNombreArchivo(array $filters): string
    {
        $parts = ['usuarios'];
        
        // Agregar filtros al nombre
        foreach ($filters as $key => $value) {
            // Limpiar el valor para usarlo en el nombre del archivo
            $cleanValue = Str::slug($value, '_');
            $cleanKey = Str::slug($key, '_');
            $parts[] = $cleanKey . '_' . $cleanValue;
        }
        
        // Agregar fecha y hora
        $parts[] = now()->format('Y-m-d_H-i-s');
        
        // Unir todas las partes con guiones
        $nombre = implode('_', $parts);
        
        // Limitar longitud del nombre (opcional)
        if (strlen($nombre) > 200) {
            $nombre = substr($nombre, 0, 200);
        }
        
        return $nombre . '.pdf';
    }

    /**
     * Extrae los valores reales de los filtros del array de Livewire
     */
    private function extractFiltersFromLivewireArray($rawFilters): array
    {
        $extracted = [];
        
        if (is_array($rawFilters) && !empty($rawFilters)) {
            // Si el array tiene un solo elemento con la estructura de Livewire
            if (isset($rawFilters[0]) && is_array($rawFilters[0])) {
                $filterData = $rawFilters[0];
            } else {
                $filterData = $rawFilters;
            }
            
            Log::info('FILTER DATA A PROCESAR:', $filterData);
            
            // Extraer cada filtro
            foreach (['name', 'role', 'is_active', 'ente_id'] as $filterKey) {
                if (isset($filterData[$filterKey])) {
                    $filterValue = null;
                    $filterItem = $filterData[$filterKey];
                    
                    // Si es un array, buscar el valor
                    if (is_array($filterItem)) {
                        // Buscar en diferentes estructuras
                        if (isset($filterItem['value'])) {
                            $filterValue = $filterItem['value'];
                        } elseif (isset($filterItem[0]['value'])) {
                            $filterValue = $filterItem[0]['value'];
                        } elseif (isset($filterItem[0]) && !is_array($filterItem[0])) {
                            $filterValue = $filterItem[0];
                        } elseif (isset($filterItem['value'][0]['value'])) {
                            // Estructura anidada más compleja
                            $filterValue = $filterItem['value'][0]['value'] ?? null;
                        }
                    } else {
                        // Si no es array, usarlo directamente
                        $filterValue = $filterItem;
                    }
                    
                    // Si encontramos un valor y no es null, lo guardamos
                    if ($filterValue !== null && $filterValue !== '') {
                        $extracted[$filterKey] = $filterValue;
                        Log::info("Valor extraído para {$filterKey}:", ['value' => $filterValue]);
                    }
                }
            }
        }
        
        return $extracted;
    }

    private function prepararFiltrosParaMostrar(array $filters, string $search): array
    {
        $display = [];
        
        if (isset($filters['name']) && !empty($filters['name'])) {
            $display['Nombre'] = $filters['name'];
        }
        
        if (isset($filters['role']) && !empty($filters['role'])) {
            $display['Rol'] = $filters['role'];
        }
        
        // CORREGIDO: Mostrar correctamente el estado
        if (isset($filters['is_active'])) {
            $state = $filters['is_active'];
            
            // Convertir a string para mostrar
            if (is_string($state)) {
                if ($state === '1' || $state === 'true' || strtolower($state) === 'activo') {
                    $display['Estado'] = 'Activos';
                } elseif ($state === '0' || $state === 'false' || strtolower($state) === 'inactivo') {
                    $display['Estado'] = 'Inactivos';
                } else {
                    $display['Estado'] = 'Todos';
                }
            } elseif (is_int($state) || is_float($state)) {
                // Si es número, convertirlo a booleano
                $display['Estado'] = (bool) $state ? 'Activos' : 'Inactivos';
            } elseif (is_bool($state)) {
                $display['Estado'] = $state ? 'Activos' : 'Inactivos';
            } else {
                $display['Estado'] = 'Todos';
            }
        }
        
        if (isset($filters['ente_id']) && !empty($filters['ente_id'])) {
            $enteId = $filters['ente_id'];
            $ente = Ente::where('id', $enteId)->first();
            $display['Ente'] = $ente ? $ente->nombre : $enteId;
        }
        
        if (!empty($search)) {
            $display['Búsqueda'] = $search;
        }
        
        return $display;
    }

    private function generarPdf(Collection $users, array $filters = [], string $nombreArchivo = 'usuarios.pdf')
    {
        $pdf = Pdf::loadView('pdf.usuarios', [
            'users' => $users,
            'filters' => $filters,
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'total' => $users->count(),
            'user' => auth()->user()->name
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
        ]);
        
        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            },
            $nombreArchivo
        );
    }

    public static function make(string $name = null): static
    {
        return parent::make($name ?? static::getDefaultName());
    }
}