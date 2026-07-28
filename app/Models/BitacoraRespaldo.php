<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitacoraRespaldo extends Model
{
    protected $table = 'bitacora_respaldos';

    protected $fillable = [
        'nombre_archivo',
        'ruta',
        'fecha_limpieza',
        'registros_afectados',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_limpieza' => 'datetime',
        'registros_afectados' => 'integer',
    ];

    /**
     * Usuario que ejecutó la limpieza
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
