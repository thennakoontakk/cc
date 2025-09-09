<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CartItem;

class CartManagementController extends Controller
{
    private $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.api_base_url', 'http://localhost:8000/api');
    }

    /**
     * Display a listing of all cart items
     */
    public function index(Request $request)
    {
        try {
            // Fetch cart items directly from database instead of API
            $cartItems = \App\Models\CartItem::with(['user', 'product'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
            
            // Group cart items by user/session for better display
            $groupedItems = [];
            foreach ($cartItems as $item) {
                $key = $item['user_id'] ? 'user_' . $item['user_id'] : 'session_' . $item['session_id'];
                if (!isset($groupedItems[$key])) {
                    $groupedItems[$key] = [
                        'user' => $item['user'] ?? null,
                        'session_id' => $item['session_id'] ?? null,
                        'items' => [],
                        'total_items' => 0,
                        'total_amount' => 0
                    ];
                }
                $groupedItems[$key]['items'][] = $item;
                $groupedItems[$key]['total_items'] += $item['quantity'];
                $groupedItems[$key]['total_amount'] += $item['total_price'];
            }
            
            return view('admin.cart.index', compact('groupedItems', 'cartItems'));
        } catch (\Exception $e) {
            Log::error('Exception while fetching cart items', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('admin.cart.index', [
                'groupedItems' => [],
                'cartItems' => [],
                'error' => 'An error occurred while loading cart items'
            ]);
        }
    }

    /**
     * Remove a cart item
     */
    public function removeItem($id)
    {
        try {
            $response = Http::delete($this->apiBaseUrl . '/cart/' . $id);
            
            if ($response->successful()) {
                return redirect()->route('admin.cart.index')
                    ->with('success', 'Cart item removed successfully');
            } else {
                return redirect()->route('admin.cart.index')
                    ->with('error', 'Failed to remove cart item');
            }
        } catch (\Exception $e) {
            Log::error('Exception while removing cart item', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.cart.index')
                ->with('error', 'An error occurred while removing the cart item');
        }
    }

    /**
     * Clear all cart items for a user/session
     */
    public function clearCart(Request $request)
    {
        try {
            $params = [];
            if ($request->user_id) {
                $params['user_id'] = $request->user_id;
            } elseif ($request->session_id) {
                $params['session_id'] = $request->session_id;
            }
            
            $response = Http::delete($this->apiBaseUrl . '/cart', $params);
            
            if ($response->successful()) {
                return redirect()->route('admin.cart.index')
                    ->with('success', 'Cart cleared successfully');
            } else {
                return redirect()->route('admin.cart.index')
                    ->with('error', 'Failed to clear cart');
            }
        } catch (\Exception $e) {
            Log::error('Exception while clearing cart', [
                'params' => $request->all(),
                'message' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.cart.index')
                ->with('error', 'An error occurred while clearing the cart');
        }
    }

    /**
     * Get cart statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $cartItems = CartItem::with(['user', 'product'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
            
            $stats = [
                'total_carts' => count(array_unique(array_map(function($item) {
                    return $item['user_id'] ? 'user_' . $item['user_id'] : 'session_' . $item['session_id'];
                }, $cartItems))),
                'total_items' => array_sum(array_column($cartItems, 'quantity')),
                'total_value' => array_sum(array_column($cartItems, 'total_price')),
                'abandoned_carts' => 0 // This would need additional logic to determine abandoned carts
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Exception while fetching cart statistics', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching cart statistics'
            ], 500);
        }
    }
}