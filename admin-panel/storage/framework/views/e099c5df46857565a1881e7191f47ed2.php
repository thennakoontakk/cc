<?php $__env->startSection('title', 'Order Management - Admin Panel'); ?>
<?php $__env->startSection('page-title', 'Order Management'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    /* Order management specific styles */
    .page-header {
        margin-bottom: 2rem;
    }
    
    .page-header h1 {
        font-size: 1.75rem;
        color: #333;
        margin-bottom: 0.625rem;
    }
    
    .breadcrumb {
        color: #666;
        font-size: 0.875rem;
    }
    
    /* Enhanced Filters */
    .filters-section {
        background: white;
        padding: 1.5625rem;
        border-radius: 0.75rem;
        box-shadow: 0 0.25rem 0.375rem rgba(0,0,0,0.07);
        margin-bottom: 1.5625rem;
    }
    
    .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        padding-bottom: 0.9375rem;
        border-bottom: 0.125rem solid #f0f0f0;
    }
    
    .filters-header h3 {
        color: #2c3e50;
        font-size: 1.125rem;
        font-weight: 600;
    }
    
    .quick-filters {
        display: flex;
        gap: 0.625rem;
    }
    
    .quick-filter {
        padding: 0.5rem 1rem;
        background: #f8f9fa;
        color: #6c757d;
        text-decoration: none;
        border-radius: 1.25rem;
        font-size: 0.8125rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 0.0625rem solid #e9ecef;
    }
    
    .quick-filter:hover {
        background: #e9ecef;
        color: #495057;
    }
    
    .quick-filter.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    
    .filters-form .filter-row {
        display: flex;
        gap: 1.25rem;
        align-items: end;
        flex-wrap: wrap;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        min-width: 10rem;
    }
    
    .filter-group label {
        margin-bottom: 0.375rem;
        font-weight: 500;
        color: #495057;
        font-size: 0.875rem;
    }
    
    .filter-group input,
    .filter-group select {
        padding: 0.625rem 0.875rem;
        border: 0.125rem solid #e9ecef;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: border-color 0.3s ease;
    }
    
    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #007bff;
    }
    
    .filter-actions {
        display: flex;
        gap: 0.75rem;
        align-items: end;
    }
    
    /* Order Statistics */
    .order-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(12.5rem, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5625rem;
    }
    
    .stat-item {
        background: white;
        padding: 1.25rem;
        border-radius: 0.625rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.05);
        text-align: center;
        border-left: 0.25rem solid #6c757d;
    }
    
    .stat-item.pending {
        border-left-color: #ffc107;
    }
    
    .stat-item.confirmed {
        border-left-color: #28a745;
    }
    
    .stat-item.delivered {
        border-left-color: #17a2b8;
    }
    
    .stat-number {
        display: block;
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.3125rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background-color: #4a90e2;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #357abd;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #545b62;
    }
    
    /* Orders Table */
    .orders-table {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.625rem rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .table-header {
        padding: 1.25rem;
        border-bottom: 0.0625rem solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table-header h3 {
        font-size: 1.125rem;
        color: #333;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th,
    .table td {
        padding: 0.75rem 0.9375rem;
        text-align: left;
        border-bottom: 0.0625rem solid #eee;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #333;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Status Badges */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 1.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03125rem;
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
    
    /* Enhanced Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .action-buttons .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-weight: 500;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .btn-info {
        background-color: #17a2b8;
        color: white;
    }
    
    .btn-info:hover {
        background-color: #138496;
        transform: translateY(-0.0625rem);
    }
    
    .btn-success {
        background-color: #28a745;
        color: white;
    }
    
    .btn-success:hover {
        background-color: #218838;
        transform: translateY(-0.0625rem);
    }
    
    .btn-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .btn-warning:hover {
        background-color: #e0a800;
        transform: translateY(-0.0625rem);
    }
    
    .btn-danger {
        background-color: #dc3545;
        color: white;
    }
    
    .btn-danger:hover {
        background-color: #c82333;
        transform: translateY(-0.0625rem);
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-0.0625rem);
    }
    
    /* Dropdown Styles */
    .dropdown {
        position: relative;
        display: inline-block;
    }
    
    .dropdown-toggle {
        background: #f8f9fa;
        border: 0.0625rem solid #dee2e6;
        padding: 0.375rem 0.625rem;
        border-radius: 0.25rem;
        cursor: pointer;
    }
    
    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background: white;
        border: 0.0625rem solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.25rem 0.375rem rgba(0,0,0,0.1);
        z-index: 1000;
        min-width: 9.375rem;
    }
    
    .dropdown-menu a {
        display: block;
        padding: 0.5rem 0.75rem;
        color: #495057;
        text-decoration: none;
        font-size: 0.8125rem;
        transition: background-color 0.2s;
    }
    
    .dropdown-menu a:hover {
        background-color: #f8f9fa;
    }
    
    /* Icons */
    .icon-eye::before { content: '👁'; }
    .icon-check::before { content: '✓'; }
    .icon-truck::before { content: '🚚'; }
    .icon-x::before { content: '✕'; }
    .icon-more::before { content: '⋯'; }
    .icon-loading::before { content: '⟳'; animation: spin 1s linear infinite; }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.625rem;
        margin-top: 1.25rem;
    }
    
    .pagination a,
    .pagination span {
        padding: 0.5rem 0.75rem;
        border: 0.0625rem solid #ddd;
        text-decoration: none;
        color: #333;
        border-radius: 0.25rem;
    }
    
    .pagination a:hover {
        background-color: #f8f9fa;
    }
    
    .pagination .current {
        background-color: #4a90e2;
        color: white;
        border-color: #4a90e2;
    }
    
    /* Alert Messages */
    .alert {
        padding: 0.75rem 1.25rem;
        border-radius: 0.25rem;
        margin-bottom: 1.25rem;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 0.0625rem solid #c3e6cb;
    }
    
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 0.0625rem solid #f5c6cb;
    }
    
    /* Status Update Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .modal-content {
        background-color: white;
        margin: 15% auto;
        padding: 1.25rem;
        border-radius: 0.5rem;
        width: 25rem;
        max-width: 90%;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }
    
    .modal-header h3 {
        margin: 0;
    }
    
    .close {
        font-size: 1.5rem;
        cursor: pointer;
        color: #999;
    }
    
    .close:hover {
        color: #333;
    }
    
    .form-group {
        margin-bottom: 0.9375rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.3125rem;
        font-weight: 600;
    }
    
    .form-group select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 0.0625rem solid #ddd;
        border-radius: 0.25rem;
    }
    
    .modal-actions {
        display: flex;
        gap: 0.625rem;
        justify-content: flex-end;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .filter-row {
            grid-template-columns: 1fr;
        }
        
        .table {
            font-size: 0.75rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>Order Management</h1>
    <div class="breadcrumb">Home / Orders / Management</div>
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

<!-- Enhanced Filters -->
<div class="filters-section">
    <div class="filters-header">
        <h3>Filter Orders</h3>
        <div class="quick-filters">
            <a href="<?php echo e(route('admin.orders.index', ['status' => 'pending'])); ?>" class="quick-filter <?php echo e(($request->status ?? '') === 'pending' ? 'active' : ''); ?>">Pending</a>
            <a href="<?php echo e(route('admin.orders.index', ['status' => 'confirmed'])); ?>" class="quick-filter <?php echo e(($request->status ?? '') === 'confirmed' ? 'active' : ''); ?>">Confirmed</a>
            <a href="<?php echo e(route('admin.orders.index', ['status' => 'delivered'])); ?>" class="quick-filter <?php echo e(($request->status ?? '') === 'delivered' ? 'active' : ''); ?>">Delivered</a>
            <a href="<?php echo e(route('admin.orders.index', ['status' => 'cancelled'])); ?>" class="quick-filter <?php echo e(($request->status ?? '') === 'cancelled' ? 'active' : ''); ?>">Cancelled</a>
        </div>
    </div>
    
    <form method="GET" action="<?php echo e(route('admin.orders.index')); ?>" class="filters-form">
        <div class="filter-row">
            <div class="filter-group">
                <label for="search">Search Orders</label>
                <input type="text" id="search" name="search" value="<?php echo e($request->search ?? ''); ?>" placeholder="Order number, customer name, email...">
            </div>
            
            <div class="filter-group">
                <label for="status">Order Status</label>
                <select id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo e(($request->status ?? '') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="confirmed" <?php echo e(($request->status ?? '') === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                    <option value="delivered" <?php echo e(($request->status ?? '') === 'delivered' ? 'selected' : ''); ?>>Delivered</option>
                    <option value="cancelled" <?php echo e(($request->status ?? '') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="payment_status">Payment Status</label>
                <select id="payment_status" name="payment_status">
                    <option value="">All Payment Status</option>
                    <option value="paid" <?php echo e(($request->payment_status ?? '') === 'paid' ? 'selected' : ''); ?>>Paid</option>
                    <option value="pending" <?php echo e(($request->payment_status ?? '') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="date_from">From Date</label>
                <input type="date" id="date_from" name="date_from" value="<?php echo e($request->date_from ?? ''); ?>">
            </div>
            
            <div class="filter-group">
                <label for="date_to">To Date</label>
                <input type="date" id="date_to" name="date_to" value="<?php echo e($request->date_to ?? ''); ?>">
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-secondary">Clear All</a>
            </div>
        </div>
    </form>
</div>

<!-- Order Statistics -->
<div class="order-stats">
    <div class="stat-item">
        <span class="stat-number"><?php echo e(count($orders)); ?></span>
        <span class="stat-label">Total Orders</span>
    </div>
    <div class="stat-item pending">
        <span class="stat-number"><?php echo e(collect($orders)->where('status', 'pending')->count()); ?></span>
        <span class="stat-label">Pending</span>
    </div>
    <div class="stat-item confirmed">
        <span class="stat-number"><?php echo e(collect($orders)->where('status', 'confirmed')->count()); ?></span>
        <span class="stat-label">Confirmed</span>
    </div>
    <div class="stat-item delivered">
        <span class="stat-number"><?php echo e(collect($orders)->where('status', 'delivered')->count()); ?></span>
        <span class="stat-label">Delivered</span>
    </div>
</div>

<!-- Orders Table -->
<div class="orders-table">
    <div class="table-header">
        <h3>Orders (<?php echo e(count($orders)); ?> found)</h3>
    </div>
    
    <?php if(count($orders) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($order['order_number'] ?? '#' . $order['id']); ?></strong></td>
                        <td>
                            <div>
                                <strong><?php echo e($order['user']['name'] ?? 'N/A'); ?></strong><br>
                                <small><?php echo e($order['user']['email'] ?? 'N/A'); ?></small>
                            </div>
                        </td>
                        <td><strong>$<?php echo e(number_format($order['total_amount'] ?? 0, 2)); ?></strong></td>
                        <td>
                            <span class="status-badge status-<?php echo e($order['status'] ?? 'pending'); ?>">
                                <?php echo e(ucfirst($order['status'] ?? 'pending')); ?>

                            </span>
                        </td>
                        <td>
                            <span class="status-badge payment-<?php echo e($order['payment_status'] ?? 'pending'); ?>">
                                <?php echo e(ucfirst($order['payment_status'] ?? 'pending')); ?>

                            </span>
                        </td>
                        <td><?php echo e(date('M d, Y', strtotime($order['created_at'] ?? now()))); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?php echo e(route('admin.orders.show', $order['id'])); ?>" class="btn btn-info btn-sm" title="View Details">
                                    <i class="icon-eye"></i> View
                                </a>
                                
                                <?php if(($order['status'] ?? 'pending') === 'pending'): ?>
                                    <button onclick="updateOrderStatus(<?php echo e($order['id']); ?>, 'confirmed', 'Confirm this order?')" class="btn btn-success btn-sm" title="Confirm Order">
                                        <i class="icon-check"></i> Confirm
                                    </button>
                                <?php endif; ?>
                                
                                <?php if(($order['status'] ?? 'pending') === 'confirmed'): ?>
                                    <button onclick="updateOrderStatus(<?php echo e($order['id']); ?>, 'delivered', 'Mark this order as delivered?')" class="btn btn-warning btn-sm" title="Mark as Delivered">
                                        <i class="icon-truck"></i> Deliver
                                    </button>
                                <?php endif; ?>
                                
                                <?php if(in_array($order['status'] ?? 'pending', ['pending', 'confirmed'])): ?>
                                    <button onclick="updateOrderStatus(<?php echo e($order['id']); ?>, 'cancelled', 'Cancel this order? This action cannot be undone.')" class="btn btn-danger btn-sm" title="Cancel Order">
                                        <i class="icon-x"></i> Cancel
                                    </button>
                                <?php endif; ?>
                                
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" onclick="toggleDropdown(<?php echo e($order['id']); ?>)">
                                        <i class="icon-more"></i>
                                    </button>
                                    <div class="dropdown-menu" id="dropdown-<?php echo e($order['id']); ?>">
                                        <a href="#" onclick="openStatusModal(<?php echo e($order['id']); ?>, '<?php echo e($order['status'] ?? 'pending'); ?>')">Change Status</a>
                                        <a href="#" onclick="printOrder(<?php echo e($order['id']); ?>)">Print Order</a>
                                        <a href="#" onclick="sendEmail(<?php echo e($order['id']); ?>)">Send Email</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        
        <!-- Pagination would go here if implemented -->
        <?php if(isset($pagination) && count($pagination) > 0): ?>
            <div class="pagination">
                <!-- Pagination links would be rendered here -->
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div style="padding: 2.5rem; text-align: center; color: #666;">
            <h3>No orders found</h3>
            <p>No orders match your current filters.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update Order Status</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        
        <form id="statusForm" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="form-group">
                <label for="new_status">New Status</label>
                <select id="new_status" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<!-- EmailJS SDK -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
<script>
    // Initialize EmailJS
        emailjs.init('Bz8Och_CZPVOe83ux'); // Replace with your EmailJS public key
    
    function updateOrderStatus(orderId, status, confirmMessage = null) {
        const message = confirmMessage || `Are you sure you want to ${status} this order?`;
        
        if (confirm(message)) {
            // Show loading state
            const buttons = document.querySelectorAll(`[onclick*="${orderId}"]`);
            buttons.forEach(btn => {
                btn.disabled = true;
                btn.innerHTML = '<i class="icon-loading"></i> Processing...';
            });
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo e(csrf_token()); ?>';
            
            // Use fetch API for AJAX request
            fetch(`/admin/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If status is delivered and we have order data, send email via EmailJS
                    if (status === 'delivered' && data.order_data) {
                        sendDeliveryNotificationEmail(data.order_data, function() {
                            // Success callback - reload page after email is sent
                            alert(data.message || 'Order status updated successfully!');
                            window.location.reload();
                        }, function(error) {
                            // Error callback - show error but still reload
                            alert('Order status updated but failed to send email notification: ' + error.text);
                            window.location.reload();
                        });
                    } else {
                        // No email to send, just show success and reload
                        alert(data.message || 'Order status updated successfully!');
                        window.location.reload();
                    }
                } else {
                    throw new Error(data.message || 'Failed to update order status');
                }
            })
            .catch(error => {
                console.error('Error updating order status:', error);
                alert('Error: ' + error.message);
                
                // Reset button states
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = btn.getAttribute('data-original-text') || 'Update';
                });
            });
        }
    }
    
    function sendDeliveryNotificationEmail(orderData, successCallback, errorCallback) {
        console.log('Attempting to send email with data:', orderData);
        
        // Validate customer email
        if (!orderData.customer_email || orderData.customer_email === null) {
            console.error('Customer email is missing or null');
            alert('Cannot send delivery notification: Customer email is not available.');
            if (errorCallback) errorCallback(new Error('Customer email is missing'));
            return;
        }
        
        const templateParams = {
            to_email: orderData.customer_email,
            customer_name: orderData.customer_name || 'Valued Customer',
            order_id: orderData.order_id,
            order_total: orderData.order_total,
            delivery_address: orderData.delivery_address || 'N/A',
            order_items: orderData.order_items.map(item => 
                `${item.product_name} (Qty: ${item.quantity}) - $${item.price}`
            ).join('\n')
        };
        
        console.log('EmailJS template params:', templateParams);
        
        emailjs.send('service_pri4y63', 'template_0lwo24z', templateParams)
            .then(function(response) {
                console.log('Delivery notification email sent successfully!', response.status, response.text);
                alert('Delivery notification email sent successfully!');
                if (successCallback) successCallback();
            })
            .catch(function(error) {
                console.error('Failed to send delivery notification email:', error);
                alert('Failed to send delivery notification email. Please check the console for details.');
                if (errorCallback) errorCallback(error);
            });
    }
    
    function toggleDropdown(orderId) {
        const dropdown = document.getElementById(`dropdown-${orderId}`);
        const allDropdowns = document.querySelectorAll('.dropdown-menu');
        
        // Close all other dropdowns
        allDropdowns.forEach(d => {
            if (d !== dropdown) d.style.display = 'none';
        });
        
        // Toggle current dropdown
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
    
    function printOrder(orderId) {
        window.open(`/admin/orders/${orderId}/print`, '_blank');
    }
    
    function sendEmail(orderId) {
        if (confirm('Send order confirmation email to customer?')) {
            fetch(`/admin/orders/${orderId}/send-email`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Email sent successfully!');
                } else {
                    alert('Failed to send email: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error sending email: ' + error.message);
            });
        }
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(d => d.style.display = 'none');
        }
    });
    
    function openStatusModal(orderId, currentStatus) {
        document.getElementById('statusForm').action = `/admin/orders/${orderId}/status`;
        document.getElementById('new_status').value = currentStatus;
        document.getElementById('statusModal').style.display = 'block';
    }
    
    function closeModal() {
        document.getElementById('statusModal').style.display = 'none';
    }
    
    function closeStatusModal() {
        document.getElementById('statusModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('statusModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.shared', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thenn\OneDrive\Desktop\softora\08 25\admin-panel\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>