<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->hasPermissionTo($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta acción.',
                    'error' => 'FORBIDDEN'
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        return $next($request);
    }
}
