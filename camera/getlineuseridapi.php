<?php
$data = json_decode(file_get_contents("php://input"), true);
$myfile = fopen("config.txt", "r") or die("Unable to open file!");
$myfile = fread($myfile, filesize("config.txt"));
$configs = json_decode($myfile, true);
fclose($myfile);

//SendLine Api Message Fuction
function boardCastMessage($lineAccessToken){
    $url = 'https://api.line.me/v2/bot/followers/ids';

    $data = ['limit' => 1000];

    $post = json_encode($data);
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $lineAccessToken,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    return $result;
}

$output = boardCastMessage($configs['token']);
echo $output;






