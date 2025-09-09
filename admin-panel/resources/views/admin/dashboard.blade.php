@extends('layouts.shared')

@section('title', 'Admin Dashboard - Crafters\' Corner')
@section('page-title', 'Dashboard')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* Dashboard specific styles */
    .dashboard-header {
        margin-bottom: 2rem;
    }
    
    .dashboard-header h1 {
        color: #2c3e50;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .breadcrumb {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    
    .stat-card.customers::before {
        background: #3498db;
    }
    
    .stat-card.orders::before {
        background: #2ecc71;
    }
    
    .stat-card.pending::before {
        background: #f39c12;
    }
    
    .stat-card.requests::before {
        background: #e74c3c;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: #7f8c8d;
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .stat-link {
        color: #3498db;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s;
    }
    
    .stat-link:hover {
        color: #2980b9;
    }
    
    .charts-container {
        margin-top: 30px;
    }
    
    .chart-filters {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        text-align: center;
    }
    
    .filter-buttons {
        display: inline-flex;
        gap: 10px;
    }
    
    .filter-btn {
        padding: 10px 20px;
        border: 2px solid #e0e0e0;
        background: white;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        color: #666;
    }
    
    .filter-btn.active {
        background: #4a90e2;
        border-color: #4a90e2;
        color: white;
    }
    
    .filter-btn:hover {
        border-color: #4a90e2;
        color: #4a90e2;
    }
    
    .filter-btn.active:hover {
        color: white;
    }
    
    .chart-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .chart-header {
        margin-bottom: 20px;
    }
    
    .chart-header h3 {
        color: #2c3e50;
        font-size: 1.4rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .chart-header h3 i {
        color: #4a90e2;
    }
    
    .chart-description {
        color: #666;
        font-size: 0.9rem;
        margin: 5px 0 0 0;
    }
    
    .chart-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .chart-tab-btn {
        padding: 8px 16px;
        border: 1px solid #e0e0e0;
        background: #f8f9fa;
        border-radius: 20px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.3s ease;
        color: #666;
    }
    
    .chart-tab-btn.active {
        background: #2ecc71;
        border-color: #2ecc71;
        color: white;
    }
    
    .chart-tab-btn:hover {
        border-color: #2ecc71;
        color: #2ecc71;
    }
    
    .chart-tab-btn.active:hover {
        color: white;
    }
    
    .customer-stats {
        display: flex;
        gap: 30px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        min-width: 120px;
    }
    
    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #666;
        text-align: center;
    }
    
    #earningsChart {
        max-height: 400px;
    }
    
    canvas {
        max-height: 400px;
    }
    
    .report-download-buttons {
        display: flex;
        gap: 8px;
    }
    
    .report-download-buttons .btn {
        font-size: 0.85rem;
        padding: 6px 12px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .report-download-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-number {
            font-size: 2rem;
        }
        
        .report-download-buttons {
            flex-direction: column;
            gap: 4px;
        }
        
        .report-download-buttons .btn {
            font-size: 0.8rem;
            padding: 4px 8px;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard-header">
    <h1>Dashboard</h1>
    <div class="breadcrumb">Home / Dashboard</div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card customers">
        <div class="stat-number">{{ $totalCustomers }}</div>
        <div class="stat-label">Total Customers</div>
        <a href="{{ route('admin.users.index') }}" class="stat-link">More info →</a>
    </div>
    
    <div class="stat-card orders">
        <div class="stat-number">{{ $todayOrders }}</div>
        <div class="stat-label">Today Orders</div>
        <a href="{{ route('admin.orders.index') }}" class="stat-link">More info →</a>
    </div>
    
    <div class="stat-card pending">
        <div class="stat-number">{{ $pendingOrders }}</div>
        <div class="stat-label">Pending Orders</div>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="stat-link">More info →</a>
    </div>
    
    <div class="stat-card requests">
        <div class="stat-number">{{ $customerRequests }}</div>
        <div class="stat-label">Customer Requests</div>
        <a href="#" class="stat-link">More info →</a>
    </div>
</div>

<!-- Cart Statistics -->
<div class="stats-grid">
    <div class="stat-card customers">
        <div class="stat-number">{{ $activeCarts }}</div>
        <div class="stat-label">Active Carts</div>
        <a href="{{ route('admin.cart.index') }}" class="stat-link">More info →</a>
    </div>
    
    <div class="stat-card orders">
        <div class="stat-number">{{ $totalCartItems }}</div>
        <div class="stat-label">Total Cart Items</div>
        <a href="{{ route('admin.cart.index') }}" class="stat-link">More info →</a>
    </div>
    
    <div class="stat-card pending">
        <div class="stat-number">${{ number_format($totalCartValue, 2) }}</div>
        <div class="stat-label">Total Cart Value</div>
        <a href="{{ route('admin.cart.index') }}" class="stat-link">More info →</a>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-container">
    <!-- Chart Filters -->
    <div class="chart-filters">
        <div class="filter-buttons">
            <button class="filter-btn active" data-period="monthly">Monthly View</button>
            <button class="filter-btn" data-period="weekly">Weekly View</button>
        </div>
    </div>
    
    <!-- Orders Analytics Chart -->
    <div class="chart-section">
        <div class="chart-header">
            <h3><i class="fas fa-shopping-cart"></i> Orders Analytics</h3>
            <p class="chart-description">Track order trends and revenue over time</p>
        </div>
        <div class="chart-tabs">
            <button class="chart-tab-btn active" data-chart="orders" data-type="count">Order Count</button>
            <button class="chart-tab-btn" data-chart="orders" data-type="revenue">Revenue</button>
            <button class="chart-tab-btn" data-chart="orders" data-type="status">Status Distribution</button>
        </div>
        <canvas id="ordersChart"></canvas>
    </div>
    
    <!-- Customers Analytics Chart -->
    <div class="chart-section">
        <div class="chart-header">
            <h3><i class="fas fa-users"></i> Customers Analytics</h3>
            <p class="chart-description">Monitor customer registration and growth trends</p>
        </div>
        <div class="customer-stats">
            <div class="stat-item">
                <span class="stat-value">{{ $customersData['total'] }}</span>
                <span class="stat-label">Total Customers</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ $customersData['active'] }}</span>
                <span class="stat-label">Active (30 days)</span>
            </div>
        </div>
        <canvas id="customersChart"></canvas>
    </div>
    
    <!-- Reports Analytics Chart -->
    <div class="chart-section">
        <div class="chart-header">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <div>
                    <h3><i class="fas fa-chart-line"></i> Performance Reports</h3>
                    <p class="chart-description">Comprehensive performance metrics and comparisons</p>
                </div>

            </div>
        </div>
        <div class="chart-tabs">
            <button class="chart-tab-btn active" data-chart="reports" data-type="orders">Orders</button>
            <button class="chart-tab-btn" data-chart="reports" data-type="revenue">Revenue</button>
            <button class="chart-tab-btn" data-chart="reports" data-type="customers">Customers</button>
        </div>
        <canvas id="reportsChart"></canvas>
    </div>
    
    <!-- Earnings Overview Chart -->
    <div class="chart-section">
        <div class="chart-header">
            <h3><i class="fas fa-dollar-sign"></i> Earnings Overview</h3>
            <p class="chart-description">Monthly earnings trend (last 6 months)</p>
        </div>
        <canvas id="earningsChart"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Chart data from backend
const ordersData = {!! json_encode($ordersData) !!};
const customersData = {!! json_encode($customersData) !!};
const reportsData = {!! json_encode($reportsData) !!};
const earningsData = {!! json_encode($earningsData) !!};

// Chart instances
let ordersChart, customersChart, reportsChart, earningsChart;
let currentPeriod = 'monthly';

// Initialize all charts
document.addEventListener('DOMContentLoaded', function() {
    initializeOrdersChart();
    initializeCustomersChart();
    initializeReportsChart();
    initializeEarningsChart();
    initializeEventListeners();
});

function initializeOrdersChart() {
    const ctx = document.getElementById('ordersChart').getContext('2d');
    ordersChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ordersData[currentPeriod].labels,
            datasets: [{
                label: 'Orders',
                data: ordersData[currentPeriod].orders,
                borderColor: '#2ecc71',
                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: getChartOptions('Orders')
    });
}

function initializeCustomersChart() {
    const ctx = document.getElementById('customersChart').getContext('2d');
    customersChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: customersData[currentPeriod].labels,
            datasets: [{
                label: 'New Registrations',
                data: customersData[currentPeriod].registrations,
                backgroundColor: 'rgba(52, 152, 219, 0.8)',
                borderColor: '#3498db',
                borderWidth: 1
            }]
        },
        options: getChartOptions('New Registrations')
    });
}

