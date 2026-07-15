<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens;
    use LogsActivity;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active', 'ente_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $ejecutor = Auth::user();
        $ejecutorNombre = $ejecutor ? "\"{$ejecutor->name}\" ({$ejecutor->email})" : 'el sistema';

        // Agregar IP, user_agent y contexto
        $props = $activity->properties->toArray();
        $props['ip'] = request()->ip();
        $props['user_agent'] = request()->userAgent();
        $props['realizado_por'] = $ejecutor?->name ?? 'Sistema';
        $activity->properties = collect($props);

        $rolNombre = $this->roles->first()?->name ?? 'Sin rol asignado';
        $enteNombre = $this->ente?->nombre ?? 'Sin ente';

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Creación de usuario';
                $activity->description = "Se registró un nuevo usuario en el sistema."
                    . " Nombre: \"{$this->name}\". Cuenta de acceso: {$this->email}."
                    . " Rol asignado: {$rolNombre}. Ente/Dependencia: {$enteNombre}."
                    . " Creado por: {$ejecutorNombre}.";
                break;

            case 'updated':
                $original = $this->getOriginal();
                $cambiosTexto = $this->buildUserCambiosTexto($activity, $original);

                // Detectar activación/desactivación
                $dirty = $this->getDirty();
                if (isset($dirty['is_active'])) {
                    $status = $this->is_active ? 'ACTIVÓ' : 'DESACTIVÓ';
                    $activity->log_name = $this->is_active ? 'Activación de usuario' : 'Desactivación de usuario';
                    $activity->description = "Se {$status} la cuenta del usuario \"{$this->name}\" (cuenta: {$this->email})."
                        . " Ente: {$enteNombre}."
                        . " Acción realizada por: {$ejecutorNombre}.";
                } else {
                    $activity->log_name = 'Actualización de usuario';
                    $activity->description = "Se modificaron los datos del usuario \"{$this->name}\" (cuenta: {$this->email})."
                        . " Cambios: {$cambiosTexto}."
                        . " Modificado por: {$ejecutorNombre}.";
                }
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de usuario';
                $activity->description = "Se eliminó permanentemente el usuario \"{$this->name}\" (cuenta: {$this->email})."
                    . " Rol: {$rolNombre}. Ente: {$enteNombre}."
                    . " Eliminado por: {$ejecutorNombre}.";
                break;
        }
    }

    /**
     * Construye texto legible de los cambios del usuario
     */
    private function buildUserCambiosTexto(Activity $activity, array $original): string
    {
        $old = $activity->properties['old'] ?? [];
        $attributes = $activity->properties['attributes'] ?? [];

        $labels = [
            'name'      => 'Nombre',
            'email'     => 'Correo electrónico',
            'is_active' => 'Estado',
            'ente_id'   => 'Ente asignado',
        ];

        $cambios = [];
        foreach ($attributes as $campo => $nuevoValor) {
            $valorAnterior = $old[$campo] ?? 'vacío';
            $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));

            if ($campo === 'is_active') {
                $valorAnterior = $valorAnterior ? 'Activo' : 'Inactivo';
                $nuevoValor = $nuevoValor ? 'Activo' : 'Inactivo';
            }

            if ($campo === 'ente_id') {
                $valorAnterior = Ente::find($valorAnterior)?->nombre ?? 'Sin ente';
                $nuevoValor = Ente::find($nuevoValor)?->nombre ?? 'Sin ente';
            }

            $cambios[] = "{$label}: \"{$valorAnterior}\" → \"{$nuevoValor}\"";
        }

        return implode('; ', $cambios) ?: 'Sin cambios relevantes';
    }

    // ── Relationships ────────────────────────────────────────

    /**
     * Relación: Un usuario (EnteObligado) pertenece a un Ente
     * Asumiendo que la tabla users tiene un campo ente_id
     */
    public function ente()
    {
        return $this->belongsTo(Ente::class);
    }

    /**
     * Relación inversa: Un usuario puede tener muchos avisos a través del ente
     * Útil para consultas rápidas
     */
    public function avisosPendientes()
    {
        if (!$this->ente) {
            return collect();
        }

        return AvisoEnte::with('aviso')
            ->where('ente_id', $this->ente->id)
            ->where('estado_envio', '!=', 'leido')
            ->whereHas('aviso', function ($query) {
                $query->where('activo', true)
                    ->where(function ($q) {
                        $q->whereNull('fecha_expiracion')
                            ->orWhere('fecha_expiracion', '>', now());
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Scope para obtener solo usuarios que son EnteObligado
     */
    public function scopeEntesObligados($query)
    {
        return $query->role('EnteObligado');
    }

    // app/Models/User.php - Agrega esta relación
    public function entesAsignados()
    {
        return $this->belongsToMany(Ente::class, 'entes_revisor', 'revisor_id', 'ente_id')
            ->withTimestamps();
    }

     /**
     * Relación con los entes que revisa (como revisor)
     */
    public function entesRevisados()
    {
        return $this->hasMany(EnteRevisor::class, 'revisor_id');
    }

    /**
     * Scope para obtener solo usuarios que son revisores
     * Ajusta según tu lógica de roles
     */
    public function scopeRevisores($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->where('name', 'Revisor');
        })->orWhere('tipo', 'Revisor');
    }
}
