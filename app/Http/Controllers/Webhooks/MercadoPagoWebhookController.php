<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcesarWebhookMercadoPagoJob;
use App\Services\Pagos\MercadoPagoService;
use Illuminate\Http\Request;

class MercadoPagoWebhookController extends Controller
{
    public function __invoke(Request $request, MercadoPagoService $mercadoPagoService)
    {
        if (! $mercadoPagoService->verifyWebhookSignature($request)) {
            return response()->json(['received' => false, 'message' => 'Invalid signature'], 401);
        }

        //loguear el webhook recibido para debug
        \Log::info('Webhook MercadoPago recibido', [
            'body' => $request->all(),
            'query' => $request->query(),
            'headers' => $request->headers->all(),
        ]);

        

        ProcesarWebhookMercadoPagoJob::dispatch([
            'body' => $request->all(),
            'query' => $request->query(),
            'headers' => $request->headers->all(),
            'empresa_id' => $request->query('empresa_id'),
        ]);

        return response()->json(['received' => true]);
    }
}
