<?php
// app/Http/Controllers/ExcelViewController.php

namespace App\Http\Controllers;

use App\Models\ArchivoDocumentoRecibido;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

class ExcelViewController extends Controller
{
    public function preview($archivoId)
    {
        $archivo = ArchivoDocumentoRecibido::with([
            'documentoRecibido.periodo', 
            'ente',
            'documentoRecibido.documento'
        ])->findOrFail($archivoId);
        
        // Ruta completa del archivo
        $ruta = storage_path('app/public/' . $archivo->ruta);
        
        // Verificar que el archivo existe
        if (!file_exists($ruta)) {
            abort(404, 'El archivo no existe en el servidor');
        }
        
        try {
            // Cargar el archivo Excel
            $spreadsheet = IOFactory::load($ruta);
            
            // Configurar el escritor HTML
            $writer = new Html($spreadsheet);
            
            // Opciones para mejorar la visualización
            $writer->setSheetIndex(0); // Mostrar primera hoja
            $writer->setPreCalculateFormulas(true); // Calcular fórmulas
            $writer->setUseInlineCss(true); // Usar CSS en línea
            
            // Capturar el HTML generado
            ob_start();
            $writer->save('php://output');
            $html = ob_get_clean();
            
            return view('documento.excel-preview', compact('html', 'archivo'));
            
        } catch (\Exception $e) {
            abort(500, 'Error al procesar el archivo Excel: ' . $e->getMessage());
        }
    }
    
    /**
     * Método alternativo para mostrar todas las hojas
     */
    public function previewAllSheets($archivoId)
    {
        $archivo = ArchivoDocumentoRecibido::findOrFail($archivoId);
        $ruta = storage_path('app/public/' . $archivo->ruta);
        
        if (!file_exists($ruta)) {
            abort(404, 'El archivo no existe');
        }
        
        try {
            $spreadsheet = IOFactory::load($ruta);
            $sheets = [];
            
            foreach ($spreadsheet->getSheetNames() as $index => $sheetName) {
                $writer = new Html($spreadsheet);
                $writer->setSheetIndex($index);
                
                ob_start();
                $writer->save('php://output');
                $sheets[] = [
                    'name' => $sheetName,
                    'html' => ob_get_clean()
                ];
            }
            
            return view('excel-preview-all', compact('sheets', 'archivo'));
            
        } catch (\Exception $e) {
            abort(500, 'Error al procesar el archivo Excel');
        }
    }
}