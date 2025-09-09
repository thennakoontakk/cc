<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Use the default guard for web routes, sanctum for API routes
        $guard = $request->expectsJson() ? auth('sanctum') : auth();
        
        if (!$guard->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 401);
            }
            
            return redirect()->route('admin.login');
        }

        $user = $guard->user();
        
        // Check if user has admin role
        if ($user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }
            
            return redirect()->route('admin.login')
                ->with('error', 'You do not have admin privileges.');
        }

        // Check if user is active (if the method exists)
        if (method_exists($user, 'isActive') && !$user->isActive()) {
            $guard->logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated.',
                ], 403);
            }
            
            return redirect()->route('admin.login')
                ->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}