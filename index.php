<style>
    * {
        background-color: black;
        color: white;
    }
</style>
<?php
$myfile = fopen("C:/inetpub/wwwroot/camera/config.txt", "r") or die("Unable to open file!");
$myfile = fread($myfile, filesize("C:/inetpub/wwwroot/camera/config.txt"));
$configs = json_decode($myfile, true);

$serverName = "10.12.12.27";
// $serverName = "85.204.247.82,26433"; // external sql ip
$userName ='nwlproduction';
$userPassword="Nwl!2563789!";
$dbName = "NWL_Detection";
$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=>$userPassword, "MultipleActiveResultSets"=>true, "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if( $conn === false ){
    die( print_r( sqlsrv_errors(), true));
}

$userID = [];
$query = "SELECT userID FROM TmstLineUserIdCustomer";
$stmt = sqlsrv_query($conn, $query);
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
    $userID[] = $row['userID'];
}

if (isset($_GET['point']) && isset($_GET['val'])){
    $Getpoint = $_GET['point'];
    $val = $_GET['val'];
    $Getval = $_GET['val'];
    $Getvaldb = $_GET['val'];

    ($Getvaldb == 1 ? $Getvaldb = "PowerOn" : ($Getvaldb == 2 ? $Getvaldb = "PowerOff" : null));
    ($Getval == 1 ? $Getval = "ไฟฟ้าปกติ" : ($Getval == 2 ? $Getval = "ไฟฟ้าขัดข้อง!" : null));
    ($Getval == 1 ? $badge = "ปกติ" : ($Getval == 2 ? $badge = "ขัดข้อง!" : null));

    $title = "ระบบเช็คกระแสไฟฟ้า";
    $point = "จุดที่: {$Getpoint}";
    $status = "{$Getval}";
    $msg = "$point\n$status";
    // echo $msg;
    // $boardcastMessage = "$title\nจุดที่: {$Getpoint} \nสถานะ: {$Getval}";
    $output = boardCastMessage($title, $point, $status, $configs['lineurlendpointpower'], $configs['tokenpowerdetect'], $configs['urllocation'], $userID);
    echo  $output;
    //Logs Record
    date_default_timezone_set("Asia/Bangkok");
    $t = date("l j F Y h:i:s");
    $ipadd = $_SERVER['REMOTE_ADDR'];
    $txt = "Point: {$Getpoint} \nValue: {$Getval} \nTimestamp: {$t} \nIp-address: {$ipadd}\nDetail: - \n_______________________________________________\n";
    $mylogfile = file_put_contents('logs.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);

    //Insert DB
    $newdt = new DateTime("now", new DateTimeZone("Asia/Bangkok"));
    $dtfm = $newdt->format('Y-m-d:H:i:s');
    $insertdb = InsertDB($Getpoint, $Getvaldb, $val, $dtfm);
    // echo $insertdb;
} else {
    echo "No Param";
}

function boardCastMessage($title, $point, $status, $lineApiEndpoint, $lineAccessToken, $urldest, $userID){
    $url = $lineApiEndpoint;
    if ($status == "ไฟฟ้าปกติ") {
        $badge = "ปกติ";
        $colorStat = "#5CB338";
        $img = "https://www.centrecities.com/assets/icon/poweron.png";
    } else if ($status == "ไฟฟ้าขัดข้อง!") {
        $badge = "ขัดข้อง!";
        $colorStat = "#BF3131";
        $img = "https://www.centrecities.com/assets/icon/poweroff.png";
    }

    $data = [
    "to" => $userID,
    'messages' => [
        [
            'type' => 'flex',
            'altText' => 'ข้อมูลเพิ่มเติม',
            'contents' => [
                'type' => 'bubble',
                "size" => "mega",
                "styles" => [
                    "header" => [
                        "backgroundColor" => "#FAFAFA"
                    ],
                    "body" => [
                        "backgroundColor" => "#FFFFFF"
                    ],
                    "footer" => [
                        "backgroundColor" => "#F0F0F0"
                    ]
                ],
                "header" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "spacing" => "sm",
                    "contents" => [
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "text",
                                    "text" => $badge,
                                    "size" => "xs",
                                    "color" => "#ffffff",
                                    "align" => "center",
                                    "gravity" => "center"
                                ]
                            ],
                            "backgroundColor" => $colorStat,
                            "paddingAll" => "2px",
                            "paddingStart" => "4px",
                            "paddingEnd" => "4px",
                            "flex" => 0,
                            "position" => "absolute",
                            "offsetStart" => "18px",
                            "offsetTop" => "18px",
                            "cornerRadius" => "100px",
                            "width" => "60px",
                            "height" => "25px"
                        ],
                        [
                            "type" => "text",
                            "text" => "แจ้งเตือน!",
                            "size" => "xxl",
                            "weight" => "bold",
                            "wrap" => true,
                            "align" => "center",
                            "color" => "#1E1E1E"
                        ],
                        [
                            "type" => "text",
                            "text" => $title,
                            "size" => "lg",
                            "wrap" => true,
                            "align" => "center",
                            "color" => "#333333"
                        ],
                        [
                            "type" => "separator",
                            "margin" => "md"
                        ]
                    ]
                ],
                "hero" => [
                    "type" => "image",
                    "url" => $img,
                    "size" => "full",
                    "aspectRatio" => "2:1",
                ],
                "body" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "spacing" => "md",
                    "contents" => [
                        [
                            "type" => "text",
                            "text" => "📍 สถานที่",
                            "size" => "lg",
                            "align" => "center",
                            "weight" => "bold",
                            "color" => "#5D4037"
                        ],
                        [
                            "type" => "text",
                            "text" => "หาดแม่รำพึงจุดที่ 1",
                            "size" => "lg",
                            "align" => "center",
                            "weight" => "bold",
                            "color" => "#1E1E1E"
                        ],
                        [
                            "type" => "separator"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "spacing" => "md",
                            "contents" => [
                                [
                                    "type" => "text",
                                    "text" => "สถานะ:",
                                    "size" => "md",
                                    "weight" => "bold",
                                    "color" => "#757575",
                                    "flex" => 2
                                ],
                                [
                                    "type" => "text",
                                    "text" => $status,
                                    "size" => "md",
                                    "weight" => "bold",
                                    "color" => $colorStat,
                                    "align" => "end",
                                    "flex" => 4
                                ]
                            ]
                        ]
                    ]
                ],
                "footer" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "spacing" => "md",
                    "contents" => [
                        [
                            "type" => "button",
                            "style" => "primary",
                            "color" => "#4CAF50",
                            "action" => [
                                "type" => "uri",
                                "label" => "ดูข้อมูลเพิ่มเติม",
                                "uri" => $urldest
                            ],
                            "height" => "sm"
                        ],
                        [
                            "type" => "text",
                            "text" => "อัปเดตล่าสุด: " . date('d/m/Y H:i'),
                            "size" => "xs",
                            "color" => "#999999",
                            "align" => "center"
                        ]
                    ]
                ]
            ]
        ]
    ]
];

    $post = json_encode($data);
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $lineAccessToken,
    ];

    $ch = curl_init($url);
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

function InsertDB($point, $Getvaldb, $val, $timestamp){
    $datas = [];
    $datas['point'] = $point;
    $datas['status'] = $Getvaldb;
    $datas['val'] = $val;
    $datas['timestamp'] = $timestamp;

    $point = $datas['point'];
    $status = $datas['status'];
    $val = $datas['val'];
    $timestamp = $datas['timestamp'];

    $url = "http://localhost:3000/api/powerlogs/{$point}/$Getvaldb/{$val}/{$timestamp}";
    // echo $url;
    $opts = array(
        'http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded'
        )
    );
    $context  = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);
    return $result;
}

http_response_code(200);
exit;