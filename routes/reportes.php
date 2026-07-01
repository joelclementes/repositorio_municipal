<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteObligacionesController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReporteActividadController;

Route::middleware('can:generar-reportes')->group(function () {

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/exportar', [ReporteController::class, 'export'])->name('reportes.export');

    Route::get('/reportes/obligaciones', function () {
        return view('reportes.reporte');
    })->name('reportes.obligaciones.index');

    Route::get('/reportes/obligaciones/pdf', [ReporteObligacionesController::class, 'exportarPdf'])
        ->name('reportes.obligaciones.pdf');

    Route::get('/reportes/obligaciones/excel', [ReporteObligacionesController::class, 'exportarExcel'])
        ->name('reportes.obligaciones.excel');

    // Rutas para Reporte de Actividad (Bitácora de Spatie)
    Route::get('/reportes/actividad', function () {
        return view('reportes.actividad');
    })->name('reportes.actividad.index');

    Route::get('/reportes/actividad/pdf', [ReporteActividadController::class, 'exportarPdf'])
        ->name('reportes.actividad.pdf');

    Route::get('/reportes/actividad/excel', [ReporteActividadController::class, 'exportarExcel'])
        ->name('reportes.actividad.excel');
});

