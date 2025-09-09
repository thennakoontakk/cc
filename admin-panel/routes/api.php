<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FirebaseController;
use App\Http\Controllers\Api\FrontendAuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/register', [AdminAuthController::class, 'register']);

// Frontend user authentication routes
Route::post('/frontend/register', [FrontendAuthController::class, 'register']);
Route::post('/frontend/login', [FrontendAuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/frontend/logout', [FrontendAuthController::class, 'logout']);
    Route::get('/frontend/me', [FrontendAuthController::class, 'me']);
});

// Public product routes (for frontend)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Public cart routes (for frontend)
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/cart', [CartController::class, 'getCartItems']);
Route::put('/cart/{id}', [CartController::class, 'updateCartItem']);
Route::delete('/cart/{id}', [CartController::class, 'removeCartItem']);
Route::delete('/cart', [CartController::class, 'clearCart']);

// Public order routes (for frontend)
Route::post('/orders', [OrderController::class, 'createOrder']);
Route::get('/orders/user/{userId}', [OrderController::class, 'getUserOrders']);
Route::get('/orders/{orderId}', [OrderController::class, 'getOrder']);

// Protected admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Admin authentication
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::get('/me', [AdminAuthController::class, 'me']);
    Route::put('/profile', [AdminAuthController::class, 'updateProfile']);
    Route::put('/password', [AdminAuthController::class, 'updatePassword']);
    
    // User management
    Route::apiResource('users', UserController::class);
    Route::put('/users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::get('/users/{user}/activity', [UserController::class, 'getActivity']);
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction']);
    
    // Admin management (super admin only)
    Route::middleware('super-admin')->group(function () {
        Route::apiResource('admins', AdminController::class);
        Route::put('/admins/{admin}/toggle-status', [AdminController::class, 'toggleStatus']);
        Route::put('/admins/{admin}/change-role', [AdminController::class, 'changeRole']);
    });
    
    // Dashboard statistics
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/analytics', [DashboardController::class, 'getUserAnalytics']);
    
    // Cart management
    Route::get('/cart/all', [CartController::class, 'getAllCartItems']);
    
    // Order management
    Route::get('/orders', [OrderController::class, 'getAllOrders']);
    Route::get('/orders/statistics', [OrderController::class, 'getOrderStatistics']);
    Route::put('/orders/{orderId}/status', [OrderController::class, 'updateOrderStatus']);
});

// Firebase user verification and management (public endpoints)
Route::post('/verify-firebase-user', [FirebaseController::class, 'verifyUser']);
Route::post('/firebase/user', [FirebaseController::class, 'getFirebaseUser']);
Route::post('/firebase/register', [FirebaseController::class, 'registerUser']);
Route::post('/firebase/custom-token', [FirebaseController::class, 'createCustomToken']);

// Protected Firebase endpoints (admin only)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/firebase/sync-status', [FirebaseController::class, 'syncUserStatus']);
});