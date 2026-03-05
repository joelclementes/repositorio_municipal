<?php

namespace App\Livewire\Avisos;

use App\Models\AvisoEnte;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class Notificaciones extends Component {
    public $cantidadPendientes = 0;
    public $mostrarDropdown = false;
    public $avisoSeleccionado = null;
    public $mostrarModal = false;
    public $pagina = 1;
    public $cargando = false;
    public $tieneMas = true;

    // Propiedad computada que se actualiza automáticamente
    #[Computed]
    public function avisosPendientes()
    {
        if (!auth()->check()) {
            return [];
        }

        $user = auth()->user();

        if (!$user->hasRole('EnteObligado')) {
            return [];
        }

        $ente = $user->ente;

        if (!$ente) {
            return [];
        }

        return AvisoEnte::with([
            'aviso' => function ($query) {
                $query->with('creador');
            },
        ])
            ->where('ente_id', $ente->id)
            ->where('estado_envio', '!=', 'leido')
            ->whereHas('aviso', function ($query) {
                $query->where('activo', true)->where(function ($q) {
                    $q->whereNull('fecha_expiracion')->orWhere('fecha_expiracion', '>', now());
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'page', $this->pagina)
            ->items();
    }

    public function mount()
    {
        $this->cargarNotificaciones();
    }

    #[On('cargarNotificaciones')]
    public function cargarNotificaciones()
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        if (!$user->hasRole('EnteObligado')) {
            return;
        }

        $ente = $user->ente;

        if ($ente) {
            $this->cantidadPendientes = AvisoEnte::where('ente_id', $ente->id)
                ->where('estado_envio', '!=', 'leido')
                ->whereHas('aviso', function ($query) {
                    $query->where('activo', true)->where(function ($q) {
                        $q->whereNull('fecha_expiracion')->orWhere('fecha_expiracion', '>', now());
                    });
                })
                ->count();

            // Resetear paginación al recargar
            $this->pagina = 1;
            unset($this->avisosPendientes); // Forzar recarga de la propiedad computada
        }
    }

    #[On('cargarMasAvisos')]
    public function cargarMasAvisos()
    {
        if ($this->cargando || !$this->tieneMas) {
            return;
        }

        $this->cargando = true;
        $this->pagina++;

        // Limpiar caché de la propiedad computada
        unset($this->avisosPendientes);

        $this->cargando = false;

        // Verificar si hay más páginas
        $user = auth()->user();
        $ente = $user->ente;

        if ($ente) {
            $total = AvisoEnte::where('ente_id', $ente->id)->where('estado_envio', '!=', 'leido')->count();

            $this->tieneMas = $this->pagina * 15 < $total;
        }
    }

    public function toggleDropdown()
    {
        $this->mostrarDropdown = !$this->mostrarDropdown;
    }

    public function verAviso($avisoEnteId)
    {
        $this->avisoSeleccionado = AvisoEnte::with(['aviso.creador', 'ente'])->find($avisoEnteId);

        if ($this->avisoSeleccionado) {
            $this->mostrarModal = true;
            $this->marcarComoLeido($avisoEnteId);
        }
    }

    #[On('marcarComoLeido')]
    public function marcarComoLeido($avisoEnteId)
    {
        $avisoEnte = AvisoEnte::find($avisoEnteId);

        if ($avisoEnte && $avisoEnte->estado_envio !== 'leido') {
            $avisoEnte->update([
                'estado_envio' => 'leido',
                'fecha_lectura' => now(),
                'leido_por' => auth()->id(),
            ]);

            // Forzar recarga
            $this->pagina = 1;
            unset($this->avisosPendientes);
            $this->cargarNotificaciones();

            $this->dispatch('notificacion-actualizada');
        }
    }

    public function marcarTodosLeidos()
    {
        $user = auth()->user();
        $ente = $user->ente;

        if ($ente) {
            $actualizados = AvisoEnte::where('ente_id', $ente->id)
                ->where('estado_envio', '!=', 'leido')
                ->update([
                    'estado_envio' => 'leido',
                    'fecha_lectura' => now(),
                    'leido_por' => auth()->id(),
                ]);

            // Forzar recarga
            $this->pagina = 1;
            unset($this->avisosPendientes);
            $this->cargarNotificaciones();

            $this->dispatch('notificacion-actualizada');
        }
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->avisoSeleccionado = null;
    }

    public function render()
    {
        return view('livewire.avisos.notificaciones-avisos');
    }
};
?>

