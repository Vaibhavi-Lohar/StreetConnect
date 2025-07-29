<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = isset($_POST['index']) ? intval($_POST['index']) : -1;
    $action = $_POST['action'] ?? '';

    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$index]['quantity']++;
        } elseif ($action === 'decrease') {
            $_SESSION['cart'][$index]['quantity']--;
            if ($_SESSION['cart'][$index]['quantity'] <= 0) {
                array_splice($_SESSION['cart'], $index, 1);
            }
        } elseif ($action === 'remove') {
            array_splice($_SESSION['cart'], $index, 1);
        }

        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
exit;
