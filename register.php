<?php
session_start();
include "config.php"; // สมมติว่าไฟล์นี้มีการเชื่อมต่อฐานข้อมูล

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $employee_code = trim($_POST['employee_code']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- 1. การตรวจสอบเบื้องต้น ---
    if (empty($username) || empty($employee_code) || empty($password) || empty($confirm_password)) {
        $error = "กรุณากรอกข้อมูลในช่องที่จำเป็นทั้งหมด!";
    } elseif ($password !== $confirm_password) {
        $error = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน!";
    } elseif (strlen($password) < 6) {
        $error = "รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร!";
    } else {
        // --- 2.1 Check if employee_code already exists (NEW CHECK) ---
        // ตรวจสอบว่ารหัสพนักงานนี้มีผู้ใช้งานแล้วหรือไม่
        $stmt_check_code = $conn->prepare("SELECT id FROM users WHERE employee_code = ?");
        $stmt_check_code->bind_param("s", $employee_code);
        $stmt_check_code->execute();
        $stmt_check_code->store_result();

        if ($stmt_check_code->num_rows > 0) {
            $error = "รหัสพนักงานนี้มีผู้ใช้งานแล้ว! กรุณาตรวจสอบรหัสพนักงานอีกครั้ง";
            $stmt_check_code->close();
        } else {
            $stmt_check_code->close();

            // --- 2.2 Check if username already exists (EXISTING CHECK) ---
            // ตรวจสอบว่าชื่อผู้ใช้มีอยู่แล้วหรือไม่
            $stmt_check_username = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt_check_username->bind_param("s", $username);
            $stmt_check_username->execute();
            $stmt_check_username->store_result();

            if ($stmt_check_username->num_rows > 0) {
                $error = "ชื่อผู้ใช้มีอยู่แล้ว กรุณาเลือกชื่อผู้ใช้อื่น";
                $stmt_check_username->close();
            } else {
                $stmt_check_username->close();

                // --- 3. Hash the password (using MD5) ---
                $hashed_password = MD5($password);

                // --- 4. Insert new user into the database ---
                $stmt_insert = $conn->prepare("INSERT INTO users (username, employee_code, password) VALUES (?, ?, ?)");
                $stmt_insert->bind_param("sss", $username, $employee_code, $hashed_password);

                if ($stmt_insert->execute()) {
                    $success = "ลงทะเบียนสำเร็จ! คุณสามารถ<a href='login.php'>เข้าสู่ระบบ</a>ได้แล้ว";
                    unset($username);
                    unset($employee_code);
                } else {
                    $error = "เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่อีกครั้งในภายหลัง";
                }
                $stmt_insert->close();
            }
        }
    }
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
<title>Register</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">
<style>
/* ---------------------------------------------------------------- */
/* CSS Style: พื้นเขียว, Card ทึบแสง, สไตล์ iOS Minimal          */
/* ---------------------------------------------------------------- */

body { 
    font-family: 'Noto Sans Lao', Arial, sans-serif; 
    background-color: #388E3C; 
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

.register-container {
    width: 300px;
    padding: 30px;
    background: white; 
    border-radius: 20px; 
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); 
    text-align: center;
}

h2 {
    color: #333; 
    margin-bottom: 25px;
    font-weight: 700; 
}

label {
    display: block;
    text-align: left;
    margin-top: 10px;
    margin-bottom: 5px;
    font-weight: 500;
    color: #444;
}

/* Input Fields */
input[type="text"], 
input[type="password"] { 
    width: 100%; 
    padding: 12px 15px; 
    margin-bottom: 10px; 
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

/* Submit Button (ใช้สีน้ำเงินตามที่ตกลงกัน) */
input[type="submit"] { 
    width: 100%; 
    padding: 15px; 
    background: #007AFF; /* Apple Blue */
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
    text-align: left;
}

.success { 
    color: #34C759; 
    margin-bottom: 15px; 
    font-size: 14px;
    background-color: #F0FFF0;
    padding: 10px;
    border-radius: 8px;
    text-align: left;
}

.success a {
    color: #388E3C;
    font-weight: 600;
}

.login-link { 
    margin-top: 25px; 
    font-size: 0.9em; 
    color: #666; 
}

.login-link a {
    color: #388E3C; 
    text-decoration: none;
    font-weight: 600;
}

.login-link a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="register-container">
    <h2>ລົງທະບຽນຜູ້ໃຊ້ງານ</h2>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <?php if($success) echo "<div class='success'>$success</div>"; ?>
    <form method="post">
        <label for="username">ຊື່ຜູ້ໃຊ້:</label>
        <input type="text" id="username" name="username" placeholder="Username" 
               value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
        
        <label for="employee_code">ລະຫັດພະນັກງານ:</label>
        <input type="text" id="employee_code" name="employee_code" placeholder="Employee Code"
               value="<?php echo isset($employee_code) ? htmlspecialchars($employee_code) : ''; ?>" required>

        <label for="password">ລະຫັດຜ່ານ:</label>
        <input type="password" id="password" name="password" placeholder="Password" required>

        <label for="confirm_password">ຢືນຍັນລະຫັດຜ່ານ:</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>

        <input type="submit" name="register" value="ลงทะเบียน">
    </form>
    <div class="login-link">
        มีบัญชีอยู่แล้วใช่ไหม? <a href="login.php">เข้าสู่ระบบที่นี่</a>
    </div>
</div>

</body>
</html>