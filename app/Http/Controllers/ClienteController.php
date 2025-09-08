<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClienteController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::with(['empresa', 'turnos']);

        if ($request->has('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        $clientes = $query->paginate(15);

        return response()->json($clientes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Form data for creating cliente']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'documento' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'nullable|date',
            'observaciones' => 'nullable|string|max:1000',
            'activo' => 'boolean',
        ]);

        $cliente = Cliente::create($validated);

        return response()->json($cliente->load('empresa'), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        $cliente->load(['empresa', 'turnos.servicio', 'turnos.recurso']);

        return response()->json($cliente);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return response()->json($cliente);
    }

        /**
     * Buscar cliente por número de teléfono y devolver sus turnos
     * GET /api/clientes/buscar-por-telefono?telefono=...
     */
    public function buscarPorTelefono(Request $request)
    {
        // return response()->json($request->all());

        $validated = $request->validate([
            'telefono' => 'required|string',
        ]);


        $cliente = \App\Models\Cliente::with(['empresa', 'turnos.servicio', 'turnos.recurso'])
            ->where('telefono', $validated['telefono'])
            ->first();

            
        if (! $cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        return response()->json($cliente);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'documento' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'nullable|date',
            'observaciones' => 'nullable|string|max:1000',
            'activo' => 'boolean',
        ]);

        $cliente->update($validated);

        return response()->json($cliente->load('empresa'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json(['message' => 'Cliente eliminado correctamente'], Response::HTTP_NO_CONTENT);
    }
}
