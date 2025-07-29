<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <a href="#" class="nav-item flex items-center space-x-3 text-white hover:bg-deep-green p-3 rounded-lg transition-colors duration-200" data-page="products">
                        <i class="fas fa-box w-5"></i>
                        <span class="font-medium">Products</span>
                    </a>
                    <a href="#" class="nav-item flex items-center space-x-3 text-white hover:bg-deep-green p-3 rounded-lg transition-colors duration-200" data-page="orders">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span class="font-medium">Orders</span>
                    </a>
                    <a href="#" class="nav-item flex items-center space-x-3 text-white hover:bg-deep-green p-3 rounded-lg transition-colors duration-200" data-page="tracking">
                        <i class="fas fa-truck w-5"></i>
                        <span class="font-medium">Tracking</span>
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
                            <span>Welcome back, <strong>Supplier Name</strong></span>
                        </div>
                        <div class="w-8 h-8 bg-fresh-green rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="p-6">
                <!-- Dashboard Page -->
                <div id="dashboard-page" class="page-section active">
                    <div class="space-y-8">
                        <!-- Stats Cards -->
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
                                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-2">1,234</p>
                                        <p class="text-sm mt-2 text-green-600">+8.2% from last month</p>
                                    </div>
                                    <div class="bg-blue-500 p-3 rounded-lg">
                                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Pending Orders</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-2">23</p>
                                        <p class="text-sm mt-2 text-red-600">-5.1% from last month</p>
                                    </div>
                                    <div class="bg-yellow-500 p-3 rounded-lg">
                                        <i class="fas fa-clock text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Products Listed</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-2">156</p>
                                        <p class="text-sm mt-2 text-green-600">+3.7% from last month</p>
                                    </div>
                                    <div class="bg-purple-500 p-3 rounded-lg">
                                        <i class="fas fa-box text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts and Top Products -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Sales Chart -->
                            <div class="bg-white rounded-xl shadow-md p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sales Overview</h3>
                                <div class="h-64 flex items-end justify-between space-x-2">
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 65%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 45%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 78%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 52%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 89%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 67%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 43%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 91%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 76%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 58%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 82%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-fresh-green to-light-green rounded-t" style="height: 69%"></div>
                                </div>
                                <div class="flex justify-between mt-4 text-sm text-gray-600">
                                    <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span>
                                    <span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span>
                                </div>
                            </div>

                            <!-- Top Products -->
                            <div class="bg-white rounded-xl shadow-md p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Selling Products</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-fresh-green rounded-lg flex items-center justify-center">
                                                <i class="fas fa-box text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">Organic Vegetables</p>
                                                <p class="text-sm text-gray-600">234 units</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-800">₹45,680</p>
                                            <div class="flex items-center">
                                                <i class="fas fa-arrow-up text-green-500 text-xs mr-1"></i>
                                                <span class="text-xs text-green-500">+5.2%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-fresh-green rounded-lg flex items-center justify-center">
                                                <i class="fas fa-box text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">Fresh Fruits</p>
                                                <p class="text-sm text-gray-600">189 units</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-800">₹32,450</p>
                                            <div class="flex items-center">
                                                <i class="fas fa-arrow-up text-green-500 text-xs mr-1"></i>
                                                <span class="text-xs text-green-500">+3.1%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-fresh-green rounded-lg flex items-center justify-center">
                                                <i class="fas fa-box text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">Dairy Products</p>
                                                <p class="text-sm text-gray-600">156 units</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-800">₹28,900</p>
                                            <div class="flex items-center">
                                                <i class="fas fa-arrow-down text-red-500 text-xs mr-1"></i>
                                                <span class="text-xs text-red-500">-1.2%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php include "suppliers/view_orders.php" ?>

                <!-- Products Page -->
                <div id="products-page" class="page-section">
                    <div class="space-y-6">
                        <!-- Header Actions -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                                <div class="relative">
                                    <input type="text" placeholder="Search products..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                    <option>All Categories</option>
                                    <option>Vegetables</option>
                                    <option>Fruits</option>
                                    <option>Dairy</option>
                                    <option>Grains</option>
                                </select>
                            </div>
                            <button onclick="openAddProductModal()" class="bg-fresh-green text-white px-6 py-2 rounded-lg hover:bg-deep-green transition-colors duration-200 flex items-center space-x-2">
                                <i class="fas fa-plus"></i>
                                <span>Add Product</span>
                            </button>
                        </div>

                        <!-- Products Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                                <img src="/placeholder.svg?height=200&width=300" alt="Organic Vegetables" class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-gray-800">Organic Vegetables</h3>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">Vegetables</p>
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-lg font-bold text-fresh-green">₹1,200</span>
                                        <span class="text-sm text-gray-600">45 in stock</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="flex-1 bg-fresh-green text-white py-2 px-4 rounded-lg hover:bg-deep-green transition-colors duration-200 text-sm">
                                            <i class="fas fa-edit mr-2"></i>Edit
                                        </button>
                                        <button class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm">
                                            <i class="fas fa-trash mr-2"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                                <img src="/placeholder.svg?height=200&width=300" alt="Fresh Fruits" class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-gray-800">Fresh Fruits</h3>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">Fruits</p>
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-lg font-bold text-fresh-green">₹850</span>
                                        <span class="text-sm text-gray-600">32 in stock</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="flex-1 bg-fresh-green text-white py-2 px-4 rounded-lg hover:bg-deep-green transition-colors duration-200 text-sm">
                                            <i class="fas fa-edit mr-2"></i>Edit
                                        </button>
                                        <button class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm">
                                            <i class="fas fa-trash mr-2"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                                <img src="/placeholder.svg?height=200&width=300" alt="Dairy Products" class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-gray-800">Dairy Products</h3>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Low Stock</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">Dairy</p>
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-lg font-bold text-fresh-green">₹2,400</span>
                                        <span class="text-sm text-gray-600">18 in stock</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="flex-1 bg-fresh-green text-white py-2 px-4 rounded-lg hover:bg-deep-green transition-colors duration-200 text-sm">
                                            <i class="fas fa-edit mr-2"></i>Edit
                                        </button>
                                        <button class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm">
                                            <i class="fas fa-trash mr-2"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Page -->
                <div id="orders-page" class="page-section">
                    <div class="space-y-6">
                        <!-- Filters -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                                <div class="relative">
                                    <input type="text" placeholder="Search orders..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                    <option>All Status</option>
                                    <option>Processing</option>
                                    <option>Shipped</option>
                                    <option>Delivered</option>
                                    <option>Cancelled</option>
                                </select>
                            </div>
                            <button class="bg-fresh-green text-white px-4 py-2 rounded-lg hover:bg-deep-green transition-colors duration-200">
                                <i class="fas fa-download mr-2"></i>Export
                            </button>
                        </div>

                        <!-- Orders List -->
                        <div class="space-y-4">
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-4">
                                    <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800">#ORD-001</h3>
                                            <p class="text-gray-600">ABC Restaurant</p>
                                            <p class="text-sm text-gray-500">2024-01-15</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Processing</span>
                                        <span class="text-xl font-bold text-fresh-green">₹8,550</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2">Products</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">Organic Vegetables x 5</span>
                                                <span class="font-medium">₹6,000</span>
                                            </div>
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">Fresh Fruits x 3</span>
                                                <span class="font-medium">₹2,550</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2">Delivery Address</h4>
                                        <p class="text-sm text-gray-600 mb-4">123 Main St, City, State 12345</p>
                                        <div class="flex space-x-2">
                                            <select class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent text-sm">
                                                <option>Processing</option>
                                                <option>Shipped</option>
                                                <option>Delivered</option>
                                                <option>Cancelled</option>
                                            </select>
                                            <button class="px-4 py-2 bg-fresh-green text-white rounded-lg hover:bg-deep-green transition-colors duration-200 text-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-4">
                                    <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800">#ORD-002</h3>
                                            <p class="text-gray-600">XYZ Cafe</p>
                                            <p class="text-sm text-gray-500">2024-01-14</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Shipped</span>
                                        <span class="text-xl font-bold text-fresh-green">₹4,800</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2">Products</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">Dairy Products x 2</span>
                                                <span class="font-medium">₹4,800</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2">Delivery Address</h4>
                                        <p class="text-sm text-gray-600 mb-4">456 Oak Ave, City, State 67890</p>
                                        <div class="flex space-x-2">
                                            <select class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent text-sm">
                                                <option>Processing</option>
                                                <option selected>Shipped</option>
                                                <option>Delivered</option>
                                                <option>Cancelled</option>
                                            </select>
                                            <button class="px-4 py-2 bg-fresh-green text-white rounded-lg hover:bg-deep-green transition-colors duration-200 text-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tracking Page -->
                <div id="tracking-page" class="page-section">
                    <div class="space-y-6">
                        <!-- Search -->
                        <div class="flex justify-between items-center">
                            <div class="relative">
                                <input type="text" placeholder="Search by Order ID, Tracking Number, or Customer..." class="pl-10 pr-4 py-2 w-96 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Tracking Cards -->
                        <div class="space-y-6">
                            <div class="bg-white rounded-xl shadow-md p-6">
                                <div class="flex flex-col lg:flex-row justify-between items-start mb-6">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">#ORD-001</h3>
                                        <p class="text-gray-600">ABC Restaurant</p>
                                        <p class="text-sm text-gray-500">Tracking: TRK123456789</p>
                                    </div>
                                    <div class="mt-4 lg:mt-0 text-right">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">In Transit</span>
                                        <p class="text-sm text-gray-600 mt-2">Est. Delivery: 2024-01-18</p>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fas fa-map-marker-alt text-fresh-green"></i>
                                        <span class="font-medium text-gray-800">Current Location</span>
                                    </div>
                                    <p class="text-gray-600 ml-6">Distribution Center - Mumbai</p>
                                </div>

                                <div>
                                    <h4 class="font-medium text-gray-800 mb-4">Tracking Timeline</h4>
                                    <div class="space-y-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-4 h-4 rounded-full bg-fresh-green flex-shrink-0">
                                                <i class="fas fa-check text-white text-xs flex items-center justify-center w-full h-full"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-gray-800">Order Placed</span>
                                                    <span class="text-sm text-gray-600">2024-01-15 10:30 AM</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="w-4 h-4 rounded-full bg-fresh-green flex-shrink-0">
                                                <i class="fas fa-check text-white text-xs flex items-center justify-center w-full h-full"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-gray-800">Order Confirmed</span>
                                                    <span class="text-sm text-gray-600">2024-01-15 11:00 AM</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="w-4 h-4 rounded-full bg-fresh-green flex-shrink-0">
                                                <i class="fas fa-check text-white text-xs flex items-center justify-center w-full h-full"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-gray-800">Packed</span>
                                                    <span class="text-sm text-gray-600">2024-01-15 02:30 PM</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="w-4 h-4 rounded-full bg-fresh-green flex-shrink-0">
                                                <i class="fas fa-check text-white text-xs flex items-center justify-center w-full h-full"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-gray-800">In Transit</span>
                                                    <span class="text-sm text-gray-600">2024-01-17 08:00 AM</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="w-4 h-4 rounded-full bg-gray-300 flex-shrink-0"></div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-gray-500">Out for Delivery</span>
                                                    <span class="text-sm text-gray-400">Pending</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="w-4 h-4 rounded-full bg-gray-300 flex-shrink-0"></div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-gray-500">Delivered</span>
                                                    <span class="text-sm text-gray-400">Pending</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden hidden"></div>

    <!-- Add Product Modal -->
    <div id="add-product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Add New Product</h2>
                <button onclick="closeAddProductModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                    <input type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price (₹)</label>
                        <input type="number" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                        <input type="number" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fresh-green focus:border-transparent">
                        <option value="Vegetables">Vegetables</option>
                        <option value="Fruits">Fruits</option>
                        <option value="Dairy">Dairy</option>
                        <option value="Grains">Grains</option>
                        <option value="Spices">Spices</option>
                    </select>
                </div>

                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="closeAddProductModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-fresh-green text-white rounded-lg hover:bg-deep-green transition-colors duration-200">
                        Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
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

        // Navigation
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
                subtitle: 'Manage your product inventory'
            },
            orders: {
                title: 'Orders Management',
                subtitle: 'Track and manage all your orders'
            },
            tracking: {
                title: 'Order Tracking',
                subtitle: 'Track your shipments and deliveries'
            }
        };

        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remove active class from all nav items
                navItems.forEach(nav => nav.classList.remove('active'));
                
                // Add active class to clicked item
                item.classList.add('active');
                
                // Hide all pages
                document.querySelectorAll('.page-section').forEach(page => {
                    page.classList.remove('active');
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
                
                // Close mobile menu
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                }
            });
        });

        // Modal functions
        function openAddProductModal() {
            document.getElementById('add-product-modal').classList.remove('hidden');
        }

        function closeAddProductModal() {
            document.getElementById('add-product-modal').classList.add('hidden');
        }

        function handleLogout() {
            alert('Logout functionality would be implemented here');
        }

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
    </script>
</body>
</html>