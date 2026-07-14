<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mensaje_raiz_id',
        'mensaje_padre_id',
        'remitente_id',
        'asunto',
        'cuerpo',
    ];

    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function destinatarios()
    {
        return $this->hasMany(MensajeDestinatario::class);
    }

    public function archivos()
    {
        return $this->hasMany(MensajeArchivo::class);
    }

    public function respuestas()
    {
        return $this->hasMany(Mensaje::class, 'mensaje_padre_id');
    }

    public function hilo()
    {
        return $this->hasMany(Mensaje::class, 'mensaje_raiz_id');
    }

    public function mensajeRaiz()
    {
        return $this->belongsTo(Mensaje::class, 'mensaje_raiz_id');
    }
}
