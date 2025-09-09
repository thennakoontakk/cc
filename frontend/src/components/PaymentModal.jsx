import React, { useState } from 'react';
import './PaymentModal.css';

const PaymentModal = ({ isOpen, onClose, cartItems, totalAmount, onPaymentSuccess }) => {
  const [paymentStep, setPaymentStep] = useState(1); // 1: Payment Method, 2: Payment Details, 3: Processing, 4: Success
  const [paymentMethod, setPaymentMethod] = useState('');
  const [paymentDetails, setPaymentDetails] = useState({
    cardNumber: '',
    expiryDate: '',
    cvv: '',
    cardholderName: '',
    // For other payment methods
    phoneNumber: '',
    email: ''
  });
  const [shippingAddress, setShippingAddress] = useState({
    fullName: '',
    address: '',
    city: '',
    postalCode: '',
    phone: ''
  });
  const [isProcessing, setIsProcessing] = useState(false);
  const [errors, setErrors] = useState({});

  const paymentMethods = [
    { id: 'fake', name: 'Test Payment (Dev)', icon: '🧪', isDev: true },
    { id: 'card', name: 'Credit/Debit Card', icon: '💳' },
    { id: 'paypal', name: 'PayPal', icon: '🅿️' },
    { id: 'googlepay', name: 'Google Pay', icon: '🔵' },
    { id: 'applepay', name: 'Apple Pay', icon: '🍎' }
  ];

  const validatePaymentDetails = () => {
    const newErrors = {};
    
    if (paymentMethod === 'card') {
      if (!paymentDetails.cardNumber || paymentDetails.cardNumber.length < 16) {
        newErrors.cardNumber = 'Please enter a valid card number';
      }
      if (!paymentDetails.expiryDate) {
        newErrors.expiryDate = 'Please enter expiry date';
      }
      if (!paymentDetails.cvv || paymentDetails.cvv.length < 3) {
        newErrors.cvv = 'Please enter a valid CVV';
      }
      if (!paymentDetails.cardholderName) {
        newErrors.cardholderName = 'Please enter cardholder name';
      }
    }
    
    // Validate shipping address
    if (!shippingAddress.fullName) newErrors.fullName = 'Full name is required';
    if (!shippingAddress.address) newErrors.address = 'Address is required';
    if (!shippingAddress.city) newErrors.city = 'City is required';
    if (!shippingAddress.postalCode) newErrors.postalCode = 'Postal code is required';
    if (!shippingAddress.phone) newErrors.phone = 'Phone number is required';
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handlePaymentMethodSelect = (method) => {
    setPaymentMethod(method);
    
    // For fake payment, skip to processing directly
    if (method === 'fake') {
      // Set minimal required data for fake payment
      setShippingAddress({
        fullName: 'Test User',
        address: '123 Test Street',
        city: 'Test City',
        postalCode: '12345',
        phone: '123-456-7890'
      });
      setPaymentStep(3);
      processFakePayment();
    } else {
      setPaymentStep(2);
    }
  };

  const handlePaymentDetailsSubmit = () => {
    if (validatePaymentDetails()) {
      setPaymentStep(3);
      processPayment();
    }
  };

  const processFakePayment = async () => {
    setIsProcessing(true);
    
    try {
      // Simulate shorter processing for fake payment
      await new Promise(resolve => setTimeout(resolve, 1500));
      
      // Create order in backend with fake payment data
      const orderData = {
        cart_items: cartItems.map(item => ({
          product_id: item.id,
          quantity: item.quantity
        })),
        total_amount: totalAmount,
        payment_method: 'fake_payment',
        payment_transaction_id: `FAKE_TXN_${Date.now()}`,
        shipping_address: shippingAddress,
        billing_address: shippingAddress,
        user_id: localStorage.getItem('userId') || 'guest'
      };
      
      const response = await fetch('http://localhost:8000/api/orders', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
      });
      
      if (response.ok) {
        const result = await response.json();
        setPaymentStep(4);
        setTimeout(() => {
          onPaymentSuccess(result.order);
          resetModal();
          onClose();
        }, 1500);
      } else {
        const errorData = await response.json();
        console.error('Backend error:', errorData);
        throw new Error(`Fake payment failed: ${errorData.message || response.statusText}`);
      }
    } catch (error) {
      console.error('Fake payment error:', error);
      alert('Fake payment failed. Please try again.');
      setPaymentStep(1);
    } finally {
      setIsProcessing(false);
    }
  };

  const processPayment = async () => {
    setIsProcessing(true);
    
    try {
      // Simulate payment processing
      await new Promise(resolve => setTimeout(resolve, 3000));
      
      // Create order in backend
      const orderData = {
        cart_items: cartItems.map(item => ({
          product_id: item.id,
          quantity: item.quantity
        })),
        total_amount: totalAmount,
        payment_method: paymentMethod,
        payment_transaction_id: `TXN_${Date.now()}`,
        shipping_address: shippingAddress,
        billing_address: shippingAddress,
        user_id: localStorage.getItem('userId') || 'guest'
      };
      
      const response = await fetch('http://localhost:8000/api/orders', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
      });
      
      if (response.ok) {
        const result = await response.json();
        setPaymentStep(4);
        setTimeout(() => {
          onPaymentSuccess(result.order);
          resetModal();
          onClose();
        }, 2000);
      } else {
        throw new Error('Payment failed');
      }
    } catch (error) {
      console.error('Payment error:', error);
      alert('Payment failed. Please try again.');
      setPaymentStep(2);
    } finally {
      setIsProcessing(false);
    }
  };

  const resetModal = () => {
    setPaymentStep(1);
    setPaymentMethod('');
    setPaymentDetails({
      cardNumber: '',
      expiryDate: '',
      cvv: '',
      cardholderName: '',
      phoneNumber: '',
      email: ''
    });
    setShippingAddress({
      fullName: '',
      address: '',
      city: '',
      postalCode: '',
      phone: ''
    });
    setErrors({});
  };

  const handleClose = () => {
    resetModal();
    onClose();
  };

  if (!isOpen) return null;

  return (
    <div className="payment-modal-overlay">
      <div className="payment-modal">
        <div className="payment-modal-header">
          <h2>Checkout</h2>
          <button className="close-btn" onClick={handleClose}>×</button>
        </div>
        
        <div className="payment-progress">
          <div className={`progress-step ${paymentStep >= 1 ? 'active' : ''}`}>1</div>
          <div className={`progress-line ${paymentStep >= 2 ? 'active' : ''}`}></div>
          <div className={`progress-step ${paymentStep >= 2 ? 'active' : ''}`}>2</div>
          <div className={`progress-line ${paymentStep >= 3 ? 'active' : ''}`}></div>
          <div className={`progress-step ${paymentStep >= 3 ? 'active' : ''}`}>3</div>
        </div>

        <div className="payment-modal-content">
          {paymentStep === 1 && (
            <div className="payment-method-selection">
              <h3>Select Payment Method</h3>
              <div className="payment-methods">
                {paymentMethods.map(method => (
                  <div 
                    key={method.id} 
                    className={`payment-method-card ${method.isDev ? 'dev-payment' : ''}`}
                    onClick={() => handlePaymentMethodSelect(method.id)}
                  >
                    <span className="payment-icon">{method.icon}</span>
                    <span className="payment-name">{method.name}</span>
                  </div>
                ))}
              </div>
            </div>
          )}

          {paymentStep === 2 && (
            <div className="payment-details">
              <h3>Payment & Shipping Details</h3>
              
              {/* Shipping Address */}
              <div className="form-section">
                <h4>Shipping Address</h4>
                <div className="form-row">
                  <input
                    type="text"
                    placeholder="Full Name"
                    value={shippingAddress.fullName}
                    onChange={(e) => setShippingAddress({...shippingAddress, fullName: e.target.value})}
                    className={errors.fullName ? 'error' : ''}
                  />
                  {errors.fullName && <span className="error-text">{errors.fullName}</span>}
                </div>
                <div className="form-row">
                  <input
                    type="text"
                    placeholder="Address"
                    value={shippingAddress.address}
                    onChange={(e) => setShippingAddress({...shippingAddress, address: e.target.value})}
                    className={errors.address ? 'error' : ''}
                  />
                  {errors.address && <span className="error-text">{errors.address}</span>}
                </div>
                <div className="form-row-group">
                  <div className="form-row">
                    <input
                      type="text"
                      placeholder="City"
                      value={shippingAddress.city}
                      onChange={(e) => setShippingAddress({...shippingAddress, city: e.target.value})}
                      className={errors.city ? 'error' : ''}
                    />
                    {errors.city && <span className="error-text">{errors.city}</span>}
                  </div>
                  <div className="form-row">
                    <input
                      type="text"
                      placeholder="Postal Code"
                      value={shippingAddress.postalCode}
                      onChange={(e) => setShippingAddress({...shippingAddress, postalCode: e.target.value})}
                      className={errors.postalCode ? 'error' : ''}
                    />
                    {errors.postalCode && <span className="error-text">{errors.postalCode}</span>}
                  </div>
                </div>
                <div className="form-row">
                  <input
                    type="tel"
                    placeholder="Phone Number"
                    value={shippingAddress.phone}
                    onChange={(e) => setShippingAddress({...shippingAddress, phone: e.target.value})}
                    className={errors.phone ? 'error' : ''}
                  />
                  {errors.phone && <span className="error-text">{errors.phone}</span>}
                </div>
              </div>

              {/* Payment Details */}
              {paymentMethod === 'card' && (
                <div className="form-section">
                  <h4>Card Details</h4>
                  <div className="form-row">
                    <input
                      type="text"
                      placeholder="Card Number"
                      value={paymentDetails.cardNumber}
                      onChange={(e) => setPaymentDetails({...paymentDetails, cardNumber: e.target.value})}
                      className={errors.cardNumber ? 'error' : ''}
                      maxLength="16"
                    />
                    {errors.cardNumber && <span className="error-text">{errors.cardNumber}</span>}
                  </div>
                  <div className="form-row-group">
                    <div className="form-row">
                      <input
                        type="text"
                        placeholder="MM/YY"
                        value={paymentDetails.expiryDate}
                        onChange={(e) => setPaymentDetails({...paymentDetails, expiryDate: e.target.value})}
                        className={errors.expiryDate ? 'error' : ''}
                        maxLength="5"
                      />
                      {errors.expiryDate && <span className="error-text">{errors.expiryDate}</span>}
                    </div>
                    <div className="form-row">
                      <input
                        type="text"
                        placeholder="CVV"
                        value={paymentDetails.cvv}
                        onChange={(e) => setPaymentDetails({...paymentDetails, cvv: e.target.value})}
                        className={errors.cvv ? 'error' : ''}
                        maxLength="4"
                      />
                      {errors.cvv && <span className="error-text">{errors.cvv}</span>}
                    </div>
                  </div>
                  <div className="form-row">
                    <input
                      type="text"
                      placeholder="Cardholder Name"
                      value={paymentDetails.cardholderName}
                      onChange={(e) => setPaymentDetails({...paymentDetails, cardholderName: e.target.value})}
                      className={errors.cardholderName ? 'error' : ''}
                    />
                    {errors.cardholderName && <span className="error-text">{errors.cardholderName}</span>}
                  </div>
                </div>
              )}

              <div className="order-summary">
                <h4>Order Summary</h4>
                <div className="summary-items">
                  {cartItems.map(item => (
                    <div key={item.id} className="summary-item">
                      <span>{item.product_name} x {item.quantity}</span>
                      <span>${(item.unit_price * item.quantity).toFixed(2)}</span>
                    </div>
                  ))}
                </div>
                <div className="summary-total">
                  <strong>Total: ${totalAmount.toFixed(2)}</strong>
                </div>
              </div>

              <div className="payment-actions">
                <button className="btn-secondary" onClick={() => setPaymentStep(1)}>Back</button>
                <button className="btn-primary" onClick={handlePaymentDetailsSubmit}>Pay Now</button>
              </div>
            </div>
          )}

          {paymentStep === 3 && (
            <div className="payment-processing">
              <div className="processing-animation">
                <div className="spinner"></div>
              </div>
              <h3>Processing Payment...</h3>
              <p>Please wait while we process your payment securely.</p>
            </div>
          )}

          {paymentStep === 4 && (
            <div className="payment-success">
              <div className="success-icon">✅</div>
              <h3>Payment Successful!</h3>
              <p>Your order has been placed successfully. You will receive a confirmation email shortly.</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default PaymentModal;