<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $token = $request->cookie('access_token');
        if (!$token) {
            return response()->json(['message' => 'Unauthorized: Token missing'], 401);
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user && in_array($user->role, $roles)) {
            return $next($request);
        }

        return response()->json(['error' => 'Forbidden: Insufficient role'], 403);
    }
}
