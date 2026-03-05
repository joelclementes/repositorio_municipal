<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\AvisoEnte;
use App\Models\Ente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Services\ReglasDocumentoService;

class AvisoController extends Controller
{
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
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'tipo_visita_id' => 'required|string|in:Aviso,Invitación,Exhorto,Convocatoria,Circular',
            'texto' => 'required|string',
            'url' => 'nullable|url|max:255',
            'destinatarios' => 'required|in:todos,seleccionados',
            'entes_seleccionados' => 'required_if:destinatarios,seleccionados|array',
            'entes_seleccionados.*' => 'exists:entes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // 1. Crear el aviso
            $aviso = Aviso::create([
                'titulo' => $request->titulo,
                'tipo_aviso' => $request->tipo_visita_id, // Cambié el nombre del campo
                'texto' => $request->texto,
                'url' => $request->url,
                'activo' => true,
                'fecha_publicacion' => now(),
                'creado_por' => auth()->id(),
                // 'archivo' => null, // Si manejaras archivos
                // 'fecha_expiracion' => $request->fecha_expiracion, // Si tuvieras este campo
            ]);

            // 2. Determinar los entes destinatarios
            if ($request->destinatarios === 'todos') {
                // Caso: Enviar a TODOS los entes
                $entes = Ente::all();

                foreach ($entes as $ente) {
                    AvisoEnte::create([
                        'aviso_id' => $aviso->id,
                        'ente_id' => $ente->id,
                        'estado_envio' => 'pendiente',
                        'fecha_envio' => null,
                        'enviado_por' => auth()->id(),
                    ]);
                }

                $mensaje = "Aviso creado y enviado a TODOS los entes ({$entes->count()} entes)";
            } else {
                // Caso: Enviar a entes SELECCIONADOS
                $entesIds = $request->entes_seleccionados;

                foreach ($entesIds as $enteId) {
                    AvisoEnte::create([
                        'aviso_id' => $aviso->id,
                        'ente_id' => $enteId,
                        'estado_envio' => 'pendiente',
                        'fecha_envio' => null,
                        'enviado_por' => null,
                    ]);
                }

                $mensaje = "Aviso creado y enviado a " . count($entesIds) . " ente(s) seleccionado(s)";
            }

            DB::commit();

            return redirect()->route('avisos.create')
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error al crear el aviso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Buscar entes para el autocomplete
     */
    public function buscarEnte(Request $request)
    {
        $search = $request->input('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $entes = Ente::with('tipoEnte')
            ->where('nombre', 'like', '%' . $search . '%')
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(function ($ente) {
                return [
                    'id' => $ente->id,
                    'nombre' => $ente->nombre,
                    'tipo_ente_nombre' => $ente->tipoEnte ? $ente->tipoEnte->nombre : 'Sin tipo',
                ];
            });

        return response()->json($entes);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        // Ejemplo de uso del servicio de reglas de negocio para documentos
        // $reglasDocumentoService = new ReglasDocumentoService();
        // $oportunidad = $reglasDocumentoService->oportunidad('mensual',5);
        // dd($oportunidad);

        $avisos = Aviso::where('creado_por', auth()->id())->get();
        return view('avisos.index', compact('avisos'));
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
}
