<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function createOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string', // Allow 'guest' or user ID
            'cart_items' => 'required|array|min:1',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|array',
            'billing_address' => 'required|array',
            'payment_method' => 'required|string',
            'payment_transaction_id' => 'nullable|string'
        ]);

        // Additional validation for user_id if not guest
        if ($request->user_id !== 'guest') {
            $userValidator = Validator::make($request->all(), [
                'user_id' => 'exists:frontend_users,id'
            ]);
            
            if ($userValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user ID',
                    'errors' => $userValidator->errors()
                ], 422);
            }
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            \Log::info('Starting order creation', $request->all());
            DB::beginTransaction();

            $totalAmount = 0;
            $orderItems = [];

            // Calculate total and prepare order items
            foreach ($request->cart_items as $cartItem) {
                $product = Product::find($cartItem['product_id']);
                
                if (!$product) {
                    throw new \Exception("Product with ID {$cartItem['product_id']} not found");
                }
                
                $itemTotal = $product->price * $cartItem['quantity'];
                $totalAmount += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'unit_price' => $product->price,
                    'quantity' => $cartItem['quantity'],
                    'total_price' => $itemTotal
                ];
            }

            // Create order with null order_number initially
            $order = Order::create([
                'user_id' => $request->user_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'payment_transaction_id' => $request->payment_transaction_id,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'notes' => $request->notes ?? null
            ]);

            // Generate proper order number after order is created and has an ID
            $order->order_number = $order->generateOrderNumber();
            $order->save();
            
            // Add unique constraint back for future orders
            // This is handled by a separate migration if needed

            // Create order items
            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);
            }

            // Clear user's cart (only for authenticated users)
            if ($request->user_id !== 'guest') {
                CartItem::where('user_id', $request->user_id)->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order->load('orderItems')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserOrders($userId): JsonResponse
    {
        try {
            $orders = Order::with('orderItems')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllOrders(): JsonResponse
    {
        try {
            $orders = Order::with(['orderItems', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateOrderStatus(Request $request, $orderId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,delivered,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $order = Order::findOrFail($orderId);
            
            $updateData = ['status' => $request->status];
            
            if ($request->status === 'confirmed') {
                $updateData['confirmed_at'] = now();
                
                // Validate stock availability before confirming order
                $stockErrors = [];
                foreach ($order->orderItems as $item) {
                    $product = Product::find($item->product_id);
                    if (!$product) {
                        $stockErrors[] = "Product with ID {$item->product_id} not found";
                    } elseif ($product->stock < $item->quantity) {
                        $stockErrors[] = "Insufficient stock for {$product->name}. Available: {$product->stock}, Required: {$item->quantity}";
                    }
                }
                
                // If there are stock errors, return them
                if (!empty($stockErrors)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot confirm order due to stock issues',
                        'errors' => $stockErrors
                    ], 400);
                }
                
                // Update stock when order is confirmed (using transaction for data integrity)
                DB::transaction(function () use ($order) {
                    foreach ($order->orderItems as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->decrement('stock', $item->quantity);
                        }
                    }
                });
            } elseif ($request->status === 'delivered') {
                $updateData['delivered_at'] = now();
            } elseif ($request->status === 'cancelled') {
                // Restore stock if order was previously confirmed
                if ($order->status === 'confirmed') {
                    DB::transaction(function () use ($order) {
                        foreach ($order->orderItems as $item) {
                            $product = Product::find($item->product_id);
                            if ($product) {
                                $product->increment('stock', $item->quantity);
                            }
                        }
                    });
                }
            }
            
            $order->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $order->load('orderItems')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrder($orderId): JsonResponse
    {
        try {
            $order = Order::with(['orderItems', 'user'])->findOrFail($orderId);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get order statistics for admin dashboard
     */
    public function getOrderStatistics(): JsonResponse
    {
        try {
            $totalOrders = Order::count();
            $pendingOrders = Order::where('status', 'pending')->count();
            $confirmedOrders = Order::where('status', 'confirmed')->count();
            $deliveredOrders = Order::where('status', 'delivered')->count();
            $cancelledOrders = Order::where('status', 'cancelled')->count();
            $todayOrders = Order::whereDate('created_at', today())->count();
            $totalRevenue = Order::whereIn('status', ['confirmed', 'delivered'])->sum('total_amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_orders' => $totalOrders,
                    'pending_orders' => $pendingOrders,
                    'confirmed_orders' => $confirmedOrders,
                    'delivered_orders' => $deliveredOrders,
                    'cancelled_orders' => $cancelledOrders,
                    'today_orders' => $todayOrders,
                    'total_revenue' => $totalRevenue
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
