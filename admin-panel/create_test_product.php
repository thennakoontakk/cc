<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

// Create a test product
$product = Product::create([
    'name' => 'Test Product',
    'description' => 'Test Description',
    'price' => 10.00,
    'category' => 'test',
    'image' => 'test.jpg',
    'stock_quantity' => 100
]);

echo "Product created with ID: " . $product->id . "\n";