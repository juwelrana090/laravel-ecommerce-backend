<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSellerOrAdmin
{
    public function handle($request, Closure $next)
    {
        $user = auth()->guard('web')->user();

        if ($user && ($user->role === 'seller' || $user->role === 'admin')) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
