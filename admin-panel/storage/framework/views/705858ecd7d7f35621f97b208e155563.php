<?php $__env->startSection('title', 'Order Details - Admin Panel'); ?>
<?php $__env->startSection('page-title', 'Order Details'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    /* Order show specific styles */
        
        .page-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .breadcrumb {
            color: #666;
            font-size: 14px;
        }
        
        .back-button {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .back-button:hover {
            background-color: #545b62;
        }
        
        /* Order Details Grid */
        .order-details {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .card-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            background-color: #f8f9fa;
        }
        
        .card-header h3 {
            font-size: 18px;
            color: #333;
            margin: 0;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Order Info */
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #333;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .payment-paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .payment-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        /* Order Items */
        .order-items {
            margin-top: 20px;
        }
        
        .item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .item-meta {
            font-size: 14px;
            color: #666;
        }
        
        .item-price {
            font-weight: 600;
            color: #333;
            text-align: right;
        }
        
        /* Customer Info */
        .customer-info {
            margin-bottom: 20px;
        }
        
        .customer-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .customer-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        .customer-email {
            color: #666;
        }
        
        /* Shipping Address */
        .shipping-address {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }
        
        .address-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .address-details {
            color: #666;
            line-height: 1.5;
        }
        
        /* Order Summary */
        .order-summary {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-row.total {
            font-weight: 600;
            font-size: 18px;
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        /* Action Buttons */
        .action-section {
            margin-top: 30px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .order-details {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .order-info {
                grid-template-columns: 1fr;
            }
        }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
                <div class="page-header">
                    <div>
                        <h1>Order Details</h1>
                        <div class="breadcrumb">Home / Orders / Order #<?php echo e($order['order_number'] ?? $order['id']); ?></div>
                    </div>
                    <a href="<?php echo e(route('admin.orders.index')); ?>" class="back-button">← Back to Orders</a>
                </div>
                
                <?php if(session('success')): ?>
                    <div class="alert alert-success">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
                
                <?php if(session('error') || isset($error)): ?>
                    <div class="alert alert-error">
                        <?php echo e(session('error') ?? $error); ?>

                    </div>
                <?php endif; ?>
                
                <div class="order-details">
                    <!-- Order Information -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Order Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="order-info">
                                <div class="info-item">
                                    <div class="info-label">Order Number</div>
                                    <div class="info-value">#<?php echo e($order['order_number'] ?? $order['id']); ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Order Date</div>
                                    <div class="info-value"><?php echo e(date('M d, Y H:i', strtotime($order['created_at'] ?? now()))); ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Order Status</div>
                                    <div class="info-value">
                                        <span class="status-badge status-<?php echo e($order['status'] ?? 'pending'); ?>">
                                            <?php echo e(ucfirst($order['status'] ?? 'pending')); ?>

                                        </span>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Payment Status</div>
                                    <div class="info-value">
                                        <span class="status-badge payment-<?php echo e($order['payment_status'] ?? 'pending'); ?>">
                                            <?php echo e(ucfirst($order['payment_status'] ?? 'pending')); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Items -->
                            <div class="order-items">
                                <h4 style="margin-bottom: 15px; color: #333;">Order Items</h4>
                                <?php if(isset($order['items']) && count($order['items']) > 0): ?>
                                    <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="item">
                                            <img src="<?php echo e($item['product_image'] ?? '/images/placeholder.jpg'); ?>" alt="<?php echo e($item['product_name'] ?? 'Product'); ?>" class="item-image">
                                            <div class="item-details">
                                                <div class="item-name"><?php echo e($item['product_name'] ?? 'Unknown Product'); ?></div>
                                                <div class="item-meta">
                                                    Quantity: <?php echo e($item['quantity'] ?? 1); ?> × $<?php echo e(number_format($item['unit_price'] ?? 0, 2)); ?>

                                                </div>
                                            </div>
                                            <div class="item-price">
                                                $<?php echo e(number_format($item['total_price'] ?? 0, 2)); ?>

                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <p style="color: #666; text-align: center; padding: 20px;">No items found for this order.</p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Order Summary -->
                            <div class="order-summary">
                                <div class="summary-row">
                                    <span>Subtotal:</span>
                                    <span>$<?php echo e(number_format(($order['total_amount'] ?? 0) - ($order['shipping_cost'] ?? 0), 2)); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Shipping:</span>
                                    <span>$<?php echo e(number_format($order['shipping_cost'] ?? 0, 2)); ?></span>
                                </div>
                                <div class="summary-row total">
                                    <span>Total:</span>
                                    <span>$<?php echo e(number_format($order['total_amount'] ?? 0, 2)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer & Shipping Info -->
                    <div>
                        <!-- Customer Information -->
                        <div class="card" style="margin-bottom: 20px;">
                            <div class="card-header">
                                <h3>Customer Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="customer-info">
                                    <div class="customer-details">
                                        <div class="customer-name"><?php echo e($order['user']['name'] ?? 'N/A'); ?></div>
                                        <div class="customer-email"><?php echo e($order['user']['email'] ?? 'N/A'); ?></div>
                                        <?php if(isset($order['user']['phone'])): ?>
                                            <div style="color: #666;"><?php echo e($order['user']['phone']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Shipping Address -->
                        <div class="card">
                            <div class="card-header">
                                <h3>Shipping Address</h3>
                            </div>
                            <div class="card-body">
                                <?php if(isset($order['shipping_address'])): ?>
                                    <div class="shipping-address">
                                        <div class="address-details">
                                            <?php echo e($order['shipping_address']['street'] ?? ''); ?><br>
                                            <?php echo e($order['shipping_address']['city'] ?? ''); ?>, <?php echo e($order['shipping_address']['state'] ?? ''); ?> <?php echo e($order['shipping_address']['zip'] ?? ''); ?><br>
                                            <?php echo e($order['shipping_address']['country'] ?? ''); ?>

                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p style="color: #666;">No shipping address provided.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-section">
                    <div class="action-buttons">
                        <?php if(($order['status'] ?? 'pending') === 'pending'): ?>
                            <form method="POST" action="<?php echo e(route('admin.orders.update-status', $order['id'])); ?>" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to confirm this order?')">Confirm Order</button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if(($order['status'] ?? 'pending') === 'confirmed'): ?>
                            <form method="POST" action="<?php echo e(route('admin.orders.update-status', $order['id'])); ?>" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to mark this order as delivered?')">Mark as Delivered</button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if(in_array($order['status'] ?? 'pending', ['pending', 'confirmed'])): ?>
                            <form method="POST" action="<?php echo e(route('admin.orders.update-status', $order['id'])); ?>" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</button>
                            </form>
                        <?php endif; ?>
                        
                        <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-secondary">Back to Orders</a>
                    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function toggleSubmenu(element) {
        const parent = element.parentElement;
        parent.classList.toggle('open');
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.shared', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thenn\OneDrive\Desktop\softora\08 25\admin-panel\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>