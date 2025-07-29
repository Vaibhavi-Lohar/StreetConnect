<?php

session_start();
$message = "";

// Database connection
$conn = new mysqli('localhost', 'root', '', 'streetfood');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// REGISTER
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if (strlen($password) !== 8) {
        $message = ""; // JavaScript will handle this
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Check for existing user
        $check = $conn->prepare("SELECT * FROM users WHERE email=? OR username=?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "Username or Email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, username, email, contact, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $username, $email, $contact, $hashed_password, $role);
            $stmt->execute();
            $message = "Registered successfully! You can now login.";
        }
    }
}

// LOGIN
if (isset($_POST['login'])) {
    $input = $_POST['email_or_username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE (email=? OR username=?) AND role=?");
    $stmt->bind_param("sss", $input, $input, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];         // ✅ Add this
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'vendor') {
                header("Location: index.php");
                exit();
            } elseif ($user['role'] === 'supplier') {
                header("Location: index.php");
                exit();
            } else {
                $message = "Unknown role!";
            }
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StreetFood Login/Register</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .message {
            color: red;
            font-weight: bold;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>StreetFood</h2>
    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <!-- Login Form -->
    <div id="login-form" class="form-box">
        <form method="post">
            <input type="text" name="email_or_username" placeholder="Username or Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="vendor">Vendor</option>
                <option value="supplier">Supplier</option>
            </select><br>
            <button type="submit" name="login">Login</button>
        </form>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
        <p>Not registered?</p>
        <button onclick="toggleForm()">Sign Up</button>
    </div>

    <!-- Register Form -->
    <div id="register-form" class="form-box" style="display:none;">
        <form method="post" onsubmit="return validatePassword()">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="text" name="contact" placeholder="Contact Number" required><br>

            <input type="password" id="reg-password" name="password" placeholder="Password (8 characters)" required><br>
            <div id="password-error" class="error"></div>

            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="vendor">Vendor</option>
                <option value="supplier">Supplier</option>
            </select><br>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account?</p>
        <button onclick="toggleForm()">Login</button>
    </div>
</div>

<script>
function toggleForm() {
    const login = document.getElementById("login-form");
    const register = document.getElementById("register-form");
    login.style.display = login.style.display === "none" ? "block" : "none";
    register.style.display = register.style.display === "none" ? "block" : "none";
}

function validatePassword() {
    const passwordField = document.getElementById("reg-password");
    const errorDiv = document.getElementById("password-error");

    if (passwordField.value.length !== 8) {
        errorDiv.textContent = "Password must be exactly 8 characters long!";
        return false; // prevent form submission
    }

    errorDiv.textContent = ""; // clear error
    return true;
}
</script>

</body>
</html>
