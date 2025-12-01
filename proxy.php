<?php
// 1. กำหนดข้อมูล API ของคุณ
$api_url = 'http://lth.com.la//grownlthapi/v1/index.php';
$auth_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjkyLCJ1c2VyTmFtZSI6ImFwaSIsImNvbXBhbnlJRCI6IidMVEgnIiwiZXhwIjoxNzk5OTUzMDE0fQ.fOU72uNHScOE_BG4JVz3_2GgRWArWPN8CriIpOuEFYk";

// 2. กำหนด Header และ Context สำหรับการร้องขอ POST
$options = array(
    'http' => array(
        'header'  => "Content-Type: application/json\r\n" .
                     "Authorizationgrown: {$auth_token}\r\n",
        'method'  => 'POST',
        // สมมติว่าต้องการ Body เปล่า (ปรับตาม API ที่แท้จริง)
        'content' => '{}' 
    )
);
$context  = stream_context_create($options);

// 3. ดึงข้อมูลจาก API
$result = file_get_contents($api_url, false, $context);

// 4. ตั้งค่า Header เพื่อส่งข้อมูลกลับไปให้ Frontend (อันนี้จะไม่ติด CORS)
header('Content-Type: application/json');
echo $result;
?>