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

$pageTitle = "จัดการโครงการ";
$projects = [];

try {
    $sql = "SELECT 
                ProjectID, 
                ProjectName, 
                CONVERT(VARCHAR(19), CreatedAt, 120) as CreatedAtFixed, 
                CONVERT(VARCHAR(19), ModifiedDate, 120) as ModifiedDateFixed
            FROM Project
            ORDER BY CreatedAt DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        if (!empty($row['CreatedAtFixed'])) {
            $ts = strtotime($row['CreatedAtFixed']);
            if ($ts !== false && $ts > 0) {
                $row['CreatedAt'] = date('d/m/Y H:i', $ts);
            } else {
                $row['CreatedAt'] = '-';
            }
        } else {
            $row['CreatedAt'] = '-';
        }

        if (!empty($row['ModifiedDateFixed'])) {
            $ts = strtotime($row['ModifiedDateFixed']);
            if ($ts !== false && $ts > 0) {
                $row['ModifiedDate'] = date('d/m/Y H:i', $ts);
            } else {
                $row['ModifiedDate'] = '-';
            }
        } else {
            $row['ModifiedDate'] = '-';
        }

        $projects[] = $row;
    }

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="mb-4 mt-3 mt-md-0">
        <h4 class="fw-bold text-dark">โครงการ</h4>
        <p class="text-muted">จัดการข้อมูลโครงการหลักของระบบ</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card bg-primary text-white p-3 rounded-4 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block opacity-75">โครงการทั้งหมด</span>
                    <h3 class="m-0 fw-bold"><?= count($projects) ?></h3>
                </div>
                <div class="icon-circle bg-white bg-opacity-25 text-white"><i class="fas fa-folder"></i></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card bg-success text-white p-3 rounded-4 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block opacity-75">ใช้งานล่าสุด</span>
                    <h3 class="m-0 fw-bold"><?= date('d/m/Y') ?></h3>
                </div>
                <div class="icon-circle bg-white bg-opacity-25 text-white"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    <div class="bg-white p-3 rounded-4 shadow-sm mb-3">
        <div class="row g-2">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 py-2" placeholder="ค้นหาชื่อโครงการ...">
                </div>
            </div>
            <div class="col-md-2 text-end">
                <button class="btn btn-success w-100 py-2" onclick="openProjectModal()">
                    <i class="fas fa-plus me-1"></i> เพิ่มโครงการ
                </button>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ชื่อโครงการ</th>
                        <th class="text-center">วันที่สร้าง</th>
                        <th class="text-center">วันที่แก้ไขล่าสุด</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="projectTableBody">
                    <?php if (count($projects) > 0): ?>
                        <?php foreach ($projects as $proj): ?>
                        <tr class="data-row"> 
                            <td class="ps-4 fw-bold text-dark project-name"><?= htmlspecialchars($proj['ProjectName']) ?></td>
                            <td class="text-center text-secondary">
                                <?= $proj['CreatedAt'] ?>
                            </td>
                            <td class="text-center text-muted">
                                <?= $proj['ModifiedDate'] ?>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-action-edit me-1" 
                                    onclick="openEditProject(this)"
                                    data-id="<?= $proj['ProjectID'] ?>"
                                    data-name="<?= htmlspecialchars($proj['ProjectName']) ?>">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn btn-danger btn-action-delete" 
                                    onclick="openDeleteModal(<?= $proj['ProjectID'] ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-25"></i>
                                <br>ไม่พบข้อมูลโครงการในระบบ
                            </td>
                        </tr>
                    <?php endif; ?>
                    
                    <tr id="noDataRow" class="d-none">
                        <td colspan="4" class="text-center py-5 text-muted">
                            ไม่พบข้อมูลที่ค้นหา
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

<div class="modal fade" id="projectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="projectModalTitle">เพิ่มโครงการใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="projectForm">
                    <input type="hidden" id="projectId" name="projectId"> 
                    <input type="hidden" id="action" name="action" value="create">

                    <div class="mb-3">
                        <label class="form-label">ชื่อโครงการ</label>
                        <input type="text" class="form-control" id="projectName" name="projectName" required placeholder="ระบุชื่อโครงการ">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success" onclick="saveProject()">บันทึกข้อมูล</button>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-action-delete.btn-danger {
        background-color: #dc3545; color: white; border: none; width: 35px; height: 35px; border-radius: 8px;
    }
    .btn-action-edit {
        background-color: #f0f0f0; border: none; width: 35px; height: 35px; border-radius: 8px; color: #333;
    }
</style>

<?php include './includes/scripts.php'; ?>