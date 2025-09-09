import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate, useNavigate, Link } from 'react-router-dom';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import { CartProvider } from './contexts/CartContext';
import Home from './components/Home';
import Login from './components/Login';
import Register from './components/Register';
import Dashboard from './components/Dashboard';
import PrivateRoute from './components/PrivateRoute';
import AuthRedirect from './components/AuthRedirect';

import SketchGenerator from './components/SketchGenerator';
import Shop from './components/Shop';
import Cart from './components/Cart';
import OrderTracking from './components/OrderTracking';
import './App.css';

// Header component with authentication
function AppHeader() {
  const { currentUser, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    try {
      await logout();
      navigate('/login');
    } catch (error) {
      console.error('Failed to log out:', error);
    }
  };

  return (
    <header className="app-header">
      <div className="header-top">
        <div className="contact-info">
          <span>📞 +94778612344</span>
          <span>✉️ CraftersCorner@gmail.com</span>
          {currentUser ? (
            <div className="user-info">
              <span>👤 {currentUser.email}</span>
              <button 
                onClick={handleLogout} 
                className="logout-btn"
                style={{
                  marginLeft: '10px',
                  padding: '5px 10px',
                  backgroundColor: '#ff4757',
                  color: 'white',
                  border: 'none',
                  borderRadius: '4px',
                  cursor: 'pointer',
                  fontSize: '12px'
                }}
              >
                Logout
              </button>
            </div>
          ) : (
            <span>👤 My Account</span>
          )}
        </div>
      </div>
      <div className="header-main">
        <div className="logo">
          <h1>CRAFTERS' CORNER</h1>
        </div>
        <div className="search-bar">
          <input type="text" placeholder="Search" />
          <button>🔍</button>
        </div>
        <div className="header-icons">
          <span>❤️</span>
          <span>🛒</span>
        </div>
      </div>
      <nav className="main-nav">
        <Link to="/">Home</Link>
        <Link to="/shop">Shop</Link>
        <Link to="/sketch-generator">Custom Craft Corner</Link>
        <Link to="/cart">Cart</Link>
        <Link to="/orders">Track Orders</Link>
        <a href="#">Contact Us</a>
      </nav>
    </header>
  );
}

function App() {
  return (
    <Router>
      <AuthProvider>
        <CartProvider>
          <div className="App">
            {/* Header */}
            <AppHeader />

            {/* Main Content */}
            <main className="main-content">
              <Routes>
                <Route path="/" element={
                  <PrivateRoute>
                    <Home />
                  </PrivateRoute>
                } />
                <Route path="/shop" element={
                  <PrivateRoute>
                    <Shop />
                  </PrivateRoute>
                } />
                <Route path="/cart" element={
                  <PrivateRoute>
                    <Cart />
                  </PrivateRoute>
                } />
                <Route path="/orders" element={
                  <PrivateRoute>
                    <OrderTracking />
                  </PrivateRoute>
                } />
                <Route path="/login" element={<Login />} />
                <Route path="/register" element={<Register />} />

                <Route 
                  path="/auth-redirect" 
                  element={
                    <PrivateRoute>
                      <AuthRedirect />
                    </PrivateRoute>
                  } 
                />
                <Route 
                  path="/dashboard" 
                  element={
                    <PrivateRoute>
                      <Dashboard />
                    </PrivateRoute>
                  } 
                />
                <Route path="/sketch-generator" element={
                  <PrivateRoute>
                    <SketchGenerator />
                  </PrivateRoute>
                } />
              </Routes>
            </main>
          </div>
        </CartProvider>
      </AuthProvider>
    </Router>
  );
}

export default App;
