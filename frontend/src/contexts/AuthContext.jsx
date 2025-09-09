import React, { createContext, useContext, useEffect, useState } from 'react';
import {
  GoogleAuthProvider,
  signInWithPopup
} from 'firebase/auth';
import { auth } from '../firebase/config';

const AuthContext = createContext();

export function useAuth() {
  return useContext(AuthContext);
}

export function AuthProvider({ children }) {
  const [currentUser, setCurrentUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [token, setToken] = useState(localStorage.getItem('auth_token'));
  const [isAdmin, setIsAdmin] = useState(false);
  const [adminData, setAdminData] = useState(null);

  // Register user with Laravel API
  async function signup(email, password, name) {
    try {
      console.log('signup called with:', { email, name });
      
      const response = await fetch('http://127.0.0.1:8000/api/frontend/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          name: name || email.split('@')[0],
          email,
          password,
          provider: 'email'
        })
      });
      
      const data = await response.json();
      console.log('Registration response:', data);
      
      if (data.success) {
        // Store token and user data
        localStorage.setItem('auth_token', data.data.token);
        localStorage.setItem('userId', data.data.user.id); // Store Laravel user ID
        console.log('Stored userId in localStorage:', data.data.user.id);
        setToken(data.data.token);
        setCurrentUser(data.data.user);
        return { success: true, user: data.data.user };
      } else {
        throw new Error(data.message || 'Registration failed');
      }
    } catch (error) {
      console.error('Error in signup:', error);
      throw error;
    }
  }

  // Alias for backward compatibility
  const signupWithDatabase = signup;

  // Login user with Laravel API
  async function login(email, password) {
    try {
      console.log('login called with:', { email });
      
      const response = await fetch('http://127.0.0.1:8000/api/frontend/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ email, password })
      });
      
      const data = await response.json();
      console.log('Login response:', data);
      
      if (data.success) {
        // Store token and user data
        localStorage.setItem('auth_token', data.data.token);
        localStorage.setItem('userId', data.data.user.id); // Store Laravel user ID
        console.log('Login - Stored userId in localStorage:', data.data.user.id);
        setToken(data.data.token);
        setCurrentUser(data.data.user);
        return { success: true, user: data.data.user };
      } else {
        throw new Error(data.message || 'Login failed');
      }
    } catch (error) {
      console.error('Error in login:', error);
      throw error;
    }
  }

  // Logout user
  async function logout() {
    try {
      if (token) {
        await fetch('http://127.0.0.1:8000/api/frontend/logout', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        });
      }
      
      // Clear local storage and state
      localStorage.removeItem('auth_token');
      localStorage.removeItem('userId'); // Clear Laravel user ID
      setToken(null);
      setCurrentUser(null);
      setIsAdmin(false);
      setAdminData(null);
      
      return { success: true };
    } catch (error) {
      console.error('Error in logout:', error);
      // Still clear local state even if API call fails
      localStorage.removeItem('auth_token');
      setToken(null);
      setCurrentUser(null);
      setIsAdmin(false);
      setAdminData(null);
    }
  }

  // Google authentication (still uses Firebase for OAuth)
  async function loginWithGoogle() {
    try {
      const provider = new GoogleAuthProvider();
      const result = await signInWithPopup(auth, provider);
      const user = result.user;
      
      // Register/login with Laravel API using Google data
      const response = await fetch('http://127.0.0.1:8000/api/frontend/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          name: user.displayName || user.email.split('@')[0],
          email: user.email,
          password: 'google_oauth_' + user.uid, // Dummy password for OAuth users
          provider: 'google'
        })
      });
      
      const data = await response.json();
      
      if (data.success) {
        localStorage.setItem('auth_token', data.data.token);
        localStorage.setItem('userId', data.data.user.id); // Store Laravel user ID
        setToken(data.data.token);
        setCurrentUser(data.data.user);
        return { success: true, user: data.data.user };
      } else {
        // If registration fails, try login (user might already exist)
        return await login(user.email, 'google_oauth_' + user.uid);
      }
    } catch (error) {
      console.error('Error in loginWithGoogle:', error);
      throw error;
    }
  }

  // Alias for backward compatibility
  const signupWithGoogle = loginWithGoogle;

  // Check if user is authenticated on app load
  useEffect(() => {
    const checkAuthState = async () => {
      console.log('AuthContext: Checking auth state');
      
      if (token) {
        try {
          // Verify token with Laravel API
          const response = await fetch('http://127.0.0.1:8000/api/frontend/me', {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            }
          });
          
          const data = await response.json();
          
          if (data.success) {
            setCurrentUser(data.data.user);
            localStorage.setItem('userId', data.data.user.id); // Store Laravel user ID
            console.log('AuthContext: User authenticated:', data.data.user.email);
            console.log('AuthContext: Stored userId from token verification:', data.data.user.id);
          } else {
            // Token is invalid, clear it
            localStorage.removeItem('auth_token');
            localStorage.removeItem('userId'); // Clear Laravel user ID
            setToken(null);
            console.log('AuthContext: Invalid token, cleared');
          }
        } catch (error) {
          console.error('AuthContext: Error verifying token:', error);
          localStorage.removeItem('auth_token');
          localStorage.removeItem('userId'); // Clear Laravel user ID
          setToken(null);
        }
      } else {
        console.log('AuthContext: No token found');
      }
      
      setLoading(false);
    };

    checkAuthState();
  }, [token]);

  // Enhanced login function that checks for admin status
  async function loginWithAdminCheck(email, password) {
    try {
      const result = await login(email, password);
      
      // For now, we'll assume no admin functionality in frontend
      // This can be extended later if needed
      setIsAdmin(false);
      setAdminData(null);
      
      return { 
        success: true, 
        isAdmin: false, 
        user: result.user,
        role: 'user',
        userData: result.user
      };
    } catch (error) {
      throw error;
    }
  }

  const value = {
    currentUser,
    isAdmin,
    adminData,
    token,
    signup,
    signupWithDatabase,
    signupWithGoogle,
    login,
    loginWithAdminCheck,
    logout,
    loginWithGoogle
  };

  return (
    <AuthContext.Provider value={value}>
      {!loading && children}
    </AuthContext.Provider>
  );
}