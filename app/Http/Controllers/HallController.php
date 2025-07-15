<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hall;
use Illuminate\Support\Facades\Auth;


class HallController extends Controller
{
    public function index()
    {
        return Hall::all();
    }

    public function show($id)
    {
        $hall = Hall::findOrFail($id);
        return response()->json([
            'id' => $hall->id,
            'name' => $hall->name,
            'rows' => $hall->rows,
            'seats_per_row' => $hall->seats_per_row,
        ]);

    }

    public function store()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = request()->validate([
            'name' => 'required|string',
            'rows' => 'required|integer',
            'seats_per_row' => 'required|integer',
        ]);

        $hall = Hall::create($data);
        return response()->json($hall, 201);
    }
}
