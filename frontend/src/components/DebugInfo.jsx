import React from 'react';
import { useCart } from '../contexts/CartContext';

const DebugInfo = () => {
  const { cart } = useCart();
  const userId = localStorage.getItem('userId');
  const authToken = localStorage.getItem('auth_token');
  const sessionId = localStorage.getItem('cart_session_id');

  // Only show debug info if user is logged in and has items in cart, or if there are issues
  const hasCartItems = cart.items && cart.items.length > 0;
  const isLoggedIn = userId && authToken;
  
  // Show debug info only if:
  // 1. User is logged in but cart is empty (potential issue)
  // 2. User has cart items (normal case with debug info)
  // 3. User is not logged in (show session info)
  const shouldShowDebug = !isLoggedIn || hasCartItems || (isLoggedIn && !hasCartItems);

  if (!shouldShowDebug) {
    return null;
  }

  return (
    <div style={{
      position: 'fixed',
      top: '10px',
      right: '10px',
      background: isLoggedIn ? (hasCartItems ? '#e8f5e8' : '#fff3cd') : '#f8d7da',
      padding: '10px',
      border: `1px solid ${isLoggedIn ? (hasCartItems ? '#28a745' : '#ffc107') : '#dc3545'}`,
      borderRadius: '5px',
      fontSize: '12px',
      fontFamily: 'monospace',
      zIndex: 9999
    }}>
      <h4 style={{ margin: '0 0 10px 0', color: '#333' }}>Cart Debug Info:</h4>
      <div><strong>User ID:</strong> {userId || 'Not logged in'}</div>
      <div><strong>Auth Status:</strong> {authToken ? 'Authenticated' : 'Not authenticated'}</div>
      <div><strong>Session ID:</strong> {sessionId || 'Not set'}</div>
      <div><strong>Cart Items:</strong> {cart.totalItems || 0}</div>
      <div><strong>Cart Total:</strong> ${cart.totalPrice || '0.00'}</div>
      {isLoggedIn && !hasCartItems && (
        <div style={{ color: '#856404', marginTop: '5px' }}>
          <strong>Note:</strong> User is logged in but cart is empty. Check if cart data is loading correctly.
        </div>
      )}
    </div>
  );
};

export default DebugInfo;