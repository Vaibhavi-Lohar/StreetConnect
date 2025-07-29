<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$vendorLoggedIn = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'vendor';

include("db.php"); // if user info is stored in a DB
$userData = [];

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
}

// Handle testimonial submission
if (isset($_POST['submit_testimonial']) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO testimonials (user_id, comment) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $comment);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Failed to submit testimonial.');</script>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshSupply - Raw Materials for Street Food Vendors</title>
    <script type="text/javascript" src="https://cdn.weglot.com/weglot.min.js"></script>
<script>
    Weglot.initialize({
        api_key: 'wg_e574e169aed40962409ceb75f81574743'
    });
</script>
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
                        'poppins': ['Poppins', 'sans-serif'],
                        'nunito': ['Nunito', 'sans-serif'],
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'gentle-bounce': 'gentleBounce 4s ease-in-out infinite',
                        'subtle-float': 'subtleFloat 8s ease-in-out infinite',
                        'soft-glow': 'softGlow 3s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        gentleBounce: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-5px)' },
                        },
                        subtleFloat: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        softGlow: {
                            '0%': { boxShadow: '0 0 5px rgba(16, 185, 129, 0.3)' },
                            '100%': { boxShadow: '0 0 15px rgba(16, 185, 129, 0.5)' },
                        }
                    }
                }
            }
        }
        let isVendorLoggedIn = <?php echo (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'vendor') ? 'true' : 'false'; ?>;
        //user auth
        // ====== User Auth Display Logic ======
        document.addEventListener("DOMContentLoaded", () => {
            const authButtons = document.getElementById('authButtons');
            const userProfile = document.getElementById('userProfile');
            const userInitial = document.getElementById('userInitial');
            const userNameElement = document.getElementById('userName');

            const userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            const userName = "<?php echo isset($userData['name']) ? addslashes($userData['name']) : ''; ?>";

            if (userLoggedIn) {
                authButtons.classList.add('hidden');
                userProfile.classList.remove('hidden');

                if (userName) {
                    userInitial.innerText = userName.charAt(0).toUpperCase();
                    userNameElement.innerText = userName;
                }
            } else {
                authButtons.classList.remove('hidden');
                userProfile.classList.add('hidden');
            }
        });

        function logout() {
            window.location.href = "logout.php";
        }

        function showLogin() {
            window.location.href = "login.php";
        }

        function showRegister() {
            window.location.href = "login.php";
        }

        function checkVendorLogin() {
            if (!isVendorLoggedIn) {
                alert("Please login as a vendor to add products to cart.");
                return false;
            }
            return true;
        }
        function toggleCartPanel() {
            const panel = document.getElementById('cartPanel');
            panel.classList.toggle('hidden');
        }
        function fetchCartItems() {
            const cart = JSON.parse(localStorage.getItem('session_cart')) || [];
            const container = document.getElementById('cartItemsContainer');
            container.innerHTML = '';

            if (cart.length > 0) {
                cart.forEach((item, index) => {
                    container.innerHTML += `
                <div class="flex items-center justify-between border-b py-3">
                    <img src="${item.image}" alt="${item.name}" class="w-12 h-12 rounded object-cover">
                    <div class="flex-1 px-3">
                        <h4 class="font-semibold text-sm">${item.name}</h4>
                        <p class="text-xs text-gray-500">₹${item.price} / ${item.unit}</p>
                        <div class="flex items-center mt-1">
                            <button onclick="changeQuantity(${index}, 'decrease')" class="px-2 py-1 text-sm bg-gray-200 rounded-l">-</button>
                            <span class="px-3">${item.quantity}</span>
                            <button onclick="changeQuantity(${index}, 'increase')" class="px-2 py-1 text-sm bg-gray-200 rounded-r">+</button>
                        </div>
                    </div>
                    <button onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
                });
            } else {
                container.innerHTML = `<p class="text-gray-500 text-center">Your cart is empty.</p>`;
            }
        }





        function toggleCart() {
            const panel = document.getElementById('cartPanel');
            panel.classList.toggle('hidden');
            fetchCartItems(); // refresh cart every time it's opened
        }
        function updateQuantity(index, action) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `index=${index}&action=${action}`
            })
                .then(res => res.json())
                .then(() => fetchCartItems());
        }

        function removeFromCart(index) {
            updateQuantity(index, 'remove');
        }


        function showCheckoutModal() {
            const modal = document.getElementById('checkoutModal');
            modal.classList.remove('hidden');

            // Get cart from localStorage
            const cart = JSON.parse(localStorage.getItem('session_cart')) || [];
            const container = document.getElementById('checkoutCartItems');
            container.innerHTML = cart.map(item => `
        <div class="flex justify-between">
            <span>${item.name} x ${item.quantity}</span>
            <span>₹${(item.price * item.quantity).toFixed(2)}</span>
        </div>
    `).join('');

            // Fetch vendor info
            fetch('get_vendor_info.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('vendorDetails').innerHTML = `
                    <p><strong>Name:</strong> ${data.name}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Phone:</strong> ${data.contact}</p>
                `;
                    } else {
                        document.getElementById('vendorDetails').innerHTML = `<p class="text-red-600">Vendor details not found.</p>`;
                    }
                });
        }



        function closeCheckoutModal() {
            document.getElementById('checkoutModal').classList.add('hidden');
        }

        function addToCart(product) {
            let cart = JSON.parse(localStorage.getItem('session_cart')) || [];
            console.log("Clicked Add to Cart", product);
            const index = cart.findIndex(item => item.id === product.id);
            if (index > -1) {
                cart[index].quantity += 1;
            } else {
                cart.push({ ...product, quantity: 1 });
            }

            localStorage.setItem('session_cart', JSON.stringify(cart));
            alert(product.name + " added to cart");
            fetchCartItems(); // Refresh the cart panel
        }



        let selectedLocation = 'live'; // default

        function setLocationChoice(choice) {
            selectedLocation = choice;
            alert(`You selected ${choice === 'live' ? 'Live Location' : 'Profile Location'}`);
        }

        const cart = JSON.parse(localStorage.getItem('session_cart')) || [];
        function submitFinalOrder() {

            if (!cart.length) {
                alert("Cart is empty!");
                return;
            }

            if (selectedLocation === 'live') {
                navigator.geolocation.getCurrentPosition(pos => {
                    alert("Live location captured");

                    startPayment(cart[0].price, pos);


                }, () => {
                    alert("Failed to fetch live location");
                });
            } else {
                fetch('get_vendor_location.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert("Profile location fetched");
                            sendOrder(data.latitude, data.longitude, cart);
                        } else {
                            alert("No profile location found");
                        }
                    });
            }
        }

        async function sendOrder(lat, lng, cart) {
            console.log("sendOrder() called");

            const res = await fetch('place_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ lat, lng, cart })
            });

            const result = await res.json();
            if (result.success) {
                alert("Order placed successfully!");
                localStorage.removeItem('session_cart');
                document.getElementById('checkoutModal').classList.add('hidden');
                toggleCart();
            } else {
                alert("Order failed: " + result.message);
            }
        }



    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Nunito:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #10B981 0%, #059669 50%, #047857 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
        }

        .category-scroll {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .category-scroll::-webkit-scrollbar {
            display: none;
        }

        .baloo {
            font-family: 'Baloo Bhai 2', cursive;
        }

        .poppins {
            font-family: 'Poppins', sans-serif;
        }

        .hero-bg {
            background: linear-gradient(135deg, #FF6B35 0%, #DC2626 25%, #F59E0B 50%, #10B981 75%, #FB923C 100%);
            background-size: 200% 200%;
            animation: gentleGradientShift 15s ease infinite;
        }

        @keyframes gentleGradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .floating-ingredient {
            position: absolute;
            animation: subtleFloat 12s ease-in-out infinite;
            opacity: 0.4;
        }

        .floating-ingredient:nth-child(2) {
            animation-delay: -3s;
        }

        .floating-ingredient:nth-child(3) {
            animation-delay: -6s;
        }

        .floating-ingredient:nth-child(4) {
            animation-delay: -9s;
        }


        .gradient-text {
            background: linear-gradient(45deg, #FF6B35, #DC2626, #F59E0B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glow-button {
            transition: all 0.3s ease;
        }

        .glow-button:hover {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
            transform: translateY(-1px);
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .floating-element {
            position: absolute;
            animation: subtleFloat 12s ease-in-out infinite;
            opacity: 0.1;
        }

        .floating-element:nth-child(2) {
            animation-delay: -3s;
        }

        .floating-element:nth-child(3) {
            animation-delay: -6s;
        }

        .floating-element:nth-child(4) {
            animation-delay: -9s;
        }

        @keyframes subtleFloat {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .cart-sidebar {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .cart-sidebar.open {
            transform: translateX(0);
        }

        .overlay {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }
    </style>

</head>

<body class="font-inter bg-gray-50">


    <!-- TODO: BACKEND - Floating elements positions should be stored in CMS -->
    <!-- Floating Background Elements -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="floating-element text-6xl text-fresh-green" style="top: 15%; left: 5%;">🥔</div>
        <div class="floating-element text-5xl text-deep-green" style="top: 25%; right: 8%;">🌶️</div>
        <div class="floating-element text-5xl text-forest-green" style="top: 65%; left: 10%;">🫙</div>
        <div class="floating-element text-4xl text-fresh-green" style="top: 80%; right: 12%;">🧂</div>
    </div>

    <!-- Cart Sidebar -->
    <!-- TODO: BACKEND - Cart items from user session/database -->
    <div class="fixed inset-0 z-50 overflow-hidden overlay" id="cartOverlay">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeCart()"></div>
        <div class="fixed right-0 top-0 h-full w-96 bg-white shadow-xl cart-sidebar" id="cartSidebar">
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="text-xl font-bold font-nunito">Shopping Cart</h2>
                <button onclick="closeCart()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6" id="cartItems">
                <!-- Cart items will be populated here -->
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                    <p>Your cart is empty</p>
                </div>
            </div>

            <div class="border-t p-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold">Total:</span>
                    <span class="text-xl font-bold text-fresh-green" id="cartTotal">₹0</span>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Proceed to Checkout Button -->
                    <button onclick="proceedToCheckout()" class="...">Proceed to Checkout</button>
                <?php else: ?>
                    <p class="text-center text-gray-500 mt-4">Please <a href="login.php"
                            class="text-green-600 underline">log in</a> to place an order.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Navigation -->
    <!-- TODO: BACKEND - User authentication status, cart count from API -->
    <nav class="bg-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Brand Logo + Name -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-fresh-green to-deep-green rounded-lg flex items-center justify-center">
                            <i class="fas fa-leaf text-white text-lg"></i>
                        </div>
                        <span class="ml-3 text-2xl font-bold font-nunito text-gray-800">FreshSupply</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#products"
                        class="text-gray-700 hover:text-fresh-green font-medium transition-colors">Products</a>
                    <a href="#suppliers"
                        class="text-gray-700 hover:text-fresh-green font-medium transition-colors">Suppliers</a>
                    <a href="#how-it-works"
                        class="text-gray-700 hover:text-fresh-green font-medium transition-colors">How it Works</a>
                    <a href="#support"
                        class="text-gray-700 hover:text-fresh-green font-medium transition-colors">Support</a>
                </div>

                <!-- Right Section (Cart + Auth/User) -->
                <div class="flex items-center space-x-4">

                    <!-- Cart Icon -->
                    <button onclick="toggleCartPanel()" class="relative">
                        🛒
                        <span class="absolute top-0 right-0 bg-red-600 text-white text-xs px-2 py-0.5 rounded-full">
                            <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                        </span>
                    </button>

   <?php if (isset($_SESSION['username'])): ?>
    <div class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-fresh-green rounded-full flex items-center justify-center">
            <span class="text-white font-bold text-sm">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
            </span>
        </div>
        <a href="<?php 
            if ($_SESSION['role'] === 'vendor') {
                echo 'Vender-Profile.php';
            } elseif ($_SESSION['role'] === 'supplier') {
                echo 'supplier.php';
            } else {
                echo '#';
            }
        ?>" 
        class="text-gray-700 font-medium hover:text-fresh-green transition-colors">
            <?= htmlspecialchars($_SESSION['username']) ?>
        </a>

        <a href="logout.php" class="text-gray-500 hover:text-red-500 ml-2" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
<?php else: ?>
    <div class="flex items-center space-x-4">
        <a href="login.php" class="text-gray-700 hover:text-fresh-green font-medium">Sign In</a>
        <a href="login.php" class="bg-fresh-green hover:bg-deep-green text-white px-6 py-2 rounded-lg font-medium transition-colors">
            Get Started
        </a>
    </div>
<?php endif; ?>


                </div>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <!-- TODO: BACKEND - Hero content should be manageable via CMS -->
    <section class="gradient-bg relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold font-nunito text-white mb-6" data-aos="fade-up"
                    data-aos-duration="800">
                    Find Raw Materials for Your<br>
                    <span class="text-mint-green">Street Food Business</span>
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto font-poppins" data-aos="fade-up"
                    data-aos-delay="200">
                    Browse verified suppliers of potatoes, oil, masala, chutneys & more — Fast & Affordable
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="400">
                    <button
                        class="bg-white text-fresh-green px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors shadow-lg"
                        onclick="scrollToSearch()">
                        <i class="fas fa-search mr-2"></i>Search Now
                    </button>
                    <button
                        class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-fresh-green transition-colors"
                        onclick="scrollToProducts()">
                        Browse Products
                    </button>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-20 left-10 text-white/20 text-6xl" data-aos="fade-right" data-aos-delay="600">🥔</div>
        <div class="absolute top-40 right-20 text-white/20 text-5xl" data-aos="fade-left" data-aos-delay="800">🌶️</div>
        <div class="absolute bottom-20 left-20 text-white/20 text-4xl" data-aos="fade-right" data-aos-delay="1000">🫙
        </div>
    </section>

    <!-- Search Bar + Categories -->
    <!-- TODO: BACKEND - Search suggestions from Elasticsearch, categories from database -->
    <section id="search" class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search Bar -->
            <div class="max-w-4xl mx-auto mb-12" data-aos="fade-up">
                <div class="relative">
                    <form method="GET" action="#products" class="relative">
                        <input type="text" id="searchInput" name="search"
                            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                            placeholder="Search potatoes, oil, spices, flour, masala..."
                            class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-2xl focus:border-fresh-green focus:outline-none shadow-lg">
                        <button type="submit"
                            class="absolute right-2 top-2 bg-fresh-green text-white px-6 py-2 rounded-xl hover:bg-deep-green transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    <!-- Search Suggestions Dropdown -->
                    <div id="searchSuggestions"
                        class="hidden absolute top-full left-0 right-0 bg-white border-2 border-gray-200 rounded-b-2xl shadow-lg z-10 max-h-60 overflow-y-auto">
                        <!-- Dynamic suggestions will be populated here -->
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="overflow-x-auto category-scroll" data-aos="fade-up" data-aos-delay="200">
                <div class="flex space-x-4 pb-4 min-w-max" id="categoriesContainer">
                    <!-- Categories will be loaded dynamically -->
                    <div class="loading-skeleton h-10 w-24 rounded-full"></div>
                    <div class="loading-skeleton h-10 w-20 rounded-full"></div>
                    <div class="loading-skeleton h-10 w-16 rounded-full"></div>
                    <div class="loading-skeleton h-10 w-22 rounded-full"></div>
                    <div class="loading-skeleton h-10 w-18 rounded-full"></div>
                    <div class="loading-skeleton h-10 w-20 rounded-full"></div>
                </div>
            </div>
        </div>
    </section>
    <form method="GET" class="mb-8">
        <div class="flex flex-wrap gap-4 justify-center">

            <!-- Category Filter -->
            <select name="category" class="px-4 py-2 border rounded">
                <option value="">All Categories</option>
                <option value="vegetable">Vegetable</option>
                <option value="spices">Spices</option>
                <option value="oil">Oil</option>
                <option value="flour">Flour</option>
                <!-- Add more if needed -->
            </select>

            <!-- Price Filter -->
            <select name="price_range" class="px-4 py-2 border rounded">
                <option value="">All Prices</option>
                <option value="0-50">₹0 - ₹50</option>
                <option value="51-100">₹51 - ₹100</option>
                <option value="101-200">₹101 - ₹200</option>
                <option value="200+">₹200+</option>
            </select>

            <!-- Unit Filter -->
            <select name="unit" class="px-4 py-2 border rounded">
                <option value="">All Units</option>
                <option value="kg">Kg</option>
                <option value="L">Liter</option>
                <option value="g">Gram</option>
                <option value="ml">ml</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">Filter</button>
        </div>
    </form>


    <!-- Product Grid -->
    <section id="products" class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold font-nunito text-gray-800 mb-4" data-aos="fade-up">
                    Fresh Products from Verified Suppliers
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    Quality ingredients at wholesale prices, delivered fresh to your business
                </p>
            </div>

            <!-- Filters and Sorting (Optional) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8" id="productGrid">
                <?php
                include 'db.php'; // Ensure this connects to your DB
                
                $where = [];

                if (!empty($_GET['category'])) {
                    $category = mysqli_real_escape_string($conn, $_GET['category']);
                    $where[] = "category = '$category'";
                }

                if (!empty($_GET['unit'])) {
                    $unit = mysqli_real_escape_string($conn, $_GET['unit']);
                    $where[] = "unit = '$unit'";
                }

                if (!empty($_GET['price_range'])) {
                    $range = $_GET['price_range'];
                    if ($range === "0-50") {
                        $where[] = "price BETWEEN 0 AND 50";
                    } elseif ($range === "51-100") {
                        $where[] = "price BETWEEN 51 AND 100";
                    } elseif ($range === "101-200") {
                        $where[] = "price BETWEEN 101 AND 200";
                    } elseif ($range === "200+") {
                        $where[] = "price > 200";
                    }
                }

                if (!empty($_GET['search'])) {
                    $search = mysqli_real_escape_string($conn, $_GET['search']);
                    $where[] = "(name LIKE '%$search%' OR category LIKE '%$search%' OR description LIKE '%$search%')";
                }

                $filterSQL = "";
                if (!empty($where)) {
                    $filterSQL = "WHERE " . implode(" AND ", $where);
                }

                $query = "SELECT * FROM products $filterSQL ORDER BY product_id DESC";

                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo "<p class='text-red-600'>Query Failed: " . mysqli_error($conn) . "</p>";
                } elseif (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $imgPath = 'uploads/' . basename($row['product_image']); // fixed key
                        ?>
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($row['name']) ?>"
                                class="h-48 w-full object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($row['name']) ?></h3>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($row['category']) ?></p>
                                <p class="text-green-700 font-bold mt-2">₹<?= number_format($row['price'], 2) ?> /
                                    <?= htmlspecialchars($row['unit']) ?>
                                </p>

                                <button type="button" onclick='addToCart(<?= json_encode([
                                    "id" => (int) $row["product_id"],
                                    "name" => $row["name"],
                                    "price" => (float) $row["price"],
                                    "unit" => $row["unit"],
                                    "image" => "uploads/" . $row["product_image"],
                                    "supplier_id" => (int) $row["supplier_id"],
                                    "vendor_id" => (int) $row["vendor_id"]
                                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>)'
                                    class="bg-green-600 text-white py-2 px-4 rounded-lg">
                                    Add to Cart
                                </button>







                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='text-gray-600 col-span-4 text-center'>No products available.</p>";
                }
                ?>

            </div>

            <!-- Optional: Pagination Placeholder -->
            <div class="flex justify-center mt-12" id="pagination"></div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold font-nunito text-gray-800 mb-4" data-aos="fade-up">
                    How It Works
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    Simple steps to get fresh ingredients for your street food business
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-fresh-green to-deep-green rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold font-poppins text-gray-800 mb-3">Search Ingredients</h3>
                    <p class="text-gray-600">Find exactly what you need from our vast catalog of fresh ingredients</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-fresh-green to-deep-green rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-balance-scale text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold font-poppins text-gray-800 mb-3">Compare Suppliers</h3>
                    <p class="text-gray-600">Compare prices, ratings, and delivery options from verified suppliers</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-fresh-green to-deep-green rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-cart text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold font-poppins text-gray-800 mb-3">Place Order</h3>
                    <p class="text-gray-600">Add items to your basket and place your order with just a few clicks</p>
                </div>

                <!-- Step 4 -->
                <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-fresh-green to-deep-green rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-truck text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold font-poppins text-gray-800 mb-3">Get Delivery</h3>
                    <p class="text-gray-600">Fast delivery to your location or convenient pickup options</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted Suppliers -->
    <!-- TODO: BACKEND - Suppliers from database with ratings and verification status -->
    <section id="suppliers" class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold font-nunito text-gray-800 mb-4" data-aos="fade-up">
                    Trusted Suppliers
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    Partner with verified suppliers who understand your business needs
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="suppliersGrid">
                <!-- Suppliers will be loaded dynamically -->
                <div class="bg-white rounded-2xl p-8 shadow-lg text-center">
                    <div class="loading-skeleton w-20 h-20 rounded-full mx-auto mb-6"></div>
                    <div class="loading-skeleton h-6 w-3/4 mx-auto mb-2"></div>
                    <div class="loading-skeleton h-4 w-1/2 mx-auto mb-4"></div>
                    <div class="loading-skeleton h-8 w-full"></div>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-lg text-center">
                    <div class="loading-skeleton w-20 h-20 rounded-full mx-auto mb-6"></div>
                    <div class="loading-skeleton h-6 w-3/4 mx-auto mb-2"></div>
                    <div class="loading-skeleton h-4 w-1/2 mx-auto mb-4"></div>
                    <div class="loading-skeleton h-8 w-full"></div>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-lg text-center">
                    <div class="loading-skeleton w-20 h-20 rounded-full mx-auto mb-6"></div>
                    <div class="loading-skeleton h-6 w-3/4 mx-auto mb-2"></div>
                    <div class="loading-skeleton h-4 w-1/2 mx-auto mb-4"></div>
                    <div class="loading-skeleton h-8 w-full"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <!-- TODO: BACKEND - Testimonials from database with user verification -->
    <!-- Testimonials -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold font-nunito text-gray-800 mb-4" data-aos="fade-up">
                    What Vendors Say
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    Real feedback from street food vendors who trust FreshSupply
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="testimonialsGrid">
                <?php
                $testimonialQuery = "SELECT t.comment, t.created_at, u.name 
                                 FROM testimonials t
                                 JOIN users u ON t.user_id = u.id
                                 ORDER BY t.created_at DESC
                                 LIMIT 6";
                $result = $conn->query($testimonialQuery);

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        ?>
                        <div class="bg-gray-50 rounded-2xl p-8">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center text-white font-bold">
                                    <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="font-semibold text-gray-800"><?= htmlspecialchars($row['name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= date('F j, Y', strtotime($row['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="text-gray-700 text-base mb-4"><?= htmlspecialchars($row['comment']) ?></div>
                            <div class="text-xs text-gray-400">Verified Vendor</div>
                        </div>
                        <?php
                    endwhile;
                else:
                    ?>
                    <p class="text-gray-500 col-span-3 text-center">No testimonials yet.</p>
                <?php endif; ?>
            </div>

            <!-- Testimonial Form -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="mt-12 bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Leave a Testimonial</h3>
                    <form method="POST">
                        <textarea name="comment" required rows="4"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none"
                            placeholder="Write your experience..."></textarea>
                        <button type="submit" name="submit_testimonial"
                            class="mt-3 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-all">
                            Submit
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <p class="mt-6 text-center text-gray-600">Please <a href="login.php" class="text-green-600 underline">log
                        in</a> to leave a testimonial.</p>
            <?php endif; ?>
        </div>
    </section>


    <!-- Newsletter Signup -->
    <!-- TODO: BACKEND - Newsletter subscription with email validation -->
    <section class="bg-fresh-green py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold font-nunito text-white mb-4" data-aos="fade-up">
                Stay Updated with Fresh Deals
            </h2>
            <p class="text-xl text-white/90 mb-8" data-aos="fade-up" data-aos-delay="200">
                Get notified about new suppliers, special offers, and seasonal ingredients
            </p>

            <form class="max-w-md mx-auto" data-aos="fade-up" data-aos-delay="400"
                onsubmit="subscribeNewsletter(event)">
                <div class="flex">
                    <input type="email" id="newsletterEmail" placeholder="Enter your email address" required
                        class="flex-1 px-6 py-3 rounded-l-lg border-0 focus:outline-none focus:ring-2 focus:ring-white">
                    <button type="submit"
                        class="bg-deep-green hover:bg-forest-green text-white px-6 py-3 rounded-r-lg font-semibold transition-colors">
                        Subscribe
                    </button>
                </div>
                <p class="text-white/80 text-sm mt-2">No spam, unsubscribe anytime</p>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <!-- TODO: BACKEND - Contact information and links from CMS -->
    <footer id="support" class="bg-gray-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Support Section -->
            <div class="text-center mb-12" data-aos="fade-up">
                <div class="bg-fresh-green rounded-2xl p-8 inline-block">
                    <h3 class="text-2xl font-bold font-nunito mb-2">Need Help?</h3>
                    <p class="text-white/90 mb-4">Our support team is here for you</p>
                    <div class="text-3xl font-bold">📞 1800-FOODSUPPLY</div>
                    <p class="text-sm text-white/80 mt-2">Available 24/7 for all your needs</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-fresh-green to-deep-green rounded-lg flex items-center justify-center">
                            <i class="fas fa-leaf text-white text-lg"></i>
                        </div>
                        <span class="ml-3 text-2xl font-bold font-nunito">FreshSupply</span>
                    </div>
                    <p class="text-gray-400 mb-6 text-lg">
                        Connecting street food vendors with trusted suppliers. Fresh ingredients, fair prices, reliable
                        delivery.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 bg-fresh-green rounded-full flex items-center justify-center hover:bg-deep-green transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-fresh-green rounded-full flex items-center justify-center hover:bg-deep-green transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-fresh-green rounded-full flex items-center justify-center hover:bg-deep-green transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-fresh-green rounded-full flex items-center justify-center hover:bg-deep-green transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold font-poppins mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-fresh-green transition-colors">About Us</a></li>
                        <li><a href="#how-it-works" class="text-gray-400 hover:text-fresh-green transition-colors">How
                                It Works</a></li>
                        <li><a href="#suppliers"
                                class="text-gray-400 hover:text-fresh-green transition-colors">Suppliers</a></li>
                        <li><a href="#products"
                                class="text-gray-400 hover:text-fresh-green transition-colors">Products</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-fresh-green transition-colors">Pricing</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="text-lg font-semibold font-poppins mb-6">Support</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-fresh-green transition-colors">Help Center</a>
                        </li>
                        <li><a href="#" class="text-gray-400 hover:text-fresh-green transition-colors">Contact Us</a>
                        </li>
                        <li><a href="#" class="text-gray-400 hover:text-fresh-green transition-colors">Track Order</a>
                        </li>
                        <li><a href="#" class="text-gray-400 hover:text-fresh-green transition-colors">Returns</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-fresh-green transition-colors">FAQ</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-12 pt-8 text-center">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 mb-4 md:mb-0">
                        © 2024 FreshSupply. Made with ❤️ for Street Food Heroes of India.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-fresh-green transition-colors">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-fresh-green transition-colors">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // TODO: BACKEND - All API endpoints and data management

        // API Configuration
        const API_BASE_URL = 'https://api.freshsupply.com/v1';
        const API_ENDPOINTS = {
            // Authentication
            login: '/auth/login',
            register: '/auth/register',
            logout: '/auth/logout',
            profile: '/auth/profile',

            // Products
            products: '/products',
            productSearch: '/products/search',
            productCategories: '/products/categories',
            productSuggestions: '/products/suggestions',

            // Cart
            cart: '/cart',
            cartAdd: '/cart/add',
            cartUpdate: '/cart/update',
            cartRemove: '/cart/remove',

            // Orders
            orders: '/orders',
            orderCreate: '/orders/create',
            orderTrack: '/orders/track',

            // Suppliers
            suppliers: '/suppliers',
            supplierProducts: '/suppliers/{id}/products',

            // Testimonials
            testimonials: '/testimonials',

            // Newsletter
            newsletter: '/newsletter/subscribe',

            // Analytics
            analytics: '/analytics/track'
        };

        // Global State Management
        let currentUser = null;
        let cartItems = [];
        let currentPage = 1;
        let currentFilters = {};
        let currentSort = 'relevance';
        let searchTimeout = null;

        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 50,
            easing: 'ease-out-cubic'
        });

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function () {
            initializeApp();
        });

        // TODO: BACKEND - Initialize application with user session check
        async function initializeApp() {
            try {
                // Check if user is logged in
                await checkUserSession();

                // Load initial data
                await Promise.all([
                    loadCategories(),

                    loadSuppliers(),
                    //loadTestimonials(),
                    loadCartFromStorage()
                ]);

                // Track page view
                trackAnalytics('page_view', { page: 'home' });

            } catch (error) {
                console.error('App initialization error:', error);

            }
        }

        // TODO: BACKEND - Check user authentication status
        async function checkUserSession() {
            const token = localStorage.getItem('freshsupply_token');
            if (!token) return;

            try {
                const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.profile}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const userData = await response.json();
                    currentUser = userData.user;
                    updateUIForLoggedInUser();
                } else {
                    // Token is invalid, remove it
                    localStorage.removeItem('freshsupply_token');
                }
            } catch (error) {
                console.error('Session check error:', error);
            }
        }

        // TODO: BACKEND - Load product categories from API
        async function loadCategories() {
            try {
                const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.productCategories}`);
                const data = await response.json();

                if (data.success) {
                    renderCategories(data.categories);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Categories loading error:', error);
                // Fallback to static categories
                renderCategories([
                    { id: 1, name: 'Potatoes', icon: '🥔', slug: 'potatoes' },
                    { id: 2, name: 'Spices', icon: '🧂', slug: 'spices' },
                    { id: 3, name: 'Oil', icon: '🫙', slug: 'oil' },
                    { id: 4, name: 'Chutney', icon: '🍛', slug: 'chutney' },
                    { id: 5, name: 'Flour', icon: '🌾', slug: 'flour' },
                    { id: 6, name: 'Onions', icon: '🧄', slug: 'onions' }
                ]);
            }
        }

        function renderCategories(categories) {
            const container = document.getElementById('categoriesContainer');
            container.innerHTML = categories.map((category, index) => `
                <div class="flex-shrink-0 ${index === 0 ? 'bg-gradient-to-r from-fresh-green to-deep-green text-white' : 'bg-white border-2 border-gray-200 text-gray-700'} px-6 py-3 rounded-full font-medium hover:border-fresh-green hover:text-fresh-green transition-all cursor-pointer category-item ${index === 0 ? 'active' : ''}" 
                     data-aos="fade-up" 
                     data-aos-delay="${(index + 1) * 100}" 
                     data-category="${category.slug}"
                     onclick="selectCategory('${category.slug}', this)">
                    ${category.icon} ${category.name}
                </div>
            `).join('');
        }

        // TODO: BACKEND - Load products with pagination and filtering


        // TODO: BACKEND - Load testimonials from API
        async function loadTestimonials() {
            try {
                const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.testimonials}`);
                const data = await response.json();

                if (data.success) {
                    renderTestimonials(data.testimonials);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Testimonials loading error:', error);
                // Fallback to static testimonials
                renderTestimonials([
                    {
                        id: 1,
                        name: 'Ravi Kumar',
                        business: 'Panipuri Vendor, Delhi',
                        rating: 5,
                        text: "It's like DMart for my thela! So easy to order fresh ingredients. My customers love the quality and I save money too.",
                        avatar: 'R'
                    },
                    {
                        id: 2,
                        name: 'Priya Sharma',
                        business: 'Dosa Stall Owner, Mumbai',
                        rating: 5,
                        text: "Fresh supplies delivered right to my stall. No more running around the market. FreshSupply has made my life so much easier!",
                        avatar: 'P'
                    },
                    {
                        id: 3,
                        name: 'Amit Patel',
                        business: 'Chaat Vendor, Bangalore',
                        rating: 5,
                        text: "Quality ingredients at wholesale prices. My profit margins have improved and customers keep coming back for more!",
                        avatar: 'A'
                    }
                ]);
            }
        }

        function renderTestimonials(testimonials) {
            const container = document.getElementById('testimonialsGrid');
            container.innerHTML = testimonials.map((testimonial, index) => `
                <div class="bg-gray-50 rounded-2xl p-8" data-aos="fade-up" data-aos-delay="${(index + 1) * 100}">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-fresh-green rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">${testimonial.avatar}</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold font-poppins text-gray-800">${testimonial.name}</h4>
                            <p class="text-sm text-gray-600">${testimonial.business}</p>
                        </div>
                    </div>
                    <p class="text-gray-700 italic">"${testimonial.text}"</p>
                    <div class="flex items-center mt-4">
                        <div class="flex text-yellow-400">
                            ${Array(testimonial.rating).fill('<i class="fas fa-star"></i>').join('')}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // TODO: BACKEND - Search functionality with debouncing
        function handleSearchInput(query) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (query.length > 2) {
                    showSearchSuggestions(query);
                } else {
                    hideSearchSuggestions();
                }
            }, 300);
        }

        function handleEnterKey(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        }

        // TODO: BACKEND - Show search suggestions from API
        async function showSearchSuggestions(query) {
            try {
                const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.productSuggestions}?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data.success && data.suggestions.length > 0) {
                    const suggestionsContainer = document.getElementById('searchSuggestions');
                    suggestionsContainer.innerHTML = data.suggestions.map(suggestion => `
                        <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" onclick="selectSuggestion('${suggestion.text}')">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">${suggestion.icon}</span>
                                <div>
                                    <div class="font-semibold">${suggestion.text}</div>
                                    <div class="text-sm text-gray-600">${suggestion.category}</div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                    suggestionsContainer.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Search suggestions error:', error);
            }
        }

        function hideSearchSuggestions() {
            document.getElementById('searchSuggestions').classList.add('hidden');
        }

        function selectSuggestion(suggestion) {
            document.getElementById('searchInput').value = suggestion;
            hideSearchSuggestions();
            performSearch();
        }

        // TODO: BACKEND - Perform search with API
        async function performSearch() {
            const query = document.getElementById('searchInput').value.trim();
            if (!query) return;

            try {
                const params = new URLSearchParams({
                    q: query,
                    page: 1,
                    limit: 12,
                    sort: currentSort,
                    ...currentFilters
                });

                const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.productSearch}?${params}`);
                const data = await response.json();

                if (data.success) {
                    renderProducts(data.products);
                    showNotification(`Found ${data.total} products for "${query}"`, 'success');

                    // Track search analytics
                    trackAnalytics('search', { query, results: data.total });
                } else {
                    showNotification('No products found for your search', 'info');
                }
            } catch (error) {
                console.error('Search error:', error);
                showNotification('Search failed. Please try again.', 'error');
            }

            hideSearchSuggestions();
        }

        // Category selection
        function selectCategory(categorySlug, element) {
            // Remove active class from all categories
            document.querySelectorAll('.category-item').forEach(cat => {
                cat.classList.remove('bg-gradient-to-r', 'from-fresh-green', 'to-deep-green', 'text-white', 'active');
                cat.classList.add('bg-white', 'border-2', 'border-gray-200', 'text-gray-700');
            });

            // Add active class to clicked category
            element.classList.remove('bg-white', 'border-2', 'border-gray-200', 'text-gray-700');
            element.classList.add('bg-gradient-to-r', 'from-fresh-green', 'to-deep-green', 'text-white', 'active');

            // Filter products by category
            currentFilters.category = categorySlug;
            loadProducts(1, currentFilters, currentSort);

            // Track category selection
            trackAnalytics('category_select', { category: categorySlug });
        }

        // TODO: BACKEND - Apply filters and reload products
        function applyFilters() {
            const priceFilter = document.getElementById('priceFilter').value;
            const ratingFilter = document.getElementById('ratingFilter').value;

            currentFilters = {};
            if (priceFilter) currentFilters.price_range = priceFilter;
            if (ratingFilter) currentFilters.min_rating = ratingFilter;

            currentPage = 1;
            loadProducts(currentPage, currentFilters, currentSort);

            // Track filter usage
            trackAnalytics('filter_apply', currentFilters);
        }

        // TODO: BACKEND - Apply sorting and reload products
        function applySorting() {
            currentSort = document.getElementById('sortBy').value;
            currentPage = 1;
            loadProducts(currentPage, currentFilters, currentSort);

            // Track sorting usage
            trackAnalytics('sort_apply', { sort: currentSort });
        }

        // TODO: BACKEND - Add product to cart
        async function addToBasket(productId, productName, price, unit, image, supplier) {
            try {
                // If user is logged in, add to server cart
                if (currentUser) {
                    const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.cartAdd}`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('freshsupply_token')}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 1
                        })
                    });

                    const data = await response.json();
                    if (!data.success) {
                        throw new Error(data.message);
                    }
                }

                // Add to local cart
                const existingItem = cartItems.find(item => item.id === productId);
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cartItems.push({
                        id: productId,
                        name: productName,
                        price: price,
                        unit: unit,
                        image: image,
                        supplier: supplier,
                        quantity: 1
                    });
                }

                // Update UI
                updateCartUI();
                saveCartToStorage();
                showNotification(`${productName} added to cart!`, 'success');

                // Track add to cart
                trackAnalytics('add_to_cart', {
                    product_id: productId,
                    product_name: productName,
                    price: price
                });

            } catch (error) {
                console.error('Add to cart error:', error);
                showNotification('Failed to add item to cart', 'error');
            }
        }

        // TODO: BACKEND - Load cart from localStorage and server
        async function loadCartFromStorage() {
            // Load from localStorage first
            const localCart = JSON.parse(localStorage.getItem('freshsupply_cart')) || [];
            cartItems = localCart;

            // If user is logged in, sync with server cart
            if (currentUser) {
                try {
                    const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.cart}`, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('freshsupply_token')}`
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        // Merge server cart with local cart
                        cartItems = mergeCartItems(localCart, data.items);
                        saveCartToStorage();
                    }
                } catch (error) {
                    console.error('Cart sync error:', error);
                }
            }

            updateCartUI();
        }

        function mergeCartItems(localItems, serverItems) {
            const merged = [...serverItems];

            localItems.forEach(localItem => {
                const existingItem = merged.find(item => item.id === localItem.id);
                if (existingItem) {
                    existingItem.quantity = Math.max(existingItem.quantity, localItem.quantity);
                } else {
                    merged.push(localItem);
                }
            });

            return merged;
        }

        function saveCartToStorage() {
            localStorage.setItem('freshsupply_cart', JSON.stringify(cartItems));
        }

        function updateCartUI() {
            const cartCount = document.getElementById('cartCount');
            const cartTotal = document.getElementById('cartTotal');
            const cartItemsContainer = document.getElementById('cartItems');

            // Update cart count
            const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = totalItems;

            // Update cart total
            const total = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            cartTotal.textContent = `₹${total.toFixed(2)}`;

            // Update cart items display
            if (cartItems.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                        <p>Your cart is empty</p>
                    </div>
                `;
            } else {
                cartItemsContainer.innerHTML = cartItems.map(item => `
                    <div class="flex items-center space-x-4 p-4 border-b border-gray-200">
                        <img src="${item.image}" alt="${item.name}" class="w-16 h-16 rounded-lg object-cover">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">${item.name}</h4>
                            <p class="text-sm text-gray-600">${item.supplier}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="font-bold text-fresh-green">₹${item.price}/${item.unit}</span>
                                <div class="flex items-center space-x-2">
                                    <button onclick="updateCartItemQuantity(${item.id}, ${item.quantity - 1})" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="font-semibold">${item.quantity}</span>
                                    <button onclick="updateCartItemQuantity(${item.id}, ${item.quantity + 1})" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                    <button onclick="removeFromCart(${item.id})" class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center hover:bg-red-200 text-red-600">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

        // TODO: BACKEND - Update cart item quantity
        async function updateCartItemQuantity(productId, newQuantity) {
            if (newQuantity < 1) {
                removeFromCart(productId);
                return;
            }

            try {
                // Update server cart if user is logged in
                if (currentUser) {
                    const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.cartUpdate}`, {
                        method: 'PUT',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('freshsupply_token')}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: newQuantity
                        })
                    });

                    const data = await response.json();
                    if (!data.success) {
                        throw new Error(data.message);
                    }
                }

                // Update local cart
                const item = cartItems.find(item => item.id === productId);
                if (item) {
                    item.quantity = newQuantity;
                    updateCartUI();
                    saveCartToStorage();
                }

            } catch (error) {
                console.error('Update cart error:', error);
                showNotification('Failed to update cart', 'error');
            }
        }

        // TODO: BACKEND - Remove item from cart


        // Cart sidebar functions
        function openCart() {
            document.getElementById('cartOverlay').classList.add('active');
            document.getElementById('cartSidebar').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeCart() {
            document.getElementById('cartOverlay').classList.remove('active');
            document.getElementById('cartSidebar').classList.remove('open');
            document.body.style.overflow = 'auto';
        }

        //  Proceed to checkout
        async function proceedToCheckout() {
            if (cartItems.length === 0) {
                showNotification('Your cart is empty!', 'error');
                return;
            }

            // No need to check currentUser here anymore
            try {
                const response = await fetch('checkout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ items: cartItems })
                });

                const data = await response.json();

                if (data.success) {
                    cartItems = [];
                    updateCartUI();
                    showNotification('Order placed successfully!', 'success');
                } else {
                    showNotification(data.message || 'Checkout failed.', 'error');
                }

            } catch (error) {
                console.error('Checkout error:', error);
                showNotification('Checkout failed.', 'error');
            }
        }


        // ===========================
        // 💳 Razorpay Payment Section
        // ===========================
        const RAZORPAY_CONFIG = {
            key: 'rzp_test_cCpIuhCcDNSsf2'
        };

        async function startPayment(amount, pos) {
            console.log(pos);

            try {
                const response = await fetch('createOrder.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ amount: amount })
                });

                const data = await response.json();

                if (!data.orderId) {
                    alert("Error: Order ID not received");
                    return;
                }

                const options = {
                    key: RAZORPAY_CONFIG.key,
                    amount: amount * 100,
                    currency: "INR",
                    name: "FreshSupply",
                    description: "Product Purchase",
                    order_id: data.orderId,
                    handler: function (response) {
                        sendOrder(pos.coords.latitude, pos.coords.longitude, cart);
                        alert("✅ Payment Successful!\nPayment ID: " + response.razorpay_payment_id);

                        fetch("payment-success.php", {
                            method: "POST",
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                payment_id: response.razorpay_payment_id,
                                order_id: data.orderId,
                                customer_name: "ABC Restaurant",
                                amount: amount,
                                method: "Card",
                                status: "Success"
                            })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    console.log("Saved to DB successfully");
                                } else {
                                    console.error("DB error:", data.error);
                                }
                            });
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

            } catch (error) {
                console.error("Payment error:", error);
                alert("Payment failed. Please try again.\n\n" + error.message);
            }
        }


        // TODO: BACKEND - Newsletter subscription
        async function subscribeNewsletter(event) {
            event.preventDefault();

            const email = document.getElementById('newsletterEmail').value;
            const submitButton = event.target.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;

            submitButton.textContent = 'Subscribing...';
            submitButton.disabled = true;

            try {
                const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.newsletter}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();
                if (data.success) {
                    showNotification('Successfully subscribed to newsletter!', 'success');
                    document.getElementById('newsletterEmail').value = '';

                    // Track newsletter subscription
                    trackAnalytics('newsletter_subscribe', { email });
                } else {
                    throw new Error(data.message);
                }

            } catch (error) {
                console.error('Newsletter subscription error:', error);
                showNotification('Subscription failed. Please try again.', 'error');
            } finally {
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            }
        }

        // TODO: BACKEND - User authentication functions
        function showLogin() {
            // In a real app, this would show a login modal or redirect to login page
            const email = prompt('Enter your email:');
            const password = prompt('Enter your password:');

            if (email && password) {
                login(email, password);
            }
        }

        function showRegister() {
            // In a real app, this would show a registration modal or redirect to register page
            const name = prompt('Enter your name:');
            const email = prompt('Enter your email:');
            const password = prompt('Enter your password:');

            if (name && email && password) {
                register(name, email, password);
            }
        }

        // TODO: BACKEND - Login function
        async function login(email, password) {
            try {
                const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.login}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();
                if (data.success) {
                    localStorage.setItem('freshsupply_token', data.token);
                    currentUser = data.user;
                    updateUIForLoggedInUser();
                    showNotification(`Welcome back, ${data.user.name}!`, 'success');

                    // Sync cart with server
                    await loadCartFromStorage();

                    // Track login
                    trackAnalytics('user_login', { user_id: data.user.id });
                } else {
                    throw new Error(data.message);
                }

            } catch (error) {
                console.error('Login error:', error);
                showNotification('Login failed. Please check your credentials.', 'error');
            }
        }

        // TODO: BACKEND - Register function
        async function register(name, email, password) {
            try {
                const response = await fetch(`${API_BASE_URL}${API_ENDPOINTS.register}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name, email, password })
                });

                const data = await response.json();
                if (data.success) {
                    localStorage.setItem('freshsupply_token', data.token);
                    currentUser = data.user;
                    updateUIForLoggedInUser();
                    showNotification(`Welcome to FreshSupply, ${data.user.name}!`, 'success');

                    // Track registration
                    trackAnalytics('user_register', { user_id: data.user.id });
                } else {
                    throw new Error(data.message);
                }

            } catch (error) {
                console.error('Registration error:', error);
                showNotification('Registration failed. Please try again.', 'error');
            }
        }

        // TODO: BACKEND - Logout function
        async function logout() {
            try {
                await fetch(`${API_BASE_URL}${API_ENDPOINTS.logout}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('freshsupply_token')}`
                    }
                });

                localStorage.removeItem('freshsupply_token');
                currentUser = null;
                cartItems = [];
                updateUIForLoggedOutUser();
                updateCartUI();
                saveCartToStorage();
                showNotification('Logged out successfully', 'success');

                // Track logout
                trackAnalytics('user_logout');

            } catch (error) {
                console.error('Logout error:', error);
                // Still log out locally even if server request fails
                localStorage.removeItem('freshsupply_token');
                currentUser = null;
                updateUIForLoggedOutUser();
            }
        }

        function updateUIForLoggedInUser() {
            document.getElementById('authButtons').classList.add('hidden');
            document.getElementById('userProfile').classList.remove('hidden');
            document.getElementById('userName').textContent = currentUser.name;
            document.getElementById('userInitial').textContent = currentUser.name.charAt(0).toUpperCase();
        }

        function updateUIForLoggedOutUser() {
            document.getElementById('authButtons').classList.remove('hidden');
            document.getElementById('userProfile').classList.add('hidden');
        }

        // TODO: BACKEND - Analytics tracking
        async function trackAnalytics(event, data = {}) {
            try {
                await fetch(`${API_BASE_URL}${API_ENDPOINTS.analytics}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        ...(currentUser && { 'Authorization': `Bearer ${localStorage.getItem('freshsupply_token')}` })
                    },
                    body: JSON.stringify({
                        event,
                        data,
                        timestamp: new Date().toISOString(),
                        user_agent: navigator.userAgent,
                        page_url: window.location.href
                    })
                });
            } catch (error) {
                console.error('Analytics tracking error:', error);
            }
        }

        // Utility functions
        function renderPagination(pagination) {
            const container = document.getElementById('pagination');
            if (!pagination || pagination.total_pages <= 1) {
                container.innerHTML = '';
                return;
            }

            const { current_page, total_pages } = pagination;
            let paginationHTML = '<div class="flex space-x-2">';

            // Previous button
            if (current_page > 1) {
                paginationHTML += `<button onclick="loadProducts(${current_page - 1}, currentFilters, currentSort)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">Previous</button>`;
            }

            // Page numbers
            for (let i = Math.max(1, current_page - 2); i <= Math.min(total_pages, current_page + 2); i++) {
                const isActive = i === current_page;
                paginationHTML += `<button onclick="loadProducts(${i}, currentFilters, currentSort)" class="px-4 py-2 ${isActive ? 'bg-fresh-green text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-100'} rounded-lg font-semibold transition-all">${i}</button>`;
            }

            // Next button
            if (current_page < total_pages) {
                paginationHTML += `<button onclick="loadProducts(${current_page + 1}, currentFilters, currentSort)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">Next</button>`;
            }

            paginationHTML += '</div>';
            container.innerHTML = paginationHTML;
        }

        function loadMoreProducts() {
            currentPage++;
            loadProducts(currentPage, currentFilters, currentSort);
        }

        function viewSupplierProducts(supplierId) {
            // In a real app, this would navigate to supplier page
            showNotification(`Loading products from supplier ${supplierId}...`, 'info');
            currentFilters.supplier_id = supplierId;
            loadProducts(1, currentFilters, currentSort);
            scrollToProducts();
        }

        // Smooth scrolling functions
        function scrollToSearch() {
            document.getElementById('search').scrollIntoView({ behavior: 'smooth' });
        }

        function scrollToProducts() {
            document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
        }

        // Notification system
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-fresh-green' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';

            notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Remove after 4 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 4000);
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Close cart when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest('#cartSidebar') && !event.target.closest('[onclick*="openCart"]')) {
                const overlay = document.getElementById('cartOverlay');
                if (overlay.classList.contains('active')) {
                    closeCart();
                }
            }
        });

        // Hide search suggestions when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest('#searchInput') && !event.target.closest('#searchSuggestions')) {
                hideSearchSuggestions();
            }
        });

        // Add hover effects to product cards
        document.addEventListener('DOMContentLoaded', function () {
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(function (node) {
                            if (node.nodeType === 1 && node.classList.contains('card-hover')) {
                                addCardHoverEffects(node);
                            }
                        });
                    }
                });
            });

            observer.observe(document.getElementById('productGrid'), {
                childList: true,
                subtree: true
            });
        });

        function addCardHoverEffects(card) {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-8px)';
                this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.1)';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
            });
        }

        // Performance optimization: Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('loading-skeleton');
                        observer.unobserve(img);
                    }
                });
            });

            // Observe all images with data-src attribute
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // Service Worker registration for offline support
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js')
                    .then(function (registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function (err) {
                        console.log('ServiceWorker registration failed');
                    });
            });
        }

        // Error handling for network failures
        window.addEventListener('online', function () {
            showNotification('Connection restored', 'success');
            // Retry failed requests
            initializeApp();
        });

        window.addEventListener('offline', function () {
            showNotification('You are offline. Some features may not work.', 'info');
        });

    </script>
    <!-- Right-side cart panel -->
    <!-- Cart Panel (Top Right) -->
    <div id="cartPanel" class="fixed top-0 right-0 w-96 h-full bg-white shadow-lg z-50 overflow-y-auto hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Your Cart</h2>
            <button onclick="toggleCart()" class="text-gray-600 hover:text-red-500"><i
                    class="fas fa-times"></i></button>
        </div>
        <div id="cartItemsContainer" class="p-4">
            <!-- Cart items will be rendered here by JS -->
        </div>
        <button onclick="showCheckoutModal()"
            class="w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 rounded-lg">
            Proceed to Checkout
        </button>

    </div>
    <!-- Checkout Confirmation Modal -->
    <div id="checkoutModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden justify-center items-center">
        <div class="bg-white w-full max-w-2xl rounded-lg p-6 shadow-lg">
            <h2 class="text-xl font-bold mb-4 text-center">Confirm Your Order</h2>

            <!-- Vendor Info -->
            <div id="vendorDetails" class="mb-4 text-gray-700"></div>

            <!-- Cart Items -->
            <div id="checkoutCartItems" class="space-y-4 mb-4 max-h-48 overflow-y-auto border-t pt-4"></div>

            <!-- Location Choice -->
            <div class="mb-4">
                <label class="font-semibold">Location Source:</label>
                <div class="flex space-x-4 mt-2">
                    <button onclick="setLocationChoice('live')"
                        class="px-4 py-2 bg-fresh-green text-white rounded-lg">Use Live Location</button>
                    <button onclick="setLocationChoice('profile')" class="px-4 py-2 bg-gray-300 rounded-lg">Use Profile
                        Location</button>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-4 mt-4">
                <button onclick="closeCheckoutModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Cancel</button>
                <button id="placeOrderBtn" onclick="submitFinalOrder()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg">Place Order</button>
            </div>
        </div>
    </div>
    <script>
        function addToCart(product) {
            let cart = JSON.parse(localStorage.getItem('session_cart')) || [];
            console.log("Clicked Add to Cart", product);
            const index = cart.findIndex(item => item.id === product.id);
            if (index > -1) {
                cart[index].quantity += 1;
            } else {
                cart.push({ ...product, quantity: 1 });
            }

            localStorage.setItem('session_cart', JSON.stringify(cart));
            alert(product.name + " added to cart");
            fetchCartItems(); // Refresh the cart panel
        }


        function fetchCartItems() {
            const cart = JSON.parse(localStorage.getItem('session_cart')) || [];
            const container = document.getElementById('cartItemsContainer');
            container.innerHTML = '';

            if (cart.length > 0) {
                cart.forEach((item, index) => {
                    container.innerHTML += `
                <div class="flex items-center justify-between border-b py-3">
                    <img src="${item.image}" alt="${item.name}" class="w-12 h-12 rounded object-cover">
                    <div class="flex-1 px-3">
                        <h4 class="font-semibold text-sm">${item.name}</h4>
                        <p class="text-xs text-gray-500">₹${item.price} / ${item.unit}</p>
                        <div class="flex items-center mt-1">
                            <button onclick="changeQuantity(${index}, 'decrease')" class="px-2 py-1 text-sm bg-gray-200 rounded-l">-</button>
                            <span class="px-3">${item.quantity}</span>
                            <button onclick="changeQuantity(${index}, 'increase')" class="px-2 py-1 text-sm bg-gray-200 rounded-r">+</button>
                        </div>
                    </div>
                    <button onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
                });
            } else {
                container.innerHTML = `<p class="text-gray-500 text-center">Your cart is empty.</p>`;
            }
        }

    </script>

</body>

</html>