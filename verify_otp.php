<?php
session_start();
if (!isset($_SESSION['otp']) || !isset($_SESSION['email'])) {
    header("Location: forgot_password.php");
    exit();
}

if (isset($_POST['otp'])) {
    $enteredOtp = $_POST['otp'];
    if ($enteredOtp == $_SESSION['otp']) {
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Verify OTP</h2>
        <?php if (isset($error)) echo "<p class='msg' style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <label>Enter the OTP sent to your email:</label><br>
            <input type="text" name="otp" required><br><br>
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>
