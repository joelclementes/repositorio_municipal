<?php

use App\Http\Controllers\EnteRevisorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('can:administrar')->group(function () {
    Route::get('/entesRevisor/registrar', [EnteRevisorController::class, 'index'])->name('entesRevisor.registro.index');    
    Route::post('/entesRevisor/registrar', [EnteRevisorController::class, 'store'])->name('entesRevisor.registro.store');    
    Route::patch('/entesRevisor/{id}/toggle-status', [EnteRevisorController::class, 'toggleStatus'])->name('entesRevisor.toggle-status');
});
