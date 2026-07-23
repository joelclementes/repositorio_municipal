<?php

use Illuminate\Support\Facades\Route;

Route::get('/manual-usuario', function () {
    return view('manual_usuario.index');
})->name('manual_usuario');
