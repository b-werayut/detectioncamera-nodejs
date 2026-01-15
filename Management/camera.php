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

$pageTitle = "จัดการกล้อง";
$cameras = [];
$projectOptions = [];
$totalCameras = 0;
$activeCameras = 0;
$inactiveCameras = 0;

try {
    $sql = "SELECT 
                c.CameraID, 
                c.CameraName, 
                c.Url,  
                c.isActive,
                CONVERT(VARCHAR(19), c.ModifiedDate, 120) as ModifiedDateFixed,
                p.ProjectID,
                p.ProjectName
            FROM Camera c
            LEFT JOIN Project p ON c.ProjectID = p.ProjectID
            ORDER BY c.CameraID DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mDate = $row['ModifiedDateFixed'];
        $row['ModifiedDate'] = '-'; 

        if (!empty($mDate)) {
            $ts = strtotime($mDate);
            if ($ts !== false && $ts > 0) {
                $row['ModifiedDate'] = date('d/m/Y H:i', $ts);
            }
        }
        $cameras[] = $row;
    }

    $sqlProj = "SELECT ProjectID, ProjectName FROM Project ORDER BY ProjectName ASC";
    $stmtProj = $conn->prepare($sqlProj);
    $stmtProj->execute();
    $projectOptions = $stmtProj->fetchAll(PDO::FETCH_ASSOC);

    $totalCameras = count($cameras);
    $activeCameras = count(array_filter($cameras, fn($c) => isset($c['isActive']) && $c['isActive'] == 1));
    $inactiveCameras = count(array_filter($cameras, fn($c) => !isset($c['isActive']) || $c['isActive'] == 0));

} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div>';
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">

    <div class="mb-4 mt-3 mt-md-0">
        <h4 class="fw-bold text-dark">กล้อง</h4>
        <p class="text-muted">บันทึกและจัดการข้อมูลกล้อง URL และการตั้งค่า</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card bg-primary text-white p-3 rounded-4 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block opacity-75">กล้องทั้งหมด</span>
                    <h3 class="m-0 fw-bold"><?= $totalCameras ?></h3>
                </div>
                <div class="icon-circle bg-white bg-opacity-25 text-white">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-success text-white p-3 rounded-4 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block opacity-75">ใช้งานอยู่</span>
                    <h3 class="m-0 fw-bold"><?= $activeCameras ?></h3>
                </div>
                <div class="icon-circle bg-white bg-opacity-25 text-white">
                    <i class="fas fa-video"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-danger text-white p-3 rounded-4 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block opacity-75">ปิดใช้งาน</span>
                    <h3 class="m-0 fw-bold"><?= $inactiveCameras ?></h3>
                </div>
                <div class="icon-circle bg-white bg-opacity-25 text-white">
                    <i class="fas fa-video-slash"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-3 rounded-4 shadow-sm mb-3">
        <div class="row g-2 mb-3">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 py-2" placeholder="ค้นหากล้อง...">
                </div>
            </div>
            <div class="col-md-2 text-end">
                <button class="btn btn-success w-100 py-2" onclick="openCameraModal()">
                    <i class="fas fa-plus me-1"></i> เพิ่มกล้อง
                </button>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-md-6">
                <select class="form-select" id="filterProject">
                    <option value="all" selected>ทุกโครงการ</option>
                    <?php foreach ($projectOptions as $pj): ?>
                    <option value="<?= htmlspecialchars($pj['ProjectName']) ?>"><?= htmlspecialchars($pj['ProjectName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="filterStatus">
                    <option value="all" selected>สถานะทั้งหมด</option>
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
                        <th class="py-3 ps-4">กล้อง</th>
                        <th>Streaming URL</th>
                        <th>โครงการ</th>
                        <th>วันที่แก้ไข</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="cameraTableBody">
                    <?php if (count($cameras) > 0): ?>
                    <?php foreach ($cameras as $cam): ?>
                    <tr class="data-row" 
                        data-project="<?= htmlspecialchars($cam['ProjectName'] ?? '') ?>"
                        data-status="<?= $cam['isActive'] ?? 0 ?>">

                        <td class="ps-4 fw-bold text-dark camera-name"><?= htmlspecialchars($cam['CameraName']) ?></td>

                        <td class="text-secondary text-truncate" style="max-width: 200px;"
                            title="<?= htmlspecialchars($cam['Url']) ?>">
                            <?= htmlspecialchars($cam['Url']) ?>
                        </td>

                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <?= htmlspecialchars($cam['ProjectName'] ?? '-') ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= $cam['ModifiedDate'] ?></td>

                        <td class="text-center">
                            <?php if( ($cam['isActive'] ?? 0) == 1 ): ?>
                            <span class="badge bg-success bg-opacity-25 text-success px-3">เปิดใช้งาน</span>
                            <?php else: ?>
                            <span class="badge bg-secondary bg-opacity-25 text-secondary px-3">ปิดใช้งาน</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center pe-4 text-nowrap">
                            <button class="btn btn-action-edit me-1" onclick="openEditCamera(this)"
                                data-id="<?= $cam['CameraID'] ?>"
                                data-name="<?= htmlspecialchars($cam['CameraName']) ?>"
                                data-url="<?= htmlspecialchars($cam['Url']) ?>" 
                                data-project="<?= $cam['ProjectID'] ?>"
                                data-status="<?= $cam['isActive'] ?? 0 ?>">
                                <i class="fas fa-pen"></i>
                            </button>

                            <button class="btn btn-danger btn-action-delete"
                                onclick="openDeleteCamera(<?= $cam['CameraID'] ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <tr id="noDataRow" class="<?= count($cameras) > 0 ? 'd-none' : '' ?>">
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-search fa-3x mb-3 text-secondary opacity-50"></i>
                            <h5 class="fw-bold">ไม่พบข้อมูล</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <nav>
            <ul class="pagination">
                <li class="page-item active"><a class="page-link bg-success border-success" href="#">1</a></li>
            </ul>
        </nav>
    </div>

    <div class="text-center mt-5 text-muted small">© 2025 Powered by NetworkLink</div>
</div>

<div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="cameraModalTitle">เพิ่มกล้องใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="cameraForm">
                    <input type="hidden" id="cameraId" name="cameraId">
                    <input type="hidden" id="action" name="action" value="create">

                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อกล้อง <span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-sm" id="cameraName" name="cameraName" required
                            placeholder="เช่น CAM-HQ-001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Streaming URL (RTSP) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-sm" id="cameraUrl" name="cameraUrl" required
                            placeholder="rtsp://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">โครงการ <span class="text-danger">*</span></label>
                        <select class="form-select shadow-sm" id="cameraProject" name="projectId" required>
                            <option value="">-- เลือกโครงการ --</option>
                            <?php foreach ($projectOptions as $pj): ?>
                            <option value="<?= $pj['ProjectID'] ?>"><?= htmlspecialchars($pj['ProjectName']) ?></option>
                            <?php endforeach; ?>
                        </select>
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
                <button type="button" class="btn btn-success" onclick="saveCamera()">บันทึกข้อมูล</button>
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