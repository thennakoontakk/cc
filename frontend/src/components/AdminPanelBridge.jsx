import React, { useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';

const AdminPanelBridge = () => {
  const { user, isAdmin } = useAuth();

  useEffect(() => {
    const redirectToAdminPanel = async () => {
      if (user && isAdmin) {
        try {
          // Get Laravel Sanctum token
          const token = localStorage.getItem('auth_token');
          
          if (token) {
            // Store Laravel auth data in session storage for the middleware
            sessionStorage.setItem('laravel_token', token);
            sessionStorage.setItem('user_email', user.email);
            sessionStorage.setItem('user_name', user.name || user.email.split('@')[0]);
            sessionStorage.setItem('user_role', 'admin');
            
            // Create URL with Laravel authentication data
            const params = new URLSearchParams({
              laravel_token: token,
              user_email: user.email,
              user_name: user.name || user.email.split('@')[0],
              user_role: 'admin'
            });
            
            // Redirect to Laravel admin panel with Laravel auth data
            window.location.href = `http://127.0.0.1:8000/admin/laravel-auth?${params.toString()}`;
          } else {
            // No token, redirect to login
            window.location.href = '/login';
          }
        } catch (error) {
          console.error('Error during admin panel bridge:', error);
          // Fallback redirect to home
          window.location.href = '/';
        }
      } else if (user && !isAdmin) {
        // Redirect regular users to home
        window.location.href = '/';
      }
    };

    redirectToAdminPanel();
  }, [user]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50">
      <div className="text-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p className="text-gray-600">Redirecting to admin panel...</p>
      </div>
    </div>
  );
};

export default AdminPanelBridge;