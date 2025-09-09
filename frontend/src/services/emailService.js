import emailjs from '@emailjs/browser';

// EmailJS configuration
const EMAILJS_SERVICE_ID = 'your_service_id'; // Replace with your EmailJS service ID
const EMAILJS_TEMPLATE_ID = 'your_template_id'; // Replace with your EmailJS template ID
const EMAILJS_PUBLIC_KEY = 'your_public_key'; // Replace with your EmailJS public key

// Initialize EmailJS
emailjs.init(EMAILJS_PUBLIC_KEY);

/**
 * Send order delivery notification email
 * @param {Object} orderData - Order information
 * @param {string} orderData.customerEmail - Customer's email address
 * @param {string} orderData.customerName - Customer's name
 * @param {string} orderData.orderId - Order ID
 * @param {string} orderData.deliveryDate - Delivery date
 * @param {Array} orderData.items - Order items
 * @param {number} orderData.totalAmount - Total order amount
 * @param {Object} orderData.shippingAddress - Shipping address
 * @returns {Promise} EmailJS response
 */
export const sendDeliveryNotification = async (orderData) => {
  try {
    // Prepare template parameters
    const templateParams = {
      to_email: orderData.customerEmail,
      to_name: orderData.customerName,
      order_id: orderData.orderId,
      delivery_date: orderData.deliveryDate,
      total_amount: orderData.totalAmount,
      shipping_address: `${orderData.shippingAddress.fullName}\n${orderData.shippingAddress.address}\n${orderData.shippingAddress.city}, ${orderData.shippingAddress.postalCode}\n${orderData.shippingAddress.phone}`,
      items_list: orderData.items.map(item => 
        `${item.product_name} - Quantity: ${item.quantity} - Price: $${item.price}`
      ).join('\n'),
      from_name: 'Softora Team',
      reply_to: 'noreply@softora.com'
    };

    // Send email using EmailJS
    const response = await emailjs.send(
      EMAILJS_SERVICE_ID,
      EMAILJS_TEMPLATE_ID,
      templateParams
    );

    console.log('Delivery notification sent successfully:', response);
    return {
      success: true,
      message: 'Delivery notification sent successfully',
      response
    };
  } catch (error) {
    console.error('Failed to send delivery notification:', error);
    return {
      success: false,
      message: 'Failed to send delivery notification',
      error: error.message
    };
  }
};

/**
 * Test email configuration
 * @returns {Promise} Test result
 */
export const testEmailConfiguration = async () => {
  const testData = {
    customerEmail: 'test@example.com',
    customerName: 'Test Customer',
    orderId: 'TEST001',
    deliveryDate: new Date().toLocaleDateString(),
    totalAmount: 99.99,
    shippingAddress: {
      fullName: 'Test Customer',
      address: '123 Test Street',
      city: 'Test City',
      postalCode: '12345',
      phone: '+1234567890'
    },
    items: [
      {
        product_name: 'Test Product',
        quantity: 1,
        price: 99.99
      }
    ]
  };

  return await sendDeliveryNotification(testData);
};