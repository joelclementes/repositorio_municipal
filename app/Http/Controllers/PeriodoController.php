<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $periodos = Periodo::all();
        return view('periodos.registro', compact('periodos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
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
        $validatedData = $request->validate([
            'mes' => 'required|string|max:125',
            'anio' => 'required|integer',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'nullable|boolean',
        ]);

        $periodo = new Periodo();
        $periodo->mes = $validatedData['mes'];
        $periodo->axo = $validatedData['anio'];
        $periodo->descripcion = $validatedData['descripcion'];
        $periodo->fecha_inicio = $validatedData['fecha_inicio'];
        $periodo->fecha_fin = $validatedData['fecha_fin'];
        $periodo->is_active = (bool)($validatedData['activo'] ?? false);
        $periodo->save();

        return redirect()->route('periodos.registro.index')->with('success', 'Período registrado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus($id)
    {
        $periodo = Periodo::findOrFail($id);
        $periodo->is_active = !$periodo->is_active;
        $periodo->save();

        return response()->json([
            'success' => true,
            'status' => $periodo->is_active,
            'message' => 'Estado actualizado correctamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Periodo $periodo)
    {
        //
    }
}
