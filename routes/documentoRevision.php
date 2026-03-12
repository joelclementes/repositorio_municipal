<?php

use App\Http\Controllers\DocumentoRegistroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('can:revisar-documentos')->group(function () {
    Route::get('/documentos/revisar', function () {
        return view('documento.revision');
    })->name('documento.revision.index');
});
