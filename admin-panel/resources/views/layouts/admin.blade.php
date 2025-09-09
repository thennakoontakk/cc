<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .sidebar-header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #fff;
        }
        
        .sidebar-menu .has-submenu > a::after {
            content: '▼';
            float: right;
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }
        
        .sidebar-menu .has-submenu.open > a::after {
            transform: rotate(180deg);
        }
        
        .sidebar-menu .submenu {
            padding-left: 20px;
            display: none;
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-menu .has-submenu.open .submenu {
            display: block;
        }
        
        .sidebar-menu .submenu a {
            padding-left: 3rem;
            font-size: 0.9rem;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 0;
        }
        
        /* Top Navigation */
        .top-nav {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .top-nav h1 {
            color: #2c3e50;
            font-size: 1.5rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        /* Content Area */
        .content {
            padding: 2rem;
        }
        
        /* Alert Styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .top-nav {
                flex-direction: column;
                gap: 1rem;
            }
        }
        
        @yield('styles')
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>CRAFTERS' CORNER</h2>
                <p>Admin Panel</p>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a></li>
                <li class="has-submenu {{ request()->routeIs('admin.products.*') ? 'open' : '' }}">
                    <a href="#" onclick="toggleSubmenu(this)">📦 Products</a>
                    <ul class="submenu">
                        <li><a href="{{ route('admin.products.create') }}">Create Product</a></li>
                        <li><a href="{{ route('admin.products.index') }}">Product List</a></li>
                    </ul>
                </li>
                <li class="has-submenu {{ request()->routeIs('admin.orders.*') ? 'open' : '' }}">
                    <a href="#" onclick="toggleSubmenu(this)">📋 Orders</a>
                    <ul class="submenu">
                        <li><a href="{{ route('admin.orders.index') }}">Order Management</a></li>
                    </ul>
                </li>
                <li class="has-submenu {{ request()->routeIs('admin.cart.*') ? 'open' : '' }}">
                    <a href="#" onclick="toggleSubmenu(this)">🛒 Cart Management</a>
                    <ul class="submenu">
                        <li><a href="{{ route('admin.cart.index') }}">Cart Management</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#" onclick="toggleSubmenu(this)">📂 Categories</a>
                    <ul class="submenu">
                        <li><a href="#">Create Category</a></li>
                        <li><a href="#">Category List</a></li>
                    </ul>
                </li>
                <li class="has-submenu {{ request()->routeIs('admin.users.*') ? 'open' : '' }}">
                    <a href="#" onclick="toggleSubmenu(this)">👥 Users</a>
                    <ul class="submenu">
                        <li><a href="{{ route('admin.users.index') }}">User List</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <div class="top-nav">
                <h1>@yield('page-title', 'Admin Panel')</h1>
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <span>Admin</span>
                    <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>
            
            <!-- Content -->
            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <script>
        function toggleSubmenu(element) {
            const parent = element.parentElement;
            parent.classList.toggle('open');
        }
        
        // Auto-open active submenus
        document.addEventListener('DOMContentLoaded', function() {
            const activeSubmenus = document.querySelectorAll('.has-submenu.open');
            activeSubmenus.forEach(submenu => {
                submenu.classList.add('open');
            });
        });
        
        @yield('scripts')
    </script>
</body>
</html>