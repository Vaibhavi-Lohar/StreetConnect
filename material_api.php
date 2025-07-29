<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'streetfood';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed";
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit();
}

$user_id = $_SESSION['user_id'];

$action = $_POST['action'] ?? '';
if ($action == 'add') {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $cost = $_POST['cost'];
    $priority = $_POST['priority'];

    $stmt = $conn->prepare("INSERT INTO raw_materials (user_id, name, quantity, unit, estimated_cost, priority) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isidss", $user_id, $name, $quantity, $unit, $cost, $priority);

    if ($stmt->execute()) {
        echo "Material added successfully";
    } else {
        http_response_code(500);
        echo "Failed to add material";
    }
} elseif ($action == 'delete') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM raw_materials WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);

    if ($stmt->execute()) {
        echo "Material deleted successfully";
    } else {
        http_response_code(500);
        echo "Failed to delete material";
    }
}
?>
