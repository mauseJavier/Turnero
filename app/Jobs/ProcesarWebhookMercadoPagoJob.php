<?php

namespace App\Jobs;

use App\Enums\TurnoEstado;
use App\Mail\TurnoConfirmadoAdmin;
use App\Mail\TurnoConfirmadoCliente;
use App\Models\Turno;
use App\Services\Pagos\MercadoPagoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcesarWebhookMercadoPagoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $payload)
    {
    }

    public function handle(MercadoPagoService $mercadoPagoService): void
    {
        $data = $this->payload['body'] ?? [];
        $query = $this->payload['query'] ?? [];

        $type = Arr::get($data, 'type') ?? Arr::get($query, 'type') ?? Arr::get($query, 'topic');
        $paymentId = Arr::get($data, 'data.id') ?? Arr::get($query, 'data_id') ?? Arr::get($data, 'id') ?? Arr::get($query, 'id');

        if ($type !== 'payment' || empty($paymentId)) {
            Log::info('Webhook Mercado Pago ignorado', ['type' => $type, 'payment_id' => $paymentId]);
            return;
        }

        $payment = $mercadoPagoService->getPaymentById((string) $paymentId);
        $externalReference = Arr::get($payment, 'external_reference');

        if (! str_starts_with((string) $externalReference, 'turno:')) {
            Log::warning('Pago sin external_reference de turno', ['payment_id' => $paymentId]);
            return;
        }

        $turnoId = (int) str_replace('turno:', '', (string) $externalReference);
        $turno = Turno::find($turnoId);

        if (! $turno) {
            Log::warning('Turno no encontrado para pago', ['turno_id' => $turnoId, 'payment_id' => $paymentId]);
            return;
        }

        $status = Arr::get($payment, 'status');
        $statusDetail = Arr::get($payment, 'status_detail');
        $monto = Arr::get($payment, 'transaction_amount');
        $currency = Arr::get($payment, 'currency_id');

        $update = [
            'pago_id' => (string) $paymentId,
            'pago_status' => $status,
            'pago_status_detail' => $statusDetail,
            'monto_pagado' => $monto,
            'moneda' => $currency,
        ];

        $wasConfirmed = $turno->estado === TurnoEstado::CONFIRMADO->value;

        if ($status === 'approved') {
            $update['estado'] = TurnoEstado::CONFIRMADO->value;
            $update['pagado_at'] = now();
        }

        $turno->update($update);

        if (! $wasConfirmed && $status === 'approved') {
            $turno->loadMissing(['empresa', 'cliente', 'servicio', 'recurso']);

            if (filled($turno->cliente?->email)) {
                Mail::to($turno->cliente->email)->queue(new TurnoConfirmadoCliente($turno));
            }

            $adminMails = $turno->empresa->usuarios()
                ->whereNotNull('email')
                ->pluck('email')
                ->all();

            foreach ($adminMails as $adminMail) {
                Mail::to($adminMail)->queue(new TurnoConfirmadoAdmin($turno));
            }
        }
    }
}
