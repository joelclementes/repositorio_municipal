<?php

use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::middleware('can:generar-reportes')->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');    
    Route::get('/reportes/exportar', [ReporteController::class, 'export'])->name('reportes.export');    
});
