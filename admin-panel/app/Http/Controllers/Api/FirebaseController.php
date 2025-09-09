<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use App\Models\User;
use App\Models\FrontendUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FirebaseController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Verify Firebase user and sync with local database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $idToken = $request->input('id_token');
            
            // Verify Firebase ID token
            $firebaseUser = $this->firebaseService->verifyIdToken($idToken);
            
            if (!$firebaseUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Firebase token'
                ], 401);
            }

            // Sync user with local database
            $user = $this->firebaseService->syncUser($firebaseUser);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to sync user with database'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'User verified and synced successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'firebase_uid' => $user->firebase_uid,
                        'name' => $user->name,
                        'email' => $user->email,
                        'provider' => $user->provider,
                        'is_active' => $user->is_active,
                        'email_verified' => !is_null($user->email_verified_at),
                        'last_login' => $user->last_login_at?->toISOString(),
                        'created_at' => $user->created_at->toISOString(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Firebase user verification failed: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User verification failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Register a new user and sync with Firebase
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'provider' => 'string|in:email,google.com',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $firebaseUid = $request->input('firebase_uid');
            $name = $request->input('name');
            $email = $request->input('email');
            $provider = $request->input('provider', 'email');

            // Check if user already exists
            $existingUser = FrontendUser::where('firebase_uid', $firebaseUid)
                                ->orWhere('email', $email)
                                ->first();

            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists'
                ], 409);
            }

            // Create new user in MySQL database
            $user = FrontendUser::create([
                'firebase_uid' => $firebaseUid,
                'name' => $name,
                'email' => $email,
                'provider' => $provider,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'firebase_uid' => $user->firebase_uid,
                        'name' => $user->name,
                        'email' => $user->email,
                        'provider' => $user->provider,
                        'created_at' => $user->created_at->toISOString(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User registration failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Firebase user information
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFirebaseUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $firebaseUid = $request->input('firebase_uid');
            
            // Get Firebase user
            $firebaseUser = $this->firebaseService->getUser($firebaseUid);
            
            if (!$firebaseUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase user not found'
                ], 404);
            }

            // Get local user
            $localUser = User::where('firebase_uid', $firebaseUid)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'firebase_user' => $firebaseUser,
                    'local_user' => $localUser ? [
                        'id' => $localUser->id,
                        'name' => $localUser->name,
                        'email' => $localUser->email,
                        'provider' => $localUser->provider,
                        'is_active' => $localUser->is_active,
                        'email_verified' => !is_null($localUser->email_verified_at),
                        'last_login' => $localUser->last_login_at?->toISOString(),
                        'created_at' => $localUser->created_at->toISOString(),
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get Firebase user: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get Firebase user',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Sync Firebase user status with local database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncUserStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'action' => 'required|in:enable,disable,delete',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $firebaseUid = $request->input('firebase_uid');
            $action = $request->input('action');
            
            // Find local user
            $user = User::where('firebase_uid', $firebaseUid)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found in local database'
                ], 404);
            }

            $success = false;
            $message = '';

            switch ($action) {
                case 'enable':
                    $success = $this->firebaseService->enableUser($firebaseUid);
                    if ($success) {
                        $user->update(['is_active' => true]);
                        $message = 'User enabled successfully';
                    }
                    break;
                    
                case 'disable':
                    $success = $this->firebaseService->disableUser($firebaseUid);
                    if ($success) {
                        $user->update(['is_active' => false]);
                        $message = 'User disabled successfully';
                    }
                    break;
                    
                case 'delete':
                    $success = $this->firebaseService->deleteUser($firebaseUid);
                    if ($success) {
                        $user->delete();
                        $message = 'User deleted successfully';
                    }
                    break;
            }

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to sync user status with Firebase'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'firebase_uid' => $firebaseUid,
                    'action' => $action,
                    'user' => $action !== 'delete' ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_active' => $user->is_active,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to sync user status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync user status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Create custom token for user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCustomToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'claims' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $firebaseUid = $request->input('firebase_uid');
            $claims = $request->input('claims', []);
            
            // Verify user exists in local database
            $user = User::where('firebase_uid', $firebaseUid)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'User account is inactive'
                ], 403);
            }

            // Create custom token
            $customToken = $this->firebaseService->createCustomToken($firebaseUid, $claims);
            
            if (!$customToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create custom token'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Custom token created successfully',
                'data' => [
                    'custom_token' => $customToken,
                    'firebase_uid' => $firebaseUid,
                    'expires_in' => 3600, // 1 hour
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create custom token: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create custom token',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}