function initializeReportsChart() {
    const ctx = document.getElementById('reportsChart').getContext('2d');
    const reportMetrics = reportsData[currentPeriod];
    
    reportsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: reportMetrics.map(item => currentPeriod === 'monthly' ? item.month : item.week),
            datasets: [{
                label: 'Orders',
                data: reportMetrics.map(item => item.orders),
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                borderWidth: 2,
                fill: false
            }]
        },
        options: getChartOptions('Orders')
    });
}

function initializeEarningsChart() {
    const ctx = document.getElementById('earningsChart').getContext('2d');
    earningsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: earningsData.labels,
            datasets: [{
                label: 'Earnings ($)',
                data: earningsData.data,
                borderColor: '#4a90e2',
                backgroundColor: 'rgba(74, 144, 226, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: getChartOptions('Earnings ($)')
    });
}

function getChartOptions(label) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f0f0f0'
                },
                ticks: {
                    callback: function(value) {
                        if (label.includes('$')) {
                            return '$' + value.toLocaleString();
                        }
                        return value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    color: '#f0f0f0'
                }
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
    };
}

function initializeEventListeners() {
    // Period filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriod = this.dataset.period;
            updateAllCharts();
        });
    });
    
    // Chart tab buttons
    document.querySelectorAll('.chart-tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const chartType = this.dataset.chart;
            const dataType = this.dataset.type;
            
            // Update active tab
            document.querySelectorAll(`[data-chart="${chartType}"]`).forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Update chart based on type
            updateChartData(chartType, dataType);
        });
    });
}

