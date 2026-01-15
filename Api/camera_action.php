<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

$response = ['status' => 'error', 'message' => 'Invalid Request'];

try {
    $configPath = dirname(__DIR__) . '/config/db_connection.php';
    if (!file_exists($configPath)) {
        throw new Exception("Config file not found: $configPath");
    }
    require_once $configPath;

    if (!isset($conn)) {
        throw new Exception("Database connection failed.");
    }

    date_default_timezone_set('Asia/Bangkok');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $name = trim($_POST['cameraName'] ?? '');
            $url = trim($_POST['cameraUrl'] ?? '');
            $projectId = $_POST['projectId'] ?? ''; 
            
            $rawStatus = $_POST['isActive'] ?? $_POST['cameraStatus'] ?? '';
            if ($rawStatus === '1' || $rawStatus === 1 || $rawStatus === 'true' || $rawStatus === 'เปิดใช้งาน') {
                $isActive = 1;
            } else {
                $isActive = 0;
            }
            
            if (!empty($name) && !empty($projectId)) {
                $now = date('Y-m-d H:i:s');
                
                $sql = "INSERT INTO Camera 
                        (CameraName, Url, CameraStatus, ProjectID, isActive, CreatedAt, ModifiedDate) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";

                $params = [$name, $url, 1, $projectId, $isActive, $now, $now];
                
                $stmt = $conn->prepare($sql);
                if ($stmt->execute($params)) {
                    $response = ['status' => 'success', 'message' => 'เพิ่มกล้องสำเร็จ'];
                } else {
                    throw new Exception("บันทึกข้อมูลไม่สำเร็จ");
                }
            } else {
                throw new Exception("กรุณาระบุชื่อกล้องและโครงการ");
            }
        }

        elseif ($action === 'update') {
            $id = $_POST['cameraId'] ?? '';
            $name = trim($_POST['cameraName'] ?? '');
            $url = trim($_POST['cameraUrl'] ?? '');
            $projectId = $_POST['projectId'] ?? '';
            
            $rawStatus = $_POST['isActive'] ?? $_POST['cameraStatus'] ?? '';
            if ($rawStatus === '1' || $rawStatus === 1 || $rawStatus === 'true' || $rawStatus === 'เปิดใช้งาน') {
                $isActive = 1;
            } else {
                $isActive = 0;
            }

            if (!empty($id) && !empty($name)) {
                $now = date('Y-m-d H:i:s');
                
                $sql = "UPDATE Camera 
                        SET CameraName = ?, Url = ?, CameraStatus = ?, isActive = ?, ProjectID = ?, ModifiedDate = ? 
                        WHERE CameraID = ?";

                $params = [$name, $url, 1, $isActive, $projectId, $now, $id];

                $stmt = $conn->prepare($sql);
                if ($stmt->execute($params)) {
                    $response = ['status' => 'success', 'message' => 'แก้ไขข้อมูลสำเร็จ'];
                } else {
                    throw new Exception("แก้ไขข้อมูลไม่สำเร็จ");
                }
            } else {
                throw new Exception("ข้อมูลไม่ครบถ้วน (ID หรือ ชื่อกล้อง หายไป)");
            }
        }
        
        elseif ($action === 'delete') {
            $id = $_POST['cameraId'] ?? '';
            
            if (!empty($id)) {
                $sql = "DELETE FROM Camera WHERE CameraID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id]);

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
            'message' => 'ไม่สามารถลบกล้องนี้ได้ เนื่องจากมีการใช้งานอยู่ในส่วนอื่น'
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