<?php
session_start();

// Get logged-in supplier's username
$supplier_username = $_SESSION['username'] ?? 'Unknown';

// DB connection
$conn = new mysqli("localhost", "root", "", "streetfood");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get supplier's full name
$stmt = $conn->prepare("SELECT name FROM users WHERE username = ?");
$stmt->bind_param("s", $supplier_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $supplier_name = $row['name'];
} else {
    $supplier_name = $supplier_username;
}

// Fetch payments for this supplier
$sql = "SELECT * FROM payments WHERE supplier_username = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $supplier_username);
$stmt->execute();
$result = $stmt->get_result();

$payments = [];
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}

header('Content-Type: application/json');
echo json_encode($payments);
?>
