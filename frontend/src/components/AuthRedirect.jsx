import React, { useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';
import AdminPanelBridge from './AdminPanelBridge';

const AuthRedirect = () => {
  const { currentUser, isAdmin } = useAuth();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = React.useState(true);

  useEffect(() => {
    console.log('AuthRedirect: Effect triggered, currentUser:', currentUser ? currentUser.email : 'null', 'isAdmin:', isAdmin);
    
    const handleRedirect = async () => {
      if (currentUser) {
        console.log('AuthRedirect: User found, checking admin status...');
        if (isAdmin) {
          console.log('AuthRedirect: User is admin, rendering AdminPanelBridge');
          // Admin user - will render AdminPanelBridge
          setIsLoading(false);
        } else {
          console.log('AuthRedirect: User is not admin, redirecting to home');
          // Regular user - redirect to home page
          navigate('/');
        }
      } else {
        console.log('AuthRedirect: No user found, redirecting to login');
        // No user, redirect to login
        navigate('/login');
      }
    };

    handleRedirect();
  }, [currentUser, isAdmin, navigate]);

  if (isLoading) {
    return (
      <div className="auth-redirect-container" style={{
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        height: '100vh',
        flexDirection: 'column'
      }}>
        <div className="loading-spinner" style={{
          border: '4px solid #f3f3f3',
          borderTop: '4px solid #3498db',
          borderRadius: '50%',
          width: '40px',
          height: '40px',
          animation: 'spin 2s linear infinite',
          marginBottom: '20px'
        }}></div>
        <p>Checking permissions...</p>
        <style jsx>{`
          @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
          }
        `}</style>
      </div>
    );
  }

  if (isAdmin) {
    return <AdminPanelBridge />;
  }

  return null;
};

export default AuthRedirect;