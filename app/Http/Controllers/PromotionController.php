<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Promotion;
class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::all();
        return response()->json($promotions);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|string', // base64 string
        ]);

        $promotion = Promotion::create($data);

        return response()->json($promotion, 201);
    }

    public function delete($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();
        return response()->json(['message' => 'Promotion deleted successfully']);
    }
}
