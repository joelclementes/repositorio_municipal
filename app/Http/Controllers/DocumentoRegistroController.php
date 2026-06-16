<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentosRecibido;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Periodo;
use Illuminate\Support\Facades\Log;

class DocumentoRegistroController extends Controller
{
    public function index()
    {
        return view('documento.registro');
    }

    public function acuse(Request $request)
    {
        Log::info('--- INICIO DEL MÉTODO ACUSE ---');
        
        // Obtener periodo_id de la query o sesión
        $periodoId = $request->query('periodo_id') ?? session('periodo_acuse');

        // MODIFICACIÓN 1: Ver si llega el periodo_id
        if (!$periodoId) {
            dd(
                'ERROR EN PANTALLA: Tu petición llegó sin "periodo_id".',
                '¿Qué venía en la URL/Request?', $request->all(),
                '¿Qué hay guardado en la Sesión?', session()->all()
            );
        }

        $periodo = Periodo::find($periodoId);

        if (!$periodo) {
            dd(
                "ERROR EN PANTALLA: Sí llegó un periodo_id ({$periodoId}), pero no existe en la tabla de periodos.",
                "ID Buscado: " . $periodoId
            );
        }

        $enteId = auth()->user()->ente_id;
        $ente = auth()->user()->ente;

        // MODIFICACIÓN 3: Ver si el usuario tiene ente
        if (!$ente) {
            dd(
                "ERROR EN PANTALLA: El usuario autenticado no tiene un Ente asociado en la BD.",
                "Datos de tu usuario actual:", auth()->user()->toArray()
            );
        }

        // Construir nombre completo en mayúsculas: HONORABLE + TIPO + DE + NOMBRE
        $tipoEnte = $ente->tipoEnte;
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

            if ($archivos->isNotEmpty()) {
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

        $periodoDescripcion = $periodo->descripcion;

        // Fecha de recepción (rango)
        $fechaRecepcion = $periodo->fecha_inicio_dma . ' al ' . $periodo->fecha_fin_dma;

        try {
            $pdf = Pdf::loadView('pdf.acuse', [
                'data'            => $data,
                'numero_recibo'   => $numeroRecibo,
                'periodo'         => $periodoDescripcion,
                'nombre_ente'     => $nombreCompletoEnte,
                'fecha_recepcion' => $fechaRecepcion,
            ]);
            
            return $pdf->download('acuse_' . $now->format('Ymd_His') . '.pdf');
            
        } catch (\Exception $e) {
            // Si DomPDF falla, también lo mostramos en pantalla gigante
            dd("ERROR AL GENERAR EL PDF CON DOMPDF:", $e->getMessage(), $e->getLine());
        }
    }
}
