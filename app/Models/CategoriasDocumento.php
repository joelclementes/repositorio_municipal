<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriasDocumento extends Model
{
    //
    protected $fillable = [
        'clave',
        'nombre',
    ];

    public function subcategorias()
    {
        return $this->hasMany(SubcategoriasDocumento::class, 'categoria_id');
    }
}
