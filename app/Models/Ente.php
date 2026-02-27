<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ente extends Model
{
    //
    protected $fillable = [
        'nombre',
        'tipos_entes_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    
}
