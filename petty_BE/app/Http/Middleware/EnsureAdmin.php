<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Lang;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     * Only allow if the authenticated model is an Admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || ! ($user instanceof Admin)) {
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.unauthorized_admin')
            ], 401);
        }

        return $next($request);
    }
}
