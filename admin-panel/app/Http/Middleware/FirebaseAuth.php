<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FirebaseAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is coming from Firebase (has firebase_token in session or header)
        $firebaseToken = $request->header('Authorization') ?? $request->session()->get('firebase_token');
        $firebaseUid = $request->header('X-Firebase-UID') ?? $request->session()->get('firebase_uid');
        $userEmail = $request->header('X-User-Email') ?? $request->session()->get('user_email');
        $userRole = $request->header('X-User-Role') ?? $request->session()->get('user_role');
        
        // If Firebase authentication data is present and user is admin
        if ($firebaseUid && $userEmail && $userRole === 'admin') {
            // Find or create user in Laravel database
            $user = User::where('firebase_uid', $firebaseUid)
                       ->orWhere('email', $userEmail)
                       ->first();
            
            if (!$user) {
                // Create user if doesn't exist
                $user = User::create([
                    'name' => $request->header('X-User-Name') ?? $request->session()->get('user_name') ?? explode('@', $userEmail)[0],
                    'email' => $userEmail,
                    'firebase_uid' => $firebaseUid,
                    'role' => 'admin',
                    'provider' => 'firebase',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'last_login_at' => now(),
                ]);
            } else {
                // Update existing user
                $user->update([
                    'firebase_uid' => $firebaseUid,
                    'role' => 'admin',
                    'last_login_at' => now(),
                ]);
            }
            
            // Log the user in
            Auth::login($user);
            
            return $next($request);
        }
        
        // If no Firebase auth, proceed with normal Laravel authentication
        return $next($request);
    }
}