<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Role name to check
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please login.'
            ], 401);
        }

        if (!method_exists($user, 'hasRole')) {
            return response()->json([
                'success' => false,
                'message' => 'User model does not support role checking.'
            ], 500);
        }

        if (!$user->hasRole($role)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have the required role.'
            ], 403);
        }

        return $next($request);
    }
}
