<?php
session_start();
include "config.php";

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = MD5(?)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Incorrect username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
/* Same CSS as before */
body { font-family: Arial; background: #f0f0f0; }
.login-container { width: 300px; margin: 100px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 0 10px #aaa; }
input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 5px 0 15px 0; border: 1px solid #ccc; border-radius: 4px; }
input[type="submit"] { width: 100%; padding: 10px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
input[type="submit"]:hover { background: #45a049; }
.error { color: red; margin-bottom: 10px; }
</style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login" value="Login">
    </form>
</div>

</body>
</html>
