<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'streetfood';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch basic user info
$userStmt = $conn->prepare("SELECT name, email, contact, username FROM users WHERE id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userData = $userStmt->get_result()->fetch_assoc();

// Fetch vendor profile info
$vendorStmt = $conn->prepare("SELECT * FROM vendor_profiles WHERE user_id = ?");
$vendorStmt->bind_param("i", $user_id);
$vendorStmt->execute();
$vendorData = $vendorStmt->get_result()->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Profile Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function showAddMaterialForm() {
    document.getElementById("addMaterialForm").classList.remove("hidden");
}

function hideAddMaterialForm() {
    document.getElementById("addMaterialForm").classList.add("hidden");
}

function addMaterial() {
    const name = document.getElementById("materialName").value;
    const quantity = document.getElementById("materialQuantity").value;
    const unit = document.getElementById("materialUnit").value;
    const cost = document.getElementById("materialCost").value;
    const priority = document.getElementById("materialPriority").value;

    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('name', name);
    formData.append('quantity', quantity);
    formData.append('unit', unit);
    formData.append('cost', cost);
    formData.append('priority', priority);

    fetch('material_api.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        alert(response);
        location.reload(); // reload to show updated list
    })
    .catch(error => {
        alert("Error: " + error);
    });
}

function deleteMaterial(id) {
    if (!confirm("Are you sure you want to delete this item?")) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch('material_api.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        alert(response);
        location.reload();
    })
    .catch(error => {
        alert("Error: " + error);
    });
}
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#22c55e',
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-green-50 min-h-screen">
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <!-- Vendor Profile Header -->
        <!-- TODO: Fetch vendor data from database -->
        <!-- Database Query: SELECT * FROM vendors WHERE id = ? -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    <!-- Profile Image -->
                    <div class="flex-shrink-0">
                        <!-- TODO: Replace with dynamic image from database -->
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&h=120&fit=crop&crop=face"
                            alt="Vendor Profile" class="w-30 h-30 rounded-full border-4 border-white shadow-lg">
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <!-- TODO: Replace with dynamic vendor data -->
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
    <?= htmlspecialchars($vendorData['business_name'] ?? 'Business Name') ?>
</h1>

<div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
    <div class="flex items-center gap-1">
        <i class="fas fa-map-marker-alt"></i>
        <span><?= htmlspecialchars($vendorData['address'] ?? 'Address not set') ?></span>
    </div>
    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
        <?= htmlspecialchars($vendorData['category'] ?? 'Category') ?>
    </span>
