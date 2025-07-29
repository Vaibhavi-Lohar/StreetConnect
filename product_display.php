<?php
session_start();
include 'db.php';

// Check if user is a logged-in supplier
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM products WHERE vendor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Products</title>
    <link rel="stylesheet" href="your-css-file.css"> <!-- optional -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <h2 class="text-3xl font-bold mb-6 text-center">My Added Products</h2>
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Your Products</h2>
        <a href="add_product.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition-all">
            <i class="fas fa-plus mr-2"></i>Add New Product
        </a>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="bg-white p-4 rounded shadow">
                <img src="<?= 'uploads/' . basename($row['product_image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="w-full h-40 object-cover rounded mb-4">
                <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($row['name']) ?></h3>
                <p class="text-gray-600"><?= htmlspecialchars($row['category']) ?></p>
                <p class="text-green-700 font-bold mt-2">₹<?= number_format($row['price'], 2) ?> / <?= htmlspecialchars($row['unit']) ?></p>
                <p class="text-sm mt-1 text-gray-500"><?= htmlspecialchars($row['description']) ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
