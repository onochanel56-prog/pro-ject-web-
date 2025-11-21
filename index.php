<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}
?>

<h1>ยินดีต้อนรับ, <?php echo $_SESSION['username']; ?>!</h1>
<a href="logout.php">ออกจากระบบ</a>
