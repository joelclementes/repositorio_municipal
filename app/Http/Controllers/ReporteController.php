<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function export()
    {
        // Obtener tipo de ente 'Municipio'
        $tipoMunicipio = DB::table('tipos_entes')->where('nombre', 'Municipio')->first();
        if (!$tipoMunicipio) {
            return redirect()->back()->with('error', 'No se encontró el tipo de ente Municipio.');
        }

        // Obtener Municipios
        $municipios = DB::table('entes')
            ->where('tipos_entes_id', $tipoMunicipio->id)
            ->orderBy('id')
            ->get();

        // Obtener Periodos
        $periodos = DB::table('periodos')
            ->orderBy('id') // O ordenar por fecha_inicio
            ->get();

        // Obtener Documentos (pueden venir con su categoría para colorear o agrupar)
        // Para coincidir con la solicitud, usaremos los documentos
        $documentos = DB::table('documentos')->orderBy('id')->get();

        // Precargar documentos recibidos validados con archivo (relacionando las tablas)
        // Solo traemos los que tienen un archivo asociado
        $entregas = DB::table('documentos_recibidos')
            ->join('archivo_documento_recibidos', 'documentos_recibidos.id', '=', 'archivo_documento_recibidos.documento_recibido_id')
            ->select('documentos_recibidos.ente_id', 'documentos_recibidos.documento_id', 'documentos_recibidos.periodo_id')
            ->distinct()
            ->get();

        // Crear una matriz de acceso rápido para entregas
        // $entregasMap[ente_id][documento_id][periodo_id] = true
        $entregasMap = [];
        foreach ($entregas as $entrega) {
            $entregasMap[$entrega->ente_id][$entrega->documento_id][$entrega->periodo_id] = true;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte General');

        // Configurar las columnas iniciales
        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'CLAVE');
        $sheet->setCellValue('C1', 'MUNICIPIO');
        
        // Merge para la cabecera lateral
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');

        $colIndex = 4; // Empezamos en la columna D (4)
        $docColumns = []; // Guardar info para la fila de totales
        $colors = ['FF99CC', '99CC00', '99CCFF', 'FFCC99', 'CC99FF']; // Colores base tipo la imagen
        $colorIdx = 0;

        foreach ($documentos as $doc) {
            $startCol = $colIndex;
            $docColor = $colors[$colorIdx % count($colors)];

            foreach ($periodos as $periodo) {
                // Escribir el mes
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValueExplicit($colLetter . '2', strtoupper($periodo->mes), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                
                // Inicializar contadores para esta columna
                $docColumns[$colIndex] = [
                    'doc_id' => $doc->id,
                    'periodo_id' => $periodo->id,
                    'presentados' => 0,
                    'no_presentados' => 0
                ];

                $colIndex++;
            }

            $endCol = $colIndex - 1;

            // Merge de la cabecera del documento
            if ($startCol <= $endCol) {
                $startLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                $endLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);
                
                $sheet->setCellValue($startLetter . '1', strtoupper($doc->nombre));
                $sheet->mergeCells($startLetter . '1:' . $endLetter . '1');

                // Estilo para el encabezado del documento
                $sheet->getStyle($startLetter . '1:' . $endLetter . '2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => $docColor],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            }
            
            $colorIdx++;
        }

        // Estilos para A1:C2
        $sheet->getStyle('A1:C2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFCC99'], // Naranja claro como en la imagen
            ],
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Calcular datos por municipio y llenar la matriz
        $dataRows = [];
        $municipioCount = 0;
        $hoy = date('Y-m-d');

        foreach ($municipios as $muni) {
            $municipioCount++;
            $rowData = [
                $municipioCount,
                str_pad($muni->id, 3, '0', STR_PAD_LEFT), // Usamos el ID o una clave
                $muni->nombre
            ];

            foreach ($docColumns as $col => $info) {
                // Verificar si entregó
                $entregado = isset($entregasMap[$muni->id][$info['doc_id']][$info['periodo_id']]);
                
                // Encontrar el periodo para revisar la fecha_fin
                $periodoObj = $periodos->firstWhere('id', $info['periodo_id']);
                
                if ($entregado) {
                    $rowData[] = 'P';
                    $docColumns[$col]['presentados']++;
                } else {
                    // Validar si el periodo ya venció o está activo para poner NP o vacío.
                    if ($periodoObj && $periodoObj->fecha_fin && $periodoObj->fecha_fin >= $hoy) {
                        // Si aún no termina el periodo, dejar celda vacía
                        $rowData[] = '';
                    } else {
                        // Si ya terminó, es NP
                        $rowData[] = 'NP';
                        $docColumns[$col]['no_presentados']++;
                    }
                }
            }

            $dataRows[] = $rowData;
        }

        // Fila 3: PRESENTADOS
        $sheet->setCellValue('A3', 'P');
        $sheet->setCellValue('B3', 'PRESENTADOS');
        $sheet->mergeCells('B3:C3');
        $sheet->getStyle('A3:C3')->getFont()->setBold(true);

        // Fila 4: NO PRESENTADOS
        $sheet->setCellValue('A4', 'NP');
        $sheet->setCellValue('B4', 'NO PRESENTADOS');
        $sheet->mergeCells('B4:C4');
        $sheet->getStyle('A4:C4')->getFont()->setBold(true);
        $sheet->getStyle('A4:C4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E0E0E0');

        // Fila 5: TOTAL
        $sheet->setCellValue('B5', 'TOTAL');
        $sheet->mergeCells('B5:C5');
        $sheet->getStyle('B5:C5')->getFont()->setBold(true);

        // Llenar los totales
        foreach ($docColumns as $col => $info) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colLetter . '3', $info['presentados']);
            $sheet->setCellValue($colLetter . '4', $info['no_presentados']);
            $sheet->setCellValue($colLetter . '5', $info['presentados'] + $info['no_presentados']);
            
            $sheet->getStyle($colLetter . '3:' . $colLetter . '5')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Bordes de totales
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex - 1);
        $sheet->getStyle('A3:' . $lastColLetter . '5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Escribir los datos de los municipios (desde fila 6)
        $startDataRow = 6;
        $rowIdx = $startDataRow;
        foreach ($dataRows as $rowData) {
            $colIdx = 1;
            foreach ($rowData as $val) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValueExplicit($colLetter . $rowIdx, $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $colIdx++;
            }
            $rowIdx++;
        }

        // Estilos para la matriz de datos
        $endDataRow = $rowIdx - 1;
        if ($endDataRow >= $startDataRow) {
            $sheet->getStyle('A' . $startDataRow . ':' . $lastColLetter . $endDataRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            
            // Alinear Municipio a la izquierda
            $sheet->getStyle('C' . $startDataRow . ':C' . $endDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        // Ajustar ancho de columnas iniciales
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        // Crear el archivo
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Reporte_General_Municipios_' . date('Ymd_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
