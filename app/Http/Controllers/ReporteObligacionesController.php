<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ente;
use App\Models\Periodo;
use App\Models\CategoriasDocumento;
use App\Models\Documento;
use App\Models\DocumentosRecibido;
use App\Models\ArchivoDocumentoRecibido;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ReporteObligacionesController extends Controller
{
    /**
     * Genera los datos del reporte para un ente y año dados.
     * Acepta filtros opcionales de categorías, subcategorías y documentos.
     *
     * @param array $categoriaIds    IDs de categorías a incluir (vacío = todas)
     * @param array $subcategoriaIds IDs de subcategorías a incluir (vacío = todas)
     * @param array $documentoIds    IDs de documentos a incluir (vacío = todos)
     */
    private function generarDatosReporte(
        int $enteId,
        int $axo,
        array $categoriaIds    = [],
        array $subcategoriaIds = [],
        array $documentoIds    = []
    ): ?array {
        $ente    = Ente::find($enteId);
        $periodos = Periodo::where('axo', $axo)->orderBy('mes_numero')->get();

        if (!$ente || $periodos->isEmpty()) {
            return null;
        }

        $periodosPorMes = $periodos->keyBy('mes_numero');

        // --- Categorías (con filtro opcional) ---
        $categoriasQuery = CategoriasDocumento::with([
            'subcategorias' => function ($query) {
                $query->orderBy('id');
            }
        ])->orderBy('id');

        if (!empty($categoriaIds)) {
            $categoriasQuery->whereIn('id', $categoriaIds);
        }

        $categorias = $categoriasQuery->get();
        $resultado  = [];

        foreach ($categorias as $categoria) {
            $subcategoriasData = [];
            $subcategorias     = $categoria->subcategorias;

            // Filtro de subcategorías
            if (!empty($subcategoriaIds)) {
                $subcategorias = $subcategorias->whereIn('id', $subcategoriaIds);
            }

            foreach ($subcategorias as $subcategoria) {
                // Filtro de documentos
                $documentosQuery = Documento::where('subcategoria_id', $subcategoria->id)
                    ->orderBy('id');

                if (!empty($documentoIds)) {
                    $documentosQuery->whereIn('id', $documentoIds);
                }

                $documentos = $documentosQuery->get();

                if ($documentos->isEmpty()) continue;

                $tipoPeriodo    = $this->getTipoPeriodoSubcategoria($documentos);
                $documentosData = [];

                foreach ($documentos as $documento) {
                    $meses = [];

                    if ($tipoPeriodo === 'trimestral') {
                        $trimestres = [1 => [1], 2 => [4], 3 => [7], 4 => [10]];
                        foreach ($trimestres as $numTrim => $mesesTrim) {
                            $mesRef    = $mesesTrim[0];
                            $periodoId = $periodosPorMes->get($mesRef)?->id;
                            $meses[$numTrim] = $this->calcularEstadoDocumento($ente->id, $documento->id, $periodoId, $documento, $mesRef);
                        }
                    } else {
                        for ($mes = 1; $mes <= 12; $mes++) {
                            $periodoId = $periodosPorMes->get($mes)?->id;
                            $meses[$mes] = $this->calcularEstadoDocumento($ente->id, $documento->id, $periodoId, $documento, $mes);
                        }
                    }

                    $observaciones = $this->obtenerObservaciones($ente->id, $documento->id, $periodos);

                    $documentosData[] = [
                        'id'            => $documento->id,
                        'clave'         => $documento->clave,
                        'nombre'        => $documento->nombre,
                        'regla'         => $documento->regla_presentacion,
                        'meses'         => $meses,
                        'observaciones' => $observaciones,
                    ];
                }

                if (!empty($documentosData)) {
                    $subcategoriasData[] = [
                        'id'           => $subcategoria->id,
                        'nombre'       => $subcategoria->nombre,
                        'tipo_periodo' => $tipoPeriodo,
                        'documentos'   => $documentosData,
                    ];
                }
            }

            if (!empty($subcategoriasData)) {
                $resultado[] = [
                    'id'           => $categoria->id,
                    'nombre'       => $categoria->nombre,
                    'clave'        => $categoria->clave,
                    'subcategorias'=> $subcategoriasData,
                ];
            }
        }

        return [
            'ente'       => $ente,
            'axo'        => $axo,
            'categorias' => $resultado,
        ];
    }

    private function documentoAplicaEnMes(Documento $documento, int $mesNumero): bool
    {
        $regla = $documento->regla_presentacion ?? 'todo_el_anio';

        return match ($regla) {
            'todo_el_anio' => true,
            'trimestral_ene_abr_jul_oct' => in_array($mesNumero, [1, 4, 7, 10]),
            'dia_1_mes' => true,
            'dias_16_25_mes' => true,
            'enero_abril' => $mesNumero >= 1 && $mesNumero <= 4,
            'septiembre_15_30' => $mesNumero === 9,
            'enero_1_a_marzo_31' => $mesNumero >= 1 && $mesNumero <= 3,
            'enero_1_31' => $mesNumero === 1,
            'marzo_1_31' => $mesNumero === 3,
            'abril_1_30' => $mesNumero === 4,
            default => true,
        };
    }

    private function getTipoPeriodoSubcategoria($documentos): string
    {
        $reglas = $documentos->pluck('regla_presentacion')->unique();
        if ($reglas->count() === 1 && $reglas->first() === 'trimestral_ene_abr_jul_oct') {
            return 'trimestral';
        }
        return 'mensual';
    }

    private function calcularEstadoDocumento(int $enteId, int $documentoId, ?int $periodoId, Documento $documento, int $mesNumero): array
    {
        if (!$this->documentoAplicaEnMes($documento, $mesNumero)) {
            return ['estado' => '', 'clase' => 'no-aplica'];
        }

        if (!$periodoId) {
            return ['estado' => '', 'clase' => 'no-aplica'];
        }

        $documentoRecibido = DocumentosRecibido::where('ente_id', $enteId)
            ->where('documento_id', $documentoId)
            ->where('periodo_id', $periodoId)
            ->first();

        if (!$documentoRecibido) {
            return ['estado' => 'NP', 'clase' => 'no-presentado'];
        }

        $archivos = ArchivoDocumentoRecibido::where('documento_recibido_id', $documentoRecibido->id)->get();
        $totalArchivos = $archivos->count();

        if ($totalArchivos === 0) {
            return ['estado' => 'NP', 'clase' => 'no-presentado'];
        }

        $aprobados = $archivos->where('estado_id', 3)->count();
        $porcentaje = ($aprobados / $totalArchivos) * 100;

        if ($porcentaje >= 80) {
            return ['estado' => 'P', 'clase' => 'presentado'];
        }

        return ['estado' => 'NP', 'clase' => 'no-presentado'];
    }

    private function obtenerObservaciones(int $enteId, int $documentoId, $periodos): array
    {
        $observaciones = [];

        foreach ($periodos as $periodo) {
            $documentoRecibido = DocumentosRecibido::where('ente_id', $enteId)
                ->where('documento_id', $documentoId)
                ->where('periodo_id', $periodo->id)
                ->first();

            if (!$documentoRecibido) continue;

            $archivosRechazados = ArchivoDocumentoRecibido::where('documento_recibido_id', $documentoRecibido->id)
                ->where('estado_id', 4)
                ->with('causaRechazo')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($archivosRechazados as $archivo) {
                $texto = '';
                $mesNombre = $periodo->mes ?? 'Mes ' . $periodo->mes_numero;

                if ($archivo->causaRechazo) {
                    $texto = "Rechazado {$mesNombre}: {$archivo->causaRechazo->descripcion}";
                }

                if ($archivo->observaciones_revisor) {
                    $texto .= ($texto ? '; ' : "Rechazado {$mesNombre}: ") . $archivo->observaciones_revisor;
                }

                if ($texto) {
                    $observaciones[] = [
                        'texto' => $texto,
                        'fecha' => $archivo->created_at,
                        'mes' => $mesNombre,
                    ];
                }
            }
        }

        usort($observaciones, fn($a, $b) => $a['fecha'] <=> $b['fecha']);
        return $observaciones;
    }

    /**
     * Exportar a PDF (respeta filtros opcionales: categorias[], subcategorias[], documentos[])
     */
    public function exportarPdf(Request $request)
    {
        $datos = $this->generarDatosReporte(
            (int) $request->ente,
            (int) $request->axo,
            array_map('intval', $request->input('categorias', [])),
            array_map('intval', $request->input('subcategorias', [])),
            array_map('intval', $request->input('documentos', []))
        );

        if (!$datos) {
            return back()->with('error', 'No se encontraron datos para generar el reporte.');
        }

        $pdf = Pdf::loadView('pdf.reporte-obligaciones', [
            'datos' => $datos,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('legal', 'landscape');

        $nombreArchivo = 'Reporte_Obligaciones_' . str_replace(' ', '_', $datos['ente']->nombre) . '_' . $datos['axo'] . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar a Excel (respeta filtros opcionales: categorias[], subcategorias[], documentos[])
     */
    public function exportarExcel(Request $request)
    {
        $datos = $this->generarDatosReporte(
            (int) $request->ente,
            (int) $request->axo,
            array_map('intval', $request->input('categorias', [])),
            array_map('intval', $request->input('subcategorias', [])),
            array_map('intval', $request->input('documentos', []))
        );

        if (!$datos) {
            return back()->with('error', 'No se encontraron datos para generar el reporte.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte Obligaciones');

        // --- Header ---
        $row = 1;
        $sheet->mergeCells("A{$row}:O{$row}");
        $sheet->setCellValue("A{$row}", 'Secretaría de Fiscalización');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('6C143A'));
        $row++;

        $sheet->mergeCells("A{$row}:O{$row}");
        $sheet->setCellValue("A{$row}", 'Departamento de Capacitación, Asesoría, Revisión y Supervisión a Municipios');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
        $row++;

        $sheet->mergeCells("A{$row}:O{$row}");
        $sheet->setCellValue("A{$row}", 'Reporte de Obligaciones Municipales');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
        $row++;

        $sheet->mergeCells("A{$row}:O{$row}");
        $sheet->setCellValue("A{$row}", 'Ayuntamiento: ' . $datos['ente']->nombre);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('6C143A'));
        $row++;

        $sheet->mergeCells("A{$row}:O{$row}");
        $sheet->setCellValue("A{$row}", 'Periodo: enero a diciembre ' . $datos['axo']);
        $sheet->getStyle("A{$row}")->getFont()->setSize(10);
        $row += 2;

        $mesesNombres = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        $trimNombres = ['1er. Trim.', '2do. Trim.', '3er. Trim.', '4to. Trim.'];

        // Estilos
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $subHeaderStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 8],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $colHeaderStyle = [
            'font' => ['bold' => true, 'size' => 8],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
        ];

        $cellStyle = [
            'font' => ['size' => 8],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        // Anchos de columna
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(40);
        foreach (range('C', 'N') as $col) {
            $sheet->getColumnDimension($col)->setWidth(6);
        }
        $sheet->getColumnDimension('O')->setWidth(50);

        foreach ($datos['categorias'] as $categoria) {
            // Título de categoría
            $sheet->mergeCells("A{$row}:O{$row}");
            $sheet->setCellValue("A{$row}", $categoria['nombre']);
            $sheet->getStyle("A{$row}:O{$row}")->applyFromArray($headerStyle);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;

            foreach ($categoria['subcategorias'] as $subcategoria) {
                $colCount = $subcategoria['tipo_periodo'] === 'trimestral' ? 4 : 12;
                $lastDataCol = chr(ord('C') + $colCount - 1);
                $obsCol = chr(ord('C') + $colCount);

                // Título subcategoría
                $sheet->mergeCells("A{$row}:{$obsCol}{$row}");
                $sheet->setCellValue("A{$row}", $subcategoria['nombre']);
                $sheet->getStyle("A{$row}:{$obsCol}{$row}")->applyFromArray($subHeaderStyle);
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;

                // Encabezados de columna
                $sheet->setCellValue("A{$row}", '#');
                $sheet->setCellValue("B{$row}", 'Documento');

                if ($subcategoria['tipo_periodo'] === 'trimestral') {
                    foreach ($trimNombres as $i => $trimNombre) {
                        $col = chr(ord('C') + $i);
                        $sheet->setCellValue("{$col}{$row}", $trimNombre);
                    }
                } else {
                    foreach ($mesesNombres as $i => $mesNombre) {
                        $col = chr(ord('C') + $i);
                        $sheet->setCellValue("{$col}{$row}", $mesNombre);
                    }
                }

                $sheet->setCellValue("{$obsCol}{$row}", 'Observaciones');
                $sheet->getStyle("A{$row}:{$obsCol}{$row}")->applyFromArray($colHeaderStyle);
                $row++;

                // Datos de documentos
                foreach ($subcategoria['documentos'] as $index => $documento) {
                    $sheet->setCellValue("A{$row}", $index + 1);
                    $sheet->setCellValue("B{$row}", $documento['nombre']);

                    $colIndex = 0;
                    foreach ($documento['meses'] as $mes => $estadoData) {
                        $col = chr(ord('C') + $colIndex);

                        if ($estadoData['clase'] === 'no-aplica') {
                            $sheet->getStyle("{$col}{$row}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('D9D9D9');
                        } elseif ($estadoData['estado'] === 'P') {
                            $sheet->setCellValue("{$col}{$row}", 'P');
                            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('2E7D32'));
                        } elseif ($estadoData['estado'] === 'NP') {
                            $sheet->setCellValue("{$col}{$row}", 'NP');
                            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('D32F2F'));
                        }

                        $colIndex++;
                    }

                    // Observaciones
                    $obsTexto = collect($documento['observaciones'])->pluck('texto')->implode("\n");
                    $sheet->setCellValue("{$obsCol}{$row}", $obsTexto);

                    $sheet->getStyle("A{$row}:{$obsCol}{$row}")->applyFromArray($cellStyle);
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Center the period columns
                    for ($c = 0; $c < $colCount; $c++) {
                        $col = chr(ord('C') + $c);
                        $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    $row++;
                }
            }

            $row++; // Espacio entre categorías
        }

        // Pie de reporte
        $sheet->mergeCells("A{$row}:O{$row}");
        $sheet->setCellValue("A{$row}", 'Reporte generado el ' . now()->format('d/m/Y H:i') . ' hrs. Criterio: ≥80% aprobado = Presentado (P)');
        $sheet->getStyle("A{$row}")->getFont()->setSize(8)->setItalic(true);

        $nombreArchivo = 'Reporte_Obligaciones_' . str_replace(' ', '_', $datos['ente']->nombre) . '_' . $datos['axo'] . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
