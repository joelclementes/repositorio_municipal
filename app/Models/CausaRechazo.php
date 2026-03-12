<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CausaRechazo extends Model
{
    use HasFactory;

    protected $table = 'causas_rechazo';

    protected $fillable = [
        'descripcion',
    ];

    public function archivos()
    {
        return $this->hasMany(ArchivoDocumentoRecibido::class, 'causas_rechazo_id');
    }
}