</div>
<p><strong>City:</strong> <?= htmlspecialchars($vendorData['city']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($userData['email']) ?></p>
<p><strong>Contact:</strong> <?= htmlspecialchars($userData['contact']) ?></p>



                                <div class="flex items-center gap-1 text-sm text-gray-600">
                                    <i class="fas fa-clock"></i>
                                    <span>Weekdays: 6:00 AM - 8:00 PM | Weekends: 7:00 AM - 6:00 PM</span>
                                </div>
                            </div>

                            <!-- TODO: Add edit profile functionality -->
                           <a href="edit-profile.php">
  <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
      <i class="fas fa-edit"></i>
      Edit Profile
  </button>
</a>


                        </div>

                        <!-- Stats -->
                        <!-- TODO: Calculate stats from database -->
                        <div class="flex items-center gap-6 mt-4 pt-4 border-t border-green-200">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-700">4.8</div>
                                <div class="text-sm text-gray-600">Rating</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-700">1,247</div>
                                <div class="text-sm text-gray-600">Total Orders</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operating Hours -->
        <!-- TODO: Fetch operating hours from database -->
        <!-- Database Query: SELECT * FROM operating_hours WHERE vendor_id = ? ORDER BY day_of_week -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Operating Hours</h2>
                    <button
                        class="text-green-600 hover:text-green-700 text-sm flex items-center gap-1 transition-colors"
                        onclick="editOperatingHours()">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
                    <!-- TODO: Loop through operating hours from database -->
                    <div class="flex flex-col items-center p-4 bg-green-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                            <span class="text-sm font-medium text-green-700">M</span>
                        </div>
                        <span class="font-medium text-gray-900 mb-1">Monday</span>
                        <span class="text-sm text-gray-600 text-center">6:00 AM<br>8:00 PM</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    </div>

                    <div class="flex flex-col items-center p-4 bg-green-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                            <span class="text-sm font-medium text-green-700">T</span>
                        </div>
                        <span class="font-medium text-gray-900 mb-1">Tuesday</span>
                        <span class="text-sm text-gray-600 text-center">6:00 AM<br>8:00 PM</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    </div>

                    <div class="flex flex-col items-center p-4 bg-green-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                            <span class="text-sm font-medium text-green-700">W</span>
                        </div>
                        <span class="font-medium text-gray-900 mb-1">Wednesday</span>
                        <span class="text-sm text-gray-600 text-center">6:00 AM<br>8:00 PM</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    </div>

                    <div class="flex flex-col items-center p-4 bg-green-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                            <span class="text-sm font-medium text-green-700">T</span>
                        </div>
                        <span class="font-medium text-gray-900 mb-1">Thursday</span>
                        <span class="text-sm text-gray-600 text-center">6:00 AM<br>8:00 PM</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    </div>

                    <div class="flex flex-col items-center p-4 bg-green-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                            <span class="text-sm font-medium text-green-700">F</span>
                        </div>
                        <span class="font-medium text-gray-900 mb-1">Friday</span>
                        <span class="text-sm text-gray-600 text-center">6:00 AM<br>8:00 PM</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    </div>

                    <div class="flex flex-col items-center p-4 bg-green-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                            <span class="text-sm font-medium text-green-700">S</span>
                        </div>
                        <span class="font-medium text-gray-900 mb-1">Saturday</span>
                        <span class="text-sm text-gray-600 text-center">7:00 AM<br>6:00 PM</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    </div>

                    <div class="flex flex-col items-center p-4 bg-green-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                            <span class="text-sm font-medium text-green-700">S</span>
                        </div>
                        <span class="font-medium text-gray-900 mb-1">Sunday</span>
                        <span class="text-sm text-gray-600 text-center">7:00 AM<br>6:00 PM</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="mt-6 p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="font-medium text-green-800">Currently Open</span>
                        </div>
                        <span class="text-sm text-green-700">Closes at 8:00 PM</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Raw Material Requirements -->
                <!-- TODO: Fetch raw materials from database -->
                <!-- Database Query: SELECT * FROM raw_materials WHERE vendor_id = ? ORDER BY priority DESC, created_at DESC -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">Raw Material Requirements</h2>
                            <button
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors"
                                onclick="showAddMaterialForm()">
                                <i class="fas fa-plus"></i>
                                Add Material
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Add Material Form (Hidden by default) -->
                        <div id="addMaterialForm" class="hidden p-4 border rounded-lg bg-green-50 mb-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                                <input type="text" id="materialName" placeholder="Material name"
                                    class="px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <input type="number" id="materialQuantity" placeholder="Quantity"
                                    class="px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <input type="text" id="materialUnit" placeholder="Unit"
                                    class="px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <input type="number" id="materialCost" placeholder="Est. Cost"
                                    class="px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <div class="flex items-center gap-2">
                                <select id="materialPriority"
                                    class="px-3 py-2 border rounded-md text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="high">High Priority</option>
                                    <option value="medium" selected>Medium Priority</option>
                                    <option value="low">Low Priority</option>
                                </select>
                                <!-- TODO: Implement add material API call -->
                                <button
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors"
                                    onclick="addMaterial()">
                                    <i class="fas fa-save"></i>
                                    Save
                                </button>
                                <button
                                    class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors"
                                    onclick="hideAddMaterialForm()">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>

                        <!-- Materials List -->
                        <?php
$materials = [];
$materialQuery = $conn->prepare("SELECT * FROM raw_materials WHERE user_id = ?");
$materialQuery->bind_param("i", $user_id);
$materialQuery->execute();
$materials = $materialQuery->get_result();
?>

<div class="space-y-4" id="materialsList">
    <?php if ($materials->num_rows > 0): ?>
        <?php while ($row = $materials->fetch_assoc()): ?>
            <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-green-50 transition-colors">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h4 class="font-medium"><?= htmlspecialchars($row['name']) ?></h4>
                        <span class="px-2 py-1 rounded-full text-xs 
                            <?= $row['priority'] === 'high' ? 'bg-red-600 text-white' : ($row['priority'] === 'medium' ? 'bg-yellow-300 text-gray-800' : 'bg-gray-200 text-gray-800') ?>">
                            <?= htmlspecialchars($row['priority']) ?>
                        </span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <?= $row['quantity'] . " " . htmlspecialchars($row['unit']) ?> • Est. ₹<?= $row['estimated_cost'] ?>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-gray-400 hover:text-green-600 p-2 transition-colors" onclick="editMaterial(<?= $row['id'] ?>)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="text-gray-400 hover:text-red-600 p-2 transition-colors" onclick="deleteMaterial(<?= $row['id'] ?>)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-sm text-gray-500">No raw materials listed yet.</p>
    <?php endif; ?>
</div>

                    </div>
                </div>

               

                <!-- Trusted Suppliers -->
                <!-- TODO: Fetch trusted suppliers from database -->
                <!-- Database Query: SELECT s.*, AVG(r.rating) as avg_rating, COUNT(o.id) as total_orders FROM suppliers s LEFT JOIN reviews r ON s.id = r.supplier_id LEFT JOIN orders o ON s.id = o.supplier_id WHERE s.verified = 1 GROUP BY s.id ORDER BY avg_rating DESC -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">Trusted Suppliers</h2>
                            <div class="flex items-center gap-2">
                                <button id="gridViewBtn" class="bg-green-600 text-white p-2 rounded-lg"
                                    onclick="switchView('grid')">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button id="listViewBtn" class="border border-gray-300 text-gray-700 p-2 rounded-lg"
                                    onclick="switchView('list')">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Grid View -->
                        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- TODO: Loop through suppliers from database -->
                            <div class="border rounded-lg p-4 hover:shadow-md hover:shadow-green-100 transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-medium mb-1">Green Valley Farms</h4>
                                        <p class="text-sm text-gray-600">Organic Produce</p>
                                    </div>
                                    <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Verified</span>
                                </div>

                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium">4.9</span>
                                    <span class="text-sm text-gray-600">(156 orders)</span>
                                </div>

                                <div class="flex items-center gap-1 text-sm text-gray-600 mb-3">
                                    <i class="fas fa-map-marker-alt text-xs"></i>
                                    <span>California, USA</span>
                                </div>

                                <div class="flex flex-wrap gap-1 mb-4">
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Organic
                                        Vegetables</span>
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Seasonal
                                        Fruits</span>
                                </div>

                                <div class="flex gap-2">
                                    <!-- TODO: Implement contact supplier functionality -->
                                    <button
                                        class="flex-1 border border-green-200 text-green-700 hover:bg-green-50 py-2 px-4 rounded-lg text-sm flex items-center justify-center gap-2 transition-colors"
                                        onclick="callSupplier(1)">
                                        <i class="fas fa-phone"></i>
                                        Call
                                    </button>
                                    <button
                                        class="flex-1 border border-green-200 text-green-700 hover:bg-green-50 py-2 px-4 rounded-lg text-sm flex items-center justify-center gap-2 transition-colors"
                                        onclick="emailSupplier(1)">
                                        <i class="fas fa-envelope"></i>
                                        Email
                                    </button>
                                </div>
                            </div>

                            <div class="border rounded-lg p-4 hover:shadow-md hover:shadow-green-100 transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-medium mb-1">Sunny Acres</h4>
                                        <p class="text-sm text-gray-600">Fresh Produce</p>
                                    </div>
                                    <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Verified</span>
                                </div>

                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star-half-alt text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium">4.7</span>
                                    <span class="text-sm text-gray-600">(89 orders)</span>
                                </div>

                                <div class="flex items-center gap-1 text-sm text-gray-600 mb-3">
                                    <i class="fas fa-map-marker-alt text-xs"></i>
                                    <span>Oregon, USA</span>
                                </div>

                                <div class="flex flex-wrap gap-1 mb-4">
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Leafy
                                        Greens</span>
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Herbs</span>
                                </div>

                                <div class="flex gap-2">
                                    <button
                                        class="flex-1 border border-green-200 text-green-700 hover:bg-green-50 py-2 px-4 rounded-lg text-sm flex items-center justify-center gap-2 transition-colors"
                                        onclick="callSupplier(2)">
                                        <i class="fas fa-phone"></i>
                                        Call
                                    </button>
                                    <button
                                        class="flex-1 border border-green-200 text-green-700 hover:bg-green-50 py-2 px-4 rounded-lg text-sm flex items-center justify-center gap-2 transition-colors"
                                        onclick="emailSupplier(2)">
                                        <i class="fas fa-envelope"></i>
                                        Email
                                    </button>
                                </div>
                            </div>

                            <div class="border rounded-lg p-4 hover:shadow-md hover:shadow-green-100 transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-medium mb-1">Farm Fresh Co.</h4>
                                        <p class="text-sm text-gray-600">Mixed Produce</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star-half-alt text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium">4.5</span>
                                    <span class="text-sm text-gray-600">(67 orders)</span>
                                </div>

                                <div class="flex items-center gap-1 text-sm text-gray-600 mb-3">
                                    <i class="fas fa-map-marker-alt text-xs"></i>
                                    <span>Washington, USA</span>
                                </div>

                                <div class="flex flex-wrap gap-1 mb-4">
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Root
                                        Vegetables</span>
                                    <span
                                        class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Peppers</span>
                                </div>

                                <div class="flex gap-2">
                                    <button
                                        class="flex-1 border border-green-200 text-green-700 hover:bg-green-50 py-2 px-4 rounded-lg text-sm flex items-center justify-center gap-2 transition-colors"
                                        onclick="callSupplier(3)">
                                        <i class="fas fa-phone"></i>
                                        Call
                                    </button>
                                    <button
                                        class="flex-1 border border-green-200 text-green-700 hover:bg-green-50 py-2 px-4 rounded-lg text-sm flex items-center justify-center gap-2 transition-colors"
                                        onclick="emailSupplier(3)">
                                        <i class="fas fa-envelope"></i>
                                        Email
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- List View (Hidden by default) -->
                        <div id="listView" class="hidden space-y-4">
                            <!-- TODO: Same suppliers data in list format -->
                            <div
                                class="flex items-center justify-between p-4 border rounded-lg hover:bg-green-50 transition-colors">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="font-medium">Green Valley Farms</h4>
                                        <span
                                            class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Verified</span>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-600 mb-2">
                                        <div class="flex items-center gap-1">
                                            <div class="flex text-yellow-400">
                                                <i class="fas fa-star text-xs"></i>
                                                <i class="fas fa-star text-xs"></i>
                                                <i class="fas fa-star text-xs"></i>
                                                <i class="fas fa-star text-xs"></i>
                                                <i class="fas fa-star text-xs"></i>
                                            </div>
                                            <span class="ml-1">4.9</span>
                                        </div>
                                        <span>156 orders</span>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-map-marker-alt text-xs"></i>
                                            <span>California, USA</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Organic
                                            Vegetables</span>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Seasonal
                                            Fruits</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <button
                                        class="border border-green-200 text-green-700 hover:bg-green-50 px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors"
                                        onclick="callSupplier(1)">
                                        <i class="fas fa-phone"></i>
                                        Call
                                    </button>
                                    <button
                                        class="border border-green-200 text-green-700 hover:bg-green-50 px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors"
                                        onclick="emailSupplier(1)">
                                        <i class="fas fa-envelope"></i>
                                        Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">

                <!-- Monthly Expense Overview -->
                <!-- TODO: Fetch expense data from database -->
                <!-- Database Query: SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total_expenses FROM expenses WHERE vendor_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Monthly Expense Overview</h2>
                        <p class="text-sm text-gray-600">Your spending trends over the last 6 months</p>
                    </div>
                    <div class="p-6">
                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 gap-4 mb-6">
                            <!-- TODO: Calculate current month expenses -->
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600">This Month</p>
                                    <p class="text-2xl font-bold">₹2,700</p>
                                </div>
                                <i class="fas fa-dollar-sign text-2xl text-gray-400"></i>
                            </div>

                            <!-- TODO: Calculate monthly change -->
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600">Monthly Change</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-lg font-semibold text-green-600">₹400</p>
                                        <i class="fas fa-arrow-down text-green-600"></i>
                                    </div>
                                    <p class="text-xs text-green-600">-12.9% from last month</p>
                                </div>
                            </div>

                            <!-- TODO: Calculate 6-month average -->
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600">6-Month Average</p>
                                    <p class="text-lg font-semibold">₹2,867</p>
                                </div>
                            </div>
                        </div>

                        <!-- Simple Chart Placeholder -->
                        <!-- TODO: Implement actual chart with Chart.js or similar -->
                        <div class="mb-4">
                            <canvas id="expenseChart" width="400" height="200"></canvas>
                        </div>

                        <!-- Budget Status -->
                        <!-- TODO: Fetch budget data from database -->
                        <div class="p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-green-800">Budget Status</span>
                                <span class="text-sm font-semibold text-green-600">Under Budget</span>
                            </div>
                            <div class="mt-2 w-full bg-green-200 rounded-full h-2">
                                <div class="h-2 rounded-full bg-green-500" style="width: 90%"></div>
                            </div>
                            <p class="text-xs text-green-700 mt-1">₹2,700 of ₹3,000 budget used</p>
                        </div>
                    </div>
                </div>

                <!-- Notifications Feed -->
                <!-- TODO: Fetch notifications from database -->
                <!-- Database Query: SELECT * FROM notifications WHERE vendor_id = ? ORDER BY created_at DESC LIMIT 20 -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl font-semibold text-gray-900">Notifications</h2>
                                <!-- TODO: Count unread notifications -->
                                <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">3</span>
                            </div>
                            <!-- TODO: Implement mark all as read functionality -->
                            <button class="text-gray-400 hover:text-gray-600 text-sm" onclick="markAllAsRead()">
                                Mark all read
                            </button>
                        </div>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div class="space-y-1" id="notificationsList">
                            <!-- TODO: Loop through notifications from database -->
                            <div class="p-4 border-b hover:bg-green-50 transition-colors bg-green-50/50">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="text-sm font-semibold">Order Delivered</h4>
                                                </div>
                                                <p class="text-sm text-gray-600 mb-2">Your order of Organic Tomatoes
                                                    from Green Valley Farms has been delivered successfully.</p>
                                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                                    <i class="fas fa-clock"></i>
                                                    <span>2 hours ago</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <!-- TODO: Implement mark as read functionality -->
                                                <button class="text-gray-400 hover:text-green-600 p-1"
                                                    onclick="markAsRead(1)">
                                                    <i class="fas fa-check-circle text-xs"></i>
                                                </button>
                                                <!-- TODO: Implement dismiss notification functionality -->
                                                <button class="text-gray-400 hover:text-red-600 p-1"
                                                    onclick="dismissNotification(1)">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 border-b hover:bg-green-50 transition-colors bg-green-50/50">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="text-sm font-semibold">Low Stock Alert</h4>
                                                    <span
                                                        class="border border-gray-300 text-gray-700 px-2 py-1 rounded-full text-xs">Action
                                                        Required</span>
                                                </div>
                                                <p class="text-sm text-gray-600 mb-2">Bell Peppers inventory is running
                                                    low. Consider placing a new order soon.</p>
                                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                                    <i class="fas fa-clock"></i>
                                                    <span>4 hours ago</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button class="text-gray-400 hover:text-green-600 p-1"
                                                    onclick="markAsRead(2)">
                                                    <i class="fas fa-check-circle text-xs"></i>
                                                </button>
                                                <button class="text-gray-400 hover:text-red-600 p-1"
                                                    onclick="dismissNotification(2)">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 border-b hover:bg-green-50 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <i class="fas fa-info-circle text-green-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="text-sm font-medium">New Supplier Available</h4>
                                                </div>
                                                <p class="text-sm text-gray-600 mb-2">Organic Harvest has joined your
                                                    trusted suppliers network. Check out their offerings.</p>
                                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                                    <i class="fas fa-clock"></i>
                                                    <span>1 day ago</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button class="text-gray-400 hover:text-red-600 p-1"
                                                    onclick="dismissNotification(3)">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 border-b hover:bg-green-50 transition-colors bg-green-50/50">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="text-sm font-semibold">Payment Failed</h4>
                                                    <span
                                                        class="border border-gray-300 text-gray-700 px-2 py-1 rounded-full text-xs">Action
                                                        Required</span>
                                                </div>
                                                <p class="text-sm text-gray-600 mb-2">Payment for order ORD-002 failed.
                                                    Please update your payment method.</p>
                                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                                    <i class="fas fa-clock"></i>
                                                    <span>1 day ago</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button class="text-gray-400 hover:text-green-600 p-1"
                                                    onclick="markAsRead(4)">
                                                    <i class="fas fa-check-circle text-xs"></i>
                                                </button>
                                                <button class="text-gray-400 hover:text-red-600 p-1"
                                                    onclick="dismissNotification(4)">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Functionality -->
    <script>
        // TODO: Replace all functions with actual API calls to backend

        // Edit Profile
        function editProfile() {
            // TODO: Implement edit profile modal/page
            // API Call: PUT /api/vendors/{id}
            console.log('Edit profile clicked');
            window.location.href = "edit-profile.html"
        }

        // Raw Materials Functions
        
        // Orders Functions
        function sortTable(column) {
            // TODO: Implement table sorting
            console.log('Sort by:', column);
            alert('TODO: Implement table sorting');
        }

        function toggleOrderActions(button) {
            const dropdown = button.nextElementSibling;
            // Hide all other dropdowns
            document.querySelectorAll('.absolute.right-0.mt-2').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
        }

        function viewOrderDetails(orderId) {
            // TODO: Implement view order details
            // API Call: GET /api/orders/{orderId}
            console.log('View order details:', orderId);
            alert('TODO: Implement view order details');
        }

        function trackOrder(orderId) {
            // TODO: Implement order tracking
            // API Call: GET /api/orders/{orderId}/tracking
            console.log('Track order:', orderId);
            alert('TODO: Implement order tracking');
        }

        function contactSupplier(orderId) {
            // TODO: Implement contact supplier
            console.log('Contact supplier for order:', orderId);
            alert('TODO: Implement contact supplier');
        }

        // Suppliers Functions
        function switchView(view) {
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const gridBtn = document.getElementById('gridViewBtn');
            const listBtn = document.getElementById('listViewBtn');

            if (view === 'grid') {
                gridView.classList.remove('hidden');
                listView.classList.add('hidden');
                gridBtn.classList.add('bg-green-600', 'text-white');
                gridBtn.classList.remove('border', 'border-gray-300', 'text-gray-700');
                listBtn.classList.remove('bg-green-600', 'text-white');
                listBtn.classList.add('border', 'border-gray-300', 'text-gray-700');
            } else {
                gridView.classList.add('hidden');
                listView.classList.remove('hidden');
                listBtn.classList.add('bg-green-600', 'text-white');
                listBtn.classList.remove('border', 'border-gray-300', 'text-gray-700');
                gridBtn.classList.remove('bg-green-600', 'text-white');
                gridBtn.classList.add('border', 'border-gray-300', 'text-gray-700');
            }
        }

        function callSupplier(supplierId) {
            // TODO: Implement call supplier functionality
            // API Call: GET /api/suppliers/{supplierId}/contact
            console.log('Call supplier:', supplierId);
            alert('TODO: Implement call supplier functionality');
        }

        function emailSupplier(supplierId) {
            // TODO: Implement email supplier functionality
            // API Call: POST /api/suppliers/{supplierId}/email
            console.log('Email supplier:', supplierId);
            alert('TODO: Implement email supplier functionality');
        }

        // Notifications Functions
        function markAllAsRead() {
            // TODO: Implement mark all notifications as read
            // API Call: PUT /api/notifications/mark-all-read
            console.log('Mark all notifications as read');
            alert('TODO: Implement mark all as read API call');
        }

        function markAsRead(notificationId) {
            // TODO: Implement mark notification as read
            // API Call: PUT /api/notifications/{notificationId}/read
            console.log('Mark notification as read:', notificationId);
            alert('TODO: Implement mark as read API call');
        }

        function dismissNotification(notificationId) {
            // TODO: Implement dismiss notification
            // API Call: DELETE /api/notifications/{notificationId}
            console.log('Dismiss notification:', notificationId);
            alert('TODO: Implement dismiss notification API call');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('.absolute.right-0.mt-2').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });

        // Operating Hours Functions
        function editOperatingHours() {
            // TODO: Navigate to operating hours edit page or open modal
            // This could redirect to edit-profile.html#operating-hours
            console.log('Edit operating hours clicked');
            alert('TODO: Implement edit operating hours functionality');
        }

        // Function to check current business status
        function updateBusinessStatus() {
            // TODO: Implement real-time business status check
            // API Call: GET /api/vendors/current-status
            const now = new Date();
            const currentHour = now.getHours();
            const currentDay = now.getDay(); // 0 = Sunday, 1 = Monday, etc.

            // This is a simplified example - in real implementation, 
            // you would check against actual operating hours from database
            let isOpen = false;
            let closingTime = '';

            if (currentDay >= 1 && currentDay <= 5) { // Monday to Friday
                isOpen = currentHour >= 6 && currentHour < 20;
                closingTime = '8:00 PM';
            } else { // Weekend
                isOpen = currentHour >= 7 && currentHour < 18;
                closingTime = '6:00 PM';
            }

            const statusElement = document.querySelector('.bg-green-50 .flex');
            if (statusElement) {
                const statusText = statusElement.querySelector('.font-medium');
                const timeText = statusElement.querySelector('.text-sm');
                const indicator = statusElement.querySelector('.w-3.h-3');

                if (isOpen) {
                    statusText.textContent = 'Currently Open';
                    statusText.className = 'font-medium text-green-800';
                    timeText.textContent = `Closes at ₹{closingTime}`;
                    indicator.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse';
                } else {
                    statusText.textContent = 'Currently Closed';
                    statusText.className = 'font-medium text-red-800';
                    timeText.textContent = 'Opens tomorrow at 6:00 AM';
                    indicator.className = 'w-3 h-3 bg-red-500 rounded-full';
                }
            }

            console.log('TODO: Implement real-time business status check');
        }

        // Chart.js Implementation
        function initializeExpenseChart() {
            const ctx = document.getElementById('expenseChart').getContext('2d');

            // Dummy data for the last 6 months
            const currentDate = new Date();
            const months = [];
            const expenses = [];

            // Generate last 6 months data
            for (let i = 5; i >= 0; i--) {
                const date = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
                months.push(date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' }));

                // Generate realistic expense data with some variation
                const baseExpense = 2500;
                const variation = Math.random() * 800 - 400; // ±400 variation
                expenses.push(Math.round(baseExpense + variation));
            }

            // Set current month to match the displayed value
            expenses[expenses.length - 1] = 2700;

            const expenseChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Monthly Expenses',
                        data: expenses,
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#22c55e',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#22c55e',
                            borderWidth: 1,
                            callbacks: {
                                label: function (context) {
                                    return 'Expenses: ₹' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                }
                            }
                        }
                        ,
                        y: {
                            beginAtZero: false,
                            min: Math.min(...expenses) - 200,
                            max: Math.max(...expenses) + 200,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                },
                                callback: function (value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            return expenseChart;
        }

        // Function to update chart with real data from API
        function updateExpenseChart(expenseData) {
            // TODO: This function will be called when real data is fetched from API
            // expenseData should be an array of objects like: 
            // [{ month: 'Jan 24', amount: 2500 }, { month: 'Feb 24', amount: 2800 }, ...]

            if (window.expenseChart) {
                window.expenseChart.data.labels = expenseData.map(item => item.month);
                window.expenseChart.data.datasets[0].data = expenseData.map(item => item.amount);
                window.expenseChart.update();
            }
        }

        function fetchRecentOrders() {
            fetch('api/featchorder.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('ordersTableBody');
                    tbody.innerHTML = ''; // clear existing

                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-50';

                        const trackUrl = getMapLink(row.location);

                        tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">${row.product_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${row.quantity}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${row.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${row.email}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${row.phno}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${row.created_at}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="${trackUrl}" target="_blank" class="inline-block px-4 py-1 text-sm text-white bg-primary-500 rounded-full hover:bg-primary-600 transition duration-200 shadow-sm">Track</a>

                    </td>
                `;

                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error("Error fetching recent orders:", err);
                });
        }

        function getMapLink(location) {
            location = location.trim();
            const regex = /(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)/;
            const match = location.match(regex);

            if (match) {
                const lat = match[1];
                const lng = match[2];
                return `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
            } else {
                return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location)}`;
            }
        }



        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            // TODO: Load initial data from APIs
            console.log('Page loaded - ready to fetch data from backend');

            // Initialize expense chart
            initializeExpenseChart();

            // TODO: Fetch vendor profile data
            // API Call: GET /api/vendors/profile

            // TODO: Fetch raw materials
            // API Call: GET /api/raw-materials

            // ✅ Fetch and render recent orders
            fetchRecentOrders();

            // TODO: Fetch trusted suppliers
            // API Call: GET /api/suppliers/trusted

            // TODO: Fetch expense data and update chart
            // API Call: GET /api/expenses/monthly
            // updateExpenseChart(data);

            // TODO: Fetch notifications
            // API Call: GET /api/notifications

            // TODO: Fetch operating hours
            // API Call: GET /api/vendors/operating-hours

            // Update business status
            updateBusinessStatus();

            // Update business status every minute
            setInterval(updateBusinessStatus, 60000);
        });
    </script>
</body>

</html>
</merged_ // Update business status updateBusinessStatus(); // Update business status every minute
    setInterval(updateBusinessStatus, 60000); }); </script>
</body>

</html>
</merged_ setInterval(updateBusinessStatus, 60000); }); </script>
</body>

</html>