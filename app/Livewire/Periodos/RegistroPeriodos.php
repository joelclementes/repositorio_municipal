<?php

namespace App\Livewire\Periodos;

use App\Models\Ente;
use App\Models\Periodo;
use App\Models\PeriodoEnte;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Carbon\Carbon;

class RegistroPeriodos extends Component
{
    public $periodo_id = '';

    public $descripcion = '';
    public $mes_numero = '';
    public $axo = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';

    public $ente_busqueda = '';
    public $ente_id = '';
    public $periodo_ente_id = '';
    public $ente_fecha_inicio = '';
    public $ente_fecha_fin = '';

    public array $meses = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre',
    ];

    public function updatedPeriodoId($value): void
    {
        $this->limpiarEnte();

        if (!$value) {
            $this->limpiarPeriodo();
            return;
        }

        $periodo = Periodo::findOrFail($value);

        $this->descripcion = $periodo->descripcion;
        $this->mes_numero = $periodo->mes_numero;
        $this->axo = $periodo->axo;
        $this->fecha_inicio = optional($periodo->fecha_inicio)->format('Y-m-d');
        $this->fecha_fin = optional($periodo->fecha_fin)->format('Y-m-d');
    }

    public function guardarPeriodo(): void
    {
        $validated = $this->validate([
            'descripcion' => ['required', 'string', 'max:255'],
            'mes_numero' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('periodos', 'mes_numero')
                    ->where(fn($query) => $query->where('axo', $this->axo))
                    ->ignore($this->periodo_id ?: null),
            ],
            'axo' => ['required', 'integer', 'min:2000', 'max:2100'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
        ], [
            'mes_numero.unique' => 'Ya existe un periodo registrado para ese mes y año.',
            'fecha_fin.after_or_equal' => 'La fecha fin debe ser igual o posterior a la fecha inicio.',
        ]);

        $inicio = \Carbon\Carbon::parse($validated['fecha_inicio']);
        $fin = \Carbon\Carbon::parse($validated['fecha_fin']);

        if (
            (int) $inicio->month !== (int) $validated['mes_numero'] ||
            (int) $fin->month !== (int) $validated['mes_numero'] ||
            (int) $inicio->year !== (int) $validated['axo'] ||
            (int) $fin->year !== (int) $validated['axo']
        ) {
            $this->addError(
                'fecha_inicio',
                'Las fechas de inicio y fin deben pertenecer al mes y año seleccionados.'
            );

            $this->addError(
                'fecha_fin',
                'Las fechas de inicio y fin deben pertenecer al mes y año seleccionados.'
            );

            return;
        }

        DB::transaction(function () use ($validated) {
            $periodo = Periodo::updateOrCreate(
                ['id' => $this->periodo_id ?: null],
                [
                    'descripcion' => $validated['descripcion'],
                    'mes_numero' => $validated['mes_numero'],
                    'mes' => $this->meses[(int) $validated['mes_numero']],
                    'axo' => $validated['axo'],
                    'fecha_inicio' => $validated['fecha_inicio'],
                    'fecha_fin' => $validated['fecha_fin'],
                    'is_active' => true,
                ]
            );

            if ($this->periodo_id) {
                // PeriodoEnte::where('periodo_id', $periodo->id)->update([
                //     'fecha_inicio' => $validated['fecha_inicio'],
                //     'fecha_fin' => $validated['fecha_fin'],
                // ]);
            } else {
                Ente::query()->select('id')->chunkById(100, function ($entes) use ($periodo, $validated) {
                    foreach ($entes as $ente) {
                        PeriodoEnte::firstOrCreate(
                            [
                                'ente_id' => $ente->id,
                                'periodo_id' => $periodo->id,
                            ],
                            [
                                'fecha_inicio' => $validated['fecha_inicio'],
                                'fecha_fin' => $validated['fecha_fin'],
                                'is_active' => true,
                            ]
                        );
                    }
                });
            }
        });

        session()->flash(
            'success',
            $this->periodo_id
                ? 'Periodo actualizado correctamente.'
                : 'Periodo creado correctamente con sus entes relacionados.'
        );

        $this->resetFormularioCompleto();
    }

    public function seleccionarEnte($enteId): void
    {
        if (!$this->periodo_id) {
            session()->flash('error', 'Primero seleccione un periodo.');
            return;
        }

        $ente = Ente::findOrFail($enteId);

        $periodoEnte = PeriodoEnte::firstOrCreate(
            [
                'periodo_id' => $this->periodo_id,
                'ente_id' => $ente->id,
            ],
            [
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'is_active' => true,
            ]
        );

        $this->ente_id = $ente->id;
        $this->periodo_ente_id = $periodoEnte->id;
        $this->ente_busqueda = $ente->nombre;
        $this->ente_fecha_inicio = optional($periodoEnte->fecha_inicio)->format('Y-m-d');
        $this->ente_fecha_fin = optional($periodoEnte->fecha_fin)->format('Y-m-d');
    }

    public function actualizarPeriodoEnte(): void
    {
        $validated = $this->validate([
            'periodo_ente_id' => ['required', 'exists:periodos_entes,id'],
            'ente_fecha_inicio' => ['required', 'date'],
            'ente_fecha_fin' => ['required', 'date', 'after_or_equal:ente_fecha_inicio'],
        ], [
            'periodo_ente_id.required' => 'Seleccione un organismo.',
            'ente_fecha_inicio.required' => 'La fecha de inicio del organismo es obligatoria.',
            'ente_fecha_fin.required' => 'La fecha de fin del organismo es obligatoria.',
            'ente_fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        ]);

        $periodo = Periodo::findOrFail($this->periodo_id);

        $inicio = Carbon::parse($validated['ente_fecha_inicio']);
        $fin = Carbon::parse($validated['ente_fecha_fin']);

        if (
            $inicio->month != $periodo->mes_numero ||
            $fin->month != $periodo->mes_numero ||
            $inicio->year != $periodo->axo ||
            $fin->year != $periodo->axo
        ) {
            $mensaje = 'Las fechas del organismo deben pertenecer al mes y año del periodo seleccionado.';

            $this->addError('ente_fecha_inicio', $mensaje);
            $this->addError('ente_fecha_fin', $mensaje);

            return;
        }

        PeriodoEnte::where('id', $this->periodo_ente_id)->update([
            'fecha_inicio' => $validated['ente_fecha_inicio'],
            'fecha_fin' => $validated['ente_fecha_fin'],
        ]);

        session()->flash('success', 'Fechas del organismo actualizadas correctamente.');

        $this->limpiarEnte();
    }

    public function limpiarPeriodo(): void
    {
        $this->descripcion = '';
        $this->mes_numero = '';
        $this->axo = '';
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
    }

    public function limpiarEnte(): void
    {
        $this->ente_busqueda = '';
        $this->ente_id = '';
        $this->periodo_ente_id = '';
        $this->ente_fecha_inicio = '';
        $this->ente_fecha_fin = '';
    }

    public function resetFormularioCompleto(): void
    {
        $this->periodo_id = '';
        $this->limpiarPeriodo();
        $this->limpiarEnte();
    }

    public function getEntesFiltradosProperty()
    {
        if (!$this->periodo_id || strlen(trim($this->ente_busqueda)) < 2 || $this->ente_id) {
            return collect();
        }

        return Ente::query()
            ->where('nombre', 'like', '%' . trim($this->ente_busqueda) . '%')
            ->orderBy('nombre')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.periodos.registro-periodos', [
            'periodos' => Periodo::query()
                ->orderBy('axo', 'desc')
                ->orderBy('mes_numero', 'desc')
                ->get(),
        ]);
    }

    public function updatedMesNumero(): void
    {
        $this->generarDescripcion();
    }

    public function updatedAxo(): void
    {
        $this->generarDescripcion();
    }

    private function generarDescripcion(): void
    {
        if ($this->mes_numero && $this->axo && isset($this->meses[(int) $this->mes_numero])) {
            $this->descripcion = ucfirst($this->meses[(int) $this->mes_numero]) . ' ' . $this->axo;
        }
    }
}
