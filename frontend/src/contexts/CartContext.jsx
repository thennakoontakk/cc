import React, { createContext, useContext, useReducer, useEffect, useState } from 'react';
import { useAuth } from './AuthContext';

const API_BASE_URL = 'http://localhost:8000/api';

const CartContext = createContext();

// Cart reducer to manage cart state
const cartReducer = (state, action) => {
  switch (action.type) {
    case 'ADD_TO_CART':
      const existingItem = state.items.find(item => item.id === action.payload.id);
      const quantityToAdd = action.payload.quantity || 1;
      if (existingItem) {
        return {
          ...state,
          items: state.items.map(item =>
            item.id === action.payload.id
              ? { ...item, quantity: item.quantity + quantityToAdd }
              : item
          ),
          totalItems: state.totalItems + quantityToAdd,
          totalPrice: state.totalPrice + (parseFloat(action.payload.price) * quantityToAdd)
        };
      } else {
        return {
          ...state,
          items: [...state.items, { ...action.payload, quantity: quantityToAdd }],
          totalItems: state.totalItems + quantityToAdd,
          totalPrice: state.totalPrice + (parseFloat(action.payload.price) * quantityToAdd)
        };
      }
    
    case 'REMOVE_FROM_CART':
      const itemToRemove = state.items.find(item => item.id === action.payload);
      if (itemToRemove) {
        return {
          ...state,
          items: state.items.filter(item => item.id !== action.payload),
          totalItems: state.totalItems - itemToRemove.quantity,
          totalPrice: state.totalPrice - (parseFloat(itemToRemove.price) * itemToRemove.quantity)
        };
      }
      return state;
    
    case 'UPDATE_QUANTITY':
      const { id, quantity } = action.payload;
      const itemToUpdate = state.items.find(item => item.id === id);
      if (itemToUpdate && quantity > 0) {
        const quantityDiff = quantity - itemToUpdate.quantity;
        return {
          ...state,
          items: state.items.map(item =>
            item.id === id ? { ...item, quantity } : item
          ),
          totalItems: state.totalItems + quantityDiff,
          totalPrice: state.totalPrice + (parseFloat(itemToUpdate.price) * quantityDiff)
        };
      }
      return state;
    
    case 'CLEAR_CART':
      return {
        items: [],
        totalItems: 0,
        totalPrice: 0
      };
    
    case 'LOAD_CART':
      return action.payload;
    
    default:
      return state;
  }
};

const initialState = {
  items: [],
  totalItems: 0,
  totalPrice: 0
};

