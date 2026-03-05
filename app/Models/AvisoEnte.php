<?php
// app/Models/AvisoEnte.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot; // Importante: extends Pivot, no Model
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvisoEnte extends Pivot
{
    use HasFactory;

    /**
     * Indica si los IDs son auto-incrementables.
     * Como la tabla tiene $table->id(), debe ser true
     */
    public $incrementing = true;

    protected $table = 'aviso_entes'; // Asegúrate que coincida con tu migración

    protected $fillable = [
        'aviso_id',
        'ente_id',
        'estado_envio',
        'fecha_envio',
        'fecha_lectura',
        'intentos_envio',
        'enviado_por',
        'leido_por',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_lectura' => 'datetime',
        'intentos_envio' => 'integer',
        'leido_por' => 'integer',
    ];

    /**
     * Relación con el Aviso
     */
    public function aviso(): BelongsTo
    {
        return $this->belongsTo(Aviso::class);
    }

    /**
     * Relación con el Ente
     */
    public function ente(): BelongsTo
    {
        return $this->belongsTo(Ente::class);
    }

    /**
     * Relación con el usuario que envió (opcional)
     */
    public function enviadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enviado_por');
    }

    public function leido(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leido_por');
    }
}
