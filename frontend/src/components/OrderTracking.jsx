import React, { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate, useSearchParams } from 'react-router-dom';
import axios from 'axios';
import './OrderTracking.css';

const OrderTracking = () => {
  const { currentUser } = useAuth();
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [searchOrderNumber, setSearchOrderNumber] = useState('');
  const [filteredOrders, setFilteredOrders] = useState([]);
  const [statusFilter, setStatusFilter] = useState('all');

  // Get order number from URL params if available
  const orderNumberFromUrl = searchParams.get('order');

  useEffect(() => {
    if (orderNumberFromUrl) {
      setSearchOrderNumber(orderNumberFromUrl);
    }
    fetchUserOrders();
  }, [currentUser]);

  useEffect(() => {
    filterOrders();
  }, [orders, searchOrderNumber, statusFilter]);

  const fetchUserOrders = async () => {
    if (!currentUser) {
      setError('Please log in to view your orders');
      setLoading(false);
      return;
    }

    try {
      setLoading(true);
      const userId = localStorage.getItem('userId');
      if (!userId) {
        setError('User not authenticated');
        setLoading(false);
        return;
      }
      
      const response = await axios.get(`http://localhost:8000/api/orders/user/${userId}`);
      
      if (response.data.success) {
        setOrders(response.data.data || []);
      } else {
        setError('Failed to fetch orders');
      }
    } catch (err) {
      console.error('Error fetching orders:', err);
      setError('Failed to load orders. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const filterOrders = () => {
    let filtered = [...orders];

    // Filter by search term
    if (searchOrderNumber.trim()) {
      filtered = filtered.filter(order => 
        order.order_number.toLowerCase().includes(searchOrderNumber.toLowerCase())
      );
    }

    // Filter by status
    if (statusFilter !== 'all') {
      filtered = filtered.filter(order => order.status === statusFilter);
    }

    // Sort by creation date (newest first)
    filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    setFilteredOrders(filtered);
  };

  const getStatusColor = (status) => {
    const colors = {
      pending: '#f59e0b',
      confirmed: '#3b82f6',
      delivered: '#10b981',
      cancelled: '#ef4444'
    };
    return colors[status] || '#6b7280';
  };

  const getPaymentStatusColor = (paymentStatus) => {
    const colors = {
      paid: '#10b981',
      pending: '#f59e0b',
      failed: '#ef4444'
    };
    return colors[paymentStatus] || '#6b7280';
  };

  const getStatusIcon = (status) => {
    const icons = {
      pending: '⏳',
      confirmed: '✅',
      delivered: '🚚',
      cancelled: '❌'
    };
    return icons[status] || '📦';
  };

  const getTrackingMessage = (order) => {
    switch (order.status) {
      case 'pending':
        return 'Your order has been received and is being processed.';
      case 'confirmed':
        return 'Your order has been confirmed and is being prepared for delivery.';
      case 'delivered':
        return 'Your order has been delivered successfully!';
      case 'cancelled':
        return 'This order has been cancelled.';
      default:
        return 'Order status unknown.';
    }
  };

  const formatPrice = (price) => {
    return parseFloat(price).toFixed(2);
  };

  if (!currentUser) {
    return (
      <div className="order-tracking-container">
        <div className="login-prompt">
          <h2>Login Required</h2>
          <p>Please log in to track your orders.</p>
          <button 
            className="btn-primary"
            onClick={() => navigate('/login')}
          >
            Go to Login
          </button>
        </div>
      </div>
    );
  }

  if (loading) {
    return (
      <div className="order-tracking-container">
        <div className="loading-container">
          <div className="loading-spinner"></div>
          <p>Loading your orders...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="order-tracking-container">
      <div className="tracking-header">
        <h1>Order Tracking</h1>
        <p>Track the status of your orders and delivery information</p>
      </div>

      {/* Search and Filter Controls */}
      <div className="tracking-controls">
        <div className="search-section">
          <div className="search-group">
            <input
              type="text"
              placeholder="Search by order number..."
              value={searchOrderNumber}
              onChange={(e) => setSearchOrderNumber(e.target.value)}
              className="search-input"
            />
            <button 
              className="search-btn"
              onClick={filterOrders}
            >
              🔍
            </button>
          </div>
        </div>

        <div className="filter-section">
          <select 
            value={statusFilter} 
            onChange={(e) => setStatusFilter(e.target.value)}
            className="status-filter"
          >
            <option value="all">All Orders</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>

      {/* Error Message */}
      {error && (
        <div className="error-message">
          <p>{error}</p>
          <button onClick={fetchUserOrders} className="retry-btn">
            Try Again
          </button>
        </div>
      )}

      {/* Orders List */}
      {filteredOrders.length === 0 && !loading && !error ? (
        <div className="no-orders">
          <div className="no-orders-icon">📦</div>
          <h3>No Orders Found</h3>
          <p>
            {searchOrderNumber || statusFilter !== 'all' 
              ? 'No orders match your search criteria.' 
              : 'You haven\'t placed any orders yet.'}
          </p>
          <button 
            className="btn-primary"
            onClick={() => navigate('/shop')}
          >
            Start Shopping
          </button>
        </div>
      ) : (
        <div className="orders-grid">
          {filteredOrders.map((order) => (
            <div key={order.id} className="order-tracking-card">
              {/* Order Header */}
              <div className="order-header">
                <div className="order-info">
                  <h3>Order #{order.order_number}</h3>
                  <p className="order-date">
                    {new Date(order.created_at).toLocaleDateString('en-US', {
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit'
                    })}
                  </p>
                </div>
                <div className="order-status-section">
                  <span 
                    className="status-badge"
                    style={{ backgroundColor: getStatusColor(order.status) }}
                  >
                    {getStatusIcon(order.status)} {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                  </span>
                  <span 
                    className="payment-status-badge"
                    style={{ backgroundColor: getPaymentStatusColor(order.payment_status) }}
                  >
                    {order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1)}
                  </span>
                </div>
              </div>

              {/* Tracking Progress */}
              <div className="tracking-progress">
                <div className="progress-steps">
                  <div className={`step ${['pending', 'confirmed', 'delivered'].includes(order.status) ? 'completed' : ''}`}>
                    <div className="step-icon">📝</div>
                    <span>Order Placed</span>
                  </div>
                  <div className={`step ${['confirmed', 'delivered'].includes(order.status) ? 'completed' : order.status === 'cancelled' ? 'cancelled' : ''}`}>
                    <div className="step-icon">✅</div>
                    <span>Confirmed</span>
                  </div>
                  <div className={`step ${order.status === 'delivered' ? 'completed' : order.status === 'cancelled' ? 'cancelled' : ''}`}>
                    <div className="step-icon">🚚</div>
                    <span>Delivered</span>
                  </div>
                </div>
                <div className="tracking-message">
                  <p>{getTrackingMessage(order)}</p>
                  {order.status === 'confirmed' && order.confirmed_at && (
                    <p className="status-date">
                      Confirmed on: {new Date(order.confirmed_at).toLocaleDateString()}
                    </p>
                  )}
                  {order.status === 'delivered' && order.delivered_at && (
                    <p className="status-date">
                      Delivered on: {new Date(order.delivered_at).toLocaleDateString()}
                    </p>
                  )}
                </div>
              </div>

              {/* Order Items */}
              <div className="order-items">
                <h4>Items Ordered ({order.order_items.length})</h4>
                <div className="items-list">
                  {order.order_items.map((item) => (
                    <div key={item.id} className="order-item">
                      <div className="item-image">
                        {item.product_image ? (
                          <img src={item.product_image} alt={item.product_name} />
                        ) : (
                          <div className="placeholder-image">📦</div>
                        )}
                      </div>
                      <div className="item-details">
                        <h5>{item.product_name}</h5>
                        <p>Quantity: {item.quantity}</p>
                        <p className="item-price">${formatPrice(item.total_price)}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Order Summary */}
              <div className="order-summary">
                <div className="summary-row">
                  <span>Payment Method:</span>
                  <span>{order.payment_method}</span>
                </div>
                <div className="summary-row total">
                  <span>Total Amount:</span>
                  <span>${formatPrice(order.total_amount)}</span>
                </div>
              </div>

              {/* Shipping Address */}
              {order.shipping_address && (
                <div className="shipping-info">
                  <h4>Shipping Address</h4>
                  <div className="address">
                    <p>{order.shipping_address.name}</p>
                    <p>{order.shipping_address.address}</p>
                    <p>{order.shipping_address.city}, {order.shipping_address.state} {order.shipping_address.zip}</p>
                    <p>{order.shipping_address.phone}</p>
                  </div>
                </div>
              )}
            </div>
          ))}
        </div>
      )}

      {/* Back to Shop */}
      <div className="back-to-shop">
        <button 
          className="btn-secondary"
          onClick={() => navigate('/shop')}
        >
          ← Continue Shopping
        </button>
      </div>
    </div>
  );
};

export default OrderTracking;