<?php
// app/Livewire/Documentos/Registro.php

namespace App\Livewire\Documentos;

use App\Models\CategoriasDocumento;
use App\Models\SubcategoriasDocumento;
use App\Models\Documento;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Registro extends Component
{
    public $categoriaSeleccionada = '';
    public $subcategoriaSeleccionada = '';

    #[Computed]
    public function categorias()
    {
        $rolesUsuario = auth()->user()->roles->pluck('name')->toArray();
        
        $categorias = CategoriasDocumento::where(function ($query) use ($rolesUsuario) {
            foreach ($rolesUsuario as $rol) {
                $query->orWhereRaw("FIND_IN_SET(?, roles_permitidos)", [$rol]);
            }
        })->get();
        
        return $categorias;
    }

    #[Computed]
    public function subcategorias()
    {
        if (!$this->categoriaSeleccionada) {
            return collect();
        }
        
        return SubcategoriasDocumento::where('categoria_id', $this->categoriaSeleccionada)->get();
    }

    #[Computed]
    public function documentos()
    {
        if (!$this->subcategoriaSeleccionada) {
            return collect();
        }
        
        return Documento::where('subcategoria_id', $this->subcategoriaSeleccionada)
            ->orderBy('clave')
            ->get();
    }

    public function updatedCategoriaSeleccionada()
    {
        $this->subcategoriaSeleccionada = '';
    }

    public function render()
    {
        return view('livewire.documentos.registro');
    }
}