<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Empresa;

class EmpresaIndexController extends Controller
{
    public function index()
    {
        $empresas = Empresa::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('publico.index', [
            'empresas' => $empresas,
        ]);
    }
}
