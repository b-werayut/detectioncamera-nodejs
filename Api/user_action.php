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
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $firstname = trim($_POST['firstname'] ?? '');
            $lastname = trim($_POST['lastname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $roleId = $_POST['roleId'] ?? null;
            $projectId = !empty($_POST['projectId']) ? $_POST['projectId'] : null;
            
            $rawStatus = $_POST['isActive'] ?? 1;
            $isActive = ($rawStatus === '1' || $rawStatus === 1 || $rawStatus === 'true' || $rawStatus === 'เปิดใช้งาน') ? 1 : 0;

            if (!empty($username) && !empty($password) && !empty($firstname) && !empty($roleId)) {
                
                $conn->beginTransaction();

                try {
                    $checkSql = "SELECT COUNT(*) FROM Users WHERE Username = ?";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->execute([$username]);
                    if ($checkStmt->fetchColumn() > 0) {
                        throw new Exception("Username นี้มีอยู่ในระบบแล้ว");
                    }

                    $now = date('Y-m-d H:i:s');

                    $sqlUser = "INSERT INTO Users 
                                (Username, Firstname, Lastname, PhoneNumber, RoleID, ProjectID, isActive, CreatedAt, ModifiedDate) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $paramsUser = [$username, $firstname, $lastname, $phone, $roleId, $projectId, $isActive, $now, $now];
                    
                    $stmtUser = $conn->prepare($sqlUser);
                    $stmtUser->execute($paramsUser);
                    
                    $newUserId = $conn->lastInsertId();
                    
                    if (!$newUserId) {
                         throw new Exception("ไม่สามารถระบุ UserId ใหม่ได้");
                    }

                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    $sqlPass = "INSERT INTO [Password] (UserId, PasswordHash, CreatedAt, ModifiedDate) 
                                VALUES (?, ?, ?, ?)";
                    $stmtPass = $conn->prepare($sqlPass);
                    $stmtPass->execute([$newUserId, $passwordHash, $now, $now]);

                    $conn->commit();
                    $response = ['status' => 'success', 'message' => 'เพิ่มผู้ใช้งานสำเร็จ'];

                } catch (Exception $ex) {
                    $conn->rollBack();
                    throw $ex;
                }

            } else {
                throw new Exception("กรุณากรอกข้อมูลให้ครบถ้วน");
            }
        }

        elseif ($action === 'update') {
            $id = $_POST['userId'] ?? '';
            $firstname = trim($_POST['firstname'] ?? '');
            $lastname = trim($_POST['lastname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $roleId = $_POST['roleId'] ?? null;
            $projectId = !empty($_POST['projectId']) ? $_POST['projectId'] : null;
            $password = $_POST['password'] ?? ''; 
            
            $rawStatus = $_POST['isActive'] ?? 1;
            $isActive = ($rawStatus === '1' || $rawStatus === 1 || $rawStatus === 'true' || $rawStatus === 'เปิดใช้งาน') ? 1 : 0;

            if (!empty($id) && !empty($firstname)) {
                $now = date('Y-m-d H:i:s');
                
                $conn->beginTransaction();

                try {
                    $sqlUser = "UPDATE Users 
                                SET Firstname = ?, Lastname = ?, PhoneNumber = ?, RoleID = ?, ProjectID = ?, isActive = ?, ModifiedDate = ? 
                                WHERE UserId = ?";
                    $paramsUser = [$firstname, $lastname, $phone, $roleId, $projectId, $isActive, $now, $id];
                    
                    $stmtUser = $conn->prepare($sqlUser);
                    $stmtUser->execute($paramsUser);

                    if (!empty($password)) {
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        
                        $checkPassSql = "SELECT COUNT(*) FROM [Password] WHERE UserId = ?";
                        $checkPassStmt = $conn->prepare($checkPassSql);
                        $checkPassStmt->execute([$id]);
                        
                        if ($checkPassStmt->fetchColumn() > 0) {
                            $sqlPass = "UPDATE [Password] 
                                        SET PasswordHash = ?, ModifiedDate = ? 
                                        WHERE UserId = ?";
                            $stmtPass = $conn->prepare($sqlPass);
                            $stmtPass->execute([$passwordHash, $now, $id]);
                        } else {
                            $sqlPass = "INSERT INTO [Password] (UserId, PasswordHash, CreatedAt, ModifiedDate) 
                                        VALUES (?, ?, ?, ?)";
                            $stmtPass = $conn->prepare($sqlPass);
                            $stmtPass->execute([$id, $passwordHash, $now, $now]);
                        }
                    }

                    $conn->commit();
                    $response = ['status' => 'success', 'message' => 'แก้ไขข้อมูลสำเร็จ'];

                } catch (Exception $ex) {
                    $conn->rollBack();
                    throw $ex;
                }

            } else {
                throw new Exception("ข้อมูลไม่ครบถ้วน");
            }
        }
        
        elseif ($action === 'delete') {
            $id = $_POST['userId'] ?? '';
            
            if (!empty($id)) {
                $conn->beginTransaction();
                try {
                    $sqlPass = "DELETE FROM [Password] WHERE UserId = ?";
                    $stmtPass = $conn->prepare($sqlPass);
                    $stmtPass->execute([$id]);

                    // 2. ลบจากตาราง [Users] (แม่)
                    $sqlUser = "DELETE FROM Users WHERE UserId = ?";
                    $stmtUser = $conn->prepare($sqlUser);
                    $stmtUser->execute([$id]);

                    $conn->commit();
                    $response = ['status' => 'success', 'message' => 'ลบข้อมูลสำเร็จ'];

                } catch (Exception $ex) {
                    $conn->rollBack();
                    // เช็ค Foreign Key Error
                    if ($ex instanceof PDOException && $ex->getCode() == '23000') {
                        throw new Exception("ไม่สามารถลบผู้ใช้งานนี้ได้ เนื่องจากข้อมูลถูกใช้อยู่ในระบบ");
                    }
                    throw $ex;
                }
            } else {
                throw new Exception("ไม่พบ ID ที่ต้องการลบ");
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