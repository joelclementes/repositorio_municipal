<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ente;
use App\Models\EnteRevisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnteRevisorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('asignacion_revisores.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Remove asignaciones de un revisor
     */
    public function destroy($id)
    {
        // Este método podría ser para eliminar una asignación específica
        // o podrías implementar un método para limpiar todas las asignaciones de un revisor
    }
}