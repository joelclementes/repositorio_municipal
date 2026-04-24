<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    //

    protected $table = 'documentos';

    protected $fillable = [
        'clave',
        'nombre',
        'subcategoria_id',
        'periodicidad',
        'regla_presentacion',
        'fecha_inicio',
        'fecha_limite',
        'formato',
    ];

    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriasDocumento::class, 'subcategoria_id');
    }


}
