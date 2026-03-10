<?php
// app/Models/ArchivoDocumentoRecibido.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchivoDocumentoRecibido extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'archivo_documento_recibidos';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'observaciones',
        'documento_recibido_id',
        'ente_id',
        'user_id',
        'tipo_recepcion',
        'fecha_cambio_estatus',
        'usuario_revisor',
        'observaciones_revisor',
        'causas_rechazo_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_cambio_estatus' => 'date',
    ];

    /**
     * Relación con el DocumentoRecibido
     */
    public function documentoRecibido(): BelongsTo
    {
        return $this->belongsTo(DocumentosRecibido::class, 'documento_recibido_id');
    }

    /**
     * Relación con el Ente
     */
    public function ente(): BelongsTo
    {
        return $this->belongsTo(Ente::class, 'ente_id');
    }

    /**
     * Relación con el Usuario que subió el archivo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con la Causa de Rechazo
     */
    public function causaRechazo(): BelongsTo
    {
        return $this->belongsTo(CausaRechazo::class, 'causas_rechazo_id');
    }

    /**
     * Accesor para obtener la ruta del archivo
     */
    public function getRutaAttribute(): string
    {
        $documentoRecibido = $this->documentoRecibido;
        return 'documentos/' . $documentoRecibido->periodo_id . '/' . $this->ente_id . '/' . $documentoRecibido->documentos_id . '/' . $this->nombre;
    }

    /**
     * Accesor para obtener la URL del archivo
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->ruta);
    }
}