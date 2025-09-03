<?php

namespace App\Http\Controllers;

use App\Models\Recurso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Recurso::with(['empresa', 'turnos']);

        if ($request->has('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $recursos = $query->paginate(15);

        return response()->json($recursos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Form data for creating recurso']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'tipo' => 'required|string|max:100',
            'capacidad' => 'required|integer|min:1',
            'activo' => 'boolean',
        ]);

        $recurso = Recurso::create($validated);

        return response()->json($recurso->load('empresa'), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recurso $recurso)
    {
        $recurso->load(['empresa', 'turnos.cliente', 'turnos.servicio']);

        return response()->json($recurso);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recurso $recurso)
    {
        return response()->json($recurso);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recurso $recurso)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'tipo' => 'required|string|max:100',
            'capacidad' => 'required|integer|min:1',
            'activo' => 'boolean',
        ]);

        $recurso->update($validated);

        return response()->json($recurso->load('empresa'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recurso $recurso)
    {
        $recurso->delete();

        return response()->json(['message' => 'Recurso eliminado correctamente'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Verificar disponibilidad de un recurso
     */
    public function verificarDisponibilidad(Request $request, Recurso $recurso)
    {
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'excluir_turno_id' => 'nullable|exists:turnos,id',
        ]);

        // Validation to ensure fecha_fin is after fecha_inicio
        if (strtotime($validated['fecha_fin']) <= strtotime($validated['fecha_inicio'])) {
            return response()->json([
                'message' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                'errors' => ['fecha_fin' => ['La fecha de fin debe ser posterior a la fecha de inicio']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $disponible = $recurso->estaDisponible(
            $validated['fecha_inicio'],
            $validated['fecha_fin'],
            $validated['excluir_turno_id'] ?? null
        );

        return response()->json(['disponible' => $disponible]);
    }
}
