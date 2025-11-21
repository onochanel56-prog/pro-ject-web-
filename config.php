<?php
$host = "127.0.0.1";
$dbname = "ono_login";
$user = "root"; // default XAMPP username
$pass = "";     // default XAMPP password

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
