<?php
header('Content-Type: application/json');

$rawInput = file_get_contents('php://input');

file_put_contents("debug.txt", $rawInput); // Write to debug.txt


// Debug: Check if input is empty
if (empty($rawInput)) {
    http_response_code(400);
    echo json_encode(['error' => 'Empty input received', 'rawInput' => $rawInput]);
    exit;
}

// Decode JSON input
$input = json_decode($rawInput, true);

// Debug: Check JSON validity
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON', 'rawInput' => $rawInput]);
    exit;
}

// Validate amount presence and type
if (!isset($input['amount']) || !is_numeric($input['amount'])) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Invalid amount',
        'rawInput' => $rawInput,
        'decodedInput' => $input
    ]);
    exit;
}

// Razorpay credentials
$keyId = 'rzp_test_cCpIuhCcDNSsf2';
$keySecret = 'K8aXd6mHWIylNAuqOW5uoRMj';

// Convert rupees to paise
$amount = intval($input['amount']) * 100;

$data = [
    'amount' => $amount,
    'currency' => 'INR',
    'receipt' => 'order_rcptid_' . rand(1000, 9999),
    'payment_capture' => 1
];

// Initialize cURL
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.razorpay.com/v1/orders",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD => "$keyId:$keySecret",
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
]);

$response = curl_exec($curl);
$httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if (curl_errno($curl)) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL Error: ' . curl_error($curl)]);
    curl_close($curl);
    exit;
}

curl_close($curl);

$responseData = json_decode($response, true);

if ($httpStatus == 200 && isset($responseData['id'])) {
    echo json_encode(['orderId' => $responseData['id']]);
} else {
    http_response_code($httpStatus);
    echo json_encode([
        'error' => 'Error creating order',
        'response' => $responseData
    ]);
}
?>
