<?php $__env->startSection('title', 'Cart Management'); ?>
<?php $__env->startSection('page-title', 'Cart Management'); ?>
<?php $__env->startSection('styles'); ?>
<style>

    /* Alert Messages */
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Statistics Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }

    .stat-card.total-carts {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stat-card.total-carts .stat-number,
    .stat-card.total-carts .stat-label {
        color: white;
    }

    .stat-card.total-items {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .stat-card.total-items .stat-number,
    .stat-card.total-items .stat-label {
        color: white;
    }

    .stat-card.total-value {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .stat-card.total-value .stat-number,
    .stat-card.total-value .stat-label {
        color: white;
    }

    /* Cart Groups */
    .cart-groups {
        display: grid;
        gap: 1.5rem;
    }

    .cart-group {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .cart-group-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .user-info-header {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .cart-summary {
        text-align: right;
    }

    .cart-summary .total-items {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .cart-summary .total-amount {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .cart-items {
        padding: 0;
    }

    .cart-item {
        display: grid;
        grid-template-columns: 60px 80px 1fr 80px 100px 120px 100px 120px 80px;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #eee;
        align-items: center;
        font-size: 0.9rem;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .product-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        background: #f8f9fa;
    }

    .product-info h4 {
        font-size: 1rem;
        color: #333;
        margin-bottom: 0.25rem;
    }

    .product-info p {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 0.25rem;
    }

    .product-category {
        background: #e9ecef;
        color: #495057;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        display: inline-block;
    }

    .quantity {
        text-align: center;
        font-weight: bold;
        color: #333;
    }

    .price {
        text-align: right;
        font-weight: bold;
        color: #333;
    }

    .total-price {
        text-align: right;
        font-weight: bold;
        color: #28a745;
        font-size: 1.1rem;
    }

    .actions {
        text-align: center;
    }

    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .group-actions {
        display: flex;
        gap: 0.5rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #666;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .cart-item {
            grid-template-columns: 1fr;
            gap: 0.5rem;
            text-align: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Alert Messages -->
<?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-error">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<?php if(isset($error)): ?>
    <div class="alert alert-error">
        <?php echo e($error); ?>

    </div>
<?php endif; ?>
<!-- Statistics -->
<?php if(count($groupedItems) > 0): ?>
                    <div class="stats-grid">
                        <div class="stat-card total-carts">
                            <div class="stat-number"><?php echo e(count($groupedItems)); ?></div>
                            <div class="stat-label">Active Carts</div>
                        </div>
                        
                        <div class="stat-card total-items">
                            <div class="stat-number"><?php echo e(array_sum(array_column($groupedItems, 'total_items'))); ?></div>
                            <div class="stat-label">Total Items</div>
                        </div>
                        
                        <div class="stat-card total-value">
                            <div class="stat-number">$<?php echo e(number_format(array_sum(array_column($groupedItems, 'total_amount')), 2)); ?></div>
                            <div class="stat-label">Total Value</div>
                        </div>
                    </div>
<?php endif; ?>

<!-- Cart Groups -->
<?php if(count($groupedItems) > 0): ?>
                    <div class="cart-groups">
                        <?php $__currentLoopData = $groupedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="cart-group">
                                <div class="cart-group-header">
                                    <div class="user-info-header">
                                        <div class="user-avatar">
                                            <?php if($group['user']): ?>
                                                <?php echo e(strtoupper(substr($group['user']['name'], 0, 1))); ?>

                                            <?php else: ?>
                                                G
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: bold;">
                                                <?php if($group['user']): ?>
                                                    <?php echo e($group['user']['name']); ?>

                                                <?php else: ?>
                                                    Guest User
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-size: 0.85rem; opacity: 0.9;">
                                                <?php if($group['user']): ?>
                                                    <?php echo e($group['user']['email']); ?>

                                                <?php else: ?>
                                                    Session: <?php echo e(substr($group['session_id'], 0, 8)); ?>...
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="cart-summary">
                                        <div class="total-items"><?php echo e($group['total_items']); ?> items</div>
                                        <div class="total-amount">$<?php echo e(number_format($group['total_amount'], 2)); ?></div>
                                    </div>
                                    
                                    <div class="group-actions">
                                        <form method="POST" action="<?php echo e(route('admin.cart.clear')); ?>" style="display: inline;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <?php if($group['user']): ?>
                                                <input type="hidden" name="user_id" value="<?php echo e($group['user']['id']); ?>">
                                            <?php else: ?>
                                                <input type="hidden" name="session_id" value="<?php echo e($group['session_id']); ?>">
                                            <?php endif; ?>
                                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to clear this cart?')">
                                                Clear Cart
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Cart Items Header -->
                                <div class="cart-items-header">
                                    <div style="font-size: 0.8rem; font-weight: bold; color: #666; padding: 0.5rem 1.5rem; background: #f8f9fa; border-bottom: 1px solid #ddd; display: grid; grid-template-columns: 60px 80px 1fr 80px 100px 120px 100px 120px 80px; gap: 1rem; align-items: center;">
                                        <div>ID</div>
                                        <div>Image</div>
                                        <div>Product Details</div>
                                        <div>Product ID</div>
                                        <div>Quantity</div>
                                        <div>Unit Price</div>
                                        <div>Total Price</div>
                                        <div>Added Date</div>
                                        <div>Actions</div>
                                    </div>
                                </div>
                                
                                <div class="cart-items">
                                    <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="cart-item">
                                            <div style="font-weight: bold; color: #007bff;">
                                                #<?php echo e($item['id']); ?>

                                            </div>
                                            
                                            <div>
                                                <?php if($item['product_image']): ?>
                                                    <img src="<?php echo e(asset('storage/' . $item['product_image'])); ?>" alt="<?php echo e($item['product_name']); ?>" class="product-image">
                                                <?php else: ?>
                                                    <div class="product-image" style="display: flex; align-items: center; justify-content: center; background: #f8f9fa; color: #666;">📦</div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="product-info">
                                                <h4><?php echo e($item['product_name']); ?></h4>
                                                <p><?php echo e(Str::limit($item['product_description'], 50)); ?></p>
                                                <span class="product-category"><?php echo e($item['product_category']); ?></span>
                                            </div>
                                            
                                            <div style="text-align: center; font-weight: bold; color: #28a745;">
                                                #<?php echo e($item['product_id']); ?>

                                            </div>
                                            
                                            <div class="quantity">
                                                <?php echo e($item['quantity']); ?>

                                            </div>
                                            
                                            <div class="price">
                                                $<?php echo e(number_format($item['product_price'], 2)); ?>

                                            </div>
                                            
                                            <div class="total-price">
                                                $<?php echo e(number_format($item['total_price'], 2)); ?>

                                            </div>
                                            
                                            <div style="text-align: center; font-size: 0.8rem; color: #666;">
                                                <?php echo e(date('M j, Y', strtotime($item['created_at']))); ?><br>
                                                <small><?php echo e(date('g:i A', strtotime($item['created_at']))); ?></small>
                                            </div>
                                            
                                            <div class="actions">
                                                <form method="POST" action="<?php echo e(route('admin.cart.remove-item', $item['id'])); ?>" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?')">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">🛒</div>
        <h3>No Cart Items Found</h3>
        <p>There are currently no items in any shopping carts.</p>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Auto-refresh every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.shared', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thenn\OneDrive\Desktop\softora\08 25\admin-panel\resources\views/admin/cart/index.blade.php ENDPATH**/ ?>