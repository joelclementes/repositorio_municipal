<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\Ente;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReporteActividadController extends Controller
{
    private function obtenerQueryFiltrada(Request $request)
    {
        $query = Activity::query()
            ->select('activity_log.*')
            ->leftJoin('users', function ($join) {
                $join->on('activity_log.causer_id', '=', 'users.id')
                     ->where('activity_log.causer_type', '=', 'App\\Models\\User');
            })
            ->with(['causer', 'causer.ente', 'causer.roles'])
            ->latest('activity_log.created_at');

        // Búsqueda
        if ($request->filled('busqueda')) {
            $termino = '%' . $request->busqueda . '%';
            $query->where(function ($q) use ($termino) {
                $q->where('activity_log.log_name', 'like', $termino)
                  ->orWhere('activity_log.description', 'like', $termino)
                  ->orWhere('users.name', 'like', $termino)
                  ->orWhere('users.email', 'like', $termino);
            });
        }

        // Tipo de usuario (Spatie Roles)
        if ($request->filled('tipo') && is_array($request->tipo)) {
            $query->where(function ($q) use ($request) {
                $incluyeCongreso = in_array('congreso', $request->tipo);
                $incluyeMunicipio = in_array('municipio', $request->tipo);

                if ($incluyeCongreso && !$incluyeMunicipio) {
                    $q->whereHas('causer.roles', function ($sq) {
                        $sq->whereIn('name', ['SuperUsuario', 'Administrador', 'Revisor']);
                    });
                } elseif ($incluyeMunicipio && !$incluyeCongreso) {
                    $q->whereHas('causer.roles', function ($sq) {
                        $sq->whereIn('name', ['Tesorero', 'Tesorero Organo Descentralizado', 'Director Obras Publicas', 'Contralor']);
                    });
                }
            });
        }

        // Estatus de usuario
        if ($request->filled('estatus') && is_array($request->estatus)) {
            $query->where(function ($q) use ($request) {
                $incluyeActivos = in_array('activos', $request->estatus);
                $incluyeInactivos = in_array('inactivos', $request->estatus);

                if ($incluyeActivos && !$incluyeInactivos) {
                    $q->where('users.is_active', true);
                } elseif ($incluyeInactivos && !$incluyeActivos) {
                    $q->where('users.is_active', false);
                }
            });
        }

        // Entes
        if ($request->filled('entes') && is_array($request->entes)) {
            $query->whereIn('users.ente_id', $request->entes);
        }

        // Fechas
        if ($request->filled('desde')) {
            $query->whereDate('activity_log.created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('activity_log.created_at', '<=', $request->hasta);
        }

        return $query;
    }

    public function exportarPdf(Request $request)
    {
        $actividades = $this->obtenerQueryFiltrada($request)->get();

        $pdf = Pdf::loadView('pdf.reporte-actividad', [
            'actividades' => $actividades,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('legal', 'landscape');

        $nombreArchivo = 'Reporte_Actividad_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    public function exportarExcel(Request $request)
    {
        $actividades = $this->obtenerQueryFiltrada($request)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bitacora Actividad');

        // Header
        $row = 1;
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'Secretaría de Fiscalización - SIFOM');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('6C143A'));
        $row++;

        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'Reporte de Bitácora de Actividad (Spatie)');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
        $row++;

        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'Fecha de Generación: ' . now()->format('d/m/Y H:i') . ' hrs');
        $sheet->getStyle("A{$row}")->getFont()->setSize(9)->setItalic(true);
        $row += 2;

        // Table headers
        $headers = ['Fecha / Hora', 'Usuario', 'Correo / ID', 'Origen / Rol', 'Acción', 'Descripción', 'IP / Dispositivo'];
        $colIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$colIndex}{$row}", $header);
            $colIndex++;
        }

        // Estilos de Encabezados
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6C143A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($headerStyle);
        $sheet->getRowDimension($row)->setRowHeight(24);
        $row++;

        $cellStyle = [
            'font' => ['size' => 8],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        // Anchos de columnas
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('F')->setWidth(50);
        $sheet->getColumnDimension('G')->setWidth(18);

        foreach ($actividades as $index => $actividad) {
            $sheet->setCellValue("A{$row}", $actividad->created_at->format('d/m/Y H:i:s'));
            $sheet->setCellValue("B{$row}", $actividad->causer?->name ?? 'Sistema');
            $sheet->setCellValue("C{$row}", $actividad->causer?->email ?? 'N/A');

            $origen = 'N/A';
            if ($actividad->causer) {
                $origen = $actividad->causer->hasAnyRole(['SuperUsuario', 'Administrador', 'Revisor'])
                    ? ($actividad->causer->roles->first()?->name ?? 'Congreso') . ' (Cong.)'
                    : ($actividad->causer->ente?->nombre ?? 'N/A') . ' (Mun.)';
            }
            $sheet->setCellValue("D{$row}", $origen);
            $sheet->setCellValue("E{$row}", $actividad->log_name);
            $sheet->setCellValue("F{$row}", $actividad->description);
            $sheet->setCellValue("G{$row}", $actividad->getExtraProperty('ip') ?? 'N/A');

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($cellStyle);
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $nombreArchivo = 'Reporte_Actividad_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
