<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentoRecibidoController;

Route::middleware('can:registrar')->group(function () {
    Route::get('/documentos/registrar', [DocumentoRecibidoController::class, 'index'])->name('documentos.registrar');

});