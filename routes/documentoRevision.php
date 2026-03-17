<?php
use App\Http\Controllers\ExcelViewController;
use Illuminate\Support\Facades\Route;


Route::middleware('can:revisar-documentos')->group(function () {
    Route::get('/documentos/revisar', function () {
        return view('documento.revision');
    })->name('documento.revision.index');

    Route::get('/excel-preview/{archivo}', [ExcelViewController::class, 'preview'])
        ->name('excel.preview');

});
