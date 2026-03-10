<?php

use App\Http\Controllers\PeriodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('can:administrar')->group(function () {
    Route::get('/periodos/registrar', [PeriodoController::class, 'index'])->name('periodos.registro.index');    
    Route::post('/periodos/registrar', [PeriodoController::class, 'store'])->name('periodos.registro.store');    
    Route::patch('/periodos/{id}/toggle-status', [PeriodoController::class, 'toggleStatus'])->name('periodos.toggle-status');
});
