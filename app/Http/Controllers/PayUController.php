<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayUService;
use App\Models\Reservation;

class PayUController extends Controller
{
    protected $payu;

    public function __construct(PayUService $payu)
    {
        $this->payu = $payu;
    }

    public function createOrder(Request $request)
    {
        \Log::info('Request body do PayUController:', $request->all());

        $data = $request->validate([
            'notifyUrl' => 'required|url',
            'continueUrl' => 'required|url',
            'products' => 'required|array',
            'totalAmount' => 'required|integer',
            'currencyCode' => 'required|string',
            // 'extOrderId' => 'required|string',
        ]);
        
        $reservationCode = $request->input('extOrderId');
        $orderData = [
            'notifyUrl' => $data['notifyUrl'],
            'continueUrl' => $data['continueUrl'],
            'customerIp' => $request->ip(),
            'merchantPosId' => config('payu.pos_id'),
            'description' => 'Rezerwacja biletu w kinie',
            'currencyCode' => $data['currencyCode'],
            'totalAmount' => $data['totalAmount'],
            'products' => $data['products'],
            'extOrderId' => $reservationCode,
        ];
        \Log::info('Sending extOrderId to PayU', ['extOrderId' => $reservationCode]);
        $result = $this->payu->createOrder($orderData);
        return response()->json(['data' => $result]);
    }

    public function notify(Request $request)
    {
        $data = $request->all();
        \Log::info('PayU notify received', $data);

        $extOrderId = $data['order']['extOrderId'] ?? null;
        $status = $data['order']['status'] ?? null;

        if (!$extOrderId || !$status) {
            \Log::error('Missing extOrderId or status', $data);
            return response()->json(['error' => 'Invalid data'], 400);
        }

        $reservations = Reservation::where('reservation_code', $extOrderId)->get();

        if ($reservations->isEmpty()) {
            \Log::warning("No reservations found for extOrderId: $extOrderId");
            return response()->json(['error' => 'Reservation not found'], 404);
        }

        foreach ($reservations as $reservation) {
            if ($status === 'COMPLETED') {
                $reservation->status = 'confirmed';
                if ($reservation->seat) {
                    $reservation->seat->is_booked = true;
                    $reservation->seat->save();
                }
            } elseif (in_array($status, ['CANCELED', 'FAILED'])) {
                $reservation->status = 'canceled';
                if ($reservation->seat) {
                    $reservation->seat->is_booked = false;
                    $reservation->seat->save();
                }
            } else {
                $reservation->status = 'pending';
            }
            $reservation->save();
        }

        return response()->json(['message' => 'OK']);
    }


}



