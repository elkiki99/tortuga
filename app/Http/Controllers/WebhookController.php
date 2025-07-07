<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $signature = $request->header('x-signature');
        $requestId = $request->header('x-request-id');
        $secret = config('services.mercadopago.webhook_test');

        if (!$signature || !$requestId) {
            return response()->json(['error' => 'Faltan headers'], 400);
        }

        $parts = explode(',', $signature);
        $ts = null;
        $hash = null;

        foreach ($parts as $part) {
            [$key, $value] = explode('=', $part);
            if (trim($key) === 'ts') $ts = trim($value);
            if (trim($key) === 'v1') $hash = trim($value);
        }

        $dataId = $request->query('data.id') ?? $request->input('data.id');

        if (!$dataId) {
            Log::warning('Webhook sin data.id');
            return response()->json(['error' => 'Falta data.id'], 400);
        }

        $manifest = "id:{$dataId};request-id:{$requestId};ts:{$ts};";
        $generatedHash = hash_hmac('sha256', $manifest, $secret);

        if ($generatedHash !== $hash) {
            Log::warning('Webhook rechazado por firma inválida', compact('manifest', 'generatedHash', 'hash'));
            return response()->json(['error' => 'Firma inválida'], 403);
        }

        Log::info('✅ Webhook recibido y validado', ['data.id' => $dataId]);
        return response()->json(['received' => true], 200);
    }
}
