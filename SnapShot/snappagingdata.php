<?php

if (isset($_GET['param'])) {
    $getparam = $_GET['param'];
    $getparamf = substr($getparam, 13, 4);
    $getparamf .= "-" . substr($getparam, 17, 2);
    $getparamf .= "-" . substr($getparam, 19, 2);
    $getparamf .= " " . substr($getparam, 22, 2);
    $getparamf .= ":" . substr($getparam, 24, 2);
    $getparamf .= ":" . substr($getparam, 26, 2);
    $getparamf = date_create($getparamf);
    $getparamf = date_format($getparamf, "d/m/Y เวลา H:i:s น.");
    $selectfolderx = "../eventfolder/{$getparam}/pic/X/*.jpg";
    $selectpicx = glob($selectfolderx);
    $basenamex = array_map('basename', $selectpicx);
    $selectfolder = "../eventfolder/{$getparam}/pic/*.jpg";
    $selectpic = glob($selectfolder);
    $basename = array_map('basename', $selectpic);
    $datasjson = ["imgname" => $basename, "filedate" => $getparamf, "imgnamex" => $basenamex];
    echo json_encode($datasjson);
}

if (isset($_GET['selectdatas'])) {

    $getselectdatas = $_GET['selectdatas'];
    $parts = explode("_", $getselectdatas);

    // ป้องกัน index error
    if (count($parts) !== 3) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid selectdatas format"]);
        exit;
    }

    $cameraName = $parts[0];
    $datePart = $parts[1];
    $timePart = $parts[2];

    // ตรวจสอบความยาว
    if (strlen($datePart) !== 8 || strlen($timePart) !== 6) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid date or time format"]);
        exit;
    }

    $datetime = DateTime::createFromFormat(
        'Ymd H:i:s',
        $datePart . ' ' .
        substr($timePart, 0, 2) . ':' .
        substr($timePart, 2, 2) . ':' .
        substr($timePart, 4, 2)
    );

    if (!$datetime) {
        http_response_code(500);
        echo json_encode(["error" => "DateTime parse failed"]);
        exit;
    }

    $getparamf = $datetime->format("d/m/Y เวลา H:i:s น.");

    $selectfolderx = "../eventfolder/{$cameraName}/{$getselectdatas}/pic/X/*.jpg";
    $selectfolder = "../eventfolder/{$cameraName}/{$getselectdatas}/pic/*.jpg";

    $basenamex = array_map('basename', glob($selectfolderx) ?: []);
    $basename = array_map('basename', glob($selectfolder) ?: []);

    echo json_encode([
        "imgnames" => $basename,
        "filedates" => $getparamf,
        "imgnamexs" => $basenamex
    ]);
}



if (isset($_GET['selectcamval'])) {
    $selectcamval = $_GET['selectcamval'];
    $selectfolder = "../eventfolder/{$selectcamval}/*";
    $globfolder = glob($selectfolder);
    $basename = array_map('basename', $globfolder);
    $datasjson = ["datas" => $basename];
    echo json_encode($datasjson);
}