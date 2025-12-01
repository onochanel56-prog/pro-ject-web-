<?php
// ไฟล์: api_sellout.php
// ฟังก์ชันสำหรับดึงข้อมูล Sell Out จาก API (พร้อม Debug)

/**
 * ฟังก์ชัน Login เพื่อขอ Token
 */
function loginToAPI(): ?string {
    $apiUrl = "http://lth.com.la/grownlthapi/v1/index.php";
    
    $loginBody = [
        "user" => "api",
        "pwd" => "28C15C0B405C1F7A107133EDF5504367"
    ];
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginBody));
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return null;

    $data = json_decode($response, true);
    return $data['token'] ?? $data['data']['token'] ?? $data['access_token'] ?? null;
}

/**
 * ฟังก์ชันดึงข้อมูล Sell Out
 */
function getSellOutFromAPI(): ?array {
    $token = loginToAPI();
    if (!$token) return null;

    $apiUrl = "http://lth.com.la/grownlthapi/v1/index.php";

    $fromDate = date("Y-m-d", strtotime("-2 years"));
    $toDate = date("Y-m-d");

    $body = [
        "action" => "getSell",
        "dataSaveArr" => [
            "fromDate" => $fromDate,
            "toDate" => $toDate,
            "invoiceNo" => ""
        ]
    ];

    $headers = [
        "Content-Type: application/json",
        "Authorizationgrown: $token"
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return null;

    $decoded = json_decode($response, true);
    if ($decoded === null) return null;

    // ตรวจสอบ datas
    $sales = $decoded['datas'] ?? [];

    // **แก้ตรงนี้ให้ตรงกับ sellout.php**
    return ["datas" => $sales];
}
?>
