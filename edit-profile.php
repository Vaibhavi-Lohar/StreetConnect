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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['ownerName'];
    $email = $_POST['email'];
    $contact = $_POST['phone'];
    $username = $_POST['username'];

    // Update users table
    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, contact=?, username=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $email, $contact, $username, $user_id);

    if ($stmt->execute()) {
        // Save vendor profile data
        $businessName = $_POST['businessName'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip_code = $_POST['zip_code'];
        $country = $_POST['country'];
        $locationNotes = $_POST['locationNotes'];

        // Check if vendor profile already exists
        $checkProfile = $conn->prepare("SELECT id FROM vendor_profiles WHERE user_id = ?");
        $checkProfile->bind_param("i", $user_id);
        $checkProfile->execute();
        $profileResult = $checkProfile->get_result();

        if ($profileResult->num_rows > 0) {
            // Update vendor_profiles
            $updateProfile = $conn->prepare("UPDATE vendor_profiles SET business_name=?, category=?, description=?, address=?, city=?, state=?, zip_code=?, country=?, location_notes=? WHERE user_id=?");
            $updateProfile->bind_param("sssssssssi", $businessName, $category, $description, $address, $city, $state, $zipCode, $country, $locationNotes, $user_id);
            $updateProfile->execute();
        } else {
            // Insert vendor_profiles
            $insertProfile = $conn->prepare("INSERT INTO vendor_profiles (user_id, business_name, category, description, address, city, state, zip_code, country, location_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insertProfile->bind_param("isssssssss", $user_id, $businessName, $category, $description, $address, $city, $state, $zipCode, $country, $locationNotes);
            $insertProfile->execute();
        }

        $message = "Profile updated successfully!";
    } else {
        $message = "Failed to update profile.";
    }
}

// Fetch users table data
$stmt = $conn->prepare("SELECT name, email, contact, username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

// Default empty vendor profile
$vendorData = [
    "business_name" => "",
    "category" => "",
    "description" => "",
    "address" => "",
    "city" => "",
    "state" => "",
    "zip_code" => "",
    "country" => "",
    "location_notes" => ""
];

// Fetch vendor_profiles data
$stmt2 = $conn->prepare("SELECT business_name, category, description, address, city, state, zip_code, country, location_notes FROM vendor_profiles WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
if ($result2->num_rows > 0) {
    $vendorData = $result2->fetch_assoc();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vendor Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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
</head>
<body class="bg-green-50 min-h-screen">
<form id="editProfileForm" class="space-y-6" method="POST" action="">
    <?php if (!empty($message)): ?>
    <div class="bg-green-100 text-green-800 p-3 rounded-lg shadow mb-4 text-center">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>


    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4 max-w-7xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- TODO: Link back to vendor profile -->
                    <button onclick="goBack()" class="text-gray-600 hover:text-green-600 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="cancelEdit()" class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Save Changes
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 max-w-4xl">
        <!-- TODO: Fetch current vendor data from database -->
        <!-- Database Query: SELECT * FROM vendors WHERE id = ? -->
        
            <!-- Profile Picture Section -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Picture</h2>
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <!-- TODO: Display current profile image from database -->
                        <img id="profileImage" src="" 
                             alt="Profile Picture" 
                             class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover">
                        <button type="button" onclick="removeProfileImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div>
                        <input type="file" id="profileImageInput" accept="image/*" class="hidden" onchange="handleImageUpload(event)">
                        <button type="button" onclick="document.getElementById('profileImageInput').click()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors mb-2">
                            <i class="fas fa-camera mr-2"></i>
                            Change Photo
                        </button>
                        <p class="text-sm text-gray-600">JPG, PNG or GIF. Max size 5MB.</p>
                        <!-- TODO: Implement image upload to server -->
                        <!-- API Call: POST /api/vendors/upload-image -->
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="businessName" class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                        <!-- TODO: Pre-populate with current data -->
                      <input type="text" id="businessName" name="businessName" required
                        value="<?= htmlspecialchars($vendorData['business_name']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <div>
                        <input type="text" id="ownerName" name="ownerName" required
                        value="<?= htmlspecialchars($userData['name']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">

                    </div>

                    <div>
                        <!-- Email -->
                        <input type="email" id="email" name="email" required
                        value="<?= htmlspecialchars($userData['email']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <!-- Phone -->
                        <input type="tel" id="phone" name="phone" required
                            value="<?= htmlspecialchars($userData['contact']) ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">

                    </div>
                    <div>
                        <!-- Username -->
                        <input type="text" id="username" name="username" required
                        value="<?= htmlspecialchars($userData['username']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">

                    </div>

                    <div class="md:col-span-2">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Business Category *</label>
                        <!-- TODO: Fetch categories from database -->
                        <!-- Database Query: SELECT * FROM business_categories ORDER BY name -->
                        <select id="category" name="category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Select a category</option>
                            <option value="organic-vegetables" <?= ($vendorData['category'] === 'organic-vegetables') ? 'selected' : '' ?>>Organic Vegetables</option>
                            <option value="organic-vegetables" <?= ($vendorData['category'] === 'organic-vegetables') ? 'selected' : '' ?>>Fresh Produce</option>
                            <option value="dairy-products" <?= ($vendorData['category'] == 'dairy-products') ? 'selected' : '' ?>>Dairy Products</option>
                            <option value="meat-poultry" <?= ($vendorData['category'] == 'meat-poultry') ? 'selected' : '' ?>>Meat & Poultry</option>
                            <option value="seafood" <?= ($vendorData['category'] == 'seafood') ? 'selected' : '' ?>>Seafood</option>
                            <option value="grains-cereals" <?= ($vendorData['category'] == 'grains-cereals') ? 'selected' : '' ?>>Grains & Cereals</option>
                            <option value="spices-herbs" <?= ($vendorData['category'] == 'spices-herbs') ? 'selected' : '' ?>>Spices & Herbs</option>
                            <option value="beverages" <?= ($vendorData['category'] == 'beverages') ? 'selected' : '' ?>>Beverages</option>
                            <option value="packaged-foods" <?= ($vendorData['category'] == 'packaged-foods') ? 'selected' : '' ?>>Packaged Foods</option>
                            <option value="other" <?= ($vendorData['category'] == 'other') ? 'selected' : '' ?>>Other</option>

                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
                        <textarea id="description" name="description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Enter your description here"><?= htmlspecialchars($vendorData['description']) ?></textarea>

                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Location Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Street Address *</label>
                        <input type="text" id="address" name="address" required
                        value="<?= htmlspecialchars($vendorData['address']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                        <input type="text" id="city" name="city" required
                        value="<?= htmlspecialchars($vendorData['city']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Province *</label>
                        <input type="text" id="state" name="state" required
                        value="<?= htmlspecialchars($vendorData['state']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal Code *</label>
                        <input type="text" id="zip_code" name="zip_code" required
                        value="<?= htmlspecialchars($vendorData['zip_code']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                        <select id="country" name="country" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="India" <?= ($vendorData['category'] === 'India') ? 'selected' : '' ?>>India</option>
                            <option value="USA" <?= ($vendorData['category'] === 'USA') ? 'selected' : '' ?>>USA</option>
                            <option value="CA" <?= ($vendorData['category'] === 'CA') ? 'selected' : '' ?>>CA</option>
                            <option value="Germany" <?= ($vendorData['category'] === 'Germany') ? 'selected' : '' ?>>Germany</option>

                            
                            <!-- TODO: Add more countries from database -->
                        </select>
                    </div>

                    <div class="md:col-span-2">
                    <label for="locationNotes" class="block text-sm font-medium text-gray-700 mb-2">Location Notes</label>
                    <input type="text" id="locationNotes" name="locationNotes"
                    value="<?= htmlspecialchars($vendorData['location_notes'] ?? '') ?>"
                    placeholder="Additional location details (e.g., building name, floor, landmarks)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                </div>
            </div>

            <!-- Operating Hours -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Operating Hours</h2>
                <!-- TODO: Store operating hours in database -->
                <!-- Database Table: operating_hours (vendor_id, day_of_week, open_time, close_time, is_closed) -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div class="font-medium text-gray-700">Monday</div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="mondayOpen" checked class="rounded text-green-600 focus:ring-green-500">
                            <label for="mondayOpen" class="text-sm text-gray-600">Open</label>
                        </div>
                        <input type="time" id="mondayStart" value="06:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <input type="time" id="mondayEnd" value="20:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div class="font-medium text-gray-700">Tuesday</div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="tuesdayOpen" checked class="rounded text-green-600 focus:ring-green-500">
                            <label for="tuesdayOpen" class="text-sm text-gray-600">Open</label>
                        </div>
                        <input type="time" id="tuesdayStart" value="06:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <input type="time" id="tuesdayEnd" value="20:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div class="font-medium text-gray-700">Wednesday</div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="wednesdayOpen" checked class="rounded text-green-600 focus:ring-green-500">
                            <label for="wednesdayOpen" class="text-sm text-gray-600">Open</label>
                        </div>
                        <input type="time" id="wednesdayStart" value="06:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <input type="time" id="wednesdayEnd" value="20:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div class="font-medium text-gray-700">Thursday</div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="thursdayOpen" checked class="rounded text-green-600 focus:ring-green-500">
                            <label for="thursdayOpen" class="text-sm text-gray-600">Open</label>
                        </div>
                        <input type="time" id="thursdayStart" value="06:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <input type="time" id="thursdayEnd" value="20:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div class="font-medium text-gray-700">Friday</div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="fridayOpen" checked class="rounded text-green-600 focus:ring-green-500">
                            <label for="fridayOpen" class="text-sm text-gray-600">Open</label>
                        </div>
                        <input type="time" id="fridayStart" value="06:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <input type="time" id="fridayEnd" value="20:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div class="font-medium text-gray-700">Saturday</div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="saturdayOpen" checked class="rounded text-green-600 focus:ring-green-500">
                            <label for="saturdayOpen" class="text-sm text-gray-600">Open</label>
                        </div>
                        <input type="time" id="saturdayStart" value="07:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <input type="time" id="saturdayEnd" value="18:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div class="font-medium text-gray-700">Sunday</div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="sundayOpen" checked class="rounded text-green-600 focus:ring-green-500">
                            <label for="sundayOpen" class="text-sm text-gray-600">Open</label>
                        </div>
                        <input type="time" id="sundayStart" value="07:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <input type="time" id="sundayEnd" value="18:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>

                <div class="mt-4 p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-info-circle text-green-600"></i>
                        <span class="font-medium text-green-800">Quick Actions</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setWeekdayHours()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                            Set Weekday Hours (6AM-8PM)
                        </button>
                        <button type="button" onclick="setWeekendHours()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                            Set Weekend Hours (7AM-6PM)
                        </button>
                        <button type="button" onclick="copyMondayToAll()" class="border border-green-600 text-green-600 hover:bg-green-50 px-3 py-1 rounded text-sm transition-colors">
                            Copy Monday to All Days
                        </button>
                    </div>
                </div>
            </div>



            <!-- Social Media & Website -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Online Presence</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                        <input type="url" id="website" name="website"
                               value="https://freshfarmproduceco.com"
                               placeholder="https://yourwebsite.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="facebook" class="block text-sm font-medium text-gray-700 mb-2">Facebook Page</label>
                        <input type="url" id="facebook" name="facebook"
                               placeholder="https://facebook.com/yourpage"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="instagram" class="block text-sm font-medium text-gray-700 mb-2">Instagram Profile</label>
                        <input type="url" id="instagram" name="instagram"
                               placeholder="https://instagram.com/yourprofile"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="twitter" class="block text-sm font-medium text-gray-700 mb-2">Twitter/X Profile</label>
                        <input type="url" id="twitter" name="twitter"
                               placeholder="https://twitter.com/yourprofile"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Settings</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Profile Visibility</h3>
                            <p class="text-sm text-gray-600">Make your profile visible to customers</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="profileVisible" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Accept New Orders</h3>
                            <p class="text-sm text-gray-600">Allow customers to place new orders</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="acceptOrders" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Email Notifications</h3>
                            <p class="text-sm text-gray-600">Receive email notifications for new orders and updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="emailNotifications" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">SMS Notifications</h3>
                            <p class="text-sm text-gray-600">Receive SMS notifications for urgent updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="smsNotifications" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <h2 class="text-lg font-semibold text-red-900 mb-4">Danger Zone</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 border border-red-200 rounded-lg bg-red-50">
                        <div>
                            <h3 class="font-medium text-red-900">Deactivate Account</h3>
                            <p class="text-sm text-red-700">Temporarily disable your account. You can reactivate it later.</p>
                        </div>
                        <!-- TODO: Implement account deactivation -->
                        <button type="button" onclick="deactivateAccount()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Deactivate
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-4 border border-red-200 rounded-lg bg-red-50">
                        <div>
                            <h3 class="font-medium text-red-900">Delete Account</h3>
                            <p class="text-sm text-red-700">Permanently delete your account and all data. This action cannot be undone.</p>
                        </div>
                        <!-- TODO: Implement account deletion -->
                        <button type="button" onclick="deleteAccount()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center gap-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
            <span class="text-gray-700">Saving changes...</span>
        </div>
    </div>

    <!-- Success Message -->
    <div id="successMessage" class="hidden fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>Profile updated successfully!</span>
        </div>
    </div>

    <!-- Error Message -->
    <div id="errorMessage" class="hidden fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span id="errorText">An error occurred. Please try again.</span>
        </div>
    </div>

    <script>
        // TODO: Replace all functions with actual API calls

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // TODO: Load current vendor data from database
            // API Call: GET /api/vendors/profile
            console.log('Loading vendor profile data...');
            loadVendorData();
        });

        function loadVendorData() {
            // TODO: Implement API call to load vendor data
            // API Call: GET /api/vendors/profile
            console.log('TODO: Load vendor data from API');
        }

        function goBack() {
            // TODO: Navigate back to vendor profile page
            window.history.back();
        }

        function cancelEdit() {
            if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
                goBack();
            }
        }

        function handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    showError('File size must be less than 5MB');
                    return;
                }

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showError('Please select a valid image file');
                    return;
                }

                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImage').src = e.target.result;
                };
                reader.readAsDataURL(file);

                // TODO: Upload image to server
                // API Call: POST /api/vendors/upload-image
                console.log('TODO: Upload image to server');
            }
        }

        function removeProfileImage() {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                document.getElementById('profileImage').src = '/placeholder.svg?height=120&width=120';
                document.getElementById('profileImageInput').value = '';
                // TODO: Remove image from server
                // API Call: DELETE /api/vendors/profile-image
                console.log('TODO: Remove profile image from server');
            }
        }

        function setWeekdayHours() {
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            days.forEach(day => {
                document.getElementById(day + 'Open').checked = true;
                document.getElementById(day + 'Start').value = '06:00';
                document.getElementById(day + 'End').value = '20:00';
            });
        }

        function setWeekendHours() {
            const days = ['saturday', 'sunday'];
            days.forEach(day => {
                document.getElementById(day + 'Open').checked = true;
                document.getElementById(day + 'Start').value = '07:00';
                document.getElementById(day + 'End').value = '18:00';
            });
        }

        function copyMondayToAll() {
            const mondayOpen = document.getElementById('mondayOpen').checked;
            const mondayStart = document.getElementById('mondayStart').value;
            const mondayEnd = document.getElementById('mondayEnd').value;

            const days = ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            days.forEach(day => {
                document.getElementById(day + 'Open').checked = mondayOpen;
                document.getElementById(day + 'Start').value = mondayStart;
                document.getElementById(day + 'End').value = mondayEnd;
            });
        }

        function saveProfile() {
            // Show loading overlay
            document.getElementById('loadingOverlay').classList.remove('hidden');

            // Validate form
            const form = document.getElementById('editProfileForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                document.getElementById('loadingOverlay').classList.add('hidden');
                return;
            }

            // Collect form data
            const formData = new FormData(form);
            
            // Add operating hours
            const operatingHours = {};
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            days.forEach(day => {
                operatingHours[day] = {
                    isOpen: document.getElementById(day + 'Open').checked,
                    startTime: document.getElementById(day + 'Start').value,
                    endTime: document.getElementById(day + 'End').value
                };
            });
            formData.append('operatingHours', JSON.stringify(operatingHours));

            // Add payment methods
            const paymentMethods = [];
            document.querySelectorAll('input[name="paymentMethods"]:checked').forEach(checkbox => {
                paymentMethods.push(checkbox.value);
            });
            formData.append('paymentMethods', JSON.stringify(paymentMethods));

            // Add account settings
            const accountSettings = {
                profileVisible: document.getElementById('profileVisible').checked,
                acceptOrders: document.getElementById('acceptOrders').checked,
                emailNotifications: document.getElementById('emailNotifications').checked,
                smsNotifications: document.getElementById('smsNotifications').checked
            };
            formData.append('accountSettings', JSON.stringify(accountSettings));

            // TODO: Send data to server
            // API Call: PUT /api/vendors/profile
            setTimeout(() => {
                document.getElementById('loadingOverlay').classList.add('hidden');
                showSuccess();
                console.log('TODO: Save profile data to server');
                console.log('Form data:', Object.fromEntries(formData));
            }, 2000);
        }

        function deactivateAccount() {
            if (confirm('Are you sure you want to deactivate your account? You can reactivate it later by contacting support.')) {
                // TODO: Implement account deactivation
                // API Call: PUT /api/vendors/deactivate
                console.log('TODO: Deactivate account');
                alert('TODO: Implement account deactivation');
            }
        }

        function deleteAccount() {
            const confirmation = prompt('Type "DELETE" to confirm account deletion:');
            if (confirmation === 'DELETE') {
                if (confirm('This action cannot be undone. Are you absolutely sure?')) {
                    // TODO: Implement account deletion
                    // API Call: DELETE /api/vendors/account
                    console.log('TODO: Delete account');
                    alert('TODO: Implement account deletion');
                }
            }
        }

        function showSuccess() {
            const successMessage = document.getElementById('successMessage');
            successMessage.classList.remove('hidden');
            setTimeout(() => {
                successMessage.classList.add('hidden');
            }, 3000);
        }

        function showError(message) {
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            errorText.textContent = message;
            errorMessage.classList.remove('hidden');
            setTimeout(() => {
                errorMessage.classList.add('hidden');
            }, 5000);
        }

        // Auto-save draft functionality (optional)
        let autoSaveTimeout;
        function autoSaveDraft() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // TODO: Auto-save draft to localStorage or server
                console.log('Auto-saving draft...');
            }, 2000);
        }

        // Add event listeners for auto-save
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('input', autoSaveDraft);
            element.addEventListener('change', autoSaveDraft);
        });
    </script>
</body>
</html>