<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "streetfood"; // Make sure this matches your DB name

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
