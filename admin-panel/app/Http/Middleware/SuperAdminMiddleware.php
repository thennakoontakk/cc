<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Use the sanctum guard for API routes
        $guard = auth('sanctum');
        
        if (!$guard->check() || !$guard->user() instanceof \App\Models\Admin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 401);
            }
            
            return redirect()->route('admin.login');
        }

        if (!$guard->user()->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Super admin access required.',
                ], 403);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}