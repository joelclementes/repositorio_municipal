<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentosRecibido;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Periodo;

class DocumentoRegistroController extends Controller
{
    public function index()
    {
        return view('documento.registro');
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
        $fechaRecepcion = $periodo->fecha_inicio_dma . ' al ' . $periodo->fecha_fin_dma;

        $pdf = Pdf::loadView('pdf.acuse', [
            'data'            => $data,
            'numero_recibo'   => $numeroRecibo,
            'periodo'         => $periodoDescripcion,
            'nombre_ente'     => $nombreCompletoEnte,
            'fecha_recepcion' => $fechaRecepcion,
        ]);

        $nombre_archivo = 'acuse_' . $periodo->axomes . '_' . $now->format('Ymd_His') . '.pdf';

        return $pdf->download($nombre_archivo);
    }
}
