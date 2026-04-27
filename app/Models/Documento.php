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
        'regla_presentacion',
        'formato',
    ];

    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriasDocumento::class, 'subcategoria_id');
    }

    public static function reglasPresentacionOptions(): array
    {
        return config('documentos.reglas_presentacion', []);
    }

    public function getReglaPresentacionEtiquetaAttribute(): string
    {
        $opciones = self::reglasPresentacionOptions();

        return $opciones[$this->regla_presentacion] ?? $this->regla_presentacion;
    }
}
