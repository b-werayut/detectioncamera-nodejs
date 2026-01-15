<?php
session_start();
include '../config/db_connection.php';

try {
    $selectProvince = $conn->prepare("SELECT * FROM Province");
    $selectProvince->execute();
    $provinceData = $selectProvince->fetchAll(PDO::FETCH_ASSOC);

    $selectProject = $conn->prepare("SELECT * FROM Project");
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
    Province.Name,
    Province.ProvinceCode
FROM Address
INNER JOIN Project
    ON Address.ProjectID = Project.ProjectID
INNER JOIN Province
    ON Address.ProvinceId = Province.ProvinceId;
");

    $selectLocation->execute();
    $locationData = $selectLocation->fetchAll(PDO::FETCH_ASSOC);

    // foreach ($locationData as $locate) {
    //     echo $locate["Name"] . "<br>";
    // }
    // ;

    // print_r($rows);

    // $queryEmail = $conn->prepare(" SELECT * FROM Users WHERE email = ? ");
    // $queryEmail->execute(array($email));
    // $rowsemail = $queryEmail->fetch(PDO::FETCH_ASSOC);

    // $usernamedb = $rows['username'] ?? NULL;
    // $emaildb = $rowsemail['email'] ?? NULL;

    // if ($username == $usernamedb) {
    //     echo 1;
    //     return;
    // } else if ($email == $emaildb) {
    //     echo 4;
    //     return;
    // } else {
    //     $query = $conn->prepare(" INSERT INTO Users (username, password, email, role, isActive, updatedAt) VALUES (?, ?, ?, ?, ?, ?) ");
    //     $query->execute(array($username, $passwordenc, $email, $role, $active, $dateNow));

    //     if ($query->rowCount()) {
    //         echo 2;
    //         return;
    //     } else {
    //         echo 3;
    //         return;
    //     }
    //     return;
    // }
} catch (PDOException $e) {
    echo 'Error' . $e->getMessage();
}

// ----------------------------------------------------------------------
// ส่วนที่ 1: PHP Process (จัดการรับค่า POST เพื่อ บันทึก/แก้ไข ข้อมูล)
// ----------------------------------------------------------------------
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // รับค่าจาก AJAX
//     $action = $_POST['action'] ?? '';
//     $locationId = $_POST['locationId'] ?? null;
//     $projectId = $_POST['projectId'];
//     $address = trim($_POST['address']);
//     $provinceId = $_POST['provinceId'];
//     $dateNow = date('Y-m-d H:i:s'); // สำหรับ SQL Server

//     // 1. เช็คว่าชื่อสถานที่ซ้ำหรือไม่?
//     // ตัด prefix DB ออก เหลือแค่ชื่อตาราง Address
//     $sqlCheck = "SELECT AddressID FROM Address WHERE Address = ? AND AddressID != ?";

//     // ถ้าเป็นการเพิ่มใหม่ locationId จะเป็น null ให้ใส่ 0 แทน
//     $checkId = $locationId ? $locationId : 0;

//     $paramsCheck = array($address, $checkId);
//     $stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck, array("Scrollable" => SQLSRV_CURSOR_KEYSET));

//     if ($stmtCheck === false) {
//         echo "ErrorDB: " . print_r(sqlsrv_errors(), true);
//         exit;
//     }

//     if (sqlsrv_num_rows($stmtCheck) > 0) {
//         echo 1; // เจอชื่อซ้ำ
//         exit;
//     }

//     // 2. ถ้าไม่ซ้ำ ให้ทำขั้นตอนต่อไป
//     if ($action == 'create') {
//         // --- เพิ่มข้อมูลใหม่ ---
//         $sqlInsert = "INSERT INTO Address (ProjectID, Address, ProvinceId, ModifiedDate) 
//                       VALUES (?, ?, ?, ?)";
//         $paramsInsert = array($projectId, $address, $provinceId, $dateNow);

//         $stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);

//         if ($stmtInsert) {
//             echo "success";
//         } else {
//             echo "ErrorInsert: " . print_r(sqlsrv_errors(), true);
//         }

//     } elseif ($action == 'edit' && $locationId) {
//         // --- แก้ไขข้อมูลเดิม ---
//         $sqlUpdate = "UPDATE Address 
//                       SET ProjectID = ?, Address = ?, ProvinceId = ?, ModifiedDate = ? 
//                       WHERE AddressID = ?";
//         $paramsUpdate = array($projectId, $address, $provinceId, $dateNow, $locationId);

//         $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $paramsUpdate);

//         if ($stmtUpdate) {
//             echo "success";
//         } else {
//             echo "ErrorUpdate: " . print_r(sqlsrv_errors(), true);
//         }
//     }

//     exit;
// }

// // ----------------------------------------------------------------------
// // ส่วนที่ 2: PHP View (ดึงข้อมูลมาแสดงผลในตาราง)
// // ----------------------------------------------------------------------

// $pageTitle = "จัดการสถานที่ติดตั้ง";

// // ดึงข้อมูลสถานที่ทั้งหมด (ตัด prefix DB ออกแล้ว)
// $locations = [];
// $sql = "SELECT 
//             a.AddressID, 
//             a.Address, 
//             a.ModifiedDate,
//             pj.ProjectID,
//             pj.ProjectName,
//             pv.ProvinceId,
//             pv.Name AS ProvinceName
//         FROM Address a
//         LEFT JOIN Project pj ON a.ProjectID = pj.ProjectID
//         LEFT JOIN Province pv ON a.ProvinceId = pv.ProvinceId
//         ORDER BY a.ModifiedDate DESC";

