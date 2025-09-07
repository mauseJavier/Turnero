<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmpresaController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $empresas = Empresa::with(['clientes', 'servicios', 'recursos', 'turnos'])
            ->paginate(15);

        return response()->json($empresas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Form data for creating empresa']);
    }

    /**
     * Mostrar todos los servicios de una empresa
     * GET /api/empresas/{empresa}/servicios
     */
    public function servicios($empresaId)
    {
        $empresa = \App\Models\Empresa::with('servicios')->findOrFail($empresaId);
        return response()->json($empresa->servicios);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:empresas,email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'tipo_servicio' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean',
        ]);

        $empresa = Empresa::create($validated);

        return response()->json($empresa, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa)
    {
        $empresa->load(['clientes', 'servicios', 'recursos', 'turnos']);

        return response()->json($empresa);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa)
    {
        return response()->json($empresa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:empresas,email,'.$empresa->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'tipo_servicio' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean',
        ]);

        $empresa->update($validated);

        return response()->json($empresa);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        return response()->json(['message' => 'Empresa eliminada correctamente'], Response::HTTP_NO_CONTENT);
    }
}
