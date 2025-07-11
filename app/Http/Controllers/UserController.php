<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function delete($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::user();

        if (!$user || ($user->id !== $id && $user->role !== 'admin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
        ];

        if ($request->filled('password')) {
            $rules['current_password'] = 'required|current_password';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $data = $request->validate($rules);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $targetUser = User::findOrFail($id);
        $targetUser->update($data);

        return response()->json($targetUser, 200);
    }
}
