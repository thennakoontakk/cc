<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    // Add item to cart
    public function addToCart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'session_id' => 'nullable|string',
                'user_id' => 'nullable|integer|exists:frontend_users,id',
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $product = Product::findOrFail($request->product_id);
            
            // Check if item already exists in cart
            $existingItem = CartItem::where('product_id', $request->product_id)
                ->where(function($query) use ($request) {
                    if ($request->user_id) {
                        $query->where('user_id', $request->user_id);
                    } else {
                        $query->where('session_id', $request->session_id);
                    }
                })
                ->first();

            if ($existingItem) {
                // Update quantity if item exists
                $existingItem->quantity += $request->quantity;
                $existingItem->calculateTotalPrice();
                $existingItem->save();
                $cartItem = $existingItem;
            } else {
                // Create new cart item
                $cartItem = CartItem::create([
                    'session_id' => $request->session_id,
                    'user_id' => $request->user_id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                    'product_category' => $product->category,
                    'product_image' => $product->image,
                    'product_price' => $product->price,
                    'quantity' => $request->quantity,
                    'total_price' => $product->price * $request->quantity
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'data' => $cartItem
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get cart items
    public function getCartItems(Request $request)
    {
        try {
            $query = CartItem::with('product');
            
            // Ensure proper filtering - only return items for the specific user or session
            if ($request->user_id) {
                // For logged-in users, only return items with matching user_id
                $query->where('user_id', $request->user_id);
            } elseif ($request->session_id) {
                // For guest users, only return items with matching session_id and null user_id
                $query->where('session_id', $request->session_id)
                      ->whereNull('user_id');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Either user_id or session_id is required'
                ], 400);
            }

            $cartItems = $query->get();
            $totalItems = $cartItems->sum('quantity');
            $totalPrice = $cartItems->sum('total_price');

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $cartItems,
                    'totalItems' => $totalItems,
                    'totalPrice' => $totalPrice
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update cart item quantity
    public function updateCartItem(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $cartItem = CartItem::findOrFail($id);
            $cartItem->quantity = $request->quantity;
            $cartItem->calculateTotalPrice();
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated successfully',
                'data' => $cartItem
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Remove item from cart
    public function removeCartItem($id)
    {
        try {
            $cartItem = CartItem::findOrFail($id);
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Clear cart
    public function clearCart(Request $request)
    {
        try {
            $query = CartItem::query();
            
            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            } elseif ($request->session_id) {
                $query->where('session_id', $request->session_id);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Either user_id or session_id is required'
                ], 400);
            }

            $query->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get all cart items for admin panel
    public function getAllCartItems()
    {
        try {
            $cartItems = CartItem::with(['user', 'product'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cartItems
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart items',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
