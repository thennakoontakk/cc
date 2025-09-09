<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
// Removed Laravel mail imports - now using EmailJS from frontend

class OrderManagementController extends Controller
{
    private $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.api_base_url', 'http://localhost:8000/api');
    }

    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        try {
            // Build query parameters
            $params = [];
            
            if ($request->has('status') && $request->status !== '') {
                $params['status'] = $request->status;
            }
            
            if ($request->has('payment_status') && $request->payment_status !== '') {
                $params['payment_status'] = $request->payment_status;
            }
            
            if ($request->has('search') && $request->search !== '') {
                $params['search'] = $request->search;
            }
            
            if ($request->has('date_from') && $request->date_from !== '') {
                $params['date_from'] = $request->date_from;
            }
            
            if ($request->has('date_to') && $request->date_to !== '') {
                $params['date_to'] = $request->date_to;
            }
            
            $params['page'] = $request->get('page', 1);
            $params['per_page'] = $request->get('per_page', 15);
            
            // Get orders directly from database
            $query = Order::with(['orderItems.product', 'user'])
                ->orderBy('created_at', 'desc');
            
            // Apply filters
            if (isset($params['status'])) {
                $query->where('status', $params['status']);
            }
            
            if (isset($params['payment_status'])) {
                $query->where('payment_status', $params['payment_status']);
            }
            
            if (isset($params['search'])) {
                $query->where(function($q) use ($params) {
                    $q->where('order_number', 'like', '%' . $params['search'] . '%')
                      ->orWhereHas('user', function($userQuery) use ($params) {
                          $userQuery->where('name', 'like', '%' . $params['search'] . '%')
                                   ->orWhere('email', 'like', '%' . $params['search'] . '%');
                      });
                });
            }
            
            if (isset($params['date_from'])) {
                $query->whereDate('created_at', '>=', $params['date_from']);
            }
            
            if (isset($params['date_to'])) {
                $query->whereDate('created_at', '<=', $params['date_to']);
            }
            
            // Paginate results
            $perPage = $params['per_page'] ?? 15;
            $orders = $query->paginate($perPage);
            
            return view('admin.orders.index', compact('orders', 'request'));
        } catch (\Exception $e) {
            Log::error('Error fetching orders: ' . $e->getMessage());
            return view('admin.orders.index', [
                'orders' => [],
                'pagination' => [],
                'request' => $request,
                'error' => 'An error occurred while loading orders.'
            ]);
        }
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        try {
            $order = Order::with(['orderItems.product', 'user'])->findOrFail($id);
            
            return view('admin.orders.show', compact('order'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Order not found.');
        } catch (\Exception $e) {
            Log::error('Error fetching order: ' . $e->getMessage());
            return redirect()->route('admin.orders.index')
                ->with('error', 'An error occurred while loading order details.');
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,delivered,cancelled'
        ]);

        try {
            $order = Order::findOrFail($id);
            
            $updateData = ['status' => $request->status];
            
            // Set timestamps for specific statuses
            if ($request->status === 'confirmed') {
                $updateData['confirmed_at'] = now();
                
                // Check stock availability before confirming
                foreach ($order->orderItems as $orderItem) {
                    $product = Product::find($orderItem->product_id);
                    if ($product && $product->stock < $orderItem->quantity) {
                        return redirect()->back()->with('error', "Insufficient stock for product: {$product->name}. Available: {$product->stock}, Required: {$orderItem->quantity}");
                    }
                }
                
                // Reduce stock for each product in the order
                foreach ($order->orderItems as $orderItem) {
                    $product = Product::find($orderItem->product_id);
                    if ($product) {
                        $product->decrement('stock', $orderItem->quantity);
                    }
                }
            } elseif ($request->status === 'delivered') {
                $updateData['delivered_at'] = now();
                
                // Return order data for EmailJS notification (handled by frontend)
                $orderData = [
                    'order_id' => $order->id,
                    'customer_name' => $order->user ? $order->user->name : 'N/A',
                    'customer_email' => $order->user ? $order->user->email : null,
                    'order_total' => $order->total_amount,
                    'delivery_address' => $order->shipping_address ? 
                        ($order->shipping_address['address'] . ', ' . $order->shipping_address['city']) : 'N/A',
                    'order_items' => $order->orderItems->map(function($item) {
                        return [
                            'product_name' => $item->product_name,
                            'quantity' => $item->quantity,
                            'price' => $item->price
                        ];
                    })->toArray()
                ];
            } elseif ($request->status === 'cancelled') {
                // Restore stock if order was previously confirmed
                if ($order->status === 'confirmed') {
                    foreach ($order->orderItems as $orderItem) {
                        $product = Product::find($orderItem->product_id);
                        if ($product) {
                            $product->increment('stock', $orderItem->quantity);
                        }
                    }
                }
            }
            
            $order->update($updateData);
            
            $message = 'Order status updated successfully.';
            
            // Add specific messages for different status changes
            switch ($request->status) {
                case 'confirmed':
                    $message = 'Order confirmed successfully.';
                    break;
                case 'delivered':
                    $message = 'Order marked as delivered successfully.';
                    break;
                case 'cancelled':
                    $message = 'Order cancelled successfully.';
                    break;
            }
            
            $response = ['success' => true, 'message' => $message];
            
            // Include order data for email notification if delivered
            if ($request->status === 'delivered' && isset($orderData)) {
                $response['order_data'] = $orderData;
            }
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json($response);
            }
            
            return redirect()->back()->with('success', $message)->with('order_data', $response['order_data'] ?? null);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Order not found.');
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating order status.');
        }
    }

    /**
     * Get order statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $response = Http::get($this->apiBaseUrl . '/admin/orders/statistics');
            
            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
            
            return [
                'total_orders' => 0,
                'pending_orders' => 0,
                'confirmed_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0,
                'today_orders' => 0,
                'total_revenue' => 0
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching order statistics: ' . $e->getMessage());
            return [
                'total_orders' => 0,
                'pending_orders' => 0,
                'confirmed_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0,
                'today_orders' => 0,
                'total_revenue' => 0
            ];
        }
    }
}