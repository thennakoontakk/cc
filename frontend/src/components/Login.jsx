import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

import Input from './Input';
import Button from './Button';
import Footer from './Footer';
import './Login.css';

function Login() {
  const navigate = useNavigate();
  const { loginWithAdminCheck } = useAuth();
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    rememberMe: false
  });
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));
    // Clear error when user starts typing
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  const validateForm = () => {
    const newErrors = {};
    
    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'Email is invalid';
    }
    
    if (!formData.password) {
      newErrors.password = 'Password is required';
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    console.log('Login form submitted with:', { email: formData.email });
    
    if (!validateForm()) {
      console.log('Form validation failed');
      return;
    }
    
    setLoading(true);
    setErrors({});

    try {
      console.log('Attempting enhanced login...');
      // Try enhanced login that checks for admin status
      const result = await loginWithAdminCheck(formData.email, formData.password);
      
      console.log('Enhanced login result:', result);
      
      if (result.success) {
        console.log('Login successful, navigating to auth-redirect');
        // Navigate to auth redirect component which will handle the redirection
        navigate('/auth-redirect');
      } else {
        console.log('Enhanced login failed:', result.error);
        setErrors({ general: result.error });
      }
    } catch (error) {
      console.error('Login error:', error);
      setErrors({ general: 'Login failed. Please check your credentials and try again.' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="login-page">
      <div className="login-header">
        <h1 className="login-title">Login | <a href="/register" className="register-link">Register</a></h1>
      </div>
      
      <div className="login-form-container">
        {errors.general && (
          <div className="error-message general-error">
            {errors.general}
          </div>
        )}
        <form onSubmit={handleSubmit} className="login-form">
          <Input
            type="email"
            placeholder="Email"
            value={formData.email}
            onChange={handleInputChange}
            name="email"
            error={errors.email}
            required
          />
          
          <Input
            type="password"
            placeholder="Password"
            value={formData.password}
            onChange={handleInputChange}
            name="password"
            error={errors.password}
            required
          />
          
          <div className="form-options">
            <label className="remember-me">
              <input
                type="checkbox"
                name="rememberMe"
                checked={formData.rememberMe}
                onChange={handleInputChange}
              />
              Remember Me
            </label>
            
            <a href="#" className="forgot-password">Forgot Your Password?</a>
          </div>
          
          <Button type="submit" variant="primary" disabled={loading}>
            {loading ? 'Logging in...' : 'Login'}
          </Button>
        </form>
      </div>
      <Footer />
    </div>
  );
}

export default Login;