import React from 'react';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';

function Dashboard() {
  const { currentUser, logout } = useAuth();
  const navigate = useNavigate();

  async function handleLogout() {
    try {
      await logout();
      navigate('/login');
    } catch (error) {
      console.error('Failed to log out:', error);
    }
  }

  return (
    <div className="dashboard-container">
      <header className="dashboard-header">
        <h1>Dashboard</h1>
        <div className="user-info">
          <span>Welcome, {currentUser?.email}</span>
          <button onClick={handleLogout} className="btn-logout">
            Logout
          </button>
        </div>
      </header>
      
      <main className="dashboard-content">
        <div className="dashboard-card">
          <h2>User Profile</h2>
          <div className="profile-info">
            <p><strong>Email:</strong> {currentUser?.email}</p>
            <p><strong>User ID:</strong> {currentUser?.id}</p>
            <p><strong>Email Verified:</strong> {currentUser?.emailVerified ? 'Yes' : 'No'}</p>
            <p><strong>Account Created:</strong> {currentUser?.metadata?.creationTime}</p>
          </div>
        </div>
        
        <div className="dashboard-card">
          <h2>Quick Actions</h2>
          <div className="actions-grid">
            <button className="action-btn">Update Profile</button>
            <button className="action-btn">Change Password</button>
            <button className="action-btn">Account Settings</button>
            <button className="action-btn">Privacy Settings</button>
          </div>
        </div>
        
        <div className="dashboard-card">
          <h2>Recent Activity</h2>
          <p>No recent activity to display.</p>
        </div>
      </main>
    </div>
  );
}

export default Dashboard;