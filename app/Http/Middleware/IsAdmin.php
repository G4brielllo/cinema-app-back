<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            Log::info('User is not logged in.');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        Log::info('User is logged in: ' . $user->id);

        if ($user->role === 'admin') {
            return $next($request);
        }

        Log::info('User is not an admin.');
        return response()->json(['error' => 'Forbidden'], 403);
    }
}
