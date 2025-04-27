<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seat;

class SeatController extends Controller
{
    public function getSeatsForScreening($screeningId)
    {
        $seats = Seat::where('screening_id', $screeningId)->get();
        return response()->json($seats);
    }

}
