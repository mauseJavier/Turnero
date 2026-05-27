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

        ProcesarWebhookMercadoPagoJob::dispatch([
            'body' => $request->all(),
            'query' => $request->query(),
            'headers' => $request->headers->all(),
        ]);

        return response()->json(['received' => true]);
    }
}
