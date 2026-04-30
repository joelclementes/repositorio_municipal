<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodoController extends Controller
{
    private array $meses = [
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

    public function index(Request $request)
    {
        $query = Periodo::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('mes', 'LIKE', "%{$search}%")
                    ->orWhere('axo', 'LIKE', "%{$search}%")
                    ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        $periodos = $query
            ->orderBy('axo', 'desc')
            ->orderBy('mes_numero', 'desc')
            ->paginate(10);

        return view('periodos.registro', [
            'periodos' => $periodos,
            'meses' => $this->meses,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'mes_numero' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('periodos', 'mes_numero')->where(function ($query) use ($request) {
                    return $query->where('axo', $request->anio);
                }),
            ],
            'anio' => 'required|integer|min:2000|max:2100',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'nullable|boolean',
        ], [
            'mes_numero.required' => 'El campo mes es obligatorio.',
            'mes_numero.unique' => 'Ya existe un período registrado para el mes y año seleccionados.',
            'anio.required' => 'El campo año es obligatorio.',
            'descripcion.required' => 'El campo descripción es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        ]);

        $mesNumero = (int) $validatedData['mes_numero'];

        Periodo::create([
            'mes_numero' => $mesNumero,
            'mes' => $this->meses[$mesNumero],
            'axo' => $validatedData['anio'],
            'descripcion' => $validatedData['descripcion'],
            'fecha_inicio' => $validatedData['fecha_inicio'],
            'fecha_fin' => $validatedData['fecha_fin'],
            'is_active' => (bool) ($validatedData['activo'] ?? false),
        ]);

        return redirect()
            ->route('periodos.registro.index')
            ->with('success', 'Período registrado exitosamente.');
    }

    public function toggleStatus($id)
    {
        $periodo = Periodo::findOrFail($id);
        $periodo->is_active = !$periodo->is_active;
        $periodo->save();

        return response()->json([
            'success' => true,
            'status' => $periodo->is_active,
            'message' => 'Estado actualizado correctamente',
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function create()
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Periodo $periodo)
    {
        //
    }
}
