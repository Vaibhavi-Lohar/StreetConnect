<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'vendor') {
    echo json_encode(['success' => false, 'message' => 'not_logged_in']);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productId = $_POST['product_id'];
$product = [
    'product_id' => $productId,
    'product_name' => $_POST['product_name'],
    'price' => $_POST['price'],
    'unit' => $_POST['unit'],
    'product_image' => $_POST['product_image'],
    'vendor_id' => $_POST['vendor_id'],
    'quantity' => 1
];

// Check if already in cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $productId) {
        $item['quantity'] += 1;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    $_SESSION['cart'][] = $product;
}

echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
