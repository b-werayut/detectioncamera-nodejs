<?php
// test_conn.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$serverName = "85.204.247.82,27433"; // ใช้ comma คั่นระหว่าง IP และ Port ถูกต้องแล้วสำหรับ sqlsrv
$connectionInfo = array(
    "Database" => "NWL_Detected",
    "UID" => "nwlproduction",
    "PWD" => "Nwl!2563789!", // *** อย่าลืมเปลี่ยนรหัสนี้ในอนาคต ***
    "MultipleActiveResultSets" => true,
    "CharacterSet" => "UTF-8",
    "TrustServerCertificate" => true, // จำเป็นมากสำหรับ Cloud
    "LoginTimeout" => 10 // เพิ่ม Timeout เพื่อไม่ให้รอนานเกินไปถ้า Network หลุด
);

echo "กำลังทดสอบเชื่อมต่อไปยัง $serverName ...<br>";

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    echo "<h3 style='color:red'>การเชื่อมต่อล้มเหลว</h3>";
    echo "<pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
    echo "<hr>";
    echo "<b>จุดที่น่าสงสัยถ้า Error:</b><br>";
    echo "1. Port 27433 อาจจะติด Firewall (Error: TCP Provider / Network related)<br>";
    echo "2. Username/Password ผิด (Error: Login failed for user)<br>";
} else {
    echo "<h3 style='color:green'>เชื่อมต่อสำเร็จ! (Connected Successfully)</h3>";
    
    // ลอง Query เช็คเวอร์ชั่น
    $sql = "SELECT @@VERSION as v";
    $stmt = sqlsrv_query($conn, $sql);
    if($stmt) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo "<b>Server Version:</b> " . $row['v'];
    }
}
?>