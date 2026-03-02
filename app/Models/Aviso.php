<?php
// app/Models/Aviso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aviso extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titulo',
        'tipo_aviso',
        'texto',
        'activo',
        'url',
        'archivo',
        'fecha_publicacion',
        'fecha_expiracion',
        'creado_por',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'fecha_expiracion' => 'datetime',
    ];

    /**
     * Relación con la tabla pivote aviso_entes
     */
    public function avisoEntes(): HasMany
    {
        return $this->hasMany(AvisoEnte::class);
    }

    /**
     * Relación con los entes a través de la tabla pivote
     */
    public function entes()
    {
        return $this->belongsToMany(Ente::class, 'aviso_entes')
            ->withPivot(['estado_envio', 'fecha_envio', 'fecha_lectura'])
            ->withTimestamps();
    }

    /**
     * Usuario que creó el aviso
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
