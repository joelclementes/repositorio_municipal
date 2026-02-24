<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ente extends Model
{
    //
    protected $fillable = [
        'nombre',
        'tipo_de_ente',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    
}
