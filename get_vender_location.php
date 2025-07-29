<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    echo json_encode(['success' => false]);
    exit;
}

$vendor_id = $_SESSION['user_id'];
$query = "SELECT latitude, longitude FROM vendor_profile WHERE vendor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result && $result['latitude'] && $result['longitude']) {
    echo json_encode(['success' => true, 'latitude' => $result['latitude'], 'longitude' => $result['longitude']]);
} else {
    echo json_encode(['success' => false]);
}
