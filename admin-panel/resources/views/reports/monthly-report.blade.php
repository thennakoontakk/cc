<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Report - {{ $month }}/{{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .summary-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            width: 22%;
            border: 1px solid #dee2e6;
        }
        .summary-card h3 {
            margin: 0 0 10px 0;
            color: #007bff;
            font-size: 24px;
        }
        .summary-card p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .currency {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Report</h1>
        <p>{{ $month }}/{{ $year }}</p>
        <p>Generated on {{ now()->format('F j, Y \\a\\t g:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-card">
            <h3>{{ $totalOrders }}</h3>
            <p>Total Orders</p>
        </div>
        <div class="summary-card">
            <h3 class="currency">${{ number_format($totalRevenue, 2) }}</h3>
            <p>Total Revenue</p>
        </div>
        <div class="summary-card">
            <h3>{{ $totalCustomers }}</h3>
            <p>Total Customers</p>
        </div>
        <div class="summary-card">
            <h3 class="currency">${{ number_format($averageOrderValue, 2) }}</h3>
            <p>Avg Order Value</p>
        </div>
    </div>

    <div class="section">
        <h2>Order Status Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordersByStatus as $status => $data)
                <tr>
                    <td>{{ ucfirst($status) }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>{{ number_format(($data['count'] / $totalOrders) * 100, 1) }}%</td>
                    <td class="currency">${{ number_format($data['revenue'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Top Products</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->orders_count }}</td>
                    <td class="currency">${{ number_format($product->total_revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Recent Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                    <td>{{ $order->created_at->format('M j, Y') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td class="currency">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report was automatically generated by the Admin Panel System</p>
        <p>© {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>