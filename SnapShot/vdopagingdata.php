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
    $selectfolderx = "../eventfolder/{$getparam}/vdo/X/*";
    $selectpicx = glob($selectfolderx);
    $basenamex = array_map('basename', $selectpicx);
    $selectfolder = "../eventfolder/{$getparam}/vdo/*";
    $selectpic = glob($selectfolder);
    $basename = array_map('basename', $selectpic);
    $datasjson = ["vdoname" => $basename, "filedate" => $getparamf, "vdonamex" => $basenamex];
    echo json_encode($datasjson);
}

if (isset($_GET['selectdatas'])) {

    $getselectdatas = $_GET['selectdatas'];
    $parts = explode("_", $getselectdatas);

    // format ต้องเป็น camera_YYYYMMDD_HHMMSS
    if (count($parts) !== 3) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid selectdatas format"]);
        exit;
    }

    $cameraName = $parts[0];
    $datePart = $parts[1];
    $timePart = $parts[2];

    if (strlen($datePart) !== 8 || strlen($timePart) !== 6) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid date/time format"]);
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

    $selectfolder = "../eventfolder/{$cameraName}/{$getselectdatas}/vdo/*.mp4";
    $selectpic = glob($selectfolder) ?: [];
    $basename = array_map('basename', $selectpic);

    echo json_encode([
        "vdonames" => $basename,
        "filedates" => $getparamf
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