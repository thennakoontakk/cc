<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\FrontendUser;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get some products to add to cart
        $products = Product::take(5)->get();
        
        if ($products->isEmpty()) {
            $this->command->info('No products found. Please seed products first.');
            return;
        }

        // Get some users
        $users = FrontendUser::take(3)->get();
        
        // Create cart items for logged-in users
        foreach ($users as $user) {
            foreach ($products->take(2) as $product) {
                CartItem::create([
                    'user_id' => $user->id,
                    'session_id' => null,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                    'product_category' => $product->category,
                    'product_image' => $product->image,
                    'product_price' => $product->price,
                    'quantity' => rand(1, 3),
                    'total_price' => $product->price * rand(1, 3)
                ]);
            }
        }
        
        // Create cart items for guest users (session-based)
        $sessionIds = [
            'session_' . time() . '_guest1',
            'session_' . time() . '_guest2',
            'session_' . time() . '_guest3'
        ];
        
        foreach ($sessionIds as $sessionId) {
            foreach ($products->take(3) as $product) {
                $quantity = rand(1, 4);
                CartItem::create([
                    'user_id' => null,
                    'session_id' => $sessionId,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                    'product_category' => $product->category,
                    'product_image' => $product->image,
                    'product_price' => $product->price,
                    'quantity' => $quantity,
                    'total_price' => $product->price * $quantity
                ]);
            }
        }
        
        $this->command->info('Cart items seeded successfully!');
    }
}