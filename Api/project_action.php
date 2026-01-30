<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

$response = ['status' => 'error', 'message' => 'Invalid Request'];

try {
    // 1. ตรวจสอบไฟล์ Config
    $configPath = dirname(__DIR__) . '/config/db_connection.php';
    if (!file_exists($configPath)) {
        throw new Exception("หาไฟล์ Config ไม่เจอ: $configPath");
    }
    require_once $configPath;

    if (!isset($conn)) {
        throw new Exception("Database connection failed.");
    }

    date_default_timezone_set('Asia/Bangkok');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $projectName = trim($_POST['projectName'] ?? '');
            
            if (!empty($projectName)) {
                $now = date('Y-m-d H:i:s');

                $sqlMax = "SELECT MAX(CAST(ProjectCode AS INT)) as LastCode 
                           FROM Project 
                           WHERE ISNUMERIC(ProjectCode) = 1";
                $stmtMax = $conn->prepare($sqlMax);
                $stmtMax->execute();
                $rowMax = $stmtMax->fetch(PDO::FETCH_ASSOC);

                $nextCode = 1001; 
                $lastCode = 0;
                if ($rowMax && !empty($rowMax['LastCode'])) {
                    $lastCode = (int)$rowMax['LastCode'];
                }

                // ถ้าค่าล่าสุด น้อยกว่า 1000 ให้เริ่มใหม่ที่ 1001
                // แต่ถ้าค่าล่าสุด คือ 1001 ขึ้นไปแล้ว ให้บวกเพิ่มทีละ 1
                if ($lastCode < 1000) {
                    $nextCode = 1001; 
                } else {
                    $nextCode = $lastCode + 1;
                }
                
                $sql = "INSERT INTO Project (ProjectCode, ProjectName, CreatedAt, ModifiedDate) 
                        VALUES (?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt->execute([$nextCode, $projectName, $now, $now])) {
                    $response = ['status' => 'success', 'message' => 'เพิ่มข้อมูลสำเร็จ (Code: ' . $nextCode . ')'];
                } else {
                    throw new Exception("บันทึกข้อมูลไม่สำเร็จ");
                }
            } else {
                throw new Exception("กรุณาระบุชื่อโครงการ");
            }
        }

        elseif ($action === 'update') {
            $projectId = $_POST['projectId'] ?? '';
            $projectName = trim($_POST['projectName'] ?? '');

            if (!empty($projectId) && !empty($projectName)) {
                $now = date('Y-m-d H:i:s');

                $sql = "UPDATE Project 
                        SET ProjectName = ?, ModifiedDate = ? 
                        WHERE ProjectID = ?";
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt->execute([$projectName, $now, $projectId])) {
                    $response = ['status' => 'success', 'message' => 'แก้ไขข้อมูลสำเร็จ'];
                } else {
                    throw new Exception("แก้ไขข้อมูลไม่สำเร็จ");
                }
            } else {
                throw new Exception("ข้อมูลไม่ครบถ้วน (ID หรือ Name หายไป)");
            }
        }

        elseif ($action === 'delete') {
            $projectId = $_POST['projectId'] ?? '';

            if (!empty($projectId)) {
                $sql = "DELETE FROM Project WHERE ProjectID = ?";
                $stmt = $conn->prepare($sql);
                
                $stmt->execute([$projectId]);

                if ($stmt->rowCount() > 0) {
                    $response = ['status' => 'success', 'message' => 'ลบข้อมูลสำเร็จ'];
                } else {
                    throw new Exception("ลบข้อมูลไม่สำเร็จ หรือไม่พบ ID นี้");
                }
            } else {
                throw new Exception("ไม่พบ ID ที่ต้องการลบ");
            }
        }
    }

} catch (PDOException $e) {
    http_response_code(500);
    
    // จัดการ Error Code 23000 ให้ชัดเจนขึ้น
    if ($e->getCode() == '23000') {
        // ถ้าเป็นการลบ แล้วติด 23000 แปลว่าติด Foreign Key
        if (isset($action) && $action === 'delete') {
            $message = 'ไม่สามารถลบโครงการนี้ได้ เนื่องจากมีการใช้งานอยู่ในส่วนอื่น (Address/Users/Camera)';
        } else {
            // ถ้าเพิ่ม/แก้ไข แล้วติด 23000 แปลว่าข้อมูลซ้ำ (Unique Key)
            $message = 'ชื่อโครงการ หรือ รหัสโครงการ มีอยู่ในระบบแล้ว';
        }

        $response = [
            'status' => 'error', 
            'message' => $message
        ];
    } else {
        $response = ['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()];
    }

} catch (Exception $e) {
    http_response_code(500);
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
exit;
?>