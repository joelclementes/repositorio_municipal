<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensajeArchivo extends Model
{
    protected $table = 'mensaje_archivos';

    protected $fillable = [
        'mensaje_id',
        'nombre_original',
        'ruta',
        'mime_type',
        'extension',
        'size',
    ];

    public function mensaje()
    {
        return $this->belongsTo(Mensaje::class);
    }
}
