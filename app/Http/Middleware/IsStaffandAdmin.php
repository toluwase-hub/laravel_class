<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStaffandAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!in_array($user['role'], ['staff', 'admin'])) {
            return response()->json([
                'message' => 'unauthorized'
            ], 403);
        }
        return $next($request);
    }
}
