<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Screening;
use Illuminate\Support\Facades\Auth;

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

        $data['hall_id'] = $data['hall_id'] ?? 1;
        $screening = Screening::create($data);
        return response()->json($screening, 201);
    }
    public function delete($id)
    {
        $screening = Screening::findOrFail($id);
        $screening->delete();
        return response()->json(['message' => 'Screening deleted successfully']);
    }
}
