<?php

namespace App\Http\Controllers\Publico;

use App\Enums\TurnoEstado;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Recurso;
use App\Models\Servicio;
use App\Models\Turno;
use App\Services\Pagos\MercadoPagoService;
use App\Services\Turnos\DisponibilidadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservaController extends Controller
{
    public function create(Empresa $empresa)
    {
        abort_unless($empresa->activo, 404);

        return view('publico.reservar', [
            'empresa' => $empresa,
            'servicios' => $empresa->servicios()->where('activo', true)->orderBy('nombre')->get(),
            'fecha' => now()->toDateString(),
        ]);
    }

    public function store(
        Request $request,
        Empresa $empresa,
        DisponibilidadService $disponibilidadService,
    ) {
        abort_unless($empresa->activo, 404);

        $validated = $request->validate([
            'website' => 'nullable|max:0',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'servicio_id' => 'required|integer|exists:servicios,id',
            'recurso_id' => 'required|integer|exists:recursos,id',
            'fecha_hora_inicio' => 'required|date|after:now',
        ]);

        $servicio = Servicio::query()
            ->where('id', $validated['servicio_id'])
            ->where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->firstOrFail();

        $recurso = Recurso::query()
            ->where('id', $validated['recurso_id'])
            ->where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->firstOrFail();

        abort_unless($servicio->recursos()->where('recursos.id', $recurso->id)->exists(), 422);

        $fechaInicio = Carbon::parse($validated['fecha_hora_inicio']);
        $fechaFin = $fechaInicio->copy()->addMinutes($servicio->duracion_minutos);

        if (! $disponibilidadService->estaDisponible($recurso->id, $fechaInicio, $fechaFin)) {
            return back()->withErrors([
                'fecha_hora_inicio' => 'El horario seleccionado ya no esta disponible.',
            ])->withInput();
        }

        $cliente = Cliente::firstOrCreate(
            [
                'empresa_id' => $empresa->id,
                'telefono' => $validated['telefono'],
            ],
            [
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email' => $validated['email'] ?? null,
                'activo' => true,
            ]
        );

        $turno = Turno::create([
            'empresa_id' => $empresa->id,
            'cliente_id' => $cliente->id,
            'servicio_id' => $servicio->id,
            'recurso_id' => $recurso->id,
            'fecha_hora_inicio' => $fechaInicio,
            'fecha_hora_fin' => $fechaFin,
            'estado' => TurnoEstado::PENDIENTE_PAGO->value,
            'origen' => 'web_publica',
            'token_publico_reserva' => Str::random(40),
            'fecha_vencimiento_pago' => now()->addMinutes(30),
            'precio_final' => $servicio->precio,
            'pago_proveedor' => 'mercadopago',
        ]);

        $mpService = MercadoPagoService::for($empresa);

        if ($mpService->isConfigured()) {
            $preference = $mpService->createPreference($turno);
            $turno->update([
                'pago_preference_id' => $preference['preference_id'],
                'pago_init_point' => $preference['init_point'],
                'pago_status' => 'pending',
            ]);

            if (! empty($preference['init_point'])) {
                return redirect()->away($preference['init_point']);
            }
        }

        return redirect()->route('publico.reserva.resultado', [
            'token' => $turno->token_publico_reserva,
            'estado' => 'pending',
        ]);
    }

    public function resultado(Request $request, string $token)
    {
        $turno = Turno::with(['empresa', 'servicio', 'cliente', 'recurso'])
            ->where('token_publico_reserva', $token)
            ->firstOrFail();

        return view('publico.resultado-reserva', [
            'turno' => $turno,
            'estado' => $request->string('estado')->toString() ?: 'pending',
        ]);
    }
}
