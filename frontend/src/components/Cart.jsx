import React, { useState, useEffect } from 'react';
import { useCart } from '../contexts/CartContext';
import PaymentModal from './PaymentModal';
import './Cart.css';

const Cart = () => {
  const { cart, removeFromCart, updateQuantity, clearCart } = useCart();
  const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);
  const [paidOrders, setPaidOrders] = useState([]);

  const handleQuantityChange = (productId, newQuantity) => {
    if (newQuantity <= 0) {
      removeFromCart(productId);
    } else {
      updateQuantity(productId, newQuantity);
    }
  };

  const formatPrice = (price) => {
    return parseFloat(price).toFixed(2);
  };

  // Load user's paid orders
  useEffect(() => {
    loadPaidOrders();
  }, []);

  const loadPaidOrders = async () => {
    try {
      const userId = localStorage.getItem('userId') || 'guest';
      const response = await fetch(`http://localhost:8000/api/orders/user/${userId}`);
      const data = await response.json();
      
      if (data.success && Array.isArray(data.data)) {
        setPaidOrders(data.data);
      } else {
        setPaidOrders([]);
      }
    } catch (error) {
      console.error('Error loading paid orders:', error);
      setPaidOrders([]);
    }
  };

  const handleCheckout = () => {
    if (!cart.items || cart.items.length === 0) {
      alert('Your cart is empty!');
      return;
    }
    setIsPaymentModalOpen(true);
  };

  const handlePaymentSuccess = (order) => {
    // Reload paid orders after successful payment
    loadPaidOrders();
    // Clear the cart after successful payment
    clearCart();
    
    // Show success message with option to track order
    const trackOrder = window.confirm(
      `Order placed successfully! Order #${order.order_number}\n\nWould you like to track your order now?`
    );
    
    if (trackOrder) {
      // Navigate to order tracking page
      window.location.href = '/orders';
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'pending': return '#fbbf24';
      case 'confirmed': return '#3b82f6';
      case 'delivered': return '#10b981';
      default: return '#6b7280';
    }
  };

  const getPaymentStatusColor = (status) => {
    switch (status) {
      case 'paid': return '#10b981';
      case 'pending': return '#fbbf24';
      case 'failed': return '#ef4444';
      default: return '#6b7280';
    }
  };

  if (!cart.items || cart.items.length === 0) {
    return (
      <div className="cart-container">
        <div className="cart-header">
          <h1>Shopping Cart</h1>
          <p>Your cart is currently empty</p>
        </div>
        <div className="empty-cart">
          <div className="empty-cart-icon">🛒</div>
          <h3>Your cart is empty</h3>
          <p>Add some products to get started!</p>
          <a href="/shop" className="continue-shopping-btn">
            Continue Shopping
          </a>
        </div>
      </div>
    );
  }

  return (
    <div className="cart-container">
      <div className="cart-header">
        <h1>Shopping Cart</h1>
        <p>{cart.totalItems || 0} item{(cart.totalItems || 0) !== 1 ? 's' : ''} in your cart</p>
      </div>

      <div className="cart-content">
        <div className="cart-items">
          {cart.items && cart.items.map((item) => (
            <div key={item.id} className="cart-item">
              <div className="item-image">
                {item.image ? (
                  <img src={item.image} alt={item.name} />
                ) : (
                  <div className="placeholder-image">📦</div>
                )}
              </div>
              
              <div className="item-details">
                <h3>{item.name}</h3>
                <p className="item-description">{item.description}</p>
                <p className="item-category">Category: {item.category}</p>
              </div>
              
              <div className="item-price">
                <span className="price">${formatPrice(item.price)}</span>
              </div>
              
              <div className="item-quantity">
                <button 
                  className="quantity-btn"
                  onClick={() => handleQuantityChange(item.id, item.quantity - 1)}
                >
                  -
                </button>
                <span className="quantity">{item.quantity}</span>
                <button 
                  className="quantity-btn"
                  onClick={() => handleQuantityChange(item.id, item.quantity + 1)}
                >
                  +
                </button>
              </div>
              
              <div className="item-total">
                <span className="total-price">
                  ${formatPrice(item.price * item.quantity)}
                </span>
              </div>
              
              <button 
                className="remove-btn"
                onClick={() => removeFromCart(item.id)}
                title="Remove from cart"
              >
                🗑️
              </button>
            </div>
          ))}
        </div>

        <div className="cart-summary">
          <div className="summary-card">
            <h3>Order Summary</h3>
            <div className="summary-row">
              <span>Subtotal ({cart.totalItems || 0} items):</span>
              <span>${formatPrice(cart.totalPrice || 0)}</span>
            </div>
            <div className="summary-row">
              <span>Shipping:</span>
              <span>Free</span>
            </div>
            <div className="summary-row total">
              <span>Total:</span>
              <span>${formatPrice(cart.totalPrice || 0)}</span>
            </div>
            
            <div className="cart-actions">
              <button className="checkout-btn" onClick={handleCheckout}>
                Proceed to Checkout
              </button>
              <button 
                className="clear-cart-btn"
                onClick={clearCart}
              >
                Clear Cart
              </button>
            </div>
          </div>
        </div>
      </div>

      <div className="continue-shopping">
        <a href="/shop" className="continue-shopping-link">
          ← Continue Shopping
        </a>
      </div>

      {/* Paid Items Section */}
      {paidOrders.length > 0 && (
        <div className="paid-orders-section">
          <div className="section-header">
            <h2>Your Orders</h2>
            <p>Track your order status and delivery information</p>
          </div>
          
          <div className="paid-orders-grid">
            {paidOrders.map((order) => (
              <div key={order.id} className="order-card">
                <div className="order-header">
                  <div className="order-info">
                    <h3>Order #{order.order_number}</h3>
                    <p className="order-date">
                      {new Date(order.created_at).toLocaleDateString()}
                    </p>
                  </div>
                  <div className="order-status">
                    <span 
                      className="status-badge"
                      style={{ backgroundColor: getStatusColor(order.status) }}
                    >
                      {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                    </span>
                    <span 
                      className="payment-status-badge"
                      style={{ backgroundColor: getPaymentStatusColor(order.payment_status) }}
                    >
                      {order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1)}
                    </span>
                  </div>
                </div>
                
                <div className="order-items">
                  {order.order_items.map((item) => (
                    <div key={item.id} className="order-item">
                      <div className="order-item-image">
                        {item.product_image ? (
                          <img src={item.product_image} alt={item.product_name} />
                        ) : (
                          <div className="placeholder-image">📦</div>
                        )}
                      </div>
                      <div className="order-item-details">
                        <h4>{item.product_name}</h4>
                        <p>Quantity: {item.quantity}</p>
                        <p className="item-price">${formatPrice(item.total_price)}</p>
                      </div>
                    </div>
                  ))}
                </div>
                
                <div className="order-footer">
                  <div className="order-total">
                    <strong>Total: ${formatPrice(order.total_amount)}</strong>
                  </div>
                  <div className="order-payment">
                    <span>Payment: {order.payment_method}</span>
                  </div>
                </div>
                
                {order.status === 'confirmed' && (
                  <div className="order-tracking">
                    <p className="tracking-info">✅ Your order has been confirmed and is being prepared for delivery.</p>
                  </div>
                )}
                
                {order.status === 'delivered' && (
                  <div className="order-tracking">
                    <p className="tracking-info">🎉 Your order has been delivered successfully!</p>
                    {order.delivered_at && (
                      <p className="delivery-date">
                        Delivered on: {new Date(order.delivered_at).toLocaleDateString()}
                      </p>
                    )}
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Payment Modal */}
      <PaymentModal
        isOpen={isPaymentModalOpen}
        onClose={() => setIsPaymentModalOpen(false)}
        cartItems={cart.items.map(item => ({
          id: item.id,
          product_name: item.name,
          product_image: item.image,
          unit_price: parseFloat(item.price),
          quantity: item.quantity,
          total_price: parseFloat(item.price) * item.quantity
        }))}
        totalAmount={cart.totalPrice}
        onPaymentSuccess={handlePaymentSuccess}
      />
    </div>
  );
};

export default Cart;