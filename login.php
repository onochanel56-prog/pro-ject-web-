<?php
session_start();
// สมมติว่าไฟล์นี้มีการเชื่อมต่อฐานข้อมูล ($conn)
include "config.php"; 

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // =========================================================================
    // !!! คำเตือนด้านความปลอดภัยที่สำคัญ !!!
    // การใช้ MD5 ในการจัดเก็บและตรวจสอบรหัสผ่าน (MD5(?)) ถือว่าไม่ปลอดภัยอย่างยิ่ง 
    // และไม่ควรใช้ในแอปพลิเคชันจริง เพราะสามารถถูกถอดรหัส (Crack) ได้ง่ายมาก
    //
    // **คำแนะนำ:** คุณควรเปลี่ยนไปใช้ฟังก์ชันมาตรฐานของ PHP:
    // 1. ตอนลงทะเบียน: ใช้ `password_hash($password, PASSWORD_DEFAULT)` เพื่อเก็บรหัสที่ถูก Hash ลงในฐานข้อมูล
    // 2. ตอนเข้าสู่ระบบ: ดึงรหัสที่ถูก Hash จากฐานข้อมูล แล้วใช้ `password_verify($password, $hashedPasswordFromDB)` 
    //    ในการตรวจสอบแทนการเปรียบเทียบใน SQL Query นี้
    // =========================================================================

    // ใช้ BINARY เพื่อบังคับให้ username มีการเปรียบเทียบแบบ Case-Sensitive
    // และยังคงใช้ MD5 ตามโค้ดเดิมของคุณเพื่อความเข้ากันได้ชั่วคราว (แต่ควรเปลี่ยน)
    $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY username = ? AND password = MD5(?)");
    $stmt->bind_param("ss", $username, $password);
    
    // ตั้งค่าตัวแปร $result เพื่อป้องกันข้อผิดพลาดหาก execute ล้มเหลว
    $result = null;
    if ($stmt->execute()) {
        $result = $stmt->get_result();
    }

    if ($result && $result->num_rows === 1) {
        // การเข้าสู่ระบบสำเร็จ
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        // การเข้าสู่ระบบล้มเหลว
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
    }
    $stmt->close();
}
// ปิดการเชื่อมต่อฐานข้อมูล
if (isset($conn)) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<!-- สำคัญ: เพิ่ม Meta Tag สำหรับการตอบสนองบนมือถือ (Responsive Design) -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<!-- กำหนด Font Noto Sans Lao เพื่อการแสดงผลภาษาไทยที่สวยงาม -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">
<style>
/* ---------------------------------------------------------------- */
/* CSS Style: ปรับปรุงสำหรับการตอบสนอง (Responsive)          */
/* ---------------------------------------------------------------- */

body { 
    /* กำหนด Font Noto Sans Lao เป็นอันดับแรก */
    font-family: 'Noto Sans Lao', Arial, sans-serif; 
    background-color: #388E3C; /* พื้นหลังสีเขียวหลัก */
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    padding: 20px; /* เพิ่ม padding สำหรับมือถือ */
    box-sizing: border-box;
}

.login-container {
    /* ปรับให้ใช้ max-width แทน fixed width เพื่อการตอบสนองที่ดีขึ้น */
    width: 100%;
    max-width: 380px; /* เพิ่มขนาดสูงสุดเล็กน้อย */
    padding: 30px;
    background: white; 
    border-radius: 20px; 
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); 
    text-align: center;
}

h2 {
    color: #333; 
    margin-bottom: 25px;
    font-weight: 600;
}

/* Input Fields */
input[type="text"], 
input[type="password"] { 
    width: 100%; 
    padding: 12px 15px; 
    margin: 8px 0; 
    border: none;
    background-color: #F0F0F0; 
    border-radius: 12px; 
    box-sizing: border-box; 
    font-size: 16px;
    outline: none;
    transition: background-color 0.3s;
}

input[type="text"]:focus, 
input[type="password"]:focus {
    background-color: #E0E0E0; 
    box-shadow: 0 0 0 2px #388E3C; 
}

/* Submit Button (ปุ่มสไตล์ iOS/Apple Blue) */
input[type="submit"] { 
    width: 100%; 
    padding: 15px; 
    background: #007AFF; 
    color: white; 
    border: none; 
    border-radius: 12px; 
    cursor: pointer; 
    font-size: 17px;
    font-weight: 600;
    margin-top: 20px;
    transition: background-color 0.2s;
}

input[type="submit"]:hover { 
    background: #0056CC; 
}

.error { 
    color: #FF453A; 
    margin-bottom: 15px; 
    font-size: 14px;
    background-color: #FFEEF0; 
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #FF453A;
}

.register-link { 
    margin-top: 25px; 
    font-size: 0.9em; 
    color: #666; 
}

.register-link a {
    color: #388E3C; 
    text-decoration: none;
    font-weight: 600;
}

.register-link a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="login-container">
    <h2>เข้าสู่ระบบ</h2>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
        <input type="password" name="password" placeholder="รหัสผ่าน" required>
        <input type="submit" name="login" value="เข้าสู่ระบบ">
    </form>
    
    <div class="register-link">
        ยังไม่มีบัญชี? <a href="register.php">ลงทะเบียนที่นี่</a>
    </div>
</div>

</body>
</html>