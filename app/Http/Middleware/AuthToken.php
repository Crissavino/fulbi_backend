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
        Log::info(json_encode($request->hasHeader('authorization')));
        if ($request->hasHeader('authorization')) {
            Log::info(json_encode('$headers'));
            Log::info(json_encode($request->headers));
            $headerToken = explode(' ', $request->header('authorization'))[1];
            Log::info(json_encode('$headerToken'));
            Log::info(json_encode($headerToken));

            Log::info(json_encode($headerToken && PersonalAccessToken::where('token', $headerToken)->exists()));

            if ($headerToken && PersonalAccessToken::where('token', $headerToken)->exists()) {
                return $next($request);
            }

        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 413);
    }
}
