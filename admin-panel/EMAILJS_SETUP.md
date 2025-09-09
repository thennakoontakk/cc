# EmailJS Setup Guide

This guide explains how to configure EmailJS for sending order delivery notifications.

## Prerequisites

1. Create an account at [EmailJS](https://www.emailjs.com/)
2. Set up an email service (Gmail, Outlook, etc.)
3. Create an email template
4. Get your credentials

## Step 1: Create EmailJS Account

1. Go to https://www.emailjs.com/
2. Sign up for a free account
3. Verify your email address

## Step 2: Add Email Service

1. Go to Email Services in your EmailJS dashboard
2. Click "Add New Service"
3. Choose your email provider (Gmail recommended)
4. Follow the setup instructions
5. Note down your **Service ID**

## Step 3: Create Email Template

1. Go to Email Templates in your dashboard
2. Click "Create New Template"
3. Use the following template variables:
   - `{{to_email}}` - Customer email
   - `{{customer_name}}` - Customer name
   - `{{order_id}}` - Order ID
   - `{{order_total}}` - Order total amount
   - `{{delivery_address}}` - Delivery address
   - `{{order_items}}` - List of ordered items

### Sample Email Template:

```
Subject: Your Order #{{order_id}} Has Been Delivered!

Dear {{customer_name}},

Great news! Your order #{{order_id}} has been successfully delivered to:
{{delivery_address}}

Order Details:
{{order_items}}

Total Amount: ${{order_total}}

Thank you for shopping with us!

Best regards,
Softora Team
```

4. Save the template and note down your **Template ID**

## Step 4: Get Public Key

1. Go to Account settings
2. Find your **Public Key**

## Step 5: Update Admin Panel Configuration

1. Open `/admin-panel/resources/views/admin/orders/index.blade.php`
2. Replace the following placeholders:
   - `YOUR_PUBLIC_KEY` with your EmailJS Public Key
   - `YOUR_SERVICE_ID` with your EmailJS Service ID
   - `YOUR_TEMPLATE_ID` with your EmailJS Template ID

```javascript
// Initialize EmailJS
emailjs.init('your_actual_public_key_here');

// In sendDeliveryNotificationEmail function
emailjs.send('your_service_id_here', 'your_template_id_here', templateParams)
```

## Step 6: Test the Configuration

1. Access your admin panel at `http://localhost:8000/admin/orders`
2. Find a confirmed order
3. Click "Deliver" to mark it as delivered
4. Check if the email is sent successfully
5. Verify the email is received at the customer's email address

## Troubleshooting

- **Email not sending**: Check browser console for errors
- **Invalid credentials**: Verify your Service ID, Template ID, and Public Key
- **Template errors**: Ensure all template variables are properly mapped
- **Rate limits**: EmailJS free plan has monthly limits

## Security Notes

- Public Key is safe to use in frontend code
- Never expose your Private Key in client-side code
- Consider upgrading to EmailJS paid plan for production use

## Support

For EmailJS specific issues, visit: https://www.emailjs.com/docs/