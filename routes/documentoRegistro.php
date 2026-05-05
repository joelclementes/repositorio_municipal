<?php

use App\Http\Controllers\DocumentoRegistroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('can:registrar')->group(function () {
    Route::get('/documentos/registrar', [DocumentoRegistroController::class, 'index'])->name('documento.registro.index');

    Route::get('/documentos/registrar/acuse', [DocumentoRegistroController::class, 'acuse'])->name('documento.registro.acuse');
});
