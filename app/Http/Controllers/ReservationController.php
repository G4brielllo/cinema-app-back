<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Seat;
use App\Models\Screening;
use App\Models\Hall;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\ReservationConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;



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
        $reservationCode = strtoupper(Str::random(8));
        $screeningId = $request->input('screening_id');
        $selectedSeats = $request->input('seats');

        $reservedSeatIds = [];
        foreach ($selectedSeats as $seatInfo) {
            $seat = Seat::where('screening_id', $screeningId)
                ->where('row', $seatInfo['row'])
                ->where('number', $seatInfo['number'])
                ->where('is_booked', false)
                ->first();
            if (!$seat) {
                return response()->json(['message' => 'Wybrane miejsce jest niedostępne'], 400);
            }
            $seat->is_booked = true;
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
        $user = Auth::user();

        Mail::to($user->email)->send(new ReservationConfirmation($reservationCode));

        return response()->json([
            'message' => 'Rezerwacja została pomyślnie zrealizowana',
            'reservation_code' => $reservationCode,
            'reserved_seats' => $reservedSeatIds,
        ]);
    }
    public function delete($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        return response()->json(['message' => 'Reservation deleted successfully']);
    }
    public function showByCode($code)
    {
        $reservation = Reservation::with(['user', 'screening.movie', 'seat'])->where('reservation_code', $code)->first();

        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }

        return response()->json($reservation);
    }
    public function show($id)
    {
        $reservation = Reservation::with(['user', 'screening.movie', 'seat'])->find($id);

        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }

        return response()->json($reservation);
    }
    public function getByUser($userId)
    {
        $reservations = Reservation::with(['user', 'screening.movie', 'seat'])
            ->where('user_id', $userId)
            ->get();

        return response()->json($reservations);
    }

}
