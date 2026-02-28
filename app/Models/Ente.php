<?php
// app/Models/Ente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipos_entes_id',
    ];

    /**
     * Relación con el tipo de ente
     */
    public function tipoEnte(): BelongsTo
    {
        return $this->belongsTo(TipoEnte::class, 'tipos_entes_id');
    }

    /**
     * Relación con los usuarios (EntesObligados)
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación con los avisos a través de la tabla pivote
     */
    public function avisos()
    {
        return $this->belongsToMany(Aviso::class, 'aviso_entes')
            ->withPivot(['estado_envio', 'fecha_envio', 'fecha_lectura'])
            ->withTimestamps();
    }

    /**
     * Relación con la tabla pivote aviso_entes
     */
    public function avisoEntes(): HasMany
    {
        return $this->hasMany(AvisoEnte::class);
    }
}
