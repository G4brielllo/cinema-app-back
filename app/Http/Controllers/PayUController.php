<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayUService;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;

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
            'description' => 'Rezerwacja biletu w kinie: ' . $reservationCode,
            'currencyCode' => $data['currencyCode'],
            'totalAmount' => $data['totalAmount'],
            'products' => $data['products'],
            'extOrderId' => $reservationCode,
        ];
        \Log::info('Sending extOrderId to PayU', ['extOrderId' => $reservationCode]);
        $result = $this->payu->createOrder($orderData);
        return response()->json(['data' => $result]);
    }
    public function refund(Request $request)
    {
        $data = $request->validate([
            'orderId' => 'required|string',
            'amount' => 'required|integer',
            'description' => 'required|string',
        ]);
        try{
            $response = $this->payu->refund($data['orderId'], $data['amount'], $data['description']);
            return [
            'success' => true,
            'status' => $response['status']['statusCode'] ?? 'UNKNOWN',
            'data' => $response
        ];
        }catch (\Exception $e) {
            \Log::error('PayU refund error', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Refund failed'], 500);
        }

    }
    public function notify(Request $request)
    {
        $data = $request->all();
        \Log::info('PayU notify received', $data);

        $extOrderId = $data['order']['extOrderId'] ?? null;
        $payuOrderId = $data['order']['orderId'] ?? null;
        $status = $data['order']['status'] ?? null;

        if (!$extOrderId || !$payuOrderId || !$status) {
            \Log::error('Missing required fields', $data);
            return response()->json(['error' => 'Invalid data'], 400);
        }

        $reservations = Reservation::where('reservation_code', $extOrderId)->get();

        if ($reservations->isEmpty()) {
            \Log::warning("No reservations found for extOrderId: $extOrderId");
            return response()->json(['error' => 'Reservation not found'], 404);
        }

        foreach ($reservations as $reservation) {
            $updateData = ['status' => $this->mapStatus($status)];
            
            if ($status === 'COMPLETED') {
                $updateData['payu_order_id'] = $payuOrderId;
            }

            $reservation->update($updateData);

            if ($reservation->seats) {
                $reservation->seats()->update([
                    'is_booked' => $status === 'COMPLETED'
                ]);
            }

            if ($updateData['status'] === 'confirmed') {
                 Mail::to($reservation->user->email)->send(new \App\Mail\ReservationConfirmation($reservation));
            }
        }

        return response()->json(['message' => 'OK']);
    }

    private function mapStatus($payuStatus)
    {
        return match($payuStatus) {
            'COMPLETED' => 'confirmed',
            'CANCELED', 'FAILED' => 'canceled',
            default => 'pending'
        };
    }

}



