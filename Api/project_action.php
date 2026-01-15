<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

$response = ['status' => 'error', 'message' => 'Invalid Request'];

try {
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
                
                $sql = "INSERT INTO Project (ProjectName, CreatedAt, ModifiedDate) 
                        VALUES (?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt->execute([$projectName, $now, $now])) {
                    $response = ['status' => 'success', 'message' => 'เพิ่มข้อมูลสำเร็จ'];
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
    
    if ($e->getCode() == '23000') {
        $response = [
            'status' => 'error', 
            'message' => 'ไม่สามารถลบโครงการนี้ได้ เนื่องจากมีการใช้งานอยู่ในส่วนอื่น (Address/Users/Camera)'
        ];
    } else {
        $response = ['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()];
    }

} catch (Exception $e) {
    // Error ทั่วไป
    http_response_code(500);
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
exit;
?>