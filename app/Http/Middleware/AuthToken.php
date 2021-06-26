<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('authorization')) {
            $headerToken = explode(' ', $request->header('authorization'))[1];

            if ($headerToken && PersonalAccessToken::where('token', $headerToken)->exists()) {
                return $next($request);
            }

        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 413);
    }
}
