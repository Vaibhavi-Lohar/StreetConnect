<?php
session_start();
require 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: forgot_password.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if (strlen($newPassword) < 8) {
        $message = "Password must be at least 8 characters.";
    } elseif ($newPassword !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $email = $_SESSION['email'];

        $stmt = $conn->prepare("UPDATE users SET password=?, otp=NULL, otp_expiry=NULL WHERE email=?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        $stmt->execute();

        session_unset();
        session_destroy();
        header("Location: login.php?reset=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (!empty($message)) echo "<p class='msg' style='color:red;'>$message</p>"; ?>
        <form method="post">
            <input type="password" name="password" placeholder="New Password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
