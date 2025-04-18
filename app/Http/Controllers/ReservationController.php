<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Seat;
use App\Models\Screening;
use App\Models\Hall;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class ReservationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $reservations = Reservation::where('user_id', $userId)->with('screening')->get();

        return response()->json($reservations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'screening_id' => 'required|integer',
            'seats' => 'required|array',
            'seats.*.row' => 'required|integer',
            'seats.*.number' => 'required|integer',
        ]);

        $userId = Auth::id();
        $reservationCode = strtoupper(str_random(8));
        $scraeningId = $request->input('screening_id');
        $selectedSeats = $request->input('seats');

        $reservedSeatIds = [];
        foreach ($selectedSeats as $seatInfo) {
            $seat = Seat::where('screening_id', $screeningId)
                ->where('row', $seatInfo['row'])
                ->where('number', $seatInfo['number'])
                ->where('reserved', false)
                ->first();
            if (!$seat) {
                return response()->json(['message' => 'Wybrane miejsce jest niedostępne'], 400);
            }
            $seat->reserved = true;
            $seat->save();

            $reservation = Reservation::create([
                'user_id' => $userId,
                'screening_id' => $screeningId,
                'seat_id' => $seat->id,
                'reservation_time' => Carbon::now(),
                'status' => 'reserved',
                'reservation_code' => $reservationCode,
            ]);
            $reservedSeatIds[] = $seat->id;
        }
        return response()->json([
            'message' => 'Rezerwacja została pomyślnie zrealizowana',
            'reservation_code' => $reservationCode,
            'reserved_seats' => $reservedSeatIds,
        ]);
    }

}
