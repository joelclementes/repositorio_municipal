<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    //
    protected $fillable = [
        'clave',
        'nombre',
        'subcategoria_id',
    ];

    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriasDocumento::class, 'subcategoria_id');
    }

    
}
