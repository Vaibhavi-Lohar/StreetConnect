<?php
session_start();
require_once "db.php";

// JSON/AJAX response if submitted via fetch
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    header('Content-Type: text/plain');

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
        echo "Unauthorized access.";
        exit;
    }

    $supplier_id = $_SESSION['user_id'];
    $name        = $_POST['name'] ?? '';
    $category    = $_POST['category'] ?? '';
    $price       = $_POST['price'] ?? '';
    $unit        = $_POST['unit'] ?? '';
    $description = $_POST['description'] ?? '';
    $message     = "";

    $imageName = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = basename($_FILES['product_image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
            echo "Failed to upload image.";
            exit;
        }
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO products 
        (name, category, price, unit, product_image, description, vendor_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("ssdsssi", $name, $category, $price, $unit, $imageName, $description, $supplier_id);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "DB Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Prepare failed: " . $conn->error;
    }

    exit;
}
?>
