<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Http\Request;
use App\Models\HallSeat;


class HallSeatController extends Controller
{
    // public function getSeatsByHall($hallId)
    // {
    //     $hall = Hall::findOrFail($hallId);
    //     return response()->json($hall->hallSeats);
    // }
    public function getSeatsByHall($hallId)
    {
        $hall = Hall::findOrFail($hallId);

        $hallSeats = HallSeat::where('hall_id', $hallId)->get(['id', 'x', 'y']);

        return response()->json($hallSeats);
    }

    public function getAvailableSeats($hallId)
    {
        $hall = Hall::findOrFail($hallId);

        $availableSeats = HallSeat::where('hall_id', $hallId)
            ->get(['id', 'x', 'y']);

        return response()->json([
            'availableSeats' => $availableSeats,
            'totalRows' => $hall->rows,
            'totalCols' => $hall->columns,
        ]);
    }

}

