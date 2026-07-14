<?php

use Illuminate\Support\Facades\Route;

Route::middleware('can:usar-mensajeria')->group(function () {
    Route::get('/mensajeria', function () {
        return view('mensajeria.index');
    })->name('mensajeria.index');
});
