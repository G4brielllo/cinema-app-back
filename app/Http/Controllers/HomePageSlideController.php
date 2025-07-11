<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\HomePageSlide;

class HomePageSlideController extends Controller
{
    public function index()
    {
        $slides = HomePageSlide::all();
        return response()->json($slides);
    }
    public function store()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $data = request()->validate([
            'title' => 'required|string',
            'image_url' => 'required|string',
            'trailer_url' => 'required|string',
            'position' => 'required|integer',
        ]);
        $slide = HomePageSlide::create($data);
        return response()->json($slide, 201);
    }
    public function delete($id)
    {
        $slide = HomePageSlide::findOrFail($id);
        $slide->delete();
        return response()->json(['message' => 'Slide deleted successfully']);
    }
}
