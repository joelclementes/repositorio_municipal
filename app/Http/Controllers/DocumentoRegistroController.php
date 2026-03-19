<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentosRecibido;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Periodo;

class DocumentoRegistroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('documento.registro');
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

    public function acuse(Request $request)
    {
        // Obtener periodo_id de la query o sesión
        $periodoId = $request->query('periodo_id') ?? session('periodo_acuse');

        if (!$periodoId) {
            return redirect()->route('documento.registro.index')
                ->withErrors(['periodo' => 'Debes seleccionar un período primero.']);
        }

        $periodo = Periodo::find($periodoId);
        if (!$periodo) {
            return redirect()->route('documento.registro.index')
                ->withErrors(['periodo' => 'El período seleccionado no es válido.']);
        }

        $enteId = auth()->user()->ente_id;
        $ente = auth()->user()->ente;

        if (!$ente) {
            return redirect()->route('documento.registro.index')
                ->withErrors(['ente' => 'El usuario no tiene un ente asociado.']);
        }

        // Obtener el tipo de ente
        $tipoEnte = $ente->tipoEnte;

        // Construir nombre completo en mayúsculas: HONORABLE + TIPO + DE + NOMBRE
        $tipo = $tipoEnte ? strtoupper($tipoEnte->nombre) : '';
        $nombreEnte = strtoupper($ente->nombre);
        
        // Si hay tipo, se incluye; si no, solo "HONORABLE DE NOMBRE"
        $nombreCompletoEnte = $tipo 
            ? "HONORABLE {$tipo} DE {$nombreEnte}"
            : "HONORABLE DE {$nombreEnte}";

        // Obtener documentos recibidos
        $documentosRecibidos = DocumentosRecibido::with(['documento', 'archivos'])
                                ->where('ente_id', $enteId)
                                ->where('periodo_id', $periodoId)
                                ->get();

        $data = [];

        foreach ($documentosRecibidos as $docRecibido) {
            $documento = $docRecibido->documento;
            if (!$documento) continue;

            $archivos = $docRecibido->archivos;

            if ($archivos->isEmpty()) {
                $data[] = [
                    'documento_validado' => $documento->clave . ' ' . $documento->nombre,
                    'tipo_archivo'       => '-',
                    'fecha'              => '-',
                    'hora'               => '-',
                ];
            } else {
                foreach ($archivos as $archivo) {
                    $data[] = [
                        'documento_validado' => $documento->clave . ' ' . $documento->nombre,
                        'tipo_archivo'       => $archivo->tipo_recepcion,
                        'fecha'              => $archivo->created_at->format('d/m/Y'),
                        'hora'               => $archivo->created_at->format('H:i:s'),
                    ];
                }
            }
        }

        if (empty($data)) {
            $data[] = [
                'documento_validado' => 'No hay documentos para el período seleccionado.',
                'tipo_archivo' => '',
                'fecha' => '',
                'hora' => ''
            ];
        }

        // Número de recibo: YmdHis + milisegundos (3 dígitos)
        $now = now();
        $numeroRecibo = $now->format('YmdHis') . sprintf('%03d', $now->milli);

        // Descripción del período
        $periodoDescripcion = $periodo->descripcion;

        // Fecha de recepción (rango)
        $fechaRecepcion = $periodo->fecha_inicio . ' al ' . $periodo->fecha_fin;

        $pdf = Pdf::loadView('pdf.acuse', [
            'data'            => $data,
            'numero_recibo'   => $numeroRecibo,
            'periodo'         => $periodoDescripcion,
            'nombre_ente'     => $nombreCompletoEnte,
            'fecha_recepcion' => $fechaRecepcion,
        ]);

        return $pdf->download('acuse_' . $now->format('Ymd_His') . '.pdf');
    }

}