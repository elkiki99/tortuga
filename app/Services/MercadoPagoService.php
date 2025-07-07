<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        // MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL); // only if using localhost, else SERVER
    }

    public function createPreference(array $items, array $payer)
    {
        $request = [
            'items' => $items,
            'payer' => $payer,
            'payment_methods' => [
                "excluded_payment_methods" => [],
                "installments" => 12,
                "default_installments" => 1
            ],
            'back_urls' => [
                'success' => route('checkout.success'),
                'failure' => route('checkout.failure'),
                'pending' => route('checkout.pending'),
            ],
            'statement_descriptor' => config('app.name'),
            'external_reference' => uniqid(),
            'expires' => false,
            'notification_url' => route('webhook'),
            'auto_return' => 'approved',
        ];

        $client = new PreferenceClient();

        try {
            $preference = $client->create($request);
            return $preference;
        } catch (MPApiException $e) {
            // The rest of the code below will not be executed.
            logger()->error('MercadoPago Preference creation error: ' . $e->getMessage());
            return null;
        }
    }
}
