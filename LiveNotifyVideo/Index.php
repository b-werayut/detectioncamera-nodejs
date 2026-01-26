<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Bangkok');

require_once '../config/db_connection.php';

if (isset($_SESSION['UserId'])) {
    $timeout_duration = 3600; // 1 ชั่วโมง

    if (isset($_SESSION['LAST_ACTIVITY'])) {
        if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
            session_unset();
            session_destroy();
            header("Location: ../login.php?timeout=1");
            exit();
        }
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}

$user = $_SESSION['Username'] ?? null;        // [Username]
$roleId = $_SESSION['RoleID'] ?? 0;           // [RoleID]
$userProjectID = $_SESSION['ProjectID'] ?? 0; // [ProjectID]
$auth = $_SESSION['auth'] ?? null;
$urlstream = '';
$userRole = $_SESSION['UserRole'];
$userId = $_SESSION['UserId'];
$selectedProjectID = $_GET['projectid'] ?? 0;

// echo $_SESSION['RoleID'];
// echo $_SESSION['UserRole'];

// 4. กรณีเข้าผ่าน Link LINE
if (isset($_GET['auth'])) {
    $_SESSION['auth'] = $_GET['auth'];
    $auth = $_SESSION['auth'];

    if (!isset($_SESSION['LAST_ACTIVITY'])) {
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    $urlstream = '../livenotifyvideo/index.php?auth=' . $auth;
}

if (empty($user) && empty($auth)) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$userRoleName = "";
if (!empty($roleId)) {
    try {
        $sqlRole = "SELECT UserRole FROM Role WHERE RoleID = ?";
        $stmtRole = $conn->prepare($sqlRole);
        $stmtRole->execute([$roleId]);
        $resultRole = $stmtRole->fetch(PDO::FETCH_ASSOC);

        if ($resultRole) {
            $userRoleName = ucfirst($resultRole['UserRole']);
        }
    } catch (Exception $e) {
    }
}

$projectOptions = "";
$isProjectDisabled = "";

try {
    if ($roleId == 1) { // SuperAdmin
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project ORDER BY ProjectName ASC";
        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute();

        $projectOptions = '<option value="" '
            . (empty($selectedProjectID) ? 'selected' : '')
            . ' disabled hidden>-- กรุณาเลือกโครงการ --</option>';

        while ($row = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
            $selected = ($row['ProjectID'] == $selectedProjectID) ? 'selected' : '';
            $projectOptions .= '<option value="' . $row['ProjectID'] . '" ' . $selected . '>'
                . "โครงการ: " . $row['ProjectName']
                . '</option>';
        }

        $isProjectDisabled = "";

    } else { // Admin / Viewer
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project WHERE ProjectID = ?";
        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute([$userProjectID]);

        if ($row = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
            $projectOptions = '<option value="' . $row['ProjectID'] . '" selected>'
                . $row['ProjectName']
                . '</option>';
        } else {
            $projectOptions = '<option value="" selected disabled>ไม่พบข้อมูลโครงการ</option>';
        }

        $isProjectDisabled = "disabled";
    }

} catch (Exception $e) {
    $projectOptions = '<option value="">Error Loading Projects</option>';
}

$getparam = $_GET['auth'] ?? '';

function expiredTime()
{
    session_unset();
    session_destroy();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

    header("Location: ../login.php?expired=1");
    exit();
}

function linePermission()
{
    $_SESSION['auth'] = $_GET['auth'];
    $_SESSION['login_time'] = time();
    $_SESSION['timeout'] = 60 * 30;
    $urlstream = '../livenotifyvideo/index.php?auth=' . $_SESSION['auth'];

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

    return $urlstream;
}

function checkAuthentication()
{
    session_unset();
    session_destroy();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

    header("Location: ../login.php");
    exit();
}


// กำหนดหน้าปัจจุบันสำหรับ Navbar Active State
$currentPage = 'streaming';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetWorklink Co.Ltd. - แดชบอร์ดสตรีมมิ่ง</title>
    <meta name="description" content="แดชบอร์ดสตรีมมิ่งแบบ Fintech สำหรับติดตามกล้องแบบเรียลไทม์">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="fonts/font-kanit.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="./css/styles.css" rel="stylesheet">
    <script src="js/jquery-3.7.1.min.js"></script>
</head>

<body>
    <?php include_once '../components/navbar.php'; ?>

    <!-- Main Content -->
    <section class="p-1">
        <div class="container px-lg-5">

            <!-- Dashboard Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value" id="totalCameras">0</div>
                            <div class="stat-label">กล้องทั้งหมด</div>
                        </div>
                        <div class="stat-icon green">
                            <i class="fas fa-video"></i>
                        </div>
                    </div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>ระบบพร้อมใช้งาน</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value" id="onlineCameras">0</div>
                            <div class="stat-label">กล้องออนไลน์</div>
                        </div>
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-trend up">
                        <i class="fas fa-signal"></i>
                        <span>กำลังสตรีม</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value" id="offlineCameras">0</div>
                            <div class="stat-label">กล้องออฟไลน์</div>
                        </div>
                        <div class="stat-icon warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="stat-trend down">
                        <i class="fas fa-minus-circle"></i>
                        <span>ไม่พร้อมใช้งาน</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value" id="frozenCameras">0</div>
                            <div class="stat-label">กล้องภาพค้าง</div>
                        </div>
                        <div class="stat-icon frozen"
                            style="background: linear-gradient(135deg, #f59e0b15 0%, #f59e0b25 100%); color: #f59e0b;">
                            <i class="fas fa-snowflake"></i>
                        </div>
                    </div>
                    <div class="stat-trend frozen" style="color: #f59e0b;">
                        <i class="fas fa-pause-circle"></i>
                        <span>ไม่มีข้อมูลใหม่</span>
                    </div>
                </div>

                <div class="stat-card d-none">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value" id="selectedCameras">0</div>
                            <div class="stat-label">กล้องที่เลือก</div>
                        </div>
                        <div class="stat-icon green">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                    <div class="stat-trend up">
                        <i class="fas fa-desktop"></i>
                        <span>กำลังแสดงผล</span>
                    </div>
                </div>
            </div>

            <!-- Camera Status Table -->
            <div class="data-table-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-table"></i>
                        ภาพรวมสถานะกล้อง
                    </h2>
                    <?php if ($roleId == 1) { ?>
                        <div class="project-selector-wrapper">
                            <div class="input-group input-group-lg shadow-sm rounded" style="
    border: 1px solid green;">
                                <span class=" input-group-text bg-white border-0">
                                    <i class="fas fa-building text-success"></i>
                                </span>
                                <select id="selectproject" class="form-select border-0 fw-semibold"
                                    onchange="changeProject()">
                                    <?php echo $projectOptions; ?>
                                </select>
                            </div>
                        </div>
                    <?php } else {
                        $projectName = $row['ProjectName'];
                        ?>
                        <h2 class="section-title">
                            <i class="fas fa-building me-1"></i>
                            <?= "โครงการ: " . htmlspecialchars($projectName); ?>
                        </h2>
                    <?php }
                    ; ?>
                </div>
                <div class="table-responsive table-striped table-hover table-bordered">
                    <table class="professional-table" id="cameraStatusTable">
                        <thead>
                            <tr>
                                <th>ชื่อกล้อง</th>
                                <th>สถานะ</th>
                                <th>ข้อมูลสตรีมที่รับ</th>
                                <th>อัพเดทล่าสุด</th>
                            </tr>
                        </thead>
                        <tbody id="cameraTableBody">
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Camera Selector -->
            <div class="camera-selector-wrapper text-center">
                <button class="btn btn-camera-selector" type="button" data-bs-toggle="collapse"
                    data-bs-target="#cameraDropdown">
                    <i class="fas fa-list"></i> เลือกกล้องที่ต้องการแสดงผล
                </button>

                <div id="cameraDropdown" class="collapse"></div>
            </div>

            <!-- Stream Container -->
            <div id="streamContainer"></div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="text-center">
                Copyright &copy; <?= date('Y'); ?> NetWorklink Co.Ltd. - All Rights Reserved
            </p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <script>
        /**
         * PHP to JavaScript Variable Injection
         * These variables are required by js/streaming-dashboard.js
         */
        var currentUserId = '<?php echo $userId ?? ""; ?>';
        var currentUserRole = '<?php echo $userRole ?? ""; ?>';
        var selectedProjectID = '<?php echo $selectedProjectID ?? 0; ?>';
        var getparams = '<?php echo $getparam ?? ''; ?>';
    </script>

    <script src="js/streaming-dashboard.js"></script>
</body>

</html>