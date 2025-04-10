<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;


class MovieController extends Controller
{
    public function index()
    {
        checkRole();
        $movies = Movie::all();
        return response()->json($movies);
    }
    public function store()
    {
        checkRole();
        $data = request()->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'show_time' => 'required|date',
            'duration' => 'required|integer',
            'release_date' => 'required|date',
        ]);
        
        $movie = Movie::create($data);
        return response()->json($movie, 201);
    }
    public function delete($id)
    {
        checkRole();
        $movie = Movie::findOrFail($id);
        $movie->delete();
        return response()->json(['message' => 'Movie deleted successfully']);
    }
    public function checkRole(){
        $user = Auth::user();
    
        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
    }
}
