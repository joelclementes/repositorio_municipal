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

    public function getMesNombreAttribute()
    {
        return $this->mes ?? 'Desconocido';
    }
}
