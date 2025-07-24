<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyNewEmail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


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
        $targetUser = User::findOrFail($id);

        $targetUser->name = $data['name'];
        $targetUser->surname = $data['surname'];

        if ($data['email'] !== $targetUser->email) {
            $token = Str::uuid()->toString();
            $targetUser->pending_email = $data['email'];
            $targetUser->email_verification_token = $token;
            $targetUser->email_verification_token_created_at = now();

            Mail::to($data['email'])->send(new VerifyNewEmail($targetUser));
        }

        if (!empty($data['password'])) {
            $targetUser->password = Hash::make($data['password']);
        }

        $targetUser->save();

        return response()->json([
            'message' => 'User updated successfully. If email was changed, verify it via email.',
        ]);
    }
    public function verifyEmail($id, $token)
    {
        $user = User::findOrFail($id);

        if ($user->email_verification_token !== $token) {
            return response('<h1>Błąd: Nieprawidłowy token.</h1>', 400)
                ->header('Content-Type', 'text/html');
        }

        if (!$user->email_verification_token_created_at) {
            return response('<h1>Błąd: Brak daty utworzenia tokenu.</h1>', 400)
                ->header('Content-Type', 'text/html');
        }

        $expiresAt = \Carbon\Carbon::parse($user->email_verification_token_created_at)->addMinutes(15);

        if (now()->greaterThan($expiresAt)) {
            return response('<h1>Token wygasł. Spróbuj ponownie zaktualizować e-mail.</h1>', 410)
                ->header('Content-Type', 'text/html');
        }

        $user->email = $user->pending_email;
        $user->pending_email = null;
        $user->email_verification_token = null;
        $user->email_verification_token_created_at = null;
        $user->save();

       return response('
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Potwierdzenie e-maila</title>
        <meta http-equiv="refresh" content="10;url=http://localhost:8080/login">
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .container {
                background-color: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                text-align: center;
            }
            h1 {
                color: #4CAF50;
                margin-bottom: 20px;
            }
            p {
                font-size: 16px;
                color: #333;
                margin-bottom: 20px;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                transition: background-color 0.3s ease;
            }
            .button:hover {
                background-color: #45a049;
            }
            .note {
                margin-top: 15px;
                color: #888;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>E-mail został potwierdzony!</h1>
            <p>Możesz teraz się zalogować.</p>
            <a href="http://localhost:8080/login" class="button">Przejdź do logowania</a>
            <p class="note">Za chwilę zostaniesz automatycznie przekierowany...</p>
        </div>
    </body>
    </html>
', 200)->header('Content-Type', 'text/html');

    }
}
