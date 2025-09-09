<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminAuthController extends Controller
{
    /**
     * Handle admin login request
     */
    public function login(Request $request)
    {
        \Log::info('Admin login attempt', ['email' => $request->email]);
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        \Log::info('Attempting authentication with admin guard', ['credentials' => $credentials['email']]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
            \Log::info('Authentication successful', ['user_id' => $user->id, 'role' => $user->role]);
            
            // Check if user has admin role
            if ($user->role === 'admin') {
                // Update last login
                $user->update(['last_login_at' => now()]);
                
                $request->session()->regenerate();
                \Log::info('Admin login successful, redirecting to dashboard');
                
                return redirect()->intended(route('admin.dashboard'));
            } else {
                \Log::warning('User does not have admin role', ['user_id' => $user->id, 'role' => $user->role]);
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => 'You do not have admin privileges.',
                ]);
            }
        }

        \Log::warning('Authentication failed', ['email' => $request->email]);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle admin logout request
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Get current admin user info
     */
    public function me(Request $request)
    {
        $user = Auth::user();
        
        if ($user && $user->role === 'admin') {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Show admin register form
     */
    public function showRegisterForm()
    {
        return view('admin.register');
    }

    /**
     * Handle admin registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Show admin profile
     */
    public function showProfile()
    {
        return view('admin.profile', ['user' => Auth::user()]);
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully');
    }

    /**
     * Handle Firebase authentication bridge
     */
    public function firebaseLogin(Request $request)
    {
        // Validate Firebase authentication data
        $request->validate([
            'firebase_token' => 'required|string',
            'firebase_uid' => 'required|string',
            'user_email' => 'required|email',
            'user_name' => 'required|string',
            'user_role' => 'required|string',
        ]);

        // Check if user role is admin
        if ($request->user_role !== 'admin') {
            return redirect('http://localhost:5173')->with('error', 'Access denied. Admin privileges required.');
        }

        // Find or create user in Laravel database
        $user = User::where('firebase_uid', $request->firebase_uid)
                   ->orWhere('email', $request->user_email)
                   ->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'firebase_uid' => $request->firebase_uid,
                'role' => 'admin',
                'provider' => 'firebase',
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ]);
        } else {
            // Update existing user
            $user->update([
                'firebase_uid' => $request->firebase_uid,
                'role' => 'admin',
                'last_login_at' => now(),
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Store Firebase data in session for middleware
        session([
            'firebase_token' => $request->firebase_token,
            'firebase_uid' => $request->firebase_uid,
            'user_email' => $request->user_email,
            'user_name' => $request->user_name,
            'user_role' => $request->user_role,
        ]);

        // Redirect to admin dashboard
        return redirect()->route('admin.dashboard');
    }

    /**
     * Handle Firebase authentication via GET request with URL parameters
     */
    public function firebaseAuth(Request $request)
    {
        // Validate Firebase authentication data from URL parameters
        $request->validate([
            'firebase_token' => 'required|string',
            'firebase_uid' => 'required|string',
            'user_email' => 'required|email',
            'user_name' => 'required|string',
            'user_role' => 'required|string',
        ]);

        // Check if user role is admin
        if ($request->user_role !== 'admin') {
            return redirect('http://localhost:5173')->with('error', 'Access denied. Admin privileges required.');
        }

        // Find or create user in Laravel database
        $user = User::where('firebase_uid', $request->firebase_uid)
                   ->orWhere('email', $request->user_email)
                   ->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'firebase_uid' => $request->firebase_uid,
                'role' => 'admin',
                'provider' => 'firebase',
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ]);
        } else {
            // Update existing user
            $user->update([
                'firebase_uid' => $request->firebase_uid,
                'role' => 'admin',
                'last_login_at' => now(),
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Store Firebase data in session for middleware
        session([
            'firebase_token' => $request->firebase_token,
            'firebase_uid' => $request->firebase_uid,
            'user_email' => $request->user_email,
            'user_name' => $request->user_name,
            'user_role' => $request->user_role,
        ]);

        // Redirect to admin dashboard
        return redirect()->route('admin.dashboard');
    }
}