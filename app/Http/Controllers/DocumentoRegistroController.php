<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriasDocumento;
use App\Models\SubCategoriasDocumento;
use App\Models\Documento;

class DocumentoRegistroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // obtener $categorias que contengan en el campo roles_premitidos el rol del usuario autenticado
        $rolesUsuario = auth()->user()->roles->pluck('name')->toArray();

        $categorias = CategoriasDocumento::where(function ($query) use ($rolesUsuario) {
            foreach ($rolesUsuario as $rol) {
                $query->orWhereRaw("FIND_IN_SET(?, roles_permitidos)", [$rol]);
            }
        })->get();
        // $categorias = CategoriasDocumento::all();


        // obtener $subcategorias que contengan en el campo categoria_id el id de la categoria seleccionada
        $subcategorias = SubCategoriasDocumento::whereIn('categoria_id', $categorias->pluck('id'))->get();
        // $subcategorias = SubCategoriasDocumento::all();

        // obtener $documentos que contengan en el campo subcategoria_id el id de la subcategoria seleccionada
        $documentos = Documento::whereIn('subcategoria_id', $subcategorias->pluck('id'))->get();

        // $documentos = Documento::all();

        return view('documento.registro', compact('categorias', 'subcategorias', 'documentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
