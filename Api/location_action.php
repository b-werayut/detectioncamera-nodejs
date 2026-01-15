<?php
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Unknown Error'];

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
            $address = trim($_POST['address'] ?? '');
            $projectId = $_POST['projectId'] ?? '';
            $provinceId = $_POST['provinceId'] ?? '';
            
            if ($address && $projectId && $provinceId) {
                $now = date('Y-m-d H:i:s');
                $sql = "INSERT INTO Address (Address, ProjectID, ProvinceId, CreatedAt, ModifiedDate) 
                        VALUES (?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$address, $projectId, $provinceId, $now, $now]);
                
                $response = ['status' => 'success', 'message' => 'เพิ่มสถานที่สำเร็จ'];
            } else {
                throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
            }
        }

        elseif ($action === 'update') {
            $locationId = $_POST['locationId'] ?? '';
            $address = trim($_POST['address'] ?? '');
            $projectId = $_POST['projectId'] ?? '';
            $provinceId = $_POST['provinceId'] ?? '';

            if ($locationId && $address) {
                $now = date('Y-m-d H:i:s');
                $sql = "UPDATE Address 
                        SET Address = ?, ProjectID = ?, ProvinceId = ?, ModifiedDate = ? 
                        WHERE AddressID = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$address, $projectId, $provinceId, $now, $locationId]);
                
                $response = ['status' => 'success', 'message' => 'แก้ไขข้อมูลสำเร็จ'];
            } else {
                throw new Exception('ข้อมูลไม่ครบถ้วนสำหรับการแก้ไข');
            }
        }

        elseif ($action === 'delete') {
            $locationId = $_POST['locationId'] ?? '';
            
            if ($locationId) {
                $sql = "DELETE FROM Address WHERE AddressID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$locationId]);
                
                if ($stmt->rowCount() > 0) {
                    $response = ['status' => 'success', 'message' => 'ลบข้อมูลสำเร็จ'];
                } else {
                    throw new Exception("ไม่พบข้อมูลที่ต้องการลบ (ID: $locationId) หรือลบไม่สำเร็จ");
                }
            } else {
                throw new Exception('ไม่ได้รับรหัสสถานที่ (Location ID)');
            }
        }
    }

} catch (PDOException $e) {
    http_response_code(500); 
    
    if ($e->getCode() == '23000') {
        $response = [
            'status' => 'error', 
            'message' => 'ไม่สามารถลบได้เนื่องจากข้อมูลนี้ถูกใช้งานอยู่ในส่วนอื่น (เช่น มีกล้องติดตั้งอยู่)'
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