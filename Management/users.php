<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

include '../config/db_connection.php';

if (empty($_SESSION['Username'])) {
    header("Location: ../login.php");
    exit();
}

$roleId = $_SESSION['RoleID'] ?? 0;
if ($roleId != 1 && $roleId != 2) {
    echo "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    echo "<br><a href='../livenotifyvideo/index.php'>กลับหน้า Streaming</a>";
    exit();
}

$pageTitle = "จัดการผู้ใช้งาน";
$users = [];
$roles = [];
$projects = [];
$totalUsers = 0;
$activeUsers = 0;
$inactiveUsers = 0;

try {
    $sql = "SELECT 
                u.UserId, 
                u.Username, 
                u.Firstname, 
                u.Lastname, 
                u.PhoneNumber, 
                u.isActive, 
                u.CreatedAt, 
                u.ModifiedDate, 
                u.RoleID, 
                u.ProjectID, 
                r.UserRole, 
                p.ProjectName,
                pwd.PasswordID
            FROM Users u
            LEFT JOIN Role r ON u.RoleID = r.RoleID
            LEFT JOIN Project p ON u.ProjectID = p.ProjectID
            OUTER APPLY (
                SELECT TOP 1 PasswordID
                FROM Password 
                WHERE UserId = u.UserId 
                ORDER BY CreatedAt DESC
            ) pwd
            ORDER BY u.UserId DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['CreatedAt'])) {
            $row['CreatedAt'] = (new DateTime($row['CreatedAt']))->format('d/m/Y');
        }
        $users[] = $row;
    }

    $roles = $conn->query("SELECT RoleID, UserRole FROM Role ORDER BY UserRole ASC")->fetchAll(PDO::FETCH_ASSOC);
    $projects = $conn->query("SELECT ProjectID, ProjectName FROM Project ORDER BY ProjectName ASC")->fetchAll(PDO::FETCH_ASSOC);

    $totalUsers = count($users);
    $activeUsers = count(array_filter($users, fn($u) => $u['isActive'] == 1));
    $inactiveUsers = $totalUsers - $activeUsers;

} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div>';
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="mb-4 mt-3 mt-md-0">
        <h4 class="fw-bold text-dark">ผู้ใช้งาน</h4>
        <p class="text-muted">จัดการข้อมูลผู้ใช้งาน สิทธิ์การเข้าถึง และโครงการ</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card bg-primary text-white p-3 rounded-4 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block opacity-75">ผู้ใช้ทั้งหมด</span>
                    <h3 class="m-0 fw-bold"><?= $totalUsers ?></h3>
                </div>
                <div class="icon-circle bg-white bg-opacity-25 text-white"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-success text-white p-3 rounded-4 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block opacity-75">ใช้งานปกติ</span>
                    <h3 class="m-0 fw-bold"><?= $activeUsers ?></h3>
                </div>
                <div class="icon-circle bg-white bg-opacity-25 text-white"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-danger text-white p-3 rounded-4 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block opacity-75">ระงับการใช้งาน</span>
                    <h3 class="m-0 fw-bold"><?= $inactiveUsers ?></h3>
                </div>
                <div class="icon-circle bg-white bg-opacity-25 text-white"><i class="fas fa-user-slash"></i></div>
            </div>
        </div>
    </div>

    <div class="bg-white p-3 rounded-4 shadow-sm mb-3">
        <div class="row g-2 mb-3">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 py-2" placeholder="ค้นหาชื่อ, เบอร์โทร...">
                </div>
            </div>
            <div class="col-md-2 text-end">
                <button class="btn btn-success w-100 py-2" onclick="openUserModal()">
                    <i class="fas fa-plus me-1"></i> เพิ่มผู้ใช้
                </button>
            </div>
        </div>
        
        <div class="row g-2">
            <div class="col-md-4">
                <select class="form-select" id="filterRole">
                    <option value="all">สิทธิ์ทั้งหมด</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= htmlspecialchars($r['UserRole']) ?>"><?= htmlspecialchars($r['UserRole']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filterProject">
                    <option value="all">ทุกโครงการ</option>
                    <?php foreach ($projects as $pj): ?>
                        <option value="<?= htmlspecialchars($pj['ProjectName']) ?>"><?= htmlspecialchars($pj['ProjectName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filterStatus">
                    <option value="all">สถานะทั้งหมด</option>
                    <option value="1">เปิดใช้งาน</option>
                    <option value="0">ปิดใช้งาน</option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-card bg-white rounded-4 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4">ชื่อผู้ใช้งาน</th>
                        <th>เบอร์โทร</th>
                        <th>สิทธิ์</th>
                        <th>โครงการ</th>
                        <th>วันที่สร้าง</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $u): ?>
                        <tr class="data-row" 
                            data-role="<?= htmlspecialchars($u['UserRole'] ?? '') ?>" 
                            data-project="<?= htmlspecialchars($u['ProjectName'] ?? '') ?>"
                            data-status="<?= $u['isActive'] ?? 0 ?>">
                            
                            <td class="ps-4">
                                <div class="fw-bold text-dark user-name"><?= htmlspecialchars($u['Username']) ?></div>
                                <div class="text-muted small user-fullname">
                                    <i class="fas fa-user me-1"></i>
                                    <?= htmlspecialchars($u['Firstname'] . ' ' . $u['Lastname']) ?>
                                </div>
                            </td>
                            <td class="user-phone"><?= htmlspecialchars($u['PhoneNumber'] ?? '-') ?></td>
                            <td><span class="badge bg-info bg-opacity-10 text-info px-2"><?= htmlspecialchars($u['UserRole'] ?? '-') ?></span></td>
                            <td><?= htmlspecialchars($u['ProjectName'] ?? '-') ?></td>
                            <td class="text-muted small"><?= $u['CreatedAt'] ?></td>
                            
                            <td class="text-center">
                                <?php if (($u['isActive'] ?? 0) == 1): ?>
                                    <span class="badge bg-success bg-opacity-25 text-success px-3">เปิดใช้งาน</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-25 text-secondary px-3">ปิดใช้งาน</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center pe-4 text-nowrap">
                                <button class="btn btn-action-edit me-1" 
                                    onclick="openEditUser(this)"
                                    data-id="<?= $u['UserId'] ?>"
                                    data-username="<?= htmlspecialchars($u['Username']) ?>"
                                    data-firstname="<?= htmlspecialchars($u['Firstname']) ?>"
                                    data-lastname="<?= htmlspecialchars($u['Lastname']) ?>"
                                    data-phone="<?= htmlspecialchars($u['PhoneNumber']) ?>"
                                    data-role="<?= $u['RoleID'] ?>"
                                    data-project="<?= $u['ProjectID'] ?>"
                                    data-status="<?= $u['isActive'] ?? 0 ?>"
                                    data-password-id="<?= $u['PasswordID'] ?? '' ?>"> <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn btn-danger btn-action-delete" 
                                    onclick="openDeleteUser(<?= $u['UserId'] ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <tr id="noDataRow" class="<?= count($users) > 0 ? 'd-none' : '' ?>">
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3 text-secondary opacity-50"></i>
                            <h5 class="fw-bold">ไม่พบข้อมูล</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-3">
         <nav><ul class="pagination"><li class="page-item active"><a class="page-link bg-success border-success" href="#">1</a></li></ul></nav>
    </div>
    <div class="text-center mt-5 text-muted small">© 2025 Powered by NetworkLink</div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="userModalTitle">เพิ่มผู้ใช้งาน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="userId">
                    <input type="hidden" id="action" name="action" value="create">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-sm" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control shadow-sm" id="password" name="password" placeholder="กำหนดรหัสผ่าน">
                        <div id="passwordHint" class="form-text d-none text-warning">ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">ชื่อจริง</label>
                            <input type="text" class="form-control shadow-sm" id="firstname" name="firstname" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">นามสกุล</label>
                            <input type="text" class="form-control shadow-sm" id="lastname" name="lastname">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">เบอร์โทรศัพท์</label>
                        <input type="text" class="form-control shadow-sm" id="phone" name="phone">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">สิทธิ์ <span class="text-danger">*</span></label>
                            <select class="form-select shadow-sm" id="roleId" name="roleId" required>
                                <option value="">-- เลือก Role --</option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['RoleID'] ?>"><?= htmlspecialchars($r['UserRole']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">โครงการ</label>
                            <select class="form-select shadow-sm" id="projectId" name="projectId">
                                <option value="">-- เลือกโครงการ --</option>
                                <?php foreach ($projects as $pj): ?>
                                    <option value="<?= $pj['ProjectID'] ?>"><?= htmlspecialchars($pj['ProjectName']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">สถานะ</label>
                        <select class="form-select shadow-sm" id="isActive" name="isActive">
                            <option value="1">เปิดใช้งาน</option>
                            <option value="0">ปิดใช้งาน</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success" onclick="saveUser()">บันทึกข้อมูล</button>
            </div>
        </div>
    </div>
</div>

<?php include './includes/scripts.php'; ?>

<style>
.btn-action-delete.btn-danger {
    background-color: #dc3545; color: white; border: none; width: 35px; height: 35px; border-radius: 8px; transition: all 0.2s;
}
.btn-action-edit {
    background-color: #f0f0f0; border: none; width: 35px; height: 35px; border-radius: 8px; color: #333;
}
.btn-action-edit:hover { background-color: #e2e2e2; }
</style>