function updateAllCharts() {
    updateOrdersChart('count');
    updateCustomersChart();
    updateReportsChart('orders');
}

function updateOrdersChart(type) {
    let data, label, color;
    
    if (type === 'count') {
        data = ordersData[currentPeriod].orders;
        label = 'Orders';
        color = '#2ecc71';
    } else if (type === 'revenue') {
        data = ordersData[currentPeriod].revenue;
        label = 'Revenue ($)';
        color = '#f39c12';
    } else if (type === 'status') {
        // Switch to pie chart for status distribution
        ordersChart.destroy();
        const ctx = document.getElementById('ordersChart').getContext('2d');
        ordersChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ordersData.status.labels,
                datasets: [{
                    data: ordersData.status.data,
                    backgroundColor: ['#2ecc71', '#f39c12', '#e74c3c', '#3498db', '#9b59b6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        return;
    }
    
    if (ordersChart.config.type === 'doughnut') {
        ordersChart.destroy();
        initializeOrdersChart();
    }
    
    ordersChart.data.labels = ordersData[currentPeriod].labels;
    ordersChart.data.datasets[0].data = data;
    ordersChart.data.datasets[0].label = label;
    ordersChart.data.datasets[0].borderColor = color;
    ordersChart.data.datasets[0].backgroundColor = color + '20';
    ordersChart.update();
}

function updateCustomersChart() {
    customersChart.data.labels = customersData[currentPeriod].labels;
    customersChart.data.datasets[0].data = customersData[currentPeriod].registrations;
    customersChart.update();
}

function updateReportsChart(type) {
    const reportMetrics = reportsData[currentPeriod];
    let data, label, color;
    
    if (type === 'orders') {
        data = reportMetrics.map(item => item.orders);
        label = 'Orders';
        color = '#e74c3c';
    } else if (type === 'revenue') {
        data = reportMetrics.map(item => item.revenue);
        label = 'Revenue ($)';
        color = '#f39c12';
    } else if (type === 'customers') {
        data = reportMetrics.map(item => item.customers);
        label = 'New Customers';
        color = '#3498db';
    }
    
    reportsChart.data.labels = reportMetrics.map(item => currentPeriod === 'monthly' ? item.month : item.week);
    reportsChart.data.datasets[0].data = data;
    reportsChart.data.datasets[0].label = label;
    reportsChart.data.datasets[0].borderColor = color;
    reportsChart.data.datasets[0].backgroundColor = color + '20';
    reportsChart.update();
}

function updateChartData(chartType, dataType) {
    if (chartType === 'orders') {
        updateOrdersChart(dataType);
    } else if (chartType === 'reports') {
        updateReportsChart(dataType);
    }
}
</script>
@endsection