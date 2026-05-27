<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Empresa;

class PublicoServiciosController extends Controller
{
    public function index(Empresa $empresa)
    {
        abort_unless($empresa->activo, 404);

        return response()->json(
            $empresa->servicios()
                ->where('activo', true)
                ->with(['recursos' => fn ($q) => $q->where('activo', true)])
                ->orderBy('nombre')
                ->get()
        );
    }
}
