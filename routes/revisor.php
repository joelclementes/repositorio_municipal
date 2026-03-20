<?php

use App\Http\Controllers\RevisorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('can:administrar')->group(function () {

    Route::get('/revisores/avances', [RevisorController::class, 'index'])->name('revisores.avances.index');
});
