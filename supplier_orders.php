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

// Check if supplier is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: index.php");
    exit();
}

$supplier_id = $_SESSION['user_id'];

$query = "SELECT o.*, u.name AS vendor_name, u.email AS vendor_email, u.contact AS vendor_contact 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          WHERE o.supplier_id = ?
          ORDER BY o.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supplier Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold mb-6 text-green-700">Received Orders</h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="grid grid-cols-1 gap-6">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-800">Order #<?= $row['id'] ?></h2>
                            <span class="text-sm text-gray-500"><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></span>
                        </div>
                        <p class="mt-2 text-gray-700"><strong>Vendor:</strong> <?= htmlspecialchars($row['vendor_name']) ?> (<?= $row['vendor_email'] ?>, <?= $row['vendor_contact'] ?>)</p>
                        <p class="text-gray-700"><strong>Address:</strong> <?= htmlspecialchars($row['delivery_address']) ?></p>
                        <p class="text-gray-700"><strong>Live Location:</strong> <?= htmlspecialchars($row['live_location']) ?></p>
                        <p class="text-gray-700"><strong>Total Price:</strong> ₹<?= number_format($row['total_price'], 2) ?></p>
                        <!-- Current Order Status -->
<p class="text-sm mt-2">
  <span class="font-medium text-gray-700">Status:</span>
  <span class="inline-block px-2 py-1 rounded-full text-white bg-<?php
      switch ($row['status']) {
          case 'Accepted': echo 'blue-500'; break;
          case 'In Transit': echo 'yellow-500'; break;
          case 'Delivered': echo 'green-500'; break;
          case 'Rejected': echo 'red-500'; break;
          default: echo 'gray-400';
      }
  ?>">
    <?= htmlspecialchars($row['status']) ?>
  </span>
</p>
<?php if ($row['status'] !== 'Delivered' && $row['status'] !== 'Rejected'): ?>
<form method="POST" action="update-order-status.php" class="mt-3 flex items-center gap-2">
  <input type="hidden" name="order_id" value="<?= $row['id'] ?>">

  <select name="status" required class="border rounded px-2 py-1 text-sm">
    <option disabled selected>Change Status</option>
    <option value="Accepted">Accept</option>
    <option value="In Transit">Mark In Transit</option>
    <option value="Delivered">Mark Delivered</option>
    <option value="Rejected">Reject</option>
  </select>

  <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
    Update
  </button>
</form>
<?php endif; ?>

                        <?php
$order_id = $row['id'];
$productQuery = "SELECT oi.quantity, oi.price, p.name AS product_name
                 FROM order_items oi
                 JOIN products p ON oi.product_id = p.product_id
                 WHERE oi.order_id = ?";
$productStmt = $conn->prepare($productQuery);
$productStmt->bind_param("i", $order_id);
$productStmt->execute();
$productResult = $productStmt->get_result();
?>

<div class="mt-4">
    <p class="font-semibold text-gray-800 mb-1">Ordered Products:</p>
    <ul class="list-disc pl-6 text-gray-700">
        <?php while ($item = $productResult->fetch_assoc()): ?>
            <li><?= htmlspecialchars($item['product_name']) ?> – <?= $item['quantity'] ?> × ₹<?= $item['price'] ?></li>
        <?php endwhile; ?>
    </ul>
</div>

                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600 text-lg">No orders received yet.</p>
        <?php endif; ?>

    </div>
</body>
</html>
