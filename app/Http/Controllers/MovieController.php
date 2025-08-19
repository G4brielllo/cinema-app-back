<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


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
            'duration' => 'required|integer',
            'image' => 'string',
            'trailer' => 'nullable|string',
            'direction' => 'required|string',
            'script' => 'required|string',
            'playing_from' => 'required|date',
            'playing_until' => 'required|date',
            'cast' => 'required|string',
            'age_group' => 'required|string',
            'announcement' => 'boolean',
            'status' => 'string',
        ]);
        if (!isset($data['status'])) {
            $data['status'] = 'movie';
        }

        $movie = Movie::create($data);
        return response()->json($movie, 201);
    }
    public function delete($id)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        
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
            'duration' => 'integer',
            'image' => 'string',
            'trailer' => 'string',
            'direction' => 'string',
            'script' => 'string',
            'production_year' => 'integer',
            'playing_from' => 'date',
            'playing_until' => 'date',
            'cast' => 'string',
            'status' => 'string',
            'age_group' => 'sometimes|string|in:Dzieci,Młodzież,Dorośli',        
        ]);

        $movie = Movie::findOrFail($id);
        $movie->update($data);
        return response()->json($movie);
    }
    public function autoArchiveMovies()
    {
        $today = Carbon::today();
        $count = Movie::whereDate('playing_until', '<', $today)
            ->where('status', '!=', 'archive')
            ->update(['status' => 'archive']);

        return response()->json([
            'archived_movies_count' => $count,
            'message' => 'Status zaktualizowany dla przeterminowanych filmów.'
        ]);
    }
}
