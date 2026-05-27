<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use App\Services\Pagos\MercadoPagoService;
use Illuminate\Http\Request;

class MercadoPagoPreferenceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'turno_id' => 'nullable|integer|exists:turnos,id',
            'token_publico_reserva' => 'nullable|string|max:64',
        ]);

        $turno = null;
        if (! empty($validated['turno_id'])) {
            abort_unless(auth()->check(), 403, 'Autenticación requerida para usar turno_id');
            $turno = Turno::findOrFail($validated['turno_id']);
        }

        if (! $turno && ! empty($validated['token_publico_reserva'])) {
            $turno = Turno::where('token_publico_reserva', $validated['token_publico_reserva'])->firstOrFail();
        }

        abort_if(! $turno, 422, 'Debe indicar turno_id o token_publico_reserva');

        $mercadoPagoService = MercadoPagoService::for($turno->empresa);
        $preference = $mercadoPagoService->createPreference($turno);

        $turno->update([
            'pago_preference_id' => $preference['preference_id'],
            'pago_init_point' => $preference['init_point'],
            'pago_status' => 'pending',
        ]);

        return response()->json([
            'preference_id' => $preference['preference_id'],
            'init_point' => $preference['init_point'],
            'sandbox_init_point' => $preference['sandbox_init_point'],
        ]);
    }
}
