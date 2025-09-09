<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\CartManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to admin login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Serve storage files with CORS headers
Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    
    if (!file_exists($file)) {
        abort(404);
    }
    
    $response = response()->file($file);
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    
    return $response;
})->where('path', '.*');

// Admin authentication routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Firebase authentication bridge (no CSRF protection needed)
    Route::get('/firebase-auth', [AdminAuthController::class, 'firebaseAuth'])->name('firebase.auth');
    Route::post('/firebase-login', [AdminAuthController::class, 'firebaseLogin'])->name('firebase.login')->withoutMiddleware(['web']);
    
    // Guest routes (not authenticated)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
        Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AdminAuthController::class, 'register'])->name('register.post');
    });
    
    // Authenticated admin routes
    Route::middleware(['auth:admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports/monthly/download', [DashboardController::class, 'downloadMonthlyReport'])->name('reports.monthly.download');
        Route::get('/reports/weekly/download', [DashboardController::class, 'downloadWeeklyReport'])->name('reports.weekly.download');
        
        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        
        // Profile management
        Route::get('/profile', [AdminAuthController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AdminAuthController::class, 'updatePassword'])->name('password.update');
        
        // User management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
            Route::put('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::get('/{user}/activity', [UserManagementController::class, 'activity'])->name('activity');
        });
        
        // Product management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        });
        
        // Order management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderManagementController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderManagementController::class, 'show'])->name('show');
            Route::put('/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('update-status');
        });
        
        // Cart management
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartManagementController::class, 'index'])->name('index');
            Route::delete('/{id}', [CartManagementController::class, 'removeItem'])->name('remove-item');
            Route::delete('/clear/user', [CartManagementController::class, 'clearCart'])->name('clear');
        });
        
        // Admin management (super admin only)
        Route::middleware('super-admin')->prefix('admins')->name('admins.')->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('store');
            Route::get('/{admin}', [AdminManagementController::class, 'show'])->name('show');
            Route::get('/{admin}/edit', [AdminManagementController::class, 'edit'])->name('edit');
            Route::put('/{admin}', [AdminManagementController::class, 'update'])->name('update');
            Route::delete('/{admin}', [AdminManagementController::class, 'destroy'])->name('destroy');
            Route::put('/{admin}/toggle-status', [AdminManagementController::class, 'toggleStatus'])->name('toggle-status');
            Route::put('/{admin}/change-role', [AdminManagementController::class, 'changeRole'])->name('change-role');
        });
    });
});