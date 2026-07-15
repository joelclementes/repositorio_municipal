<?php
// app/Models/ArchivoDocumentoRecibido.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ArchivoDocumentoRecibido extends Model
{
    use HasFactory;
    use LogsActivity;

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

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nombre', 'estado_id', 'observaciones_ente', 'observaciones_revisor',
                'causas_rechazo_id', 'autorizado_reenviar', 'tipo_recepcion',
                'ente_id', 'user_id', 'documento_recibido_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $user = Auth::user();
        $userNombre = $user ? "\"{$user->name}\" ({$user->email})" : 'el sistema';
        $rolUser = $user?->roles->first()?->name ?? 'N/A';

        // Agregar IP, user_agent y contexto
        $props = $activity->properties->toArray();
        $props['ip'] = request()->ip();
        $props['user_agent'] = request()->userAgent();
        $props['realizado_por'] = $user?->name ?? 'Sistema';
        $activity->properties = collect($props);

        $enteNombre = $this->ente?->nombre ?? 'N/A';
        $periodoInfo = $this->getPeriodoInfo();

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Carga de documento';
                $tipoRecepcion = $this->tipo_recepcion ?? 'N/A';
                $observaciones = $this->observaciones_ente ? " Observaciones del ente: \"{$this->observaciones_ente}\"." : '';

                $activity->description = "Se cargó el archivo \"{$this->nombre}\" al sistema."
                    . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                    . " Tipo de recepción: {$tipoRecepcion}."
                    . " Subido por: {$userNombre}.{$observaciones}";
                break;

            case 'updated':
                $this->handleDocumentoUpdated($activity, $user, $userNombre, $rolUser, $enteNombre, $periodoInfo);
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de documento';
                $activity->description = "Se eliminó el archivo \"{$this->nombre}\"."
                    . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                    . " Eliminado por: {$userNombre}.";
                break;
        }
    }

    /**
     * Maneja la lógica especial para actualizaciones de documentos
     */
    private function handleDocumentoUpdated(Activity $activity, $user, string $userNombre, string $rolUser, string $enteNombre, string $periodoInfo): void
    {
        $dirty = $this->getDirty();
        $original = $this->getOriginal();

        // Obtener nombres de estados
        $estadoNuevoNombre = $this->estado?->nombre ?? 'Desconocido';
        $estadoAnteriorId = $original['estado_id'] ?? null;
        $estadoAnteriorNombre = $estadoAnteriorId ? (Estado::find($estadoAnteriorId)?->nombre ?? 'Sin estado') : 'Sin estado';

        if (isset($dirty['estado_id'])) {
            if ($this->estado_id == 3) {
                // ── APROBACIÓN ──
                $activity->log_name = 'Aprobación de documento';
                $observaciones = $this->observaciones_revisor ? " Observaciones del revisor: \"{$this->observaciones_revisor}\"." : '';

                $activity->description = "Se APROBÓ el documento \"{$this->nombre}\"."
                    . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                    . " Estado anterior: \"{$estadoAnteriorNombre}\" → Nuevo estado: \"{$estadoNuevoNombre}\"."
                    . " Revisado y aprobado por: {$userNombre} (rol: {$rolUser}).{$observaciones}";

            } elseif ($this->estado_id == 4) {
                // ── RECHAZO ──
                $activity->log_name = 'Rechazo de documento';
                $causa = $this->causaRechazo?->descripcion ?? 'Sin causa especificada';
                $observaciones = $this->observaciones_revisor ? " Observaciones del revisor: \"{$this->observaciones_revisor}\"." : '';
                $reenvio = $this->autorizado_reenviar ? 'Sí' : 'No';

                $activity->description = "Se RECHAZÓ el documento \"{$this->nombre}\"."
                    . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                    . " Estado anterior: \"{$estadoAnteriorNombre}\" → Nuevo estado: \"{$estadoNuevoNombre}\"."
                    . " Causa del rechazo: \"{$causa}\". Reenvío autorizado: {$reenvio}."
                    . " Revisado por: {$userNombre} (rol: {$rolUser}).{$observaciones}";

            } else {
                // ── OTRO CAMBIO DE ESTADO ──
                $activity->log_name = 'Actualización de documento';
                $activity->description = "Se cambió el estado del documento \"{$this->nombre}\"."
                    . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                    . " Estado anterior: \"{$estadoAnteriorNombre}\" → Nuevo estado: \"{$estadoNuevoNombre}\"."
                    . " Actualizado por: {$userNombre}.";
            }
        } else {
            // ── CAMBIO SIN ESTADO (otros campos) ──
            $activity->log_name = 'Actualización de documento';
            $cambios = $this->buildDocCambiosTexto($activity);
            $activity->description = "Se modificaron datos del documento \"{$this->nombre}\"."
                . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                . " Cambios: {$cambios}."
                . " Actualizado por: {$userNombre}.";
        }
    }

    /**
     * Obtiene la información del período del documento
     */
    private function getPeriodoInfo(): string
    {
        $docRecibido = $this->documentoRecibido;
        if ($docRecibido) {
            $periodo = $docRecibido->periodo ?? null;
            if ($periodo) {
                return " Período: {$periodo->mes} {$periodo->axo}.";
            }
        }
        return '';
    }

    /**
     * Construye texto legible de los cambios del documento
     */
    private function buildDocCambiosTexto(Activity $activity): string
    {
        $old = $activity->properties['old'] ?? [];
        $attributes = $activity->properties['attributes'] ?? [];

        $labels = [
            'nombre'               => 'Nombre del archivo',
            'estado_id'            => 'Estado',
            'observaciones_ente'   => 'Observaciones del ente',
            'observaciones_revisor' => 'Observaciones del revisor',
            'causas_rechazo_id'    => 'Causa de rechazo',
            'autorizado_reenviar'  => 'Autorizado reenviar',
            'tipo_recepcion'       => 'Tipo de recepción',
        ];

        $cambios = [];
        foreach ($attributes as $campo => $nuevoValor) {
            $valorAnterior = $old[$campo] ?? 'vacío';
            $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
            $cambios[] = "{$label}: \"{$valorAnterior}\" → \"{$nuevoValor}\"";
        }

        return implode('; ', $cambios) ?: 'Sin cambios relevantes';
    }

    // ── Relationships ────────────────────────────────────────

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
