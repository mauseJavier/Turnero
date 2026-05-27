<?php

namespace App\Services\Pagos;

use App\Models\Empresa;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MercadoPagoService
{
    private ?string $accessToken = null;
    private ?string $publicKey = null;
    private ?string $webhookSecret = null;
    private string $baseUrl;
    private string $currency;

    public function __construct(?Empresa $empresa = null)
    {
        $this->baseUrl = rtrim(config('services.mercadopago.base_url', 'https://api.mercadopago.com'), '/');
        $this->currency = config('services.mercadopago.currency', 'ARS');

        if ($empresa && $empresa->mp_access_token) {
            $this->accessToken = $empresa->mp_access_token;
            $this->publicKey = $empresa->mp_public_key;
        } else {
            $this->accessToken = config('services.mercadopago.access_token');
            $this->publicKey = config('services.mercadopago.public_key');
        }

        $this->webhookSecret = config('services.mercadopago.webhook_secret');
    }

    public static function for(?Empresa $empresa): self
    {
        return new self($empresa);
    }

    public function isConfigured(): bool
    {
        return filled($this->accessToken);
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function createPreference(Turno $turno, array $backUrls = []): array
    {
        $notificationUrl = route('api.webhooks.mercadopago').'?source_news=webhooks';

        $payload = [
            'items' => [[
                'id' => (string) $turno->servicio_id,
                'title' => $turno->servicio?->nombre ?? 'Reserva de turno',
                'quantity' => 1,
                'currency_id' => $this->currency,
                'unit_price' => (float) ($turno->precio_final ?? $turno->servicio?->precio ?? 0),
            ]],
            'external_reference' => 'turno:'.$turno->id,
            'notification_url' => $notificationUrl,
            'back_urls' => [
                'success' => $backUrls['success'] ?? route('publico.reserva.resultado', ['token' => $turno->token_publico_reserva, 'estado' => 'success']),
                'failure' => $backUrls['failure'] ?? route('publico.reserva.resultado', ['token' => $turno->token_publico_reserva, 'estado' => 'failure']),
                'pending' => $backUrls['pending'] ?? route('publico.reserva.resultado', ['token' => $turno->token_publico_reserva, 'estado' => 'pending']),
            ],
            'auto_return' => 'approved',
            'metadata' => [
                'turno_id' => $turno->id,
                'empresa_id' => $turno->empresa_id,
            ],
        ];

        $idempotencyKey = 'pref-turno-'.$turno->id;

        $response = Http::asJson()
            ->withToken($this->accessToken)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->post($this->baseUrl.'/checkout/preferences', $payload)
            ->throw()
            ->json();

        return [
            'preference_id' => $response['id'] ?? null,
            'init_point' => $response['init_point'] ?? null,
            'sandbox_init_point' => $response['sandbox_init_point'] ?? null,
            'raw' => $response,
        ];
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $secret = (string) ($this->webhookSecret ?? '');
        if ($secret === '') {
            return true;
        }

        $signatureHeader = (string) $request->header('x-signature', '');
        $requestId = (string) $request->header('x-request-id', '');
        $dataId = (string) ($request->query('data.id') ?? $request->query('id') ?? '');

        if ($signatureHeader === '' || $requestId === '' || $dataId === '') {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $chunk) {
            $kv = explode('=', trim($chunk), 2);
            if (count($kv) === 2) {
                $parts[strtolower($kv[0])] = trim($kv[1]);
            }
        }

        $ts = $parts['ts'] ?? null;
        $v1 = $parts['v1'] ?? null;
        if (! $ts || ! $v1) {
            return false;
        }

        if (ctype_alnum($dataId)) {
            $dataId = strtolower($dataId);
        }

        $manifest = "id:{$dataId};request-id:{$requestId};ts:{$ts};";
        $calculated = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($calculated, $v1);
    }

    public function getPaymentById(string|int $paymentId): array
    {
        return Http::asJson()
            ->withToken($this->accessToken)
            ->get($this->baseUrl.'/v1/payments/'.$paymentId)
            ->throw()
            ->json();
    }
}
