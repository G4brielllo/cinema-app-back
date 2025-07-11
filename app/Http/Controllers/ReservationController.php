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
use Illuminate\Support\Facades\DB;



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
    private function generateUniqueReservationCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Reservation::where('reservation_code', $code)->exists());

        return $code;
    }


    public function store(Request $request)
    {
        $reservedSeatIds = [];
        $request->validate([
            'screening_id' => 'required|integer',
            'seats' => 'required|array',
            'seats.*.row' => 'required|integer',
            'seats.*.number' => 'required|integer',
        ]);

        $reservationCode = $this->generateUniqueReservationCode();
        $screeningId = $request->input('screening_id');
        $selectedSeats = $request->input('seats');
        $userId = Auth::id();

        $totalAmount = $this->calculateTotalAmount($selectedSeats, Screening::find($screeningId));

        foreach ($selectedSeats as $seatInfo) {
            $existing = Seat::where('screening_id', $screeningId)
                ->where('row', $seatInfo['row'])
                ->where('number', $seatInfo['number'])
                ->where('is_booked', true)
                ->first();

            if ($existing) {
                return response()->json(['message' => 'Wybrane miejsce jest już zarezerwowane'], 400);
            }
        }

        $reservation = Reservation::create([
            'user_id' => $userId,
            'screening_id' => $screeningId,
            'reservation_time' => Carbon::now(),
            'status' => 'pending',
            'reservation_code' => $reservationCode,
            'total_amount' => $totalAmount,  // <== poprawka tutaj
            'selected_seats_json' => json_encode($selectedSeats),
        ]);

        foreach ($selectedSeats as $seatInfo) {
            $seatData[] = [
                'row' => $seatInfo['row'],
                'number' => $seatInfo['number'],
            ];
        }

        $reservation->selected_seats_json = json_encode($seatData); // tymczasowe zapisanie miejsc
        $reservation->save();

        $reservation->load('screening.movie', 'seats');

        return response()->json([
            'message' => 'Rezerwacja została pomyślnie zrealizowana',
            'reservation_code' => $reservationCode,
            'reserved_seats' => $reservedSeatIds,
        ]);
    }
    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $reservation = Reservation::with(['seats', 'screening'])->findOrFail($id);

            $screeningDateTime = Carbon::parse($reservation->screening->screening_date . ' ' . $reservation->screening->screening_time);
            $now = Carbon::now();

            if ($screeningDateTime->diffInMinutes($now, false) > -60) {
                return response()->json([
                    'error' => 'Nie można anulować rezerwacji na mniej niż godzinę przed seansem.'
                ], 403);
            }
            if ($reservation->status === 'confirmed' && $reservation->payu_order_id) {
                $refundResponse = $this->payu->refund(
                    $reservation->payu_order_id,
                    $this->calculateTotalAmount($reservation->seats, $reservation->screening),
                    'Anulowanie rezerwacji #' . $reservation->id
                );

                \Log::info('PayU refund response:', $refundResponse);
            }

            $reservation->seats()->update(['is_booked' => false]);
            $reservation->seats()->detach();

            $reservation->update(['status' => 'refunded']);

            DB::commit();

            return response()->json([
                'message' => 'Rezerwacja anulowana' .
                    ($reservation->payu_order_id ? ' i zwrot środków został zainicjowany' : ''),
                'payu_order_id' => $reservation->payu_order_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Błąd podczas anulowania rezerwacji: ' . $e->getMessage());
            return response()->json([
                'error' => 'Wystąpił błąd podczas anulowania rezerwacji',
                'details' => $e->getMessage(),
                'payu_order_id' => $reservation->payu_order_id ?? null
            ], 500);
        }
    }

    private function calculateTotalAmount($seats, $screening)
    {
        \Log::info('Screening format in calculateTotalAmount:', ['format' => $screening->format]);
        if ($screening->format === '3D') {
            $ticketPrice = 25;
        } else {
            $ticketPrice = 22;
        }
        return (count($seats) * $ticketPrice) * 100;

    }
    public function showByCode($code)
    {
        $reservation = Reservation::with(['user', 'screening.movie', 'seats'])
            ->where('reservation_code', $code)
            ->first();

        if (!$reservation) {
            return response()->json(['error' => 'Rezerwacja nie znaleziona!'], 404);
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
    public function checkUsersReservations($userId)
    {
        $reservations = Reservation::with(['user', 'screening.movie', 'seats'])
            ->where('user_id', $userId)
            ->where('status', 'confirmed')
            ->get();

        $reservations->each(function ($reservation) {
            $reservation->selected_seats_json = $reservation->seats->map(function ($seat) {
                return [
                    'seat_id' => $seat->id,
                    'row' => $seat->row,
                    'number' => $seat->number,
                ];
            })->values();
        });

        return response()->json($reservations);
    }

}
