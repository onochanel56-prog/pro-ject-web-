<?php
header("Content-Type: application/json");

// API จริง
$api_url = "http://lth.com.la/grownlthapi/v1/index.php";

// ส่งข้อมูลไป API จริง
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Authorizationgrown: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjkyLCJ1c2VyTmFtZSI6ImFwaSIsImNvbXBhbnlJRCI6IidMVEgnIiwiZXhwIjoxNzk5OTUzMDE0fQ.fOU72uNHScOE_BG4JVz3_2GgRWArWPN8CriIpOuEFYk"
));

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "action" => "getSellout"
]));

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
