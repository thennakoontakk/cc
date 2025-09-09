@extends('layouts.shared')

@section('title', 'View Product - Admin Panel')
@section('page-title', 'Product Details')

@section('styles')
<style>
    /* Product show specific styles */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .page-header h1 {
        color: #2c3e50;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .breadcrumb {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    
    .card-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-header h3 {
        color: #2c3e50;
        font-size: 1.25rem;
        margin: 0;
    }
    
    .card-body {
        padding: 2rem;
    }
    
    .product-details {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 2rem;
        align-items: start;
    }
    
    .product-image {
        text-align: center;
    }
    
    .product-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        border: 1px solid #ddd;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .no-image {
        width: 300px;
        height: 300px;
        background: #f0f0f0;
        border: 2px dashed #ccc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 1.125rem;
        margin: 0 auto;
    }
    
    .info-row {
        display: flex;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .info-label {
        font-weight: 600;
        color: #2c3e50;
        width: 150px;
        flex-shrink: 0;
    }
    
    .info-value {
        color: #34495e;
        flex: 1;
    }
    
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-active {
        background: #d4edda;
        color: #155724;
    }
    
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    
    .price-display {
        font-size: 1.5rem;
        font-weight: 700;
        color: #27ae60;
    }
    
    .stock-display {
        font-size: 1.125rem;
        font-weight: 600;
    }
    
    .stock-low {
        color: #e74c3c;
    }
    
    .stock-medium {
        color: #f39c12;
    }
    
    .stock-high {
        color: #27ae60;
    }
    
    .description-text {
        line-height: 1.6;
        color: #555;
    }
    
    .action-buttons {
        margin-top: 1.25rem;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-block;
        margin-right: 0.5rem;
    }
    
    .btn-primary {
        background: #3498db;
        color: white;
    }
    
    .btn-primary:hover {
        background: #2980b9;
    }
    
    .btn-secondary {
        background: #95a5a6;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #7f8c8d;
    }
    
    .btn-warning {
        background: #f39c12;
        color: white;
    }
    
    .btn-warning:hover {
        background: #e67e22;
    }
    
    .btn-danger {
        background: #e74c3c;
        color: white;
    }
    
    .btn-danger:hover {
        background: #c0392b;
    }
    
    .delete-form {
        display: inline;
    }
    
    @media (max-width: 768px) {
        .product-details {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .info-row {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .info-label {
            width: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Product Details</h1>
        <div class="breadcrumb">Home / Products / View / {{ $product->name }}</div>
    </div>
    <div class="action-buttons">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">Edit Product</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>{{ $product->name }}</h3>
    </div>
    <div class="card-body">
        <div class="product-details">
            <div class="product-image">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                @else
                    <div class="no-image">
                        📷 No Image Available
                    </div>
                @endif
            </div>
            
            <div class="product-info">
                <div class="info-row">
                    <div class="info-label">Product ID:</div>
                    <div class="info-value">#{{ $product->id }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value">{{ $product->name }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Category:</div>
                    <div class="info-value">{{ $product->category }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Price:</div>
                    <div class="info-value">
                        <span class="price-display">${{ number_format($product->price, 2) }}</span>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Stock:</div>
                    <div class="info-value">
                        <span class="stock-display {{ $product->stock <= 5 ? 'stock-low' : ($product->stock <= 20 ? 'stock-medium' : 'stock-high') }}">
                            {{ $product->stock }} units
                            @if($product->stock <= 5)
                                (Low Stock!)
                            @elseif($product->stock <= 20)
                                (Medium Stock)
                            @else
                                (In Stock)
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge {{ $product->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Created:</div>
                    <div class="info-value">{{ $product->created_at->format('F d, Y \\a\\t g:i A') }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Last Updated:</div>
                    <div class="info-value">{{ $product->updated_at->format('F d, Y \\a\\t g:i A') }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Description:</div>
                    <div class="info-value">
                        <div class="description-text">{{ $product->description }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">Edit Product</a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Create New Product</a>
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Product</button>
            </form>
        </div>
    </div>
</div>
@endsection