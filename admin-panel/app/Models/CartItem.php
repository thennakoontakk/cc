<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'product_name',
        'product_description',
        'product_category',
        'product_image',
        'product_price',
        'quantity',
        'total_price'
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Calculate total price based on quantity and product price
    public function calculateTotalPrice()
    {
        $this->total_price = $this->quantity * $this->product_price;
        return $this->total_price;
    }
}
