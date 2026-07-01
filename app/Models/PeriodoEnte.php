<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoEnte extends Model
{
    protected $table = 'periodos_entes';

    protected $fillable = [
        'ente_id',
        'periodo_id',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'is_active' => 'boolean',
    ];

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function ente()
    {
        return $this->belongsTo(Ente::class, 'ente_id');
    }
}
