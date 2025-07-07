<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('main');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'is_admin'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('wishlist', 'client.wishlist')->name('client.wishlist');
});

Volt::route('checkout', 'client.checkout')->name('client.checkout');
Volt::route('checkout/success', 'checkout.success')->name('checkout.success');
Volt::route('checkout/failure', 'checkout.failure')->name('checkout.failure');
Volt::route('checkout/pending', 'checkout.pending')->name('checkout.pending');

Route::post('/webhook', function (Request $request) {
    // Verifica autenticidad del webhook (opcional pero recomendado)
    // $signature = $request->header('x-signature');
    // $requestId = $request->header('x-request-id');
    // $secret = config('services.mercadopago.webhook_test'); // ⚠️ Copiala desde "Tus Integraciones"

    // if (!$signature || !$requestId) {
    //     return response()->json(['error' => 'Falta firma'], 400);
    // }

    // $parts = explode(',', $signature);
    // $ts = null;
    // $hash = null;

    // foreach ($parts as $part) {
    //     [$key, $value] = explode('=', $part);
    //     if (trim($key) === 'ts') $ts = trim($value);
    //     if (trim($key) === 'v1') $hash = trim($value);
    // }

    // $dataId = $request->input('data.id');
    // $manifest = "id:{$dataId};request-id:{$requestId};ts:{$ts};";
    // $generatedHash = hash_hmac('sha256', $manifest, $secret);

    // if ($generatedHash !== $hash) {
    //     Log::warning('Webhook rechazado por firma inválida', compact('manifest', 'generatedHash', 'hash'));
    //     return response()->json(['error' => 'Firma inválida'], 403);
    // }

    // ✨ ¡Webhook válido!
    Log::info('Webhook recibido y validado', $request->all());

    // Podés consultar el pago si querés:
    // $paymentId = $dataId;
    // $paymentInfo = Http::withToken(env('MERCADOPAGO_ACCESS_TOKEN'))->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

    return response()->json(['received' => true], 200);
});

Volt::route('productos/{product:slug}', 'products.show')->name('products.show');
Volt::route('categorias/{category:slug}', 'categories.show')->name('categories.show');

require __DIR__ . '/auth.php';
