<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $table = 'periodos';

    protected $fillable = [
        'mes_numero',
        'mes',
        'axo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
    ];

    protected $casts = [
        'mes_numero' => 'integer',
        'axo' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'is_active' => 'boolean',
    ];

    public function getFechaInicioDmaAttribute(): ?string
    {
        return $this->fecha_inicio
            ? $this->fecha_inicio->format('d-m-Y')
            : null;
    }

    public function getFechaFinDmaAttribute(): ?string
    {
        return $this->fecha_fin
            ? $this->fecha_fin->format('d-m-Y')
            : null;
    }

    public function getMesNombreAttribute()
    {
        return $this->mes ?? 'Desconocido';
    }
    public function getAxoMesAttribute(): string
    {
        return sprintf(
            '%02d%02d',
            $this->axo,
            $this->mes_numero
        );
    }
}
