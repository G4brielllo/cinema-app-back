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
use App\Services\PayUService;



class ReservationController extends Controller
{
    protected $payu;
    public function __construct(PayUService $payu)
    {
        $this->payu = $payu;
    }

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

        $reservationCode = strtoupper(Str::random(8));
        $screeningId = $request->input('screening_id');
        $selectedSeats = $request->input('seats');
        $userId = Auth::id();
        $reservedSeatIds = [];
        $totalAmount = 0;

        foreach ($selectedSeats as $seatInfo) {
            
            $existing = Seat::where('screening_id', $screeningId)
                ->where('row', $seatInfo['row'])
                ->where('number', $seatInfo['number'])
                ->first();

            if ($existing) {
                return response()->json(['message' => 'Wybrane miejsce jest już zarezerwowane'], 400);
            }

            $seat = Seat::create([//bookowanie miejsca
                'screening_id' => $screeningId,
                'row' => $seatInfo['row'],
                'number' => $seatInfo['number'],
                'is_booked' => true,
            ]);


            $reservation = Reservation::create([//tworzenie rezerwacji
                'user_id' => $userId,
                'screening_id' => $screeningId,
                'seat_id' => $seat->id,
                'reservation_time' => Carbon::now(),
                'status' => 'pending',
                'reservation_code' => $reservationCode,
            ]);
        }
        return response()->json([
            'message' => 'Rezerwacja została pomyślnie zrealizowana',
            'reservation_code' => $reservationCode,
            'reserved_seats' => $reservedSeatIds,
        ]);

        // $user = Auth::user();

        Mail::to($user->email)->send(new ReservationConfirmation($reservationCode));

        // return response()->json([
        //     'message' => 'Rezerwacja została pomyślnie zrealizowana',
        //     'reservation_code' => $reservationCode,
        //     'reserved_seats' => $reservedSeatIds,
        // ]);
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
