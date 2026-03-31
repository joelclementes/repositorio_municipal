<?php
// app/Models/ArchivoDocumentoRecibido.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchivoDocumentoRecibido extends Model
{
    use HasFactory;

    protected $table = 'archivo_documento_recibidos';

    protected $fillable = [
        'nombre',
        'observaciones_ente',
        'documento_recibido_id',
        'ente_id',
        'user_id',
        'tipo_recepcion',
        'fecha_cambio_estatus',
        'usuario_revisor',
        'estado_id',
        'observaciones_revisor',
        'causas_rechazo_id',
        'autorizado_reenviar',
    ];

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
     * Accesor para obtener la ruta del archivo (CORREGIDO)
     */
    public function getRutaAttribute(): string
    {
        $periodo = $this->documentoRecibido->periodo;
        $ente = $this->ente;

        // Construir la ruta: documentos/{axo}/{nombre_ente}/{mes_nombre}/{nombre_archivo}
        return 'documentos/' .
            $periodo->axo . '/' .
            $ente->nombre . '/' .
            $periodo->mes_nombre . '/' .  // ← Esto ahora funcionará
            $this->nombre;
    }

    /**
     * Accesor para obtener la URL del archivo (CORREGIDO)
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->ruta);
    }

    /**
     * Helper para obtener nombre del mes
     */
    private function getMesNombre($mes)
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        return $meses[$mes] ?? 'Desconocido';
    }

    /**
     * Relación con el Estado del documento
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function getEstadoNombreAttribute(): string
    {
        return $this->estado?->nombre ?? 'Sin estado';
    }

    public function getCausaRechazoDescripcionAttribute(): string
    {
        return $this->causaRechazo?->descripcion ?? '';
    }
}
