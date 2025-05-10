<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;


class MovieController extends Controller
{
    // public function index()
    // {
    //     $movies = Movie::all();
    //     return response()->json($movies);
    // }

    public function index()
    {
        $movies = Movie::with('screenings')->get();
        return response()->json($movies);
    }
    public function show()
    {
        $id = request()->route('id');
        $movie = Movie::with('screenings')->findOrFail($id);
        return response()->json($movie);
    }
    public function store()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = request()->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'show_time' => 'required|date',
            'duration' => 'required|integer',
            'release_date' => 'required|date',
            'image' => 'string',
            'direction' => 'required|string',
            'script' => 'required|string',
            'production_year' => 'required|integer',
            'cast' => 'required|string',
        ]);

        $movie = Movie::create($data);
        return response()->json($movie, 201);
    }
    public function delete($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();
        return response()->json(['message' => 'Movie deleted successfully']);
    }
    public function update($id)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = request()->validate([
            'title' => 'string',
            'description' => 'string',
            'category' => 'string',
            'show_time' => 'date',
            'duration' => 'integer',
            'release_date' => 'date',
            'image' => 'string',
            'direction' => 'string',
            'script' => 'string',
            'production_year' => 'integer',
            'cast' => 'string',
        ]);

        $movie = Movie::findOrFail($id);
        $movie->update($data);
        return response()->json($movie);
    }
}
