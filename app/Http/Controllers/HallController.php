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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'rows' => 'required|integer|min:1',
            'seats_per_row' => 'required|integer|min:1',
        ]);

        $hall = Hall::create($validated);
        return response()->json($hall, 201);
    }
    public function storeLayout(Request $request, Hall $hall)
    {
        $request->validate([
            'seats' => 'required|array',
            'seats.*.x' => 'required|integer|min:1',
            'seats.*.y' => 'required|integer|min:1',
        ]);

        $hall->hallSeats()->delete();

        foreach ($request->input('seats') as $seat) {
            $hall->hallSeats()->create([
                'x' => $seat['x'],
                'y' => $seat['y'],
            ]);
        }

        return response()->json(['message' => 'Układ zapisany']);
    }

    public function update(Request $request, $id)
    {
        $hall = Hall::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'rows' => 'sometimes|integer|min:1',
            'seats_per_row' => 'sometimes|integer|min:1',
        ]);

        $hall->update($validated);
        return response()->json($hall);
    }

    public function delete($id)
    {
        $hall = Hall::findOrFail($id);
        $hall->delete();
        return response()->json(null, 204);
    }

}
