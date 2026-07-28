<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolePermissionController;

Route::get('/roles-permisos', [RolePermissionController::class, 'index'])
        ->name('roles-permisos.index');

    Route::post('/roles', [RolePermissionController::class, 'storeRole'])
        ->name('roles.store');

    Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])
        ->name('roles.update');

    Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])
        ->name('roles.destroy');

    Route::post('/permisos', [RolePermissionController::class, 'storePermission'])
        ->name('permisos.store');

    Route::put('/permisos/{permission}', [RolePermissionController::class, 'updatePermission'])
        ->name('permisos.update');

    Route::delete('/permisos/{permission}', [RolePermissionController::class, 'destroyPermission'])
        ->name('permisos.destroy');
