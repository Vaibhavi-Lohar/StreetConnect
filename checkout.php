<?php
session_start();
require_once 'db.php'; // Database connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch vendor profile
$vendorQuery = $conn->prepare("SELECT * FROM vendore_profile WHERE user_id = ?");
$vendorQuery->bind_param("i", $user_id);
$vendorQuery->execute();
$vendorResult = $vendorQuery->get_result();
$vendorProfile = $vendorResult->fetch_assoc();

// Fetch user details
$userQuery = $conn->prepare("SELECT name, email, contact FROM users WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userData = $userResult->fetch_assoc();

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $location_option = $_POST['location_option'] ?? 'profile';
    $live_location = $_POST['live_location'] ?? '';
    $delivery_location = ($location_option === 'live') ? $live_location : ($vendorProfile['address'] . ', ' . $vendorProfile['city']);

    $items = json_decode($_POST['cart_items'], true); // From JS
    $total_price = array_reduce($items, function ($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);

    $stmt = $conn->prepare("INSERT INTO orders (user_id, name, email, contact, delivery_location, total_price, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issssd", $user_id, $userData['name'], $userData['email'], $userData['contact'], $delivery_location, $total_price);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $productStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

    foreach ($items as $item) {
        $productStmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $productStmt->execute();
    }

    echo "<script>alert('Order placed successfully!'); window.location.href='dashboard.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <script>
    async function getLocationString() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject("Geolocation is not supported.");
            }
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    resolve(`Lat: ${latitude}, Lng: ${longitude}`);
                },
                (err) => {
                    reject("Failed to get location.");
                }
            );
        });
    }

    async function handleSubmit(e) {
        e.preventDefault();

        const locationOption = document.querySelector('input[name="location_option"]:checked').value;
        let liveLocation = '';

        if (locationOption === 'live') {
            try {
                liveLocation = await getLocationString();
            } catch (err) {
                alert('Could not fetch live location. Using profile address.');
                liveLocation = '';
            }
        }

        const cartItems = JSON.parse(localStorage.getItem('freshsupply_cart') || '[]');
        if (cartItems.length === 0) {
            alert('Your cart is empty!');
            return;
        }

        const form = document.getElementById('checkoutForm');
        const cartInput = document.createElement('input');
        cartInput.type = 'hidden';
        cartInput.name = 'cart_items';
        cartInput.value = JSON.stringify(cartItems);
        form.appendChild(cartInput);

        const locationInput = document.createElement('input');
        locationInput.type = 'hidden';
        locationInput.name = 'live_location';
        locationInput.value = liveLocation;
        form.appendChild(locationInput);

        form.submit();
    }
    </script>
</head>
<body>
    <h2>Checkout Page</h2>
    <form method="POST" id="checkoutForm" onsubmit="handleSubmit(event)">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Location</label>
            <div class="flex items-center gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="location_option" value="live" class="form-radio text-green-600" checked>
                    <span class="ml-2">Use Current Live Location</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="location_option" value="profile" class="form-radio text-green-600">
                    <span class="ml-2">Use Profile Location</span>
                </label>
            </div>
        </div>
        <button type="submit" name="submit_order">Place Order</button>
    </form>
</body>
</html>
