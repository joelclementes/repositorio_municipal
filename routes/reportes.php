<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteObligacionesController;

Route::middleware('can:generar-reportes')->group(function () {

    Route::get('/reportes/obligaciones', function () {
        return view('reportes.reporte');
    })->name('reportes.obligaciones.index');

    Route::get('/reportes/obligaciones/pdf', [ReporteObligacionesController::class, 'exportarPdf'])
        ->name('reportes.obligaciones.pdf');

    Route::get('/reportes/obligaciones/excel', [ReporteObligacionesController::class, 'exportarExcel'])
        ->name('reportes.obligaciones.excel');
});
