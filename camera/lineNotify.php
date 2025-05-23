<?php
$data = json_decode(file_get_contents("php://input"), true);
$myfile = fopen("config.txt", "r") or die("Unable to open file!");
$myfile = fread($myfile, filesize("config.txt"));
$configs = json_decode($myfile, true);
fclose($myfile);
$getparam = $data['param'];
$flagFile = "webhook_request.txt";

//SendLine Api Message Fuction
function boardCastMessage($message, $title, $lineApiEndpoint, $lineAccessToken, $urldest, $getparam){
    $url = $lineApiEndpoint;

    //U65bcdfc422a6129e33b6186b6b993b9d => a
    $data = [
        'to' => ["U2b60fdb08235f7dd68fc06c6d5219df5"],
        'messages' => [
            [
                'type' => 'flex',
                'altText' => 'ข้อมูลเพิ่มเติม',
                'contents' => [
                    'type' => 'bubble',
                    "styles" => [
                        "header" => [
                            "backgroundColor" => "#FFFFFF"
                        ],
                        "body" => [
                            "backgroundColor" => "#FFFFFF"
                        ],
                        "footer" => [
                            "backgroundColor" => "#FFFFFF"
                        ]
                    ],
                    "size" => "mega",
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
                                    "text" => "แจ้งเตือน!",
                                    "size" => "xs",
                                    "color" => "#ffffff",
                                    "align" => "center",
                                    "gravity" => "center"
                                  ]
                                ],
                                "backgroundColor" => "#EC3D44",
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
                                "scaling" => true,
                                "weight" => "bold",
                                "wrap" => true,
                                "align" => "center"
                            ],
                            [
                                "type" => "text",
                                "text" => $title,
                                "size" => "lg",
                                "scaling" => true,
                                "wrap" => true,
                                "align" => "center"
                            ],
                            [
                                "type" => "separator"
                            ]
                        ]
                    ],
                    "hero" => [
                        "type" => "image",
                        "url" => "https://www.drrrayong.com/VMS/assets/human-detect.png",
                        "size" => "full",
                        "aspectRatio" => "2:1"
                    ],
                    'body' => [
                        'type' => 'box',
                        'layout' => 'vertical',
                        "spacing" => "md",
                        'contents' => [
                            [
                                'type' => 'text',
                                'text' => "สถานที่",
                                "size" => "lg",
                                "align" => "center",
                                "scaling" => true,
                                "wrap" => true,
                                "weight" => "bold",

                            ],
                            [
                                'type' => 'text',
                                'text' => "หาดแม่รำพึงจุดที่ 1",
                                "size" => "lg",
                                "align" => "center",
                                "scaling" => true,
                                "wrap" => true,
                                "weight" => "bold",
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
                                        'type' => 'text',
                                        'text' => $message,
                                        "size" => "lg",
                                        "align" => "center",
                                        "color" => "#EC3D44",
                                        "scaling" => true,
                                        "wrap" => true,
                                        "weight" => "bold",
                                    ]
                                ]
                            ],
                            [
                                "type" => "separator"
                            ],
                        ]

                    ],
                    "footer" => [
                        "type" => "box",
                        "layout" => "vertical",
                        "contents" => [
                            [
                                "type" => "separator"
                            ],
                            [
                                "type" => "button",
                                "style" => "primary",
                                "color" => "#412500",
                                "action" => [
                                    "type" => "uri",
                                    "label" => ">> คลิกเพื่อดูข้อมูลเพิ่มเติม <<",
                                    "uri" => "{$urldest}?param={$getparam}"
                                ]
                            ]

                        ]
                    ]
                ]
            ]
        ]
    ];

    $post = json_encode($data);
    // print_r($post);
    // print("\n");
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

$newdt = new DateTime("now", new DateTimeZone("Asia/Bangkok") );
$years = date('Y')+ 543;
$week = [ 'อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤษหัสบดี', 'ศุกร์', 'เสาร์'];
$days =  $week[date('w')];
$thmonth = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
$months = $thmonth[date('m')-1];

//boardcast line message api
date_default_timezone_set("Asia/Bangkok");
$title = "ระบบตรวจสอบผู้บุกรุก";
$datesendline = $newdt->format("{$days} j {$months} {$years}");
$timesendline = $newdt->format('H:i:s');
$message = "ตรวจพบผู้ต้องสงสัย!\nวัน{$datesendline}\nเวลา {$timesendline} น.";
$output = boardCastMessage($message, $title,  $configs['lineurlendpointcamera'], $configs['tokencameradetect'], $configs['urllocation'], $getparam);
echo $output;

//SendlineLogs Record
$dtfmlogs = $newdt->format('YmdHis');
$dtfm = $newdt->format('Y-m-d H:i:s');
$txt = ["sendlinelogs"=> $dtfmlogs, "datetimelogs"=> $dtfm];
$txtjson = json_encode($txt);
$filedelay = fopen("delaylogs.txt","w") or die("Cannot open");
fwrite($filedelay, $txtjson); 
fclose($filedelay);
// print_r($txt);





