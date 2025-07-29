<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supplier') {
    // Redirect if not logged in or not supplier
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Dashboard - Payment Integration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'fresh-green': '#10B981',
                        'deep-green': '#059669',
                        'light-green': '#34D399',
                        'mint-green': '#6EE7B7',
                        'forest-green': '#047857',
                        'sage-green': '#A7F3D0',
                    },
                    fontFamily: {
                        'sans': ['system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .page-section {
            display: none;
        }
        .page-section.active {
            display: block;
        }
        .nav-item.active {
            background-color: #059669;
        }
        .payment-status-paid {
            background-color: #10B981;
            color: white;
        }
        .payment-status-cod {
            background-color: #F59E0B;
            color: white;
        }
        .payment-status-pending {
            background-color: #EF4444;
            color: white;
        }
        .payment-status-failed {
            background-color: #6B7280;
            color: white;
        }
        .modal {
            display: none;
        }
        .modal.active {
            display: flex;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="bg-fresh-green w-64 min-h-screen shadow-lg fixed lg:relative z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
            <div class="p-6">
                <h2 class="text-white text-2xl font-bold mb-8">Supplier Panel</h2>
                <nav class="space-y-2">
                    <a href="#" class="nav-item flex items-center space-x-3 text-white hover:bg-deep-green p-3 rounded-lg transition-colors duration-200 active" data-page="dashboard">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="product_display.php" class="flex items-center space-x-3 text-white hover:bg-deep-green p-3 rounded-lg transition-colors duration-200">
    <i class="fas fa-box text-white-600"></i>
   <span class="font-medium">Products</span>
</a>

                    <a href="supplier_orders.php" class="nav-item flex items-center space-x-3 text-white hover:bg-deep-green p-3 rounded-lg transition-colors duration-200" data-page="orders">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span class="font-medium">Orders</span>
                    </a>
                    <a href="#" class="nav-item flex items-center space-x-3 text-white hover:bg-deep-green p-3 rounded-lg transition-colors duration-200" data-page="payments">
                        <i class="fas fa-credit-card w-5"></i>
                        <span class="font-medium">Payments</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 text-white hover:bg-red-600 p-3 rounded-lg transition-colors duration-200 mt-8" onclick="handleLogout()">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="font-medium">Logout</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Mobile menu button -->
        <div class="lg:hidden fixed top-4 left-4 z-40">
            <button id="menu-toggle" class="bg-fresh-green text-white p-2 rounded-lg shadow-lg">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-0">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 id="page-title" class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
                        <p id="page-subtitle" class="text-gray-600 mt-1">Welcome to your supplier dashboard</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                           <span>Welcome back, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>

                        </div>
                        <div class="w-8 h-8 bg-fresh-green rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-bold">
    <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
</span>

                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="p-6">
                <!-- Dashboard Page -->
                <div id="dashboard-page" class="page-section active">
                    <div class="space-y-8">
                        <!-- Enhanced Stats Cards with Payment Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-2">₹2,45,680</p>
                                        <p class="text-sm mt-2 text-green-600">+12.5% from last month</p>
                                    </div>
                                    <div class="bg-green-500 p-3 rounded-lg">
                                        <i class="fas fa-rupee-sign text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Paid Orders</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-2">1,156</p>
                                        <p class="text-sm mt-2 text-green-600">+8.2% from last month</p>
                                    </div>
                                    <div class="bg-blue-500 p-3 rounded-lg">
                                        <i class="fas fa-check-circle text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">COD Pending</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-2">₹45,200</p>
                                        <p class="text-sm mt-2 text-yellow-600">23 orders pending</p>
                                    </div>
                                    <div class="bg-yellow-500 p-3 rounded-lg">
                                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Failed Payments</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-2">12</p>
                                        <p class="text-sm mt-2 text-red-600">Need attention</p>
                                    </div>
                                    <div class="bg-red-500 p-3 rounded-lg">
                                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary Cards -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="bg-white rounded-xl shadow-md p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Methods</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-credit-card text-blue-500"></i>
                                            <span class="text-gray-700">Online Payments</span>
                                        </div>
                                        <span class="font-semibold text-gray-800">₹1,85,400</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-money-bill-wave text-yellow-500"></i>
                                            <span class="text-gray-700">Cash on Delivery</span>
                                        </div>
                                        <span class="font-semibold text-gray-800">₹45,200</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-university text-green-500"></i>
                                            <span class="text-gray-700">Bank Transfer</span>
                                        </div>
                                        <span class="font-semibold text-gray-800">₹15,080</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Payments -->
                            <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Payments</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">#ORD-001 - ABC Restaurant</p>
                                                <p class="text-sm text-gray-600">Online Payment • 2 hours ago</p>
                                            </div>
                                        </div>
                                        <span class="font-semibold text-green-600">₹1,200</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-clock text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">#ORD-002 - XYZ Cafe</p>
                                                <p class="text-sm text-gray-600">COD • Delivered, awaiting collection</p>
                                            </div>
                                        </div>
                                        <span class="font-semibold text-yellow-600">₹850</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-times text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">#ORD-003 - Food Corner</p>
                                                <p class="text-sm text-gray-600">Payment Failed • Needs retry</p>
                                            </div>
                                        </div>
                                        <span class="font-semibold text-red-600">₹2,400</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Recent Orders Table with Payment Status -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Orders with Payment Status</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Order ID</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Customer</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Amount</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Payment Status</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Order Status</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium text-gray-800">#ORD-001</td>
                                            <td class="py-3 px-4 text-gray-600">ABC Restaurant</td>
                                            <td class="py-3 px-4 font-semibold text-fresh-green">₹1,200</td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>Paid Online
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Processing</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <button class="text-blue-500 hover:text-blue-700 mr-2" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium text-gray-800">#ORD-002</td>
                                            <td class="py-3 px-4 text-gray-600">XYZ Cafe</td>
                                            <td class="py-3 px-4 font-semibold text-fresh-green">₹850</td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-money-bill-wave mr-1"></i>COD Pending
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Delivered</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <button onclick="markCODCollected('ORD-002')" class="text-green-500 hover:text-green-700 mr-2" title="Mark COD Collected">
                                                    <i class="fas fa-hand-holding-usd"></i>
                                                </button>
                                                <button class="text-blue-500 hover:text-blue-700" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium text-gray-800">#ORD-003</td>
                                            <td class="py-3 px-4 text-gray-600">Food Corner</td>
                                            <td class="py-3 px-4 font-semibold text-fresh-green">₹2,400</td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Payment Failed
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">On Hold</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <button onclick="retryPayment('ORD-003')" class="text-orange-500 hover:text-orange-700 mr-2" title="Retry Payment">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                                <button class="text-blue-500 hover:text-blue-700" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Page -->
                <div id="products-page" class="page-section">
                    <div class="space-y-6">
                        <!-- Product Controls -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                                    <div class="relative">
                                        <input type="text" id="product-search" placeholder="Search products..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <select id="category-filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                        <option value="">All Categories</option>
                                        <option value="vegetables">Vegetables</option>
                                        <option value="fruits">Fruits</option>
                                        <option value="dairy">Dairy</option>
                                        <option value="grains">Grains</option>
                                        <option value="spices">Spices</option>
                                    </select>
                                </div>
                                <a href="#" class="flex items-center gap-2 text-sm px-4 py-2 hover:bg-green-100 rounded">
                                <i class="fas fa-plus-circle text-green-600"></i>
                                    Add Product
                                </a>

                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div id="products-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <!-- Products will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Orders Page -->
                <div id="orders-page" class="page-section">
                    <div class="space-y-6">
                        <!-- Order Filters -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                                    <div class="relative">
                                        <input type="text" id="order-search" placeholder="Search orders..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <select id="order-status-filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                        <option value="">All Order Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    <select id="payment-status-filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                        <option value="">All Payment Status</option>
                                        <option value="paid">Paid Online</option>
                                        <option value="cod_collected">COD Collected</option>
                                        <option value="cod_pending">COD Pending</option>
                                        <option value="failed">Payment Failed</option>
                                    </select>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="exportOrders()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center space-x-2">
                                        <i class="fas fa-download"></i>
                                        <span>Export</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div class="bg-white rounded-xl shadow-md overflow-hidden">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-shopping-cart text-fresh-green mr-2"></i>
                                    All Orders
                                </h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600">Order ID</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600">Customer</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600">Product</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600">Quantity</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600">Amount</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600">Payment Status</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600">Order Status</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600">Date</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orders-table">
                                        <!-- Orders will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Page -->
                <div id="payments-page" class="page-section">
                    <div class="space-y-6">
                        <!-- Payment Overview Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-md p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-100">Total Collected</p>
                                        <p class="text-2xl font-bold mt-2">₹2,45,680</p>
                                    </div>
                                    <i class="fas fa-wallet text-3xl text-green-200"></i>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-md p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-yellow-100">COD Pending</p>
                                        <p class="text-2xl font-bold mt-2">₹45,200</p>
                                    </div>
                                    <i class="fas fa-hourglass-half text-3xl text-yellow-200"></i>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-md p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-100">Online Payments</p>
                                        <p class="text-2xl font-bold mt-2">₹1,85,400</p>
                                    </div>
                                    <i class="fas fa-credit-card text-3xl text-blue-200"></i>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-md p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-red-100">Failed Payments</p>
                                        <p class="text-2xl font-bold mt-2">₹15,080</p>
                                    </div>
                                    <i class="fas fa-times-circle text-3xl text-red-200"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Filters -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                                    <div class="relative">
                                        <input type="text" placeholder="Search payments..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                        <option>All Payment Status</option>
                                        <option>Paid Online</option>
                                        <option>COD Pending</option>
                                        <option>Payment Failed</option>
                                        <option>Refunded</option>
                                    </select>
                                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                        <option>All Methods</option>
                                        <option>Credit/Debit Card</option>
                                        <option>UPI</option>
                                        <option>Net Banking</option>
                                        <option>Cash on Delivery</option>
                                    </select>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="bg-fresh-green text-white px-4 py-2 rounded-lg hover:bg-deep-green transition-colors duration-200">
                                        <i class="fas fa-download mr-2"></i>Export
                                    </button>
                                    <button onclick="openPaymentTestModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-200">
                                        <i class="fas fa-credit-card mr-2"></i>Test Payment
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Payment List -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Transactions</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Transaction ID</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Order ID</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Customer</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Amount</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Method</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Status</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Date</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-600">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-mono text-sm text-gray-800">pay_123456789</td>
                                            <td class="py-3 px-4 font-medium text-gray-800">#ORD-001</td>
                                            <td class="py-3 px-4 text-gray-600">ABC Restaurant</td>
                                            <td class="py-3 px-4 font-semibold text-fresh-green">₹1,200</td>
                                            <td class="py-3 px-4">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-credit-card text-blue-500"></i>
                                                    <span class="text-sm text-gray-600">Card</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>Success
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-gray-600">2024-01-15 10:30</td>
                                            <td class="py-3 px-4">
                                                <button class="text-blue-500 hover:text-blue-700 mr-2" title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <button class="text-green-500 hover:text-green-700" title="Download Invoice">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-mono text-sm text-gray-800">cod_987654321</td>
                                            <td class="py-3 px-4 font-medium text-gray-800">#ORD-002</td>
                                            <td class="py-3 px-4 text-gray-600">XYZ Cafe</td>
                                            <td class="py-3 px-4 font-semibold text-fresh-green">₹850</td>
                                            <td class="py-3 px-4">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-money-bill-wave text-yellow-500"></i>
                                                    <span class="text-sm text-gray-600">COD</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-gray-600">2024-01-14 15:45</td>
                                            <td class="py-3 px-4">
                                                <button onclick="markCODCollected('ORD-002')" class="text-green-500 hover:text-green-700 mr-2" title="Mark as Collected">
                                                    <i class="fas fa-hand-holding-usd"></i>
                                                </button>
                                                <button class="text-blue-500 hover:text-blue-700" title="Contact Customer">
                                                    <i class="fas fa-phone"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-mono text-sm text-gray-800">pay_failed_456</td>
                                            <td class="py-3 px-4 font-medium text-gray-800">#ORD-003</td>
                                            <td class="py-3 px-4 text-gray-600">Food Corner</td>
                                            <td class="py-3 px-4 font-semibold text-fresh-green">₹2,400</td>
                                            <td class="py-3 px-4">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-university text-purple-500"></i>
                                                    <span class="text-sm text-gray-600">Net Banking</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-times mr-1"></i>Failed
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-gray-600">2024-01-13 09:20</td>
                                            <td class="py-3 px-4">
                                                <button onclick="retryPayment('ORD-003')" class="text-orange-500 hover:text-orange-700 mr-2" title="Retry Payment">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                                <button class="text-blue-500 hover:text-blue-700" title="Contact Customer">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    

    <!-- Payment Test Modal -->
    <div id="payment-test-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <!-- Test Mode Banner -->
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-2 rounded-lg mb-4 text-center text-sm">
                🧪 TEST MODE - No real money will be charged
            </div>
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Test Payment Integration</h2>
                <button onclick="closePaymentTestModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <!-- Sample Order -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Sample Order</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fresh Vegetables (2x)</span>
                        <span class="font-medium">₹240</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Organic Fruits (1x)</span>
                        <span class="font-medium">₹180</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery Charges</span>
                        <span class="font-medium">₹50</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">GST (5%)</span>
                        <span class="font-medium">₹24</span>
                    </div>
                </div>
                <hr class="my-3">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800">Total Amount</span>
                    <span class="text-xl font-bold text-fresh-green">₹494</span>
                </div>
            </div>
            <!-- Payment Button -->
            <button id="razorpay-payment-button" onclick="initializeTestPayment()" class="w-full bg-fresh-green hover:bg-deep-green text-white font-semibold py-4 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                <i class="fas fa-lock"></i>
                <span>Test Pay ₹494</span>
            </button>
            <!-- Test Card Info -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h4 class="font-semibold text-blue-800 mb-2">Test Card Details:</h4>
                <div class="text-sm text-blue-700 space-y-1">
                    <p><strong>Card:</strong> 4111 1111 1111 1111</p>
                    <p><strong>Expiry:</strong> Any future date</p>
                    <p><strong>CVV:</strong> Any 3 digits</p>
                    <p><strong>Name:</strong> Any name</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden hidden"></div>

    <script>
        // Sample Data Storage
        let products = [
            {
                id: 1,
                name: 'Fresh Tomatoes',
                category: 'vegetables',
                price: 40,
                stock: 100,
                unit: 'kg',
                description: 'Fresh red tomatoes from local farms, perfect for cooking',
                image: 'https://images.unsplash.com/photo-1546470427-e5ac89c8ba3a?w=300&h=200&fit=crop'
            },
            {
                id: 2,
                name: 'Organic Apples',
                category: 'fruits',
                price: 120,
                stock: 50,
                unit: 'kg',
                description: 'Organic red apples, sweet and crispy, pesticide-free',
                image: 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=300&h=200&fit=crop'
            },
            {
                id: 3,
                name: 'Fresh Milk',
                category: 'dairy',
                price: 60,
                stock: 30,
                unit: 'l',
                description: 'Fresh cow milk from local dairy, rich in nutrients',
                image: 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=300&h=200&fit=crop'
            },
            {
                id: 4,
                name: 'Basmati Rice',
                category: 'grains',
                price: 80,
                stock: 200,
                unit: 'kg',
                description: 'Premium quality basmati rice, aromatic and long grain',
                image: 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=300&h=200&fit=crop'
            },
            {
                id: 5,
                name: 'Turmeric Powder',
                category: 'spices',
                price: 150,
                stock: 25,
                unit: 'kg',
                description: 'Pure turmeric powder, ground from fresh turmeric roots',
                image: 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=300&h=200&fit=crop'
            },
            {
                id: 6,
                name: 'Fresh Spinach',
                category: 'vegetables',
                price: 30,
                stock: 80,
                unit: 'kg',
                description: 'Fresh green spinach leaves, rich in iron and vitamins',
                image: 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?w=300&h=200&fit=crop'
            }
        ];

        let orders = [
            {
                id: 'ORD-001',
                customer: 'ABC Restaurant',
                customerPhone: '+91 9876543210',
                product: 'Fresh Tomatoes',
                quantity: 10,
                amount: 400,
                paymentStatus: 'paid',
                paymentMethod: 'online',
                transactionId: 'pay_123456789',
                orderStatus: 'completed',
                date: '2024-01-15',
                time: '10:30 AM'
            },
            {
                id: 'ORD-002',
                customer: 'XYZ Cafe',
                customerPhone: '+91 9876543211',
                product: 'Organic Apples',
                quantity: 5,
                amount: 600,
                paymentStatus: 'cod_pending',
                paymentMethod: 'cod',
                transactionId: 'cod_987654321',
                orderStatus: 'pending',
                date: '2024-01-14',
                time: '03:45 PM'
            },
            {
                id: 'ORD-003',
                customer: 'Food Corner',
                customerPhone: '+91 9876543212',
                product: 'Fresh Milk',
                quantity: 20,
                amount: 1200,
                paymentStatus: 'failed',
                paymentMethod: 'online',
                transactionId: 'pay_failed_456',
                orderStatus: 'pending',
                date: '2024-01-13',
                time: '09:20 AM'
            },
            {
                id: 'ORD-004',
                customer: 'Green Bistro',
                customerPhone: '+91 9876543213',
                product: 'Basmati Rice',
                quantity: 8,
                amount: 640,
                paymentStatus: 'paid',
                paymentMethod: 'online',
                transactionId: 'pay_789123456',
                orderStatus: 'completed',
                date: '2024-01-12',
                time: '02:15 PM'
            },
            {
                id: 'ORD-005',
                customer: 'Spice Kitchen',
                customerPhone: '+91 9876543214',
                product: 'Turmeric Powder',
                quantity: 3,
                amount: 450,
                paymentStatus: 'cod_collected',
                paymentMethod: 'cod',
                transactionId: 'cod_collected_789',
                orderStatus: 'completed',
                date: '2024-01-11',
                time: '11:00 AM'
            },
            {
                id: 'ORD-006',
                customer: 'Fresh Market',
                customerPhone: '+91 9876543215',
                product: 'Fresh Spinach',
                quantity: 15,
                amount: 450,
                paymentStatus: 'cod_pending',
                paymentMethod: 'cod',
                transactionId: 'cod_pending_123',
                orderStatus: 'pending',
                date: '2024-01-10',
                time: '04:30 PM'
            }
        ];

    
// Mobile menu toggle
const menuToggle = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebar-overlay');

menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
    sidebarOverlay.classList.toggle('hidden');
});

