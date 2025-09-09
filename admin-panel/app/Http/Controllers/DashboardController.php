<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FrontendUser;
use App\Models\Product;
use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $totalCustomers = FrontendUser::count();
        
        // Get today's orders count
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        
        // Get pending orders count
        $pendingOrders = Order::where('status', 'pending')->count();
        
        // Get customer requests (assuming this is contact form submissions or similar)
        $customerRequests = 0; // Placeholder - implement based on your requirements

        // Get cart statistics
        $activeCarts = CartItem::distinct('session_id')->count('session_id');
        $totalCartItems = CartItem::sum('quantity');
        $totalCartValue = CartItem::join('products', 'cart_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(cart_items.quantity * products.price) as total')
            ->value('total') ?? 0;
        
        // Get comprehensive analytics data
        $ordersData = $this->getOrdersAnalytics();
        $customersData = $this->getCustomersAnalytics();
        $reportsData = $this->getReportsAnalytics();
        $earningsData = $this->getEarningsData();
        
        return view('admin.dashboard', compact(
            'totalCustomers',
            'todayOrders', 
            'pendingOrders',
            'customerRequests',
            'earningsData',
            'activeCarts',
            'totalCartItems',
            'totalCartValue',
            'ordersData',
            'customersData',
            'reportsData'
        ));
    }
    
    private function getOrdersAnalytics()
    {
        // Monthly orders data (last 12 months)
        $monthlyOrders = Order::where('created_at', '>=', Carbon::now()->subMonths(12))
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Weekly orders data (last 8 weeks)
        $weeklyOrders = Order::where('created_at', '>=', Carbon::now()->subWeeks(8))
            ->selectRaw('WEEK(created_at) as week, YEAR(created_at) as year, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('year', 'week')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();
        
        // Order status distribution
        $statusDistribution = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        // Prepare monthly data
        $monthlyLabels = [];
        $monthlyOrderCounts = [];
        $monthlyRevenue = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            
            $monthData = $monthlyOrders->where('month', $date->month)
                                     ->where('year', $date->year)
                                     ->first();
            
            $monthlyOrderCounts[] = $monthData ? (int)$monthData->count : 0;
            $monthlyRevenue[] = $monthData ? (float)$monthData->revenue : 0;
        }
        
        // Prepare weekly data
        $weeklyLabels = [];
        $weeklyOrderCounts = [];
        $weeklyRevenue = [];
        
        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::now()->subWeeks($i);
            $weeklyLabels[] = 'Week ' . $date->weekOfYear;
            
            $weekData = $weeklyOrders->where('week', $date->weekOfYear)
                                   ->where('year', $date->year)
                                   ->first();
            
            $weeklyOrderCounts[] = $weekData ? (int)$weekData->count : 0;
            $weeklyRevenue[] = $weekData ? (float)$weekData->revenue : 0;
        }
        
        return [
            'monthly' => [
                'labels' => $monthlyLabels,
                'orders' => $monthlyOrderCounts,
                'revenue' => $monthlyRevenue
            ],
            'weekly' => [
                'labels' => $weeklyLabels,
                'orders' => $weeklyOrderCounts,
                'revenue' => $weeklyRevenue
            ],
            'status' => [
                'labels' => $statusDistribution->pluck('status')->toArray(),
                'data' => $statusDistribution->pluck('count')->toArray()
            ]
        ];
    }
    
    private function getCustomersAnalytics()
    {
        // Monthly customer registrations (last 12 months)
        $monthlyCustomers = FrontendUser::where('created_at', '>=', Carbon::now()->subMonths(12))
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Weekly customer registrations (last 8 weeks)
        $weeklyCustomers = FrontendUser::where('created_at', '>=', Carbon::now()->subWeeks(8))
            ->selectRaw('WEEK(created_at) as week, YEAR(created_at) as year, COUNT(*) as count')
            ->groupBy('year', 'week')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();
        
        // Active customers (customers with orders in last 30 days)
        $activeCustomers = FrontendUser::whereHas('orders', function($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        })->count();
        
        // Prepare monthly data
        $monthlyLabels = [];
        $monthlyRegistrations = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            
            $monthData = $monthlyCustomers->where('month', $date->month)
                                        ->where('year', $date->year)
                                        ->first();
            
            $monthlyRegistrations[] = $monthData ? (int)$monthData->count : 0;
        }
        
        // Prepare weekly data
        $weeklyLabels = [];
        $weeklyRegistrations = [];
        
        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::now()->subWeeks($i);
            $weeklyLabels[] = 'Week ' . $date->weekOfYear;
            
            $weekData = $weeklyCustomers->where('week', $date->weekOfYear)
                                      ->where('year', $date->year)
                                      ->first();
            
            $weeklyRegistrations[] = $weekData ? (int)$weekData->count : 0;
        }
        
        return [
            'monthly' => [
                'labels' => $monthlyLabels,
                'registrations' => $monthlyRegistrations
            ],
            'weekly' => [
                'labels' => $weeklyLabels,
                'registrations' => $weeklyRegistrations
            ],
            'active' => $activeCustomers,
            'total' => FrontendUser::count()
        ];
    }
    
    private function getReportsAnalytics()
    {
        // Monthly performance metrics
        $monthlyMetrics = [];
        for ($i = 11; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth();
            
            $orders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
            $revenue = Order::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', '!=', 'cancelled')
                          ->sum('total_amount');
            $customers = FrontendUser::whereBetween('created_at', [$startDate, $endDate])->count();
            
            $monthlyMetrics[] = [
                'month' => $startDate->format('M Y'),
                'orders' => $orders,
                'revenue' => (float)$revenue,
                'customers' => $customers
            ];
        }
        
        // Weekly performance metrics
        $weeklyMetrics = [];
        for ($i = 7; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $orders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
            $revenue = Order::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', '!=', 'cancelled')
                          ->sum('total_amount');
            $customers = FrontendUser::whereBetween('created_at', [$startDate, $endDate])->count();
            
            $weeklyMetrics[] = [
                'week' => 'Week ' . $startDate->weekOfYear,
                'orders' => $orders,
                'revenue' => (float)$revenue,
                'customers' => $customers
            ];
        }
        
        return [
            'monthly' => $monthlyMetrics,
            'weekly' => $weeklyMetrics
        ];
    }
    
    private function getEarningsData()
    {
        // Get earnings data for chart - last 6 months
        $monthlyEarnings = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Prepare chart data
        $labels = [];
        $data = [];
        
        // Fill in the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $labels[] = $monthName;
            
            // Find earnings for this month
            $earnings = $monthlyEarnings->where('month', $date->month)
                                      ->where('year', $date->year)
                                      ->first();
            
            $data[] = $earnings ? (float)$earnings->total : 0;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function downloadMonthlyReport()
    {
        $currentMonth = Carbon::now();
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();
        
        // Get monthly data
        $orders = Order::with('user')->whereBetween('created_at', [$startDate, $endDate])->get();
        $totalOrders = $orders->count();
        $totalRevenue = $orders->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalCustomers = FrontendUser::whereBetween('created_at', [$startDate, $endDate])->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Order status breakdown with revenue
        $ordersByStatus = [];
        foreach ($orders->groupBy('status') as $status => $statusOrders) {
            $ordersByStatus[$status] = [
                'count' => $statusOrders->count(),
                'revenue' => $statusOrders->where('status', '!=', 'cancelled')->sum('total_amount')
            ];
        }
        
        // Top products
        $topProducts = Product::withCount(['orderItems' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                });
            }])
            ->with(['orderItems' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                });
            }])
            ->get()
            ->map(function($product) {
                $product->total_revenue = $product->orderItems->sum(function($item) {
                    return $item->quantity * $item->price;
                });
                return $product;
            })
            ->sortByDesc('orders_count')
            ->take(10);
        
        // Recent orders
        $recentOrders = $orders->sortByDesc('created_at')->take(20);
        
        $data = [
            'month' => $currentMonth->format('F'),
            'year' => $currentMonth->format('Y'),
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'totalCustomers' => $totalCustomers,
            'averageOrderValue' => $averageOrderValue,
            'ordersByStatus' => $ordersByStatus,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders
        ];
        
        $pdf = Pdf::loadView('reports.monthly-report', $data);
        $filename = 'monthly-report-' . $currentMonth->format('Y-m') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function downloadWeeklyReport()
    {
        $currentWeek = Carbon::now();
        $startDate = $currentWeek->copy()->startOfWeek();
        $endDate = $currentWeek->copy()->endOfWeek();
        
        // Get weekly data
        $orders = Order::with('user')->whereBetween('created_at', [$startDate, $endDate])->get();
        $totalOrders = $orders->count();
        $totalRevenue = $orders->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalCustomers = FrontendUser::whereBetween('created_at', [$startDate, $endDate])->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Order status breakdown with revenue
        $ordersByStatus = [];
        foreach ($orders->groupBy('status') as $status => $statusOrders) {
            $ordersByStatus[$status] = [
                'count' => $statusOrders->count(),
                'revenue' => $statusOrders->where('status', '!=', 'cancelled')->sum('total_amount')
            ];
        }
        
        // Daily breakdown for the week
        $dailyData = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOrders = $orders->filter(function($order) use ($date) {
                return $order->created_at->format('Y-m-d') === $date->format('Y-m-d');
            });
            $dayRevenue = $dayOrders->where('status', '!=', 'cancelled')->sum('total_amount');
            $dayCustomers = FrontendUser::whereDate('created_at', $date->format('Y-m-d'))->count();
            
            $dailyData[$date->format('Y-m-d')] = [
                'orders' => $dayOrders->count(),
                'revenue' => $dayRevenue,
                'customers' => $dayCustomers,
                'avg_order_value' => $dayOrders->count() > 0 ? $dayRevenue / $dayOrders->count() : 0
            ];
        }
        
        // Top products
        $topProducts = Product::withCount(['orderItems' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                });
            }])
            ->with(['orderItems' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                });
            }])
            ->get()
            ->map(function($product) {
                $product->total_revenue = $product->orderItems->sum(function($item) {
                    return $item->quantity * $item->price;
                });
                return $product;
            })
            ->sortByDesc('orders_count')
            ->take(10);
        
        // Recent orders
        $recentOrders = $orders->sortByDesc('created_at')->take(15);
        
        $data = [
            'week' => $currentWeek->weekOfYear,
            'year' => $currentWeek->format('Y'),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'totalCustomers' => $totalCustomers,
            'averageOrderValue' => $averageOrderValue,
            'ordersByStatus' => $ordersByStatus,
            'dailyData' => $dailyData,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders
        ];
        
        $pdf = Pdf::loadView('reports.weekly-report', $data);
        $filename = 'weekly-report-week-' . $currentWeek->weekOfYear . '-' . $currentWeek->format('Y') . '.pdf';
        
        return $pdf->download($filename);
    }
}