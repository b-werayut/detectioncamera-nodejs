<?php
session_start();

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

include '../config/db_connection.php';

$pageTitle = "จัดการสถานที่ติดตั้ง";
$locationData = [];
$provinceData = [];
$projectData = [];

try {
    $selectProvince = $conn->prepare("SELECT * FROM Province ORDER BY Name ASC");
    $selectProvince->execute();
    $provinceData = $selectProvince->fetchAll(PDO::FETCH_ASSOC);

    $selectProject = $conn->prepare("SELECT * FROM Project ORDER BY ProjectName ASC");
    $selectProject->execute();
    $projectData = $selectProject->fetchAll(PDO::FETCH_ASSOC);

    $selectLocation = $conn->prepare("
        SELECT
            Address.AddressID,
            Address.Address,
            Address.City,
            Address.ModifiedDate,
            Project.ProjectID,
            Project.ProjectName,
            Province.ProvinceId,
            Province.Name as ProvinceName,
            Province.ProvinceCode
        FROM Address
        INNER JOIN Project ON Address.ProjectID = Project.ProjectID
        INNER JOIN Province ON Address.ProvinceId = Province.ProvinceId
        ORDER BY Address.AddressID DESC
    ");

    $selectLocation->execute();
    $locationData = $selectLocation->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errorMessage = 'Error: ' . $e->getMessage();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="mb-4 mt-3 mt-md-0">
        <h4 class="fw-bold text-dark">สถานที่ติดตั้ง</h4>
        <p class="text-muted">บันทึกและจัดการข้อมูลสถานที่ติดตั้งกล้อง</p>
    </div>

    <?php if(isset($errorMessage)): ?>
        <div class="alert alert-danger shadow-sm rounded-3 border-0 mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= $errorMessage ?>
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="icon-circle me-3"><i class="fas fa-map-marker-alt"></i></div>
                    <div><span class="d-block opacity-75">สถานที่ทั้งหมด</span>
                        <h3 class="m-0 fw-bold"><?php echo count($locationData); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="icon-circle me-3"><i class="fas fa-building"></i></div>
                    <div><span class="d-block opacity-75">จังหวัดที่มีการติดตั้ง</span>
                        <h3 class="m-0 fw-bold">Active</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-3 rounded-4 shadow-sm mb-3">
        <div class="row g-2 mb-3">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 py-2" placeholder="ค้นหาที่อยู่...">
                </div>
            </div>
            <div class="col-md-2 text-end">
                <button class="btn btn-success w-100 py-2" onclick="openAddModal()">
                    <i class="fas fa-plus me-1"></i> เพิ่มสถานที่
                </button>
            </div>
        </div>
        
        <div class="row g-2">
            <div class="col-md-6">
                <select class="form-select" id="filterProject">
                    <option value="all">ทุกโครงการ</option>
                    <?php foreach ($projectData as $p): ?>
                        <option value="<?= htmlspecialchars($p['ProjectName']) ?>"><?= htmlspecialchars($p['ProjectName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="filterProvince">
                    <option value="all">ทุกจังหวัด</option>
                    <?php foreach ($provinceData as $pv): ?>
                        <option value="<?= htmlspecialchars($pv['Name']) ?>"><?= htmlspecialchars($pv['Name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>โครงการ</th>
                        <th class="ps-4">ที่อยู่</th>
                        <th>จังหวัด</th>
                        <th class="text-center">วันที่แก้ไข</th>
                        <th class="text-center pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="locationTableBody">
                    <?php if (count($locationData) > 0) { ?>
                        <?php foreach ($locationData as $location) { ?>
                            <tr class="data-row" 
                                data-project="<?= htmlspecialchars($location['ProjectName']) ?>" 
                                data-province="<?= htmlspecialchars($location['ProvinceName']) ?>">
                                
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <?php echo $location['ProjectName']; ?>
                                    </span>
                                </td>
                                <td class="ps-4 fw-bold text-dark"><?php echo $location['Address']; ?></td>
                                <td><?php echo $location['ProvinceName']; ?></td>
                                <td class="text-center text-muted"><?php echo (new DateTime($location['ModifiedDate']))->format('Y-m-d H:i'); ?></td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-action-edit me-1" onclick="openEditModal(this)"
                                        data-id="<?php echo $location['AddressID']; ?>"
                                        data-address="<?php echo $location['Address']; ?>"
                                        data-province="<?php echo $location['ProvinceId']; ?>"
                                        data-project="<?php echo $location['ProjectID']; ?>">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-danger btn-action-delete"
                                        onclick="openDeleteLocation(<?php echo $location['AddressID']; ?>)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    
                    <tr id="noDataRow" class="<?= count($locationData) > 0 ? 'd-none' : '' ?>">
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-map-marked-alt fa-3x mb-3 text-secondary opacity-25"></i>
                            <br>ไม่พบข้อมูลสถานที่
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

<div class="modal fade" id="locationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTitle">เพิ่มสถานที่ใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="locationForm">
                    <input type="hidden" id="locationId" name="locationId">
                    <input type="hidden" id="action" name="action" value="create">

                    <div class="mb-3">
                        <label class="form-label">โครงการ</label>
                        <select class="form-select" id="projectInput" name="projectId" required>
                            <option value="">-- เลือกโครงการ --</option>
                            <?php foreach ($projectData as $row) { ?>
                                <option value="<?php echo $row['ProjectID']; ?>"><?php echo $row['ProjectName']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ที่อยู่ / จุดติดตั้ง</label>
                        <input type="text" class="form-control" id="addressInput" name="address" required
                            placeholder="ระบุรายละเอียดที่อยู่">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">จังหวัด</label>
                        <select class="form-select" id="provinceInput" name="provinceId" required>
                            <option value="">-- เลือกจังหวัด --</option>
                            <?php foreach ($provinceData as $row) { ?>
                                <option value="<?php echo $row['ProvinceId']; ?>"><?php echo $row['Name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success" onclick="saveLocation()">บันทึกข้อมูล</button>
            </div>
        </div>
    </div>
</div>

<?php include './includes/scripts.php'; ?>