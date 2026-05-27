<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Services\Turnos\DisponibilidadService;
use Illuminate\Http\Request;

class PublicoDisponibilidadController extends Controller
{
    public function index(Request $request, Empresa $empresa, DisponibilidadService $disponibilidadService)
    {
        abort_unless($empresa->activo, 404);

        $validated = $request->validate([
            'fecha' => 'required|date',
            'servicio_id' => 'nullable|integer|exists:servicios,id',
        ]);

        return response()->json(
            $disponibilidadService->listarPorServicio(
                $empresa,
                $validated['fecha'],
                $validated['servicio_id'] ?? null
            )
        );
    }
}