sidebarOverlay.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
});

// Navigation handling
const navItems = document.querySelectorAll('.nav-item');
const pageTitle = document.getElementById('page-title');
const pageSubtitle = document.getElementById('page-subtitle');

const pageConfig = {
    dashboard: {
        title: 'Dashboard Overview',
        subtitle: 'Welcome to your supplier dashboard'
    },
    products: {
        title: 'Products Management',
        subtitle: 'Manage your product inventory and pricing'
    },
    orders: {
        title: 'Orders Management',
        subtitle: 'Track and manage all customer orders'
    },
    payments: {
        title: 'Payment Management',
        subtitle: 'Track payments, COD collections, and financial overview'
    }
};

navItems.forEach(item => {
    item.addEventListener('click', () => {
        const page = item.dataset.page;
        if (pageConfig[page]) {
            pageTitle.textContent = pageConfig[page].title;
            pageSubtitle.textContent = pageConfig[page].subtitle;
        }
    });
});

// ===========================
// 💳 Razorpay Payment Section
// ===========================

// This function starts payment. You can call it on button click.

const RAZORPAY_CONFIG = {
    key: 'rzp_test_cCpIuhCcDNSsf2' // ✅ Your Razorpay Test Key
};

async function startPayment(amount) {
    try {
        // Call your backend to create Razorpay order
        const response = await fetch('/createOrder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ amount: amount })
        });

        const data = await response.json();

        if (!data.orderId) {
            alert("Error: Order ID not received");
            return;
        }

        const options = {
            key: RAZORPAY_CONFIG.key,
            amount: amount * 100, // amount in paise
            currency: "INR",
            name: "Your Company Name",
            description: "Product Purchase",
            order_id: data.orderId, // <-- Must come from backend
            handler: function (response) {
                alert("✅ Payment Successful!\nPayment ID: " + response.razorpay_payment_id);
                // Optional: Send to server for confirmation
            },
            prefill: {
                name: "Kavita Kharade",
                email: "kavitakharade22@gmail.com",
                contact: "9876543210"
            },
            theme: {
                color: "#3399cc"
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();

    } catch (err) {
        console.error("Payment Error:", err);
        alert("Payment failed. Please try again.");
    }
}


        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remove active class from all nav items
                navItems.forEach(nav => nav.classList.remove('active'));
                
                // Add active class to clicked item
                item.classList.add('active');
                
                // Hide all pages
                document.querySelectorAll('.page-section').forEach(page => {
                    page.classList.remove('active')
                });
                
                // Show selected page
                const pageName = item.getAttribute('data-page');
                const targetPage = document.getElementById(pageName + '-page');
                if (targetPage) {
                    targetPage.classList.add('active');
                }
                
                // Update header
                if (pageConfig[pageName]) {
                    pageTitle.textContent = pageConfig[pageName].title;
                    pageSubtitle.textContent = pageConfig[pageName].subtitle;
                }
                
                // Load page data
                if (pageName === 'products') {
                    loadProducts();
                } else if (pageName === 'orders') {
                    loadOrders();
                }
                
                // Close mobile menu
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                }
            });
        });

        // Load Products
        function loadProducts() {
            const productsGrid = document.getElementById('products-grid');
            productsGrid.innerHTML = '';
            
            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 border border-gray-100';
                productCard.innerHTML = `
                    <div class="relative">
                        <img src="${product.image}" alt="${product.name}" class="w-full h-48 object-cover">
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 bg-white bg-opacity-90 text-xs font-medium text-gray-700 rounded-full">
                                ${product.category}
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">${product.name}</h3>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">${product.description}</p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xl font-bold text-fresh-green">₹${product.price}/${product.unit}</span>
                            <span class="text-sm px-2 py-1 ${product.stock > 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} rounded-full">
                                Stock: ${product.stock} ${product.unit}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-2">
                                <button onclick="editProduct(${product.id})" class="text-blue-500 hover:text-blue-700 p-2 hover:bg-blue-50 rounded-lg transition-colors duration-200" title="Edit Product">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteProduct(${product.id})" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Delete Product">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <button onclick="viewProductDetails(${product.id})" class="text-fresh-green hover:text-deep-green text-sm font-medium">
                                View Details
                            </button>
                        </div>
                    </div>
                `;
                productsGrid.appendChild(productCard);
            });
        }

        // Load Orders
        function loadOrders() {
            const ordersTable = document.getElementById('orders-table');
            ordersTable.innerHTML = '';
            
            orders.forEach(order => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200';
                
                const statusClass = order.orderStatus === 'completed' ? 'bg-green-100 text-green-800' : 
                                  order.orderStatus === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800';
                
                const trackButton = order.orderStatus === 'pending' ? 
                    `<button onclick="trackOrder('${order.id}')" class="bg-fresh-green text-white px-3 py-1 rounded-lg hover:bg-deep-green transition-colors duration-200 text-sm">
                        <i class="fas fa-map-marker-alt mr-1"></i>Track
                    </button>` :
                    `<button class="bg-gray-300 text-gray-500 px-3 py-1 rounded-lg cursor-not-allowed text-sm" disabled>
                        -
                    </button>`;

                row.innerHTML = `
                    <td class="py-4 px-6">
                        <div class="font-medium text-gray-800">${order.id}</div>
                        <div class="text-sm text-gray-500">${order.time}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="font-medium text-gray-800">${order.customer}</div>
                        <div class="text-sm text-gray-500">${order.customerPhone}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="font-medium text-gray-800">${order.product}</div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="font-semibold text-gray-800">${order.quantity}</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="font-semibold text-lg text-gray-800">₹${order.amount}</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex flex-col space-y-1">
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${getPaymentStatusClass(order.paymentStatus)} inline-flex items-center">
                                <i class="fas ${getPaymentStatusIcon(order.paymentStatus)} mr-1"></i>
                                ${getPaymentStatusText(order.paymentStatus)}
                            </span>
                            <span class="text-xs text-gray-500">${order.paymentMethod.toUpperCase()}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                            ${order.orderStatus}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-gray-600">${order.date}</td>
                    <td class="py-4 px-6">
                        ${trackButton}
                    </td>
                `;
                ordersTable.appendChild(row);
            });
        }

        // Track order function
        function trackOrder(orderId) {
            alert(`Tracking order ${orderId}. This will redirect to tracking page or show tracking details.`);
            // Here you can implement actual tracking functionality
            // For example, redirect to a tracking page or show a tracking modal
        }

        // Helper Functions for Payment Status
        function getPaymentStatusClass(status) {
            switch(status) {
                case 'paid': return 'bg-green-100 text-green-800';
                case 'cod_collected': return 'bg-green-100 text-green-800';
                case 'cod_pending': return 'bg-yellow-100 text-yellow-800';
                case 'failed': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function getPaymentStatusText(status) {
            switch(status) {
                case 'paid': return 'Paid Online';
                case 'cod_collected': return 'COD Collected';
                case 'cod_pending': return 'COD Pending';
                case 'failed': return 'Payment Failed';
                default: return status;
            }
        }

        function getPaymentStatusIcon(status) {
            switch(status) {
                case 'paid': return 'fa-check-circle';
                case 'cod_collected': return 'fa-check-circle';
                case 'cod_pending': return 'fa-clock';
                case 'failed': return 'fa-times-circle';
                default: return 'fa-question-circle';
            }
        }

        // Product Management Functions
        function openAddProductModal() {
            document.getElementById('add-product-modal').classList.remove('hidden');
        }

        function closeAddProductModal() {
            document.getElementById('add-product-modal').classList.add('hidden');
            document.getElementById('add-product-form').reset();
        }

        // Add Product Form Handler
        document.getElementById('add-product-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newProduct = {
                id: Date.now(),
                name: document.getElementById('product-name').value,
                category: document.getElementById('product-category').value,
                price: parseFloat(document.getElementById('product-price').value),
                stock: parseInt(document.getElementById('product-stock').value),
                unit: document.getElementById('product-unit').value,
                description: document.getElementById('product-description').value,
                image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=300&h=200&fit=crop'
            };
            
            products.push(newProduct);
            loadProducts();
            closeAddProductModal();
            
            alert('Product added successfully!');
        });

        // Edit Product Function
        function editProduct(productId) {
            const product = products.find(p => p.id === productId);
            if (product) {
                const newName = prompt('Enter new product name:', product.name);
                const newPrice = prompt('Enter new price:', product.price);
                const newStock = prompt('Enter new stock:', product.stock);
                
                if (newName && newPrice && newStock) {
                    product.name = newName;
                    product.price = parseFloat(newPrice);
                    product.stock = parseInt(newStock);
                    loadProducts();
                    alert('Product updated successfully!');
                }
            }
        }

        // Delete Product Function
        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                products = products.filter(p => p.id !== productId);
                loadProducts();
                alert('Product deleted successfully!');
            }
        }

        // View Product Details
        function viewProductDetails(productId) {
            const product = products.find(p => p.id === productId);
            if (product) {
                alert(`Product Details:\n\nName: ${product.name}\nCategory: ${product.category}\nPrice: ₹${product.price}/${product.unit}\nStock: ${product.stock} ${product.unit}\nDescription: ${product.description}`);
            }
        }

        // Export Orders Function
        function exportOrders() {
            const csvContent = "data:text/csv;charset=utf-8," 
                + "Order ID,Customer,Phone,Product,Quantity,Amount,Payment Status,Payment Method,Order Status,Date,Time\n"
                + orders.map(order => 
                    `${order.id},"${order.customer}","${order.customerPhone}","${order.product}",${order.quantity},${order.amount},"${getPaymentStatusText(order.paymentStatus)}","${order.paymentMethod.toUpperCase()}","${order.orderStatus}","${order.date}","${order.time}"`
                ).join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `orders_export_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            alert('Orders exported successfully!');
        }

        // Payment Test Modal Functions
        function openPaymentTestModal() {
            document.getElementById('payment-test-modal').classList.remove('hidden');
        }

        function closePaymentTestModal() {
            document.getElementById('payment-test-modal').classList.add('hidden');
        }

        // Initialize Test Payment
        function initializeTestPayment() {
            const button = document.getElementById('razorpay-payment-button');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>';
            button.disabled = true;

            // Sample order data
            const orderData = {
                amount: 49400, // ₹494 in paise
                currency: 'INR',
                customer: {
                    name: 'Test Customer',
                    email: 'test@example.com',
                    contact: '9876543210'
                }
            };

            // Razorpay options
            const options = {
                key: RAZORPAY_CONFIG.keyId,
                amount: orderData.amount,
                currency: orderData.currency,
                name: 'Fresh Foods Marketplace',
                description: 'Test Order Payment',
                image: 'https://cdn-icons-png.flaticon.com/512/2331/2331970.png',
                order_id: 'order_test_' + Date.now(),
                handler: function(response) {
                    handlePaymentSuccess(response);
                },
                prefill: {
                    name: orderData.customer.name,
                    email: orderData.customer.email,
                    contact: orderData.customer.contact
                },
                theme: {
                    color: '#10B981'
                },
                modal: {
                    ondismiss: function() {
                        resetPaymentButton();
                    }
                }
            };

            const rzp = new Razorpay(options);
            
            rzp.on('payment.failed', function(response) {
                console.error('Payment failed:', response.error);
                alert('Payment failed: ' + response.error.description);
                resetPaymentButton();
            });

            rzp.open();
        }

        // Handle successful payment
        function handlePaymentSuccess(response) {
            console.log('Payment successful:', response);
            
            // Add to payment history (simulate)
            addPaymentToHistory({
                transactionId: response.razorpay_payment_id,
                orderId: '#ORD-TEST-' + Date.now().toString().slice(-3),
                customer: 'Test Customer',
                amount: 494,
                method: 'Card',
                status: 'Success',
                date: new Date().toLocaleString()
            });

            closePaymentTestModal();
            alert('Payment successful! Transaction ID: ' + response.razorpay_payment_id);
            resetPaymentButton();
        }

        // Reset payment button
        function resetPaymentButton() {
            const button = document.getElementById('razorpay-payment-button');
            button.innerHTML = '<i class="fas fa-lock"></i><span>Test Pay ₹494</span>';
            button.disabled = false;
        }

        // Add payment to history (simulate)
        function addPaymentToHistory(payment) {
            // In a real app, this would update the database
            console.log('New payment added:', payment);
        }

        // COD Collection Functions
        function markCODCollected(orderId) {
            if (confirm('Mark this COD order as collected?')) {
                // Update UI to show collected status
                alert(`COD for ${orderId} marked as collected!`);
                // In real app, update database and refresh UI
            }
        }

        // Retry Payment Function
        function retryPayment(orderId) {
            if (confirm(`Send payment retry link to customer for ${orderId}?`)) {
                alert(`Payment retry link sent to customer for ${orderId}!`);
                // In real app, send email/SMS with payment link
            }
        }

        function handleLogout() {
            if (confirm('Are you sure you want to logout? Any unsaved changes will be lost.')) {
                alert('Logged out successfully!');
                // In a real application, you would redirect to login page
                setTimeout(() => {
                    alert('Redirecting to login page...');
                    // window.location.href = '/login.html';
                }, 1000);
            }
        }

        // Initialize Dashboard on Load
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts(); // Load products by default
        });

        // Close sidebar when clicking on a link (mobile)
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                }
            });
        });

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            const modals = document.querySelectorAll('.modal.active');
            modals.forEach(modal => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>