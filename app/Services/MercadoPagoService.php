<?php

namespace App\Services;

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::SERVER); // only if using localhost, else SERVER
        // MercadoPagoConfig::setIntegratorId(config('services.mercadopago.integrator_id'));
    }

    public function createPreference(array $items, array $payer)
    {
        $externalReference = uniqid();

        $request = [
            'items' => $items,
            'payer' => $payer,
            'payment_methods' => [
                "excluded_payment_methods" => [],
                "installments" => 6,
                "default_installments" => 1
            ],
            'back_urls' => [
                'success' => config('app.url') . '/checkout/exito',
                'pending' => config('app.url') . '/checkout',
                'failure' => config('app.url'),
            ],
            'notification_url' => config('app.url') . '/webhook',
            'notification_url' => route('webhook'),
            'statement_descriptor' => config('app.name'),
            'external_reference' => $externalReference,
            'expires' => false,
            'auto_return' => 'approved',
        ];

        $client = new PreferenceClient();

        try {
            $preference = $client->create($request);
            session()->put('guest_order_access', $externalReference);
            return $preference;
        } catch (MPApiException $e) {
            // The rest of the code below will not be executed.
            logger()->error('MercadoPago Preference creation error: ' . $e->getMessage());
            return null;
        }
    }
}
