<?php

include "header_origin.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!empty($data['Username']) && !empty($data['Email']) && !empty($data['Password']) && !empty($data['Firstname']) && !empty($data['Lastname']) && !empty($data['PhoneNumber'])) {

        $Username = $data['username'];
        $Password = $data['password'];
        $Firstname = $data['Firstname'];
        $Lastname = $data['Lastname'];
        $Email = $data['email'];
        $PhoneNumber = $data['PhoneNumber'];


        $response = registerUser($Username, $Password, $Firstname, $Lastname, $Email, $PhoneNumber);

        header('Content-Type: application/json');
        echo $response;
    } else {
        http_response_code(400);
        echo json_encode(["error" => "กรุณาส่งข้อมูลให้ครบถ้วน (username, email, password)"]);
    }
}

function registerUser($Username, $Password, $Firstname, $Lastname, $Email, $PhoneNumber)
{
    $url = "http://localhost:3000/api/register";

    $data = [
        "Username" => $Username,
        "Password" => $Password,
        "Firstname" => $Firstname,
        "Lastname" => $Lastname,
        "Email" => $Email,
        "PhoneNumber" => $PhoneNumber
    ];

    $payload = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Content-Length: " . strlen($payload)
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = "cURL error: " . curl_error($ch);
        curl_close($ch);
        return json_encode(["error" => $error]);
    }

    curl_close($ch);
    return $response;
}
?>