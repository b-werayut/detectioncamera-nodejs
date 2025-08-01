<?php

if(isset($_GET['param'])){
    $getparam = $_GET['param'];
    $getparamf = substr($getparam, 13, 4);
    $getparamf .= "-".substr($getparam, 17, 2);
    $getparamf .= "-".substr($getparam, 19, 2);
    $getparamf .= " ".substr($getparam, 22, 2);
    $getparamf .= ":".substr($getparam, 24, 2);
    $getparamf .= ":".substr($getparam, 26, 2);
    $getparamf = date_create($getparamf);
    $getparamf = date_format($getparamf,"d/m/Y เวลา H:i:s น.");
    $selectfolderx = "../eventfolder/{$getparam}/vdo/X/*";
    $selectpicx = glob($selectfolderx);
    $basenamex = array_map('basename', $selectpicx);
    $selectfolder = "../eventfolder/{$getparam}/vdo/*";
    $selectpic = glob($selectfolder);
    $basename = array_map('basename', $selectpic);
    $datasjson = ["vdoname"=>$basename, "filedate"=>$getparamf, "vdonamex"=>$basenamex];
    echo json_encode($datasjson);
}
 
if(isset($_GET['selectdatas'])){
    $getselectdatas = $_GET['selectdatas'];
    $camnameRaw = explode("_", $getselectdatas);
    $cameraName = $camnameRaw[0];
    $getparamf = substr($getselectdatas, 13, 4);
    $getparamf .= "-".substr($getselectdatas, 17, 2);
    $getparamf .= "-".substr($getselectdatas, 19, 2);
    $getparamf .= " ".substr($getselectdatas, 22, 2);
    $getparamf .= ":".substr($getselectdatas, 24, 2);
    $getparamf .= ":".substr($getselectdatas, 26, 2);
    $getparamf = date_create($getparamf);
    $getparamf = date_format($getparamf,"d/m/Y เวลา H:i:s น.");
    // $selectfolderx = "../eventfolder/{$cameraName}/{$getselectdatas}/vdo/X/*.{mp4}";
    // $selectpicx = glob($selectfolderx);
    // $basenamex = array_map('basename', $selectpicx);
    $selectfolder = "../eventfolder/{$cameraName}/{$getselectdatas}/vdo/*.mp4";
    $selectpic = glob($selectfolder);
    $basename = array_map('basename', $selectpic);
    $datasjson = ["vdonames"=>$basename, "filedates"=>$getparamf];
    echo json_encode($datasjson);
}

if(isset($_GET['selectcamval'])){
    $selectcamval = $_GET['selectcamval'];
    $selectfolder = "../eventfolder/{$selectcamval}/*";
    $globfolder = glob($selectfolder);
    $basename = array_map('basename', $globfolder);
    $datasjson = ["datas"=>$basename];
    echo json_encode($datasjson);
}