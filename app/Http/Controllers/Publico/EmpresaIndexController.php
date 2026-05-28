<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Servicio;

class EmpresaIndexController extends Controller
{
    public function index()
    {
        $empresas = Empresa::where('activo', true)
            ->withCount('servicios')
            ->orderBy('nombre')
            ->get();

        $stats = [
            'empresas' => $empresas->count(),
            'servicios' => Servicio::whereHas('empresa', fn($q) => $q->where('activo', true))
                ->where('activo', true)
                ->count(),
        ];

        return view('publico.index', compact('empresas', 'stats'));
    }
}
