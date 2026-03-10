<?php
// app/Models/DocumentosRecibido.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentosRecibido extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'documentos_recibidos';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'ente_id',
        'user_id',
        'documentos_id',
        'periodo_id',
        'tipo_recepcion',
        'fecha_recibido',
        'fecha_cambio_estatus',
        'nombre_archivo',
        'descripcion',
        'estados_id',
        'causas_rechazo_id',
        'usuario_revisor',
    ];

    // protected $fillable = [
    //     'ente_id',
    //     'user_id',
    //     'documentos_id',
    //     'periodo_id',
    // ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el Ente
     */
    public function ente(): BelongsTo
    {
        return $this->belongsTo(Ente::class, 'ente_id');
    }

    /**
     * Relación con el Usuario que subió el documento
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el Documento (catálogo)
     */
    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documentos_id');
    }

    public function archivos()
    {
        return $this->hasMany(ArchivoDocumentoRecibido::class, 'documento_recibido_id');
    }


    /**
     * Relación con el Período
     */
    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    /**
     * Scope para filtrar por ente
     */
    public function scopePorEnte($query, $enteId)
    {
        return $query->where('ente_id', $enteId);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopePorPeriodo($query, $periodoId)
    {
        return $query->where('periodo_id', $periodoId);
    }


}