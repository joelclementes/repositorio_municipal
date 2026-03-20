<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $table = 'periodos';

    protected $fillable = [
        'mes',
        'axo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
    ];

    public function getMesNombreAttribute()
    {
        return $this->mes ?? 'Desconocido';
    }
}
