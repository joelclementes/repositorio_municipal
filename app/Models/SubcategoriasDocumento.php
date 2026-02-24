<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubcategoriasDocumento extends Model
{
    //
    protected $fillable = [
        'clave',
        'nombre',
        'categoria_id',
    ];
    
    public function categoria()
    {
        return $this->belongsTo(CategoriasDocumento::class, 'categoria_id');
    }


}
