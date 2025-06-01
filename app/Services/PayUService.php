<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class PayUService
{
    protected $client;
    protected $config;

    public function __construct()
    {
        $this->config = config('payu');
        $this->client = new Client([
            'base_uri' => $this->config['base_url'],
            'timeout'  => 10.0,
        ]);
    }

    // Pobierz token OAuth2, cache'uj na 1h (PayU token ważny 1h)
    protected function getAccessToken()
    {
        return Cache::remember('payu_access_token', 3600, function () {
            $response = $this->client->post('/pl/standard/user/oauth/authorize', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['access_token'];
        });
    }
    public function createOrder(array $orderData)
    {
        $accessToken = $this->getAccessToken();

        // 1) Zrób request, nie follow-redirects, chroń JSON
        $response = Http::withToken($accessToken)
            ->withHeaders(['Accept' => 'application/json'])
            ->withoutRedirecting()
            ->post('https://secure.snd.payu.com/api/v2_1/orders', $orderData);

        // 2) Jeżeli dostaniesz 201 z JSON-em, parsuj normalnie...
        if ($response->status() === 201 || $response->status() === 200) {
            return $response->json();
        }

        // 3) Jeżeli dostaniesz 302, weź Location i zwróć klientowi
        if (in_array($response->status(), [301, 302])) {
            return [
                'redirectUri' => $response->header('Location'),
            ];
        }

        // 4) W pozostałych wypadkach rzuć wyjątek
        $body = $response->body();
        \Log::error("PayU unexpected response ({$response->status()}): {$body}");
        throw new \Exception("PayU createOrder failed: {$response->status()}");
    }
}
