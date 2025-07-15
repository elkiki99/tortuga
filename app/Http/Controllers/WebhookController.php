<?php

// namespace App\Http\Controllers;

// use Illuminate\Support\Facades\Log;
// use Illuminate\Http\Request;

// class WebhookController extends Controller
// {
//     public function __invoke(Request $request)
//     {
//         $signature = $request->header('x-signature');
//         $requestId = $request->header('x-request-id');
//         $secret = config('services.mercadopago.webhook_test');

//         if (!$signature || !$requestId) {
//             return response()->json(['error' => 'Faltan headers'], 400);
//         }

//         $parts = explode(',', $signature);
//         $ts = null;
//         $hash = null;

//         foreach ($parts as $part) {
//             [$key, $value] = explode('=', $part);
//             if (trim($key) === 'ts') $ts = trim($value);
//             if (trim($key) === 'v1') $hash = trim($value);
//         }

//         $dataId = $request->query('data.id') ?? $request->input('data.id');

//         if (!$dataId) {
//             Log::warning('Webhook sin data.id');
//             return response()->json(['error' => 'Falta data.id'], 400);
//         }

//         $manifest = "id:{$dataId};request-id:{$requestId};ts:{$ts};";
//         $generatedHash = hash_hmac('sha256', $manifest, $secret);

//         if ($generatedHash !== $hash) {
//             Log::warning('Webhook rechazado por firma inválida', compact('manifest', 'generatedHash', 'hash'));
//             return response()->json(['error' => 'Firma inválida'], 403);
//         }

//         Log::info('✅ Webhook recibido y validado', ['data.id' => $dataId]);
//         return response()->json(['received' => true], 200);
//     }
// }

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $type = $request->input('type');
        $action = $request->input('action');
        $signature = $request->header('x-signature');
        $requestId = $request->header('x-request-id');
        $secret = config('services.mercadopago.webhook_test');

        Log::info('🔔 Webhook recibido', [
            'type' => $type,
            'action' => $action,
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        // 🔒 Validación solo para eventos tipo "payment"
        if ($type === 'payment') {
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
                Log::warning('⚠️ Payment webhook sin data.id', ['body' => $request->all()]);
                return response()->json(['error' => 'Falta data.id en payment'], 400);
            }

            $manifest = "id:{$dataId};request-id:{$requestId};ts:{$ts};";
            $generatedHash = hash_hmac('sha256', $manifest, $secret);

            if ($generatedHash !== $hash) {
                Log::warning('❌ Firma inválida', compact('manifest', 'generatedHash', 'hash'));
                return response()->json(['error' => 'Firma inválida'], 403);
            }

            Log::info('✅ Webhook de payment validado correctamente', ['data.id' => $dataId]);
            // Aquí podrías despachar un job o evento con $dataId
        } else {
            Log::info('ℹ️ Webhook de tipo no manejado (por ahora), aceptado para evitar reintentos', compact('type', 'action'));
        }

        return response()->json(['received' => true], 200);
    }
}
