<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'streetfood');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$transaction_id = $conn->real_escape_string($data['payment_id']);
$order_id = $conn->real_escape_string($data['order_id']);
$customer_name = $conn->real_escape_string($data['customer_name']);
$amount = $conn->real_escape_string($data['amount']);
$method = $conn->real_escape_string($data['method']);
$status = $conn->real_escape_string($data['status']);
$date = date("Y-m-d H:i:s");
$supplier_username = $_SESSION['supplier_username']; // Store this during login

$sql = "INSERT INTO payments (transaction_id, order_id, customer_name, amount, method, status, date, supplier_username)
        VALUES ('$transaction_id', '$order_id', '$customer_name', '$amount', '$method', '$status', '$date', '$supplier_username')";

if ($conn->query($sql)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}
?>
