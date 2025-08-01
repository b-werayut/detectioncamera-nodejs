<?php
// =====================================================================================
// File Name   : index.php
// Description : [API รับค่าวัดกระแสไฟ งานห้วยใหญ่ เพื่อตรวจสอบกระแสไฟบนป้าย VMS]
// Created By  : Mr. Sivadol Jitcahreon
// Created Date: 05/06/2025
// Version     : 1.0
// Last Modified: 05/06/2025
// Modified By : Mr. Sivadol Jitcharoen
// =====================================================================================
/* Log Create
1. Create file Req. P Yo 06/05/2025
2. Edit Parameter `vol` by Sivadol 06/05/2025
3. Add Function Delete date < -60 Req P Yo by Sivadol 20/05/2025
*/

header('Content-Type: application/json');
// เชื่อมต่อ SQL Server
$serverName = "10.12.12.27,1433";
$userName = 'nwlproduction';
$userPassword = "Nwl!2563789!";
$dbName = "NWL_Detection";
$connectionInfo = array(
    "Database" => $dbName,
    "UID" => $userName,
    "PWD" => $userPassword,
    "MultipleActiveResultSets" => true,
    "CharacterSet" => "UTF-8"
);
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(json_encode(['status' => 'error', 'message' => sqlsrv_errors()]));
}

// รับข้อมูล
$data = $_GET;
if (empty($data)) {
    $data = json_decode(file_get_contents('php://input'), true);
}
$device_code = match ($data['DeviceCode'] ?? null) {
    'Device001' => 'Device002',
    'Device002' => 'Device001',
    default => $data['DeviceCode'] ?? null,
};

// ตรวจสอบพารามิเตอร์

$lines       = $data['Line'] ?? null;
$voltage     = $data['Voltage'] ?? null;
$current     = $data['Current'] ?? null;
$power       = $data['Power'] ?? null;
$energy      = $data['Energy'] ?? null;
$frequency   = $data['Frequency'] ?? null;
$pf          = $data['PF'] ?? null;

if (!$device_code || !$lines || !$voltage || !$current || !$power || !$energy || !$frequency || !isset($pf)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

// เริ่ม Transaction
sqlsrv_begin_transaction($conn);
// ลบข้อมูลเก่ากว่า 60 วัน สำหรับอุปกรณ์นี้
$deleteSql = "DELETE FROM electrical_data WHERE created_at < DATEADD(DAY, -60, GETDATE()) AND device_code = ?";
$deleteStmt = sqlsrv_query($conn, $deleteSql, [$device_code]);

if ($deleteStmt === false) {
    sqlsrv_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => 'Delete failed', 'errors' => sqlsrv_errors()]);
    exit;
}

// วนลูป Insert
$line_array = explode(',', $lines);
$inserted = 0;
foreach ($line_array as $line) {
    $line = trim($line);
    $insertSql = "INSERT INTO electrical_data (device_code, line, voltage, currentel, powerel, energy, frequency, pf)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [$device_code, $line, $voltage, $current, $power, $energy, $frequency, $pf];
    $stmt = sqlsrv_query($conn, $insertSql, $params);

    if ($stmt) {
        $inserted++;
    } else {
        // ถ้ามี insert ผิดพลาด ยกเลิกทั้งหมด
        sqlsrv_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'Insert failed', 'errors' => sqlsrv_errors()]);
        exit;
    }
}

// ยืนยันการทำงาน
sqlsrv_commit($conn);
sqlsrv_close($conn);

// ตอบกลับ
echo json_encode([
    'status' => 'success',
    'inserted' => $inserted,
    'total_lines' => count($line_array)
]);

 /// End 
?>
