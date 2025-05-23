<?php
$serverName = "85.204.247.82,26433";
$userName ='nwlproduction';
$userPassword="Nwl!2563789!";
$dbName = "NWL_Detection";
$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=>$userPassword, "MultipleActiveResultSets"=>true, "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn === false ) {
    die( print_r( sqlsrv_errors(), true));
}

$arr = [];
$query = "SELECT foldername FROM TmstCameraDetectionLogs";
$stmt = sqlsrv_query($conn, $query);
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
    $arr[] = $row['foldername'];
  }

$jsondat = json_encode($arr);
echo $jsondat;
// print_r($arr);

