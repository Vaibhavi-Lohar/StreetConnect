<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['cart']) || !is_array($data['cart']) || empty($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$lat = $data['lat'];
$lng = $data['lng'];
$cart = $data['cart'];

// Get vendor_id from first product
$vendor_id = $cart[0]['vendor_id'];
$total = 0;

foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Insert into orders table
$stmt = $conn->prepare("INSERT INTO orders (user_id, vendor_id, total_amount, delivery_latitude, delivery_longitude) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iidds", $user_id, $vendor_id, $total, $lat, $lng);
$stmt->execute();
$order_id = $stmt->insert_id;

// Insert items
$stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($cart as $item) {
    $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
    $stmt->execute();
}

echo json_encode(['success' => true, 'order_id' => $order_id]);
?>
