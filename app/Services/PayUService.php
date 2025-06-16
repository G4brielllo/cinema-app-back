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

    // Pobieranie tokenu OAuth2, wazny' 1h
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
        \Log::info('PayU createOrder payload', $orderData);

        $response = Http::withToken($accessToken)
            ->withHeaders(['Accept' => 'application/json'])
            ->withoutRedirecting()
            ->post('https://secure.snd.payu.com/api/v2_1/orders', $orderData);

        if ($response->status() === 201 || $response->status() === 200) {
            return $response->json();
        }

        if (in_array($response->status(), [301, 302])) {
            return [
                'redirectUri' => $response->header('Location'),
            ];
        }

        $body = $response->body();
        \Log::error("PayU unexpected response ({$response->status()}): {$body}");
        throw new \Exception("PayU createOrder failed: {$response->status()}");
    }
    public function refund($orderId, $amount, $description)
    {
        $url = (config('payu.sandbox') 
            ? 'https://secure.snd.payu.com' 
            : 'https://secure.payu.com') . '/api/v2_1/orders/' . $orderId . '/refunds';
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json'
        ])->post($url, [
            'refund' => [
                'description' => $description,
                'amount' => $amount
            ]
        ]);
        
        if ($response->failed()) {
            \Log::error('PayU refund error:', [
                'status' => $response->status(),
                'response' => $response->body(),
                'orderId' => $orderId
            ]);
            throw new \Exception('PayU error: ' . $response->body());
        }
        
        return $response->json();
    }
}
