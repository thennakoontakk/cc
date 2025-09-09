import React, { useState } from 'react';
import './QuantityModal.css';

const QuantityModal = ({ isOpen, onClose, product, onAddToCart }) => {
  const [quantity, setQuantity] = useState(1);

  const handleQuantityChange = (newQuantity) => {
    if (newQuantity >= 1 && newQuantity <= product.stock) {
      setQuantity(newQuantity);
    }
  };

  const handleAddToCart = () => {
    onAddToCart(product, quantity);
    setQuantity(1); // Reset quantity
    onClose();
  };

  const handleClose = () => {
    setQuantity(1); // Reset quantity when closing
    onClose();
  };

  if (!isOpen) return null;

  return (
    <div className="modal-overlay" onClick={handleClose}>
      <div className="modal-content" onClick={(e) => e.stopPropagation()}>
        <div className="modal-header">
          <h3>Add to Cart</h3>
          <button className="close-btn" onClick={handleClose}>
            ×
          </button>
        </div>
        
        <div className="modal-body">
          <div className="product-info">
            <div className="product-image">
              {product.image ? (
                <img src={product.image} alt={product.name} />
              ) : (
                <div className="placeholder-image">
                  <span>📦</span>
                </div>
              )}
            </div>
            
            <div className="product-details">
              <h4>{product.name}</h4>
              <p className="product-price">${parseFloat(product.price).toFixed(2)}</p>
              <p className="product-stock">{product.stock} available</p>
            </div>
          </div>
          
          <div className="quantity-selector">
            <label htmlFor="quantity">Quantity:</label>
            <div className="quantity-controls">
              <button 
                className="quantity-btn"
                onClick={() => handleQuantityChange(quantity - 1)}
                disabled={quantity <= 1}
              >
                -
              </button>
              <input 
                type="number" 
                id="quantity"
                value={quantity}
                onChange={(e) => handleQuantityChange(parseInt(e.target.value) || 1)}
                min="1"
                max={product.stock}
                className="quantity-input"
              />
              <button 
                className="quantity-btn"
                onClick={() => handleQuantityChange(quantity + 1)}
                disabled={quantity >= product.stock}
              >
                +
              </button>
            </div>
          </div>
          
          <div className="total-price">
            <strong>Total: ${(parseFloat(product.price) * quantity).toFixed(2)}</strong>
          </div>
        </div>
        
        <div className="modal-footer">
          <button className="cancel-btn" onClick={handleClose}>
            Cancel
          </button>
          <button className="add-to-cart-btn" onClick={handleAddToCart}>
            Add to Cart
          </button>
        </div>
      </div>
    </div>
  );
};

export default QuantityModal;