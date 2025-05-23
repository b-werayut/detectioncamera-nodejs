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
    $selectfolderx = "C:\/FTP/{$getparam}/pic/X/*.jpg";
    $selectpicx = glob($selectfolderx);
    $basenamex = array_map('basename', $selectpicx);
    $selectfolder = "C:\/FTP/{$getparam}/pic/*.jpg";
    $selectpic = glob($selectfolder);
    $basename = array_map('basename', $selectpic);
    $datasjson = ["imgname"=>$basename, "filedate"=>$getparamf, "imgnamex"=>$basenamex];
    echo json_encode($datasjson);
}

 
if(isset($_GET['selectdatas'])){
    $getselectdatas = $_GET['selectdatas'];
    $getparamf = substr($getselectdatas, 13, 4);
    $getparamf .= "-".substr($getselectdatas, 17, 2);
    $getparamf .= "-".substr($getselectdatas, 19, 2);
    $getparamf .= " ".substr($getselectdatas, 22, 2);
    $getparamf .= ":".substr($getselectdatas, 24, 2);
    $getparamf .= ":".substr($getselectdatas, 26, 2);
    $getparamf = date_create($getparamf);
    $getparamf = date_format($getparamf,"d/m/Y เวลา H:i:s น.");
    $selectfolderx = "C:\/FTP/{$getselectdatas}/pic/X/*.jpg";
    $selectpicx = glob($selectfolderx);
    $basenamex = array_map('basename', $selectpicx);
    $selectfolder = "C:\/FTP/{$getselectdatas}/pic/*.jpg";
    $selectpic = glob($selectfolder);
    $basename = array_map('basename', $selectpic);
    $datasjson = ["imgnames"=>$basename, "filedates"=>$getparamf, "imgnamexs"=>$basenamex];
    echo json_encode($datasjson);
}
