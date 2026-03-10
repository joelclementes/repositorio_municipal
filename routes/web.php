<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentoRecibidoController;

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Ruta existente
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    require __DIR__ . '/avisos.php';
    require __DIR__ . '/documentoRegistro.php';
});
