<?php
$LineData = file_get_contents('php://input');
$jsonData = json_decode($LineData,true);  

$serverName = "10.12.12.206";
// $serverName = "85.204.247.82,26433"; // external sql ip
$userName ='nwlproduction';
$userPassword="Nwl!2563789!";
$dbName = "NWL_Detection";
$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=>$userPassword, "MultipleActiveResultSets"=>true, "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if( $conn === false ){
    die( print_r( sqlsrv_errors(), true));
}

$userID = $jsonData["events"][0]["source"]["userId"];
$replyToken = $jsonData["events"][0]["replyToken"];
$text = $jsonData["events"][0]["message"]["text"];
$quoteToken = $jsonData["events"][0]["message"]["quoteToken"];
$timestamp = $jsonData["events"][0]["timestamp"]; //Unix time

$params = array($userID);
$sql = " SELECT userID FROM TmstLineUserIdCustomer WHERE userID = ? ";
$stmt = sqlsrv_query($conn, $sql, $params);
$result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$userIddb = $result['userID'];
if($stmt === false){
    die(print_r( sqlsrv_errors(), true));
}

function replyEnableMessage($userID, $replyToken, $quoteToken){
  $urlEndpoint = "https://api.line.me/v2/bot/message/reply";
  $accesstoken = "FWL29SlqhBjOS2RCtkyiWXBFg9Xc9V2DV0UE/lOSPClGEt/0XlMlzdNr5hhLAJaXyRkhojEMNoxgPJfntRfdCS9FQf2SVBX0XW2dl0C1C+peK05si03q7gId09i+ACNQgbmfq8tiTTm0WhVsY7zJFQdB04t89/1O/w1cDnyilFU=";
  
  $data = [
      "replyToken"=> $replyToken,
      "messages"=>[
          [
              "type"=>"text",
              "text"=>"$  Enable Detection!  $",
              "emojis"=> [
      [
        "index"=> 0,
        "productId"=> "5ac2213e040ab15980c9b447",
        "emojiId"=> "038"
      ],
      [
        "index"=> 22,
        "productId"=> "5ac2213e040ab15980c9b447",
        "emojiId"=> "038"
      ]
        ]
          ],
          [
                  "type"=> "sticker",
                  "packageId"=> "8522",
                  "stickerId"=> "16581266",
                //   "quoteToken"=> $quoteToken
          ]
      ]
  ];

  $post = json_encode($data);
  $headers = [
      'Content-Type: application/json',
      'Authorization: Bearer ' . $accesstoken,
  ];

  $ch = curl_init($urlEndpoint);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
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

function replyDisableMessage($userID, $replyToken, $quoteToken){
    $urlEndpoint = "https://api.line.me/v2/bot/message/reply";
    $accesstoken = "FWL29SlqhBjOS2RCtkyiWXBFg9Xc9V2DV0UE/lOSPClGEt/0XlMlzdNr5hhLAJaXyRkhojEMNoxgPJfntRfdCS9FQf2SVBX0XW2dl0C1C+peK05si03q7gId09i+ACNQgbmfq8tiTTm0WhVsY7zJFQdB04t89/1O/w1cDnyilFU=";
    
    $data = [
        "replyToken"=> $replyToken,
        "messages"=>[
            [
                "type"=>"text",
                "text"=>"$  Disable Detection!  $",
                "emojis"=> [
        [
          "index"=> 0,
          "productId"=> "5ac21e6c040ab15980c9b444",
          "emojiId"=> "005"
        ],
        [
          "index"=> 23,
          "productId"=> "5ac21e6c040ab15980c9b444",
          "emojiId"=> "005"
        ]
          ]
            ],
            [
                    "type"=> "sticker",
                    "packageId"=> "6136",
                    "stickerId"=> "10551380",
                  //   "quoteToken"=> $quoteToken
            ]
        ]
    ];
  
    $post = json_encode($data);
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accesstoken,
    ];
  
    $ch = curl_init($urlEndpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
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

if($userID == $userIddb){
    if($text == 'disable detection'){
        $params = array($userID);
        $sql = " DELETE FROM TmstLineUserIdCustomer WHERE userID = ? ";
        $stmt = sqlsrv_query($conn, $sql, $params);
        if($stmt === false){
        die(print_r( sqlsrv_errors(), true));  
        }

        $replymsg = replyDisableMessage($userID, $replyToken, $quoteToken);
        $mylogfile = file_put_contents('useriddelete.txt', $userIddb .PHP_EOL, FILE_APPEND | LOCK_EX);
        fclose($mylogfile);
        echo 'Delete Success!';
        die();
        }
        echo 'User is already!';
        die();
}else{
    if($text == 'detection'){
        $params = array($userID, $replyToken, $quoteToken, $timestamp);
        $sql = " INSERT INTO TmstLineUserIdCustomer (userID, replyToken, quoteToken, timestamp) VALUES (?, ?, ?, ?) ";
        $stmt = sqlsrv_query($conn, $sql, $params);
        if( $stmt === false ) {
            die( print_r( sqlsrv_errors(), true));
        }

        $replymsg = replyEnableMessage($userID, $replyToken, $quoteToken);
        $mylogfile = file_put_contents('useridinsert.txt', $userID .PHP_EOL, FILE_APPEND | LOCK_EX);
        fclose($mylogfile);
        echo 'Insert Success!';
        die();
    }
}

$mylogfile = file_put_contents('rawjson.txt', $LineData .PHP_EOL, FILE_APPEND | LOCK_EX);
fclose($mylogfile);


http_response_code(200);

