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
        // Intentar obtener el periodo_id de la query, si no, de la sesión
        $periodoId = $request->query('periodo_id') ?? session('periodo_acuse');

        if (!$periodoId) {
            return redirect()->route('documento.registro.index')
                ->withErrors(['periodo' => 'Debes seleccionar un período primero.']);
        }

        // Validar que el período exista en la base de datos
        if (!Periodo::where('id', $periodoId)->exists()) {
            return redirect()->route('documento.registro.index')
                ->withErrors(['periodo' => 'El período seleccionado no es válido.']);
        }

        $enteId = auth()->user()->ente_id;

        // Obtener documentos recibidos (igual que antes)
        $documentosRecibidos = DocumentosRecibido::with(['documento', 'archivos'])
                                ->where('ente_id', $enteId)
                                ->where('periodo_id', $periodoId)
                                ->get();

        // Preparar datos para el PDF...
        $data = [];
        foreach ($documentosRecibidos as $docRecibido) {
            $documento = $docRecibido->documento;
            if (!$documento) continue;
            foreach ($docRecibido->archivos as $archivo) {
                $data[] = [
                    'documento_validado' => $documento->clave . ' ' . $documento->nombre,
                    'tipo_archivo'       => $archivo->tipo_recepcion,
                    'fecha'              => $archivo->created_at->format('d/m/Y'),
                    'hora'               => $archivo->created_at->format('H:i:s'),
                ];
            }
        }

        if (empty($data)) {
            $data[] = [
                'documento_validado' => 'No se encontraron archivos para el período seleccionado.',
                'tipo_archivo' => '',
                'fecha' => '',
                'hora' => ''
            ];
        }

        $pdf = Pdf::loadView('pdf.acuse', compact('data'));
        return $pdf->download('acuse_' . now()->format('Ymd_His') . '.pdf');
    }
}