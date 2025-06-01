<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayUService;

class PayUController extends Controller
{
    protected $payu;

    public function __construct(PayUService $payu)
    {
        $this->payu = $payu;
    }

    public function createOrder(Request $request)
    {
        $data = $request->validate([
            'notifyUrl' => 'required|url',
            'continueUrl' => 'required|url',
            'products' => 'required|array',
            'totalAmount' => 'required|integer',
            'currencyCode' => 'required|string',
        ]);
        $orderData = [
            'notifyUrl' => $data['notifyUrl'],
            'continueUrl' => $data['continueUrl'],
            'customerIp' => $request->ip(),
            'merchantPosId' => config('payu.pos_id'),
            'description' => 'Rezerwacja biletu w kinie',
            'currencyCode' => $data['currencyCode'],
            'totalAmount' => $data['totalAmount'],
            'products' => $data['products'],
        ];

    $result = $this->payu->createOrder($orderData);
        return response()->json(['data' => $result]);
        }
}
