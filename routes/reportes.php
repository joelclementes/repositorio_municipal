<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteObligacionesController;
use App\Http\Controllers\ReporteController;

Route::middleware('can:generar-reportes')->group(function () {

    // Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes', function () {
        return view('reportes.index-cards');
    })->name('reportes.index');
    
    Route::get('/reportes/general', [ReporteController::class, 'index'])->name('reportes.general');

    Route::get('/reportes/exportar', [ReporteController::class, 'export'])->name('reportes.export');

    Route::get('/reportes/obligaciones', function () {
        return view('reportes.reporte');
    })->name('reportes.obligaciones.index');

    Route::get('/reportes/obligaciones/pdf', [ReporteObligacionesController::class, 'exportarPdf'])
        ->name('reportes.obligaciones.pdf');

    Route::get('/reportes/obligaciones/excel', [ReporteObligacionesController::class, 'exportarExcel'])
        ->name('reportes.obligaciones.excel');
});
