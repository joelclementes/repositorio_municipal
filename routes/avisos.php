<?php

use App\Models\AvisoEnte;
use App\Http\Controllers\AvisoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Ruta para ver todos los avisos pendientes
Route::get('/avisos/pendientes', function (Request $request) {
    $user = auth()->user();

    // Verificar que el usuario es EnteObligado
    if (!$user->hasRole('EnteObligado')) {
        abort(403, 'No tienes permiso para ver esta página.');
    }

    $ente = $user->ente;

    if (!$ente) {
        abort(404, 'No se encontró el ente asociado.');
    }

    $avisosPendientes = AvisoEnte::with(['aviso.creador'])
        ->where('ente_id', $ente->id)
        ->where('estado_envio', '!=', 'leido')
        ->whereHas('aviso', function ($query) {
            $query->where('activo', true)
                ->where(function ($q) {
                    $q->whereNull('fecha_expiracion')
                        ->orWhere('fecha_expiracion', '>', now());
                });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('avisos.pendientes', compact('avisosPendientes'));
})->name('avisos.pendientes');

// Ruta para marcar un aviso como leído (opcional)
Route::patch('/avisos/{avisoEnte}/marcar-leido', function ($avisoEnteId) {
    $avisoEnte = AvisoEnte::findOrFail($avisoEnteId);

    // Verificar que el aviso pertenece al ente del usuario
    if ($avisoEnte->ente_id !== auth()->user()->ente->id) {
        abort(403);
    }

    $avisoEnte->update([
        'estado_envio' => 'leido',
        'fecha_lectura' => now()
    ]);

    return back()->with('success', 'Aviso marcado como leído');
})->name('avisos.marcar-leido');

// Ruta para marcar todos como leídos (opcional)
Route::post('/avisos/marcar-todos-leidos', function () {
    $ente = auth()->user()->ente;

    if (!$ente) {
        abort(404);
    }

    AvisoEnte::where('ente_id', $ente->id)
        ->where('estado_envio', '!=', 'leido')
        ->update([
            'estado_envio' => 'leido',
            'fecha_lectura' => now()
        ]);

    return back()->with('success', 'Todos los avisos marcados como leídos');
})->name('avisos.marcar-todos-leidos');

Route::middleware('can:administrar')->group(function () {
    // Route::get('/avisos/crear', [AvisoController::class, 'create'])->name('avisos.crear');
    Route::get('/avisos/index', [AvisoController::class, 'index'])->name('avisos.index');
    Route::get('/avisos/buscar-ente', [AvisoController::class, 'buscarEnte'])->name('avisos.buscarEnte');
    Route::post('/avisos/store', [AvisoController::class, 'store'])->name('avisos.store');
});
