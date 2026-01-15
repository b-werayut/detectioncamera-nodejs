<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$response = ['val' => 0, 'message' => 'Invalid Request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $configPath = dirname(__DIR__) . '/config/db_connection.php'; 
        if (!file_exists($configPath)) {
            $configPath = __DIR__ . '/config/db_connection.php'; 
            if (!file_exists($configPath)) {
                 $configPath = '../config/db_connection.php';
            }
        }
        
        if (file_exists($configPath)) {
             require_once $configPath;
        } else {
             throw new Exception("Config file not found.");
        }

        if (!isset($conn) || !($conn instanceof PDO)) {
            throw new Exception("Database connection failed.");
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $username = trim($data['username'] ?? $_POST['username'] ?? '');
        $password = trim($data['password'] ?? $_POST['password'] ?? '');

        if (!empty($username) && !empty($password)) {
            
            $sql = "SELECT 
                        u.UserId, 
                        u.Username, 
                        u.Firstname, 
                        u.Lastname, 
                        u.isActive, 
                        u.RoleID,
                        u.ProjectID, 
                        r.UserRole
                    FROM Users u
                    LEFT JOIN Role r ON u.RoleID = r.RoleID
                    WHERE u.Username = ? AND u.isActive = 1";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $sqlPass = "SELECT TOP 1 PasswordHash FROM [Password] 
                            WHERE UserId = ? 
                            ORDER BY CreatedAt DESC";
                $stmtPass = $conn->prepare($sqlPass);
                $stmtPass->execute([$user['UserId']]);
                $passData = $stmtPass->fetch(PDO::FETCH_ASSOC);

                if ($passData && password_verify($password, $passData['PasswordHash'])) {
                    
                    session_regenerate_id(true);

                    $_SESSION['UserId'] = $user['UserId'];
                    $_SESSION['Username'] = $user['Username'];
                    $_SESSION['Firstname'] = $user['Firstname'];
                    $_SESSION['Lastname'] = $user['Lastname'];
                    $_SESSION['Fullname'] = $user['Firstname'] . ' ' . $user['Lastname'];
                    
                    $_SESSION['RoleID'] = $user['RoleID'];       // ใช้สำหรับ Check สิทธิ์
                    $_SESSION['ProjectID'] = $user['ProjectID']; // ใช้สำหรับ Filter โครงการ
                    $_SESSION['UserRole'] = $user['UserRole'];   // เก็บชื่อ Role ไว้แสดงผล
                    
                    // --- แก้ปัญหา Session Loop ---
                    $_SESSION['login_time'] = time();
                    $_SESSION['LAST_ACTIVITY'] = time(); 
                    $_SESSION['auth'] = true; 
                    $_SESSION['success'] = "<div>Login Success</div>";

                    $response = ['val' => 3, 'message' => 'Login Success'];

                    // ปิด Session Write เพื่อปลด Lock ไฟล์ Session ทันที
                    session_write_close();

                } else {
                    $response = ['val' => 2, 'message' => 'Wrong Password'];
                }
            } else {
                $response = ['val' => 1, 'message' => 'User not found or Inactive'];
            }

        } else {
             $response = ['val' => 0, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน'];
        }

    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $response = ['val' => 0, 'message' => 'System Error'];
    } catch (Exception $e) {
        error_log("General Error: " . $e->getMessage());
        $response = ['val' => 0, 'message' => 'System Error'];
    }
}

echo json_encode($response);
exit;
?>