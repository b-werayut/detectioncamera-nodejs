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

    if (!isset($conn) || !($conn instanceof PDO)) {
        throw new Exception("Database connection failed.");
    }

    date_default_timezone_set('Asia/Bangkok');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'toggle_notify') {
            $userId = $_POST['userId'] ?? '';
            
            $rawStatus = $_POST['status'] ?? '';
            $status = ($rawStatus === 'true' || $rawStatus === 1 || $rawStatus === '1') ? 1 : 0;

            if (!empty($userId)) {
                $now = date('Y-m-d H:i:s');
                
                // ใช้ PDO Prepare Statement
                $sql = "UPDATE Users 
                        SET LineNotifyActive = ?, ModifiedDate = ? 
                        WHERE UserId = ?";
                
                $params = [$status, $now, $userId];
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt->execute($params)) {
                    $response = [
                        'status' => 'success', 
                        'message' => 'อัปเดตสถานะเรียบร้อย',
                        'newState' => $status
                    ];
                } else {
                    throw new Exception("อัปเดตข้อมูลไม่สำเร็จ");
                }
            } else {
                throw new Exception("ไม่พบ User ID");
            }
        }
    }

} catch (PDOException $e) {
    http_response_code(500);
    $response = ['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()];
} catch (Exception $e) {
    http_response_code(500);
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
exit;
?>