<?php
// app/Models/EnteRevisor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnteRevisor extends Model
{
    use HasFactory;

    protected $table = 'entes_revisor';

    protected $fillable = [
        'ente_id',
        'revisor_id',
    ];

    /**
     * Relación con el Ente
     */
    public function ente(): BelongsTo
    {
        return $this->belongsTo(Ente::class, 'ente_id');
    }

    /**
     * Relación con el Revisor (Usuario)
     */
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }
}