export const CartProvider = ({ children }) => {
  const [state, dispatch] = useReducer(cartReducer, initialState);
  const [sessionId, setSessionId] = useState(null);
  const { currentUser } = useAuth();

  // Generate or get session ID for guest users
  useEffect(() => {
    let storedSessionId = localStorage.getItem('cart_session_id');
    if (!storedSessionId) {
      storedSessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
      localStorage.setItem('cart_session_id', storedSessionId);
    }
    setSessionId(storedSessionId);
  }, []);

  // Load cart from database on mount
  useEffect(() => {
    if (sessionId) {
      loadCartFromDatabase();
    }
  }, [sessionId]);

  // Reload cart when user logs in/out
  useEffect(() => {
    if (sessionId) {
      loadCartFromDatabase();
    }
  }, [currentUser, sessionId]);

  const loadCartFromDatabase = async () => {
    try {
      const userId = localStorage.getItem('userId');
      let url = `${API_BASE_URL}/cart?session_id=${sessionId}`;
      if (userId) {
        url += `&user_id=${userId}`;
      }
      
      console.log('Loading cart with:', { userId, sessionId, url });
      
      const response = await fetch(url);
      const data = await response.json();
      
      console.log('Cart API response:', data);
      
      if (data.success && data.data) {
        const cartData = {
          items: Array.isArray(data.data.items) ? data.data.items.map(item => ({
            id: item.product_id,
            name: item.product_name,
            description: item.product_description,
            category: item.product_category,
            image: item.product_image,
            price: item.product_price,
            quantity: item.quantity
          })) : [],
          totalItems: data.data.totalItems || 0,
          totalPrice: data.data.totalPrice || 0
        };
        dispatch({ type: 'LOAD_CART', payload: cartData });
      } else {
        // If no cart data or unsuccessful response, ensure we have a valid cart structure
        dispatch({ type: 'LOAD_CART', payload: initialState });
      }
    } catch (error) {
      console.error('Error loading cart from database:', error);
      // On error, ensure we have a valid cart structure
      dispatch({ type: 'LOAD_CART', payload: initialState });
    }
  };

  const addToCart = async (product, quantity = 1) => {
    try {
      const userId = localStorage.getItem('userId');
      const requestBody = {
        session_id: sessionId,
        product_id: product.id,
        quantity: quantity
      };
      
      // Add user_id if user is authenticated
      if (userId) {
        requestBody.user_id = parseInt(userId);
      }
      
      const response = await fetch(`${API_BASE_URL}/cart/add`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestBody)
      });
      
      const data = await response.json();
      if (data.success) {
        // Reload cart from database to get updated state
        await loadCartFromDatabase();
      } else {
        console.error('Failed to add to cart:', data.message);
      }
    } catch (error) {
      console.error('Error adding to cart:', error);
      // Fallback to local state update if API fails
      dispatch({ type: 'ADD_TO_CART', payload: { ...product, quantity } });
    }
  };

  const removeFromCart = async (productId) => {
    try {
      // Get fresh cart data to find the correct cart item ID
      const userId = localStorage.getItem('userId');
      let url = `${API_BASE_URL}/cart?session_id=${sessionId}`;
      if (userId) {
        url += `&user_id=${userId}`;
      }
      const response = await fetch(url);
      const cartData = await response.json();
      
      if (cartData.success && cartData.data) {
        const cartItem = cartData.data.items.find(item => item.product_id === productId);
        if (!cartItem) return;
        
        const deleteResponse = await fetch(`${API_BASE_URL}/cart/${cartItem.id}`, {
          method: 'DELETE'
        });
        
        const deleteData = await deleteResponse.json();
        if (deleteData.success) {
          await loadCartFromDatabase();
        } else {
          console.error('Failed to remove from cart:', deleteData.message);
        }
      }
    } catch (error) {
      console.error('Error removing from cart:', error);
      // Fallback to local state update if API fails
      dispatch({ type: 'REMOVE_FROM_CART', payload: productId });
    }
  };

  const updateQuantity = async (productId, quantity) => {
    try {
      // Get fresh cart data to find the correct cart item ID
      const userId = localStorage.getItem('userId');
      let url = `${API_BASE_URL}/cart?session_id=${sessionId}`;
      if (userId) {
        url += `&user_id=${userId}`;
      }
      const response = await fetch(url);
      const cartData = await response.json();
      
      if (cartData.success && cartData.data) {
        const cartItem = cartData.data.items.find(item => item.product_id === productId);
        if (!cartItem) return;
        
        const updateResponse = await fetch(`${API_BASE_URL}/cart/${cartItem.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ quantity })
        });
        
        const updateData = await updateResponse.json();
        if (updateData.success) {
          await loadCartFromDatabase();
        } else {
          console.error('Failed to update quantity:', updateData.message);
        }
      }
    } catch (error) {
      console.error('Error updating quantity:', error);
      // Fallback to local state update if API fails
      dispatch({ type: 'UPDATE_QUANTITY', payload: { id: productId, quantity } });
    }
  };

  const clearCart = async () => {
    try {
      const userId = localStorage.getItem('userId');
      let url = `${API_BASE_URL}/cart?session_id=${sessionId}`;
      if (userId) {
        url += `&user_id=${userId}`;
      }
      const response = await fetch(url, {
        method: 'DELETE'
      });
      
      const data = await response.json();
      if (data.success) {
        dispatch({ type: 'CLEAR_CART' });
      } else {
        console.error('Failed to clear cart:', data.message);
      }
    } catch (error) {
      console.error('Error clearing cart:', error);
      // Fallback to local state update if API fails
      dispatch({ type: 'CLEAR_CART' });
    }
  };

  const value = {
    cart: state,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart
  };

  return (
    <CartContext.Provider value={value}>
      {children}
    </CartContext.Provider>
  );
};

export const useCart = () => {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within a CartProvider');
  }
  return context;
};

export default CartContext;