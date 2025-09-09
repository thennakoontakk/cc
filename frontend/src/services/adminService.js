// Admin service for handling admin authentication and role checking

const API_BASE_URL = 'http://localhost:8000/api';

/**
 * Check if a Laravel user is an admin by verifying with the Laravel backend
 * @param {Object} user - User object with Laravel token
 * @returns {Promise<Object>} - Admin verification result
 */
export const checkIfUserIsAdmin = async (user) => {
  try {
    if (!user) {
      return { isAdmin: false, adminData: null };
    }

    // Get Laravel Sanctum token
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
      return { isAdmin: false, adminData: null };
    }
    
    // Check if user has admin role in the backend
    const adminCheckResponse = await fetch(`${API_BASE_URL}/frontend/me`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    });

    if (adminCheckResponse.ok) {
      const userData = await adminCheckResponse.json();
      const isAdmin = userData.user && userData.user.is_admin === 1;
      
      return {
        isAdmin,
        adminData: isAdmin ? userData.user : null,
        token
      };
    }
    
    return { isAdmin: false, adminData: null };
  } catch (error) {
    console.error('Error checking admin status:', error);
    return { isAdmin: false, adminData: null, error: error.message };
  }
};

/**
 * Login admin using email and password
 * @param {string} email - Admin email
 * @param {string} password - Admin password
 * @returns {Promise<Object>} - Login result
 */
export const loginAdmin = async (email, password) => {
  try {
    const response = await fetch(`${API_BASE_URL}/admin/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ email, password })
    });

    const data = await response.json();
    
    if (data.success) {
      // Store admin token in localStorage
      localStorage.setItem('adminToken', data.token);
      localStorage.setItem('adminData', JSON.stringify(data.admin));
      
      return {
        success: true,
        admin: data.admin,
        token: data.token
      };
    } else {
      return {
        success: false,
        error: data.message || 'Login failed'
      };
    }
  } catch (error) {
    console.error('Admin login error:', error);
    return {
      success: false,
      error: 'Network error. Please try again.'
    };
  }
};

/**
 * Check if current user has admin token stored
 * @returns {Object} - Admin authentication status
 */
export const getStoredAdminAuth = () => {
  try {
    const token = localStorage.getItem('adminToken');
    const adminData = localStorage.getItem('adminData');
    
    if (token && adminData) {
      return {
        isAuthenticated: true,
        token,
        admin: JSON.parse(adminData)
      };
    }
    
    return { isAuthenticated: false };
  } catch (error) {
    console.error('Error getting stored admin auth:', error);
    return { isAuthenticated: false };
  }
};

/**
 * Clear admin authentication data
 */
export const clearAdminAuth = () => {
  localStorage.removeItem('adminToken');
  localStorage.removeItem('adminData');
};

/**
 * Get admin panel URL based on environment
 * @returns {string} - Admin panel URL
 */
export const getAdminPanelUrl = () => {
  // Laravel admin panel URL
  return 'http://127.0.0.1:8000/admin/dashboard';
};

export default {
  checkIfUserIsAdmin,
  loginAdmin,
  getStoredAdminAuth,
  clearAdminAuth,
  getAdminPanelUrl
};