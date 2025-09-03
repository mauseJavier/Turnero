<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Servicio::with(['empresa', 'turnos']);

        if ($request->has('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        $servicios = $query->paginate(15);

        return response()->json($servicios);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Form data for creating servicio']);
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
            'duracion_minutos' => 'required|integer|min:1|max:1440', // máximo 24 horas
            'precio' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);

        $servicio = Servicio::create($validated);

        return response()->json($servicio->load('empresa'), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Servicio $servicio)
    {
        $servicio->load(['empresa', 'turnos.cliente', 'turnos.recurso']);

        return response()->json($servicio);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servicio $servicio)
    {
        return response()->json($servicio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'duracion_minutos' => 'required|integer|min:1|max:1440',
            'precio' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);

        $servicio->update($validated);

        return response()->json($servicio->load('empresa'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servicio $servicio)
    {
        $servicio->delete();

        return response()->json(['message' => 'Servicio eliminado correctamente'], Response::HTTP_NO_CONTENT);
    }
}
