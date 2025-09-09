<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        try {
            // Get current date ranges
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            // User statistics
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            $inactiveUsers = User::where('is_active', false)->count();
            $verifiedUsers = User::whereNotNull('email_verified_at')->count();
            $unverifiedUsers = User::whereNull('email_verified_at')->count();

            // New users statistics
            $newUsersToday = User::whereDate('created_at', $today)->count();
            $newUsersThisWeek = User::where('created_at', '>=', $thisWeek)->count();
            $newUsersThisMonth = User::where('created_at', '>=', $thisMonth)->count();
            $newUsersLastMonth = User::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();

            // Calculate growth percentage
            $userGrowthPercentage = $newUsersLastMonth > 0 
                ? round((($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100, 2)
                : ($newUsersThisMonth > 0 ? 100 : 0);

            // Admin statistics
            $totalAdmins = Admin::count();
            $activeAdmins = Admin::where('is_active', true)->count();
            $superAdmins = Admin::where('role', 'super_admin')->where('is_active', true)->count();
            $regularAdmins = Admin::where('role', 'admin')->where('is_active', true)->count();
            $moderators = Admin::where('role', 'moderator')->where('is_active', true)->count();

            // User registration by provider
            $usersByProvider = User::select('provider', DB::raw('count(*) as count'))
                ->groupBy('provider')
                ->get()
                ->pluck('count', 'provider')
                ->toArray();

            // Recent user registrations (last 7 days)
            $recentRegistrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item->date)->format('M d'),
                        'count' => $item->count
                    ];
                });

            // User activity (users who logged in recently)
            $activeUsersLastWeek = User::where('last_login_at', '>=', Carbon::now()->subWeek())->count();
            $activeUsersLastMonth = User::where('last_login_at', '>=', Carbon::now()->subMonth())->count();

            // Recent users (last 10)
            $recentUsers = User::select('id', 'name', 'email', 'provider', 'is_active', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'provider' => ucfirst($user->provider),
                        'status' => $user->is_active ? 'Active' : 'Inactive',
                        'joined' => $user->created_at->diffForHumans()
                    ];
                });

            // System health indicators
            $systemHealth = [
                'database_connection' => $this->checkDatabaseConnection(),
                'storage_writable' => is_writable(storage_path()),
                'cache_working' => $this->checkCacheWorking(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_users' => $totalUsers,
                        'active_users' => $activeUsers,
                        'inactive_users' => $inactiveUsers,
                        'verified_users' => $verifiedUsers,
                        'unverified_users' => $unverifiedUsers,
                        'total_admins' => $totalAdmins,
                        'active_admins' => $activeAdmins,
                    ],
                    'user_growth' => [
                        'new_today' => $newUsersToday,
                        'new_this_week' => $newUsersThisWeek,
                        'new_this_month' => $newUsersThisMonth,
                        'new_last_month' => $newUsersLastMonth,
                        'growth_percentage' => $userGrowthPercentage,
                    ],
                    'admin_breakdown' => [
                        'super_admins' => $superAdmins,
                        'admins' => $regularAdmins,
                        'moderators' => $moderators,
                    ],
                    'user_providers' => $usersByProvider,
                    'recent_registrations' => $recentRegistrations,
                    'user_activity' => [
                        'active_last_week' => $activeUsersLastWeek,
                        'active_last_month' => $activeUsersLastMonth,
                    ],
                    'recent_users' => $recentUsers,
                    'system_health' => $systemHealth,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user analytics data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserAnalytics(Request $request)
    {
        try {
            $period = $request->get('period', '30'); // days
            $startDate = Carbon::now()->subDays($period);

            // User registrations over time
            $registrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'count' => $item->count
                    ];
                });

            // User status distribution
            $statusDistribution = [
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
            ];

            // Verification status
            $verificationStatus = [
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'unverified' => User::whereNull('email_verified_at')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'registrations' => $registrations,
                    'status_distribution' => $statusDistribution,
                    'verification_status' => $verificationStatus,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user analytics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Check database connection
     *
     * @return bool
     */
    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if cache is working
     *
     * @return bool
     */
    private function checkCacheWorking()
    {
        try {
            $key = 'health_check_' . time();
            cache()->put($key, 'test', 60);
            $value = cache()->get($key);
            cache()->forget($key);
            return $value === 'test';
        } catch (\Exception $e) {
            return false;
        }
    }
}