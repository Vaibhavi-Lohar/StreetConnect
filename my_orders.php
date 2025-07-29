<?php
session_start();
require_once 'db.php'; // Your DB connection file

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all orders by vendor (user)
$orderQuery = $conn->prepare("
    SELECT o.id, o.total_price, o.delivery_address, o.live_location, o.created_at, u.name AS supplier_name
    FROM orders o
    JOIN users u ON o.supplier_id = u.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$orderQuery->bind_param("i", $user_id);
$orderQuery->execute();
$orders = $orderQuery->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-green-50">
    <div class="container mx-auto p-6 max-w-5xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">My Past Orders</h1>

        <?php if ($orders->num_rows > 0): ?>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-green-700 mb-2">Order #<?= $order['id'] ?> — <?= $order['created_at'] ?></h2>
                    <p class="mb-1"><strong>Supplier:</strong> <?= htmlspecialchars($order['supplier_name']) ?></p>
                    <p class="mb-1"><strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                    <p class="mb-1"><strong>Live Location:</strong> <?= htmlspecialchars($order['live_location']) ?></p>
                    <p class="mb-4"><strong>Total Price:</strong> ₹<?= $order['total_price'] ?></p>

                    <!-- Fetching order items -->
                    <?php
                    $orderId = $order['id'];
                    $itemQuery = $conn->prepare("
                        SELECT oi.quantity, oi.price, p.name 
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.product_id
                        WHERE oi.order_id = ?
                    ");
                    $itemQuery->bind_param("i", $orderId);
                    $itemQuery->execute();
                    $items = $itemQuery->get_result();
                    ?>

                    <table class="w-full text-left table-auto border">
                        <thead>
                            <tr>
                                <th class="border p-2">Product</th>
                                <th class="border p-2">Quantity</th>
                                <th class="border p-2">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $items->fetch_assoc()): ?>
                                <tr>
                                    <td class="border p-2"><?= htmlspecialchars($item['name']) ?></td>
                                    <td class="border p-2"><?= $item['quantity'] ?></td>
                                    <td class="border p-2">₹<?= $item['price'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-600">You haven't placed any orders yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
