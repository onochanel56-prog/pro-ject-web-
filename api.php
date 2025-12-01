<?php

function getStockFromAPI() {
    $apiUrl = "http://lth.com.la//grownlthapi/v1/index.php";
    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjkyLCJ1c2VyTmFtZSI6ImFwaSIsImNvbXBhbnlJRCI6IidMVEgnIiwiZXhwIjoxNzk4NDc5NTY1fQ._K24-PSUiFPz-t1q6hul6P_zILN4zmVe8HseIVtmdOA";

    $body = [
        "action" => "getStock",
        "dataSaveArr" => new stdClass()
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorizationgrown: $token"
    ]);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
