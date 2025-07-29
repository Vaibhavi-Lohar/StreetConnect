<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    echo json_encode(['success' => false]);
    exit;
}

$vendor_id = $_SESSION['user_id'];
$query = "SELECT name, email, contact FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode([
    'success' => true,
    'name' => $result['name'],
    'email' => $result['email'],
    'contact' => $result['contact']
]);
