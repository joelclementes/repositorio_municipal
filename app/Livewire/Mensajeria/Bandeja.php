<?php

namespace App\Livewire\Mensajeria;

use App\Models\Mensaje;
use App\Models\MensajeArchivo;
use App\Models\MensajeDestinatario;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Bandeja extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $tipo = 'recibidos';

    public string $estado = 'todos';

    public string $buscar = '';

    public ?string $fechaInicio = null;

    public ?string $fechaFin = null;

    public int $perPage = 10;

    public bool $modalRedactar = false;

    public bool $modalLeer = false;

    public bool $mostrarRespuesta = false;

    public array $destinatarios = [];

    public string $asunto = '';

    public string $cuerpo = '';

    public array $archivos = [];

    public ?int $mensajeSeleccionadoId = null;

    public string $respuestaCuerpo = '';

    public array $respuestaArchivos = [];

    protected function rules(): array
    {
        return [
            'destinatarios' => ['required', 'array', 'min:1'],
            'destinatarios.*' => ['integer', 'exists:users,id'],
            'asunto' => ['required', 'string', 'max:255'],
            'cuerpo' => ['required', 'string'],
            'archivos.*' => ['nullable', 'file', 'mimes:pdf,xlsx', 'max:10240'],
        ];
    }

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function updatedTipo(): void
    {
        $this->resetPage();
    }

    public function updatedEstado(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function abrirRedactar(): void
    {
        $this->reset(['destinatarios', 'asunto', 'cuerpo', 'archivos']);
        $this->modalRedactar = true;
    }

    public function cerrarRedactar(): void
    {
        $this->modalRedactar = false;
        $this->resetValidation();
    }

    public function enviar(): void
    {
        $this->validate();

        DB::transaction(function () {
            $mensaje = Mensaje::create([
                'remitente_id' => auth()->id(),
                'asunto' => $this->asunto,
                'cuerpo' => $this->cuerpo,
            ]);

            $mensaje->update([
                'mensaje_raiz_id' => $mensaje->id,
            ]);

            foreach ($this->destinatarios as $destinatarioId) {
                MensajeDestinatario::create([
                    'mensaje_id' => $mensaje->id,
                    'destinatario_id' => $destinatarioId,
                    'estado' => 'no_leido',
                ]);
            }

            $this->guardarArchivos($mensaje, $this->archivos);
        });

        $this->cerrarRedactar();
        session()->flash('success', 'Mensaje enviado correctamente.');
    }

    public function abrirMensaje(int $mensajeId): void
    {
        $this->mensajeSeleccionadoId = $mensajeId;
        $this->modalLeer = true;
        $this->mostrarRespuesta = false;
        $this->respuestaCuerpo = '';
        $this->respuestaArchivos = [];

        MensajeDestinatario::where('mensaje_id', $mensajeId)
            ->where('destinatario_id', auth()->id())
            ->where('estado', 'no_leido')
            ->update([
                'estado' => 'leido',
                'leido_at' => now(),
            ]);
    }

    public function cerrarLeer(): void
    {
        $this->modalLeer = false;
        $this->mensajeSeleccionadoId = null;
    }

    public function mostrarFormularioRespuesta(): void
    {
        $this->mostrarRespuesta = true;
    }

    public function responder(): void
    {
        $this->validate([
            'respuestaCuerpo' => ['required', 'string'],
            'respuestaArchivos.*' => ['nullable', 'file', 'mimes:pdf,xlsx', 'max:10240'],
        ]);

        $mensajePadre = Mensaje::findOrFail($this->mensajeSeleccionadoId);

        DB::transaction(function () use ($mensajePadre) {
            $mensaje = Mensaje::create([
                'mensaje_raiz_id' => $mensajePadre->mensaje_raiz_id ?: $mensajePadre->id,
                'mensaje_padre_id' => $mensajePadre->id,
                'remitente_id' => auth()->id(),
                'asunto' => str_starts_with($mensajePadre->asunto, 'Re:')
                    ? $mensajePadre->asunto
                    : 'Re: '.$mensajePadre->asunto,
                'cuerpo' => $this->respuestaCuerpo,
            ]);

            foreach ($this->destinatariosRespuesta($mensajePadre) as $destinatarioId) {
                MensajeDestinatario::create([
                    'mensaje_id' => $mensaje->id,
                    'destinatario_id' => $destinatarioId,
                    'estado' => 'no_leido',
                ]);
            }

            $this->guardarArchivos($mensaje, $this->respuestaArchivos);
        });

        $this->mostrarRespuesta = false;
        $this->respuestaCuerpo = '';
        $this->respuestaArchivos = [];

        session()->flash('success', 'Respuesta enviada correctamente.');
    }

    private function destinatariosRespuesta(Mensaje $mensajePadre): array
    {
        $ids = [$mensajePadre->remitente_id];

        $otros = MensajeDestinatario::where('mensaje_id', $mensajePadre->id)
            ->pluck('destinatario_id')
            ->toArray();

        return collect(array_merge($ids, $otros))
            ->unique()
            ->reject(fn ($id) => (int) $id === (int) auth()->id())
            ->values()
            ->toArray();
    }

    private function guardarArchivos(Mensaje $mensaje, array $archivos): void
    {
        foreach ($archivos as $archivo) {
            $ruta = $archivo->store("mensajes/{$mensaje->id}", 'public');

            MensajeArchivo::create([
                'mensaje_id' => $mensaje->id,
                'nombre_original' => $archivo->getClientOriginalName(),
                'ruta' => $ruta,
                'mime_type' => $archivo->getMimeType(),
                'extension' => $archivo->getClientOriginalExtension(),
                'size' => $archivo->getSize(),
            ]);
        }
    }

    public function getUsuariosDestinoProperty()
    {
        $user = auth()->user();

        $rolesAdministrativos = [
            'SuperUsuario',
            'Administrador',
        ];

        $rolesEnte = [
            'Tesorero',
            'Tesorero Organo Descentralizado',
            'Director Obras Publicas',
            'Contralor',
        ];

        if ($user->hasAnyRole($rolesAdministrativos)) {
            return User::role($rolesEnte)
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        }

        return User::role($rolesAdministrativos)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();
    }

    public function getMensajeSeleccionadoProperty()
    {
        if (! $this->mensajeSeleccionadoId) {
            return null;
        }

        return Mensaje::with(['remitente', 'destinatarios.destinatario', 'archivos'])
            ->find($this->mensajeSeleccionadoId);
    }

    public function getHiloProperty()
    {
        if (! $this->mensajeSeleccionado) {
            return collect();
        }

        $raizId = $this->mensajeSeleccionado->mensaje_raiz_id ?: $this->mensajeSeleccionado->id;

        return Mensaje::with(['remitente', 'destinatarios.destinatario', 'archivos'])
            ->where('mensaje_raiz_id', $raizId)
            ->orderBy('created_at')
            ->get();
    }

    public function render()
    {
        $userId = auth()->id();

        $mensajes = Mensaje::query()
            ->with(['remitente', 'archivos', 'destinatarios'])
            ->when($this->tipo === 'recibidos', function ($query) use ($userId) {
                $query->whereHas('destinatarios', function ($q) use ($userId) {
                    $q->where('destinatario_id', $userId);
                });
            })
            ->when($this->tipo === 'enviados', function ($query) use ($userId) {
                $query->where('remitente_id', $userId);
            })
            ->when($this->estado === 'leidos', function ($query) use ($userId) {
                $query->whereHas('destinatarios', function ($q) use ($userId) {
                    $q->where('destinatario_id', $userId)
                        ->where('estado', 'leido');
                });
            })
            ->when($this->estado === 'no_leidos', function ($query) use ($userId) {
                $query->whereHas('destinatarios', function ($q) use ($userId) {
                    $q->where('destinatario_id', $userId)
                        ->where('estado', 'no_leido');
                });
            })
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('asunto', 'like', '%'.$this->buscar.'%')
                        ->orWhere('cuerpo', 'like', '%'.$this->buscar.'%');
                });
            })
            ->when($this->fechaInicio, function ($query) {
                $query->whereDate('created_at', '>=', $this->fechaInicio);
            })
            ->when($this->fechaFin, function ($query) {
                $query->whereDate('created_at', '<=', $this->fechaFin);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.mensajeria.bandeja', [
            'mensajes' => $mensajes,
        ]);
    }
}
