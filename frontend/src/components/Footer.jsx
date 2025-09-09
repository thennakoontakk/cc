import React from 'react';
import './Footer.css';

const Footer = () => {
  return (
    <footer className="footer">
      <div className="footer-content">
        <div className="footer-section">
          <h3 className="footer-title">CRAFTERS' CORNER</h3>
          <p className="footer-description">
            Crafters' Corner Celebrates Personalized Creations With Customized Crafts, Wall Art, Bags, 
            Baskets, And Intricate Paper Quilling Arts. Discover Unique Elegance!
          </p>
        </div>
        
        <div className="footer-section">
          <h4 className="section-title">Contact Info</h4>
          <ul className="footer-list">
            <li>+94778562544</li>
            <li>CraftersCorner@Gmail.Com</li>
            <li>Kurunegala, Sri Lanka.</li>
          </ul>
        </div>
        
        <div className="footer-section">
          <h4 className="section-title">Quick Links</h4>
          <ul className="footer-list">
            <li><a href="#home">Home</a></li>
            <li><a href="#shop">Shop</a></li>
            <li><a href="#custom">Custom Craft Corner</a></li>
            <li><a href="#contact">Contact Us</a></li>
          </ul>
        </div>
        
        <div className="footer-section">
          <h4 className="section-title">Categories</h4>
          <ul className="footer-list">
            <li><a href="#gifts">Customized Crafts/Gifts</a></li>
            <li><a href="#wall-art">Wall Art</a></li>
            <li><a href="#bags">Bags & Baskets</a></li>
            <li><a href="#quilling">Paper Quilling Arts</a></li>
          </ul>
        </div>
      </div>
      
      <div className="footer-social">
        <div className="social-icons">
          <a href="#" className="social-icon instagram" aria-label="Instagram">
            <i className="fab fa-instagram"></i>
          </a>
          <a href="#" className="social-icon twitter" aria-label="Twitter">
            <i className="fab fa-twitter"></i>
          </a>
          <a href="#" className="social-icon youtube" aria-label="YouTube">
            <i className="fab fa-youtube"></i>
          </a>
          <a href="#" className="social-icon facebook" aria-label="Facebook">
            <i className="fab fa-facebook-f"></i>
          </a>
        </div>
      </div>
    </footer>
  );
};

export default Footer;