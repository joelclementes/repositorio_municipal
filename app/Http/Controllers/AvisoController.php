<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use Illuminate\Http\Request;
use App\Models\Ente;

class AvisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('avisos.crear');
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
    public function show(Aviso $aviso)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aviso $aviso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aviso $aviso)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aviso $aviso)
    {
        //
    }

    public function buscarEnte(Request $request)
    {
        $search = $request->input('q');

        $entes = Ente::where('nombre', 'like', '%' . $search . '%')
            ->orderBy('nombre')
            ->get();

        return response()->json($entes);
    }
}
