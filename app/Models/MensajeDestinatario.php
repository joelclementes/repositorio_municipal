<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensajeDestinatario extends Model
{
    protected $table = 'mensaje_destinatarios';

    protected $fillable = [
        'mensaje_id',
        'destinatario_id',
        'estado',
        'leido_at',
    ];

    protected $casts = [
        'leido_at' => 'datetime',
    ];

    public function mensaje()
    {
        return $this->belongsTo(Mensaje::class);
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }
}
