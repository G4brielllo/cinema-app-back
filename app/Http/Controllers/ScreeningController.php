<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Screening;
use Illuminate\Support\Facades\Auth;
use App\Models\Seat;
use App\Models\Hall;

class ScreeningController extends Controller
{
    // public function index()
    // {
    //     $movies = Movie::all();
    //     return response()->json($movies);
    // }

    public function index()
    {
        $screenings = Screening::with('movie')->get();
        return response()->json($screenings);
    }
    public function show(){
        $id = request()->route('id');
        $screening = Screening::with('movie')->findOrFail($id);
        return response()->json($screening);
    }

    // public function store()
    // {
    //     $user = Auth::user();

    //     if (!$user || $user->role !== 'admin') {
    //         return response()->json(['error' => 'Forbidden'], 403);
    //     }

    //     $data = request()->validate([
    //         'movie_id' => 'required|integer',
    //         'screening_date' => 'required|date',
    //         'screening_time' => 'required|date_format:H:i',
    //         'hall_id' => 'required|integer|nullable',
    //     ]);

    //     $data['hall_id'] = $data['hall_id'] ?? 1;
    //     $screening = Screening::create($data);
    //     return response()->json($screening, 201);
    // }
    public function store()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = request()->validate([
            'movie_id' => 'required|integer',
            'screening_date' => 'required|date',
            'screening_time' => 'required|date_format:H:i',
            'hall_id' => 'required|integer|nullable',
        ]);

        // Domyślnie hall_id = 1
        $data['hall_id'] = $data['hall_id'] ?? 1;

        // Tworzenie seansu
        $screening = Screening::create($data);

        $hall = Hall::findOrFail($data['hall_id']);

        // Tworzenie miejsca na podstawie sali
        for ($row = 1; $row <= $hall->rows; $row++) {
            for ($number = 1; $number <= $hall->seats_per_row; $number++) {
                Seat::create([
                    'screening_id' => $screening->id,
                    'row' => $row,
                    'number' => $number,
                    'is_booked' => false,
                ]);
            }
        }

        return response()->json($screening, 201);
    }
    public function delete($id)
    {
        $screening = Screening::findOrFail($id);
        $screening->delete();
        return response()->json(['message' => 'Screening deleted successfully']);
    }
    public function update($id)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = request()->validate([
            'movie_id' => 'integer',
            'screening_date' => 'date',
            'screening_time' => 'date_format:H:i',
            'hall_id' => 'integer|nullable',
        ]);

        $screening = Screening::findOrFail($id);
        $screening->update($data);
        return response()->json($screening);
    }
}
