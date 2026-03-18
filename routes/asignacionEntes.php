<?php

use App\Http\Controllers\EnteRevisorController;
// use App\Livewire\AsignacionRevisores\AsignacionRevisores; 
use Illuminate\Support\Facades\Route;

Route::middleware('can:administrar')->group(function () {
    // Route::get('/asignacion-revisores', AsignacionRevisores::class)
    //     ->name('asignacion.revisores');
    Route::get('/asignacion-revisores/asignar', [EnteRevisorController::class, 'index'])->name('revisor.asignar.index');
});