<?php
session_start();

if (empty($_SESSION['Username'])) {
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo "unauthorized";
        exit;
    }
    header("Location: ../login.php");
    exit();
}

$roleId = $_SESSION['RoleID'] ?? 0;
if ($roleId != 1 && $roleId != 2) {
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo "forbidden";
        exit;
    }
    echo "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    echo "<br><a href='../livenotifyvideo/index.php'>กลับหน้า Streaming</a>";
    exit();
}

include '../config/db_connection.php';

$pageTitle = "การแจ้งเตือน";
$users = [];

try {
    $sql = "SELECT 
                u.UserId, 
                u.Username, 
                u.Firstname, 
                u.Lastname, 
                u.LineNotifyActive,
                p.ProjectName
            FROM Users u
            LEFT JOIN Project p ON u.ProjectID = p.ProjectID
            ORDER BY u.UserId ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errorMessage = "Database Error: " . $e->getMessage();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    
    <div class="mb-4 mt-3 mt-md-0">
        <h4 class="fw-bold text-dark">การแจ้งเตือน</h4>
        <p class="text-muted">ระบบแจ้งเตือนอัตโนมัติผ่าน LINE Official Account เมื่อ AI ตรวจพบเหตุการณ์</p>
    </div>

    <?php if(isset($errorMessage)): ?>
        <div class="alert alert-danger shadow-sm rounded-3 border-0 mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= $errorMessage ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-3 rounded-4 shadow-sm mb-4">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 ps-3 border"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="searchInput" class="form-control border-start-0 py-2" placeholder="ค้นหา ชื่อผู้ใช้ ชื่อ-นามสกุล หรือ โครงการ">
        </div>
    </div>

    <div class="bg-white rounded-4 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-5 py-3 fw-bold text-secondary">ชื่อผู้ใช้</th>
                        <th class="fw-bold text-secondary">ชื่อ - นามสกุล</th>
                        <th class="fw-bold text-secondary">โครงการ</th>
                        <th class="fw-bold text-secondary text-center">สถานะการแจ้งเตือน</th>
                    </tr>
                </thead>
                <tbody class="bg-white" id="alertTableBody">
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                        <?php 
                            $isActive = ($user['LineNotifyActive'] ?? 0) == 1;
                            $fullName = $user['Firstname'] . ' ' . $user['Lastname'];
                        ?>
                        <tr class="data-row border-bottom-light">
                            <td class="ps-5 text-muted fw-bold"><?= htmlspecialchars($user['Username']) ?></td>
                            <td class="text-dark"><?= htmlspecialchars($fullName) ?></td>
                            <td class="text-muted">
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-normal">
                                    <?= htmlspecialchars($user['ProjectName'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input custom-switch" type="checkbox" 
                                           id="switch_<?= $user['UserId'] ?>" 
                                           <?= $isActive ? 'checked' : '' ?>
                                           onchange="toggleNotification(this, '<?= htmlspecialchars($fullName) ?>', <?= $user['UserId'] ?>)">
                                    <label class="form-check-label" for="switch_<?= $user['UserId'] ?>"></label>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <tr id="noDataRow" class="<?= count($users) > 0 ? 'd-none' : '' ?>">
                         <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-search fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="m-0">ไม่พบรายชื่อที่ค้นหา</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end p-3">
             <nav><ul class="pagination mb-0"><li class="page-item active"><a class="page-link bg-success border-success" href="#">1</a></li></ul></nav>
        </div>
    </div>
    
    <div class="text-center mt-5 text-muted small">© 2025 Powered by NetworkLink</div>
</div>

<style>
.custom-switch { width: 3em; height: 1.5em; cursor: pointer; }
.custom-switch:checked { background-color: #198754; border-color: #198754; }
</style>

<?php include './includes/scripts.php'; ?>