// $stmt = sqlsrv_query($conn, $sql);

// if ($stmt === false) {
//     die(print_r(sqlsrv_errors(), true));
// }

// while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
//     if (isset($row['ModifiedDate']) && $row['ModifiedDate'] instanceof DateTime) {
//         $row['ModifiedDate'] = $row['ModifiedDate']->format('d/m/Y H:i');
//     }
//     $locations[] = $row;
// }

// // ดึงข้อมูล Project
// $projectOptions = [];
// $sqlProj = "SELECT ProjectID, ProjectName FROM Project ORDER BY ProjectName ASC";
// $stmtProj = sqlsrv_query($conn, $sqlProj);
// if ($stmtProj !== false) {
//     while ($row = sqlsrv_fetch_array($stmtProj, SQLSRV_FETCH_ASSOC)) {
//         $projectOptions[] = $row;
//     }
// }

// // ดึงข้อมูล Province
// $provinceOptions = [];
// $sqlProv = "SELECT ProvinceId, Name FROM Province ORDER BY Name ASC";
// $stmtProv = sqlsrv_query($conn, $sqlProv);
// if ($stmtProv !== false) {
//     while ($row = sqlsrv_fetch_array($stmtProv, SQLSRV_FETCH_ASSOC)) {
//         $provinceOptions[] = $row;
//     }
// }

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="mb-4 mt-3 mt-md-0">
        <h4 class="fw-bold text-dark">สถานที่ติดตั้ง</h4>
        <p class="text-muted">บันทึกและจัดการข้อมูลสถานที่ติดตั้งกล้อง</p>
    </div>

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

    <div class="row g-2 mb-3">
        <div class="col-md-10">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 ps-3"><i
                        class="fas fa-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 py-2" placeholder="ค้นหา">
            </div>
        </div>
        <div class="col-md-2 text-end">
            <button class="btn btn-success w-100 py-2" onclick="openAddModal()">
                <i class="fas fa-plus me-1"></i> เพิ่มสถานที่
            </button>
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
                    <?php
                    $countTable = count($locationData);
                    if ($countTable > 0) { ?>
                        <?php foreach ($locationData as $location) { ?>
                            <tr class="data-row">
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <?php echo $location['ProjectName']; ?>
                                    </span>
                                </td>
                                <td class="ps-4"><?php echo $location['Address']; ?></td>
                                <td><?php echo $location['Name']; ?></td>
                                <td class="text-center text-muted"><?php echo $location['ModifiedDate']; ?></td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-action-edit me-1" onclick="openEditModal(this)"
                                        data-id="<?php echo $location['AddressID']; ?>"
                                        data-address="<?php echo $location['Address']; ?>"
                                        data-province="<?php echo $location['ProvinceId']; ?>"
                                        data-project="<?php echo $location['ProjectID']; ?>">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-action-delete"
                                        onclick="openDeleteLocation(<?php echo $location['AddressID']; ?>)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php }
                        ; ?>
                    <?php } else { ?>
                        <tr id="noDataRow">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-map-marked-alt fa-3x mb-3 text-secondary opacity-25"></i>
                                <br>ไม่พบข้อมูลสถานที่
                            </td>
                        </tr>
                    <?php }
                    ; ?>
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
                            <?php }
                            ; ?>
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

<!-- <script>
    function openAddModal() {
        document.getElementById('locationForm').reset();
        document.getElementById('locationId').value = '';
        document.getElementById('action').value = 'create';
        document.getElementById('modalTitle').innerText = 'เพิ่มสถานที่ใหม่';
        new bootstrap.Modal(document.getElementById('locationModal')).show();
    }

    function openEditModal(btn) {
        var id = btn.getAttribute('data-id');
        var address = btn.getAttribute('data-address');
        var province = btn.getAttribute('data-province');
        var project = btn.getAttribute('data-project');

        document.getElementById('locationId').value = id;
        document.getElementById('addressInput').value = address;
        document.getElementById('provinceInput').value = province;
        document.getElementById('projectInput').value = project;
        document.getElementById('action').value = 'edit';
        document.getElementById('modalTitle').innerText = 'แก้ไขสถานที่';

        new bootstrap.Modal(document.getElementById('locationModal')).show();
    }

    function saveLocation() {
        var form = document.getElementById('locationForm');
        var formData = new FormData(form);

        if (!formData.get('projectId') || !formData.get('address') || !formData.get('provinceId')) {
            alert('กรุณากรอกข้อมูลให้ครบถ้วน');
            return;
        }

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                if (data.trim() == '1') {
                    alert('ชื่อสถานที่นี้มีอยู่ในระบบแล้ว');
                } else if (data.trim() == 'success') {
                    alert('บันทึกข้อมูลเรียบร้อยแล้ว');
                    location.reload();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('ไม่สามารถเชื่อมต่อกับ Server ได้');
            });
    }

    function openDeleteLocation(id) {
        if (confirm('คุณต้องการลบสถานที่นี้ใช่หรือไม่?')) {
            alert('ฟังก์ชันลบยังไม่ได้เปิดใช้งาน');
        }
    }
</script> -->