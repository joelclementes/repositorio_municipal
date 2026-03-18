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

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens;

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
