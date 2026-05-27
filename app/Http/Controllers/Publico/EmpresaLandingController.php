<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Empresa;

class EmpresaLandingController extends Controller
{
    public function show(Empresa $empresa)
    {
        abort_unless($empresa->activo, 404);

        $servicios = $empresa->servicios()->where('activo', true)->orderBy('nombre')->get();

        return view('publico.empresa-landing', [
            'empresa' => $empresa,
            'servicios' => $servicios,
        ]);
    }
}
