<?php
session_start();
require 'db.php'; // DB connection file
require 'send_otp.php'; // Mail function file

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // Debug line
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $stmt = $conn->prepare("UPDATE users SET otp=?, otp_expiry=? WHERE email=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error); // Debug line
        }
        $stmt->bind_param("sss", $otp, $expiry, $email);
        $stmt->execute();

        $_SESSION['otp'] = $otp; // Store OTP in session for verify_otp.php
        $_SESSION['email'] = $email; // Store email
        send_otp_mail($email, $otp); // Custom mail function

        header("Location: verify_otp.php");
        exit();
    } else {
        $message = "Email not registered!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Enter your registered email" required><br>
            <button type="submit">Send OTP</button>
        </form>
    </div>
</body>
</html>
