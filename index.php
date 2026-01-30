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

$projects = [];
$currentProjectTitle = "-- กรุณาเลือกโครงการ --";

try {
    if ($roleId == 1) { // SuperAdmin
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project ORDER BY ProjectName ASC";
        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute();

        while ($row = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
            $projects[] = $row;
            if ($row['ProjectID'] == $selectedProjectID) {
                $currentProjectTitle = $row['ProjectName'];
            }
        }
    } else { // Admin / Viewer
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project WHERE ProjectID = ?";
        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute([$userProjectID]);

        if ($row = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
            $projects[] = $row;
            $currentProjectTitle = $row['ProjectName'];
        }
    }
} catch (Exception $e) {
    // Error handling
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
    <section class="py-4 py-lg-5">
        <div class="container px-lg-5">

            <!-- Page Header -->
            <div class="page-context-header mb-4 mb-lg-5">
                <div class="d-flex align-items-center gap-3">
                    <div class="context-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1">Streaming Control Center</h1>
                        <p class="page-subtitle text-muted mb-0">ระบบติดตามและบริหารจัดการกล้องแบบเรียลไทม์</p>
                    </div>
                </div>
                <div class="d-none d-md-block text-end">
                    <div class="current-time-badge shadow-sm">
                        <i class="far fa-clock me-2 text-success"></i>
                        <span id="liveTime"><?= date('H:i'); ?></span> น.
                    </div>
                </div>
            </div>

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

            <!-- Camera Monitoring Overview -->
            <div class="data-table-section">
                <div class="section-header">
                    <div>
                        <div class="section-badge"><i class="fas fa-table"></i> OVERVIEW</div>
                        <h2 class="section-title d-none">
                            <i class="fas fa-table"></i>
                            ภาพรวมสถานะกล้อง
                        </h2>
                    </div>
                    <?php if ($roleId == 1) { ?>
                        <div class="project-switcher-container">
                            <button class="btn btn-project-switcher shadow-sm" type="button" data-bs-toggle="collapse"
                                data-bs-target="#projectDropdownContainer">
                                <i class="fas fa-building me-2"></i>
                                <span class="active-project-label">
                                    <?= htmlspecialchars($currentProjectTitle); ?>
                                </span>
                                <i class="fas fa-chevron-down ms-3 dropdown-arrow"></i>
                            </button>

                            <!-- Hidden Select for JS Compatibility -->
                            <select id=" selectproject" class="d-none">
                                <?php foreach ($projects as $proj): ?>
                                    <option value="<?= $proj['ProjectID'] ?>" <?= ($proj['ProjectID'] == $selectedProjectID) ? 'selected' : '' ?>>
                                        <?= $proj['ProjectName'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="projectDropdownContainer" class="collapse project-hub-dropdown shadow-lg mt-3">
                                <div class="project-search-wrapper">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="projectSearch" placeholder="ค้นหาโครงการ..."
                                        onkeyup="filterProjectHub()">
                                </div>
                                <div class="project-hub-grid" id="projectHubGrid">
                                    <?php foreach ($projects as $proj): ?>
                                        <div class="project-hub-item <?= ($proj['ProjectID'] == $selectedProjectID) ? 'active' : '' ?>"
                                            onclick="confirmProjectSwitch('<?= $proj['ProjectID'] ?>', '<?= htmlspecialchars($proj['ProjectName']) ?>')"
                                            data-name="
                                <?= strtolower($proj['ProjectName']) ?>">
                                            <div class="hub-icon">
                                                <i class="fas fa-city"></i>
                                            </div>
                                            <span class="hub-name"><?= htmlspecialchars($proj['ProjectName']); ?></span>
                                            <?php if ($proj['ProjectID'] == $selectedProjectID): ?>
                                                <i class="fas fa-check-circle ms-auto text-success"></i>
                                            <?php endif; ?>
                                        </div> <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php } else {
                        $projectName = $row['ProjectName'];
                        ?>
                        <?= "<div class='section-badge'>
        <i class='fas fa-building me-1'></i> โครงการ: " . htmlspecialchars($projectName) . "
    </div>" ?>
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

            <!-- Global Stream Controls -->
            <div class="stream-controls-section shadow-sm text-center">
                <div class="section-badge mx-auto mb-3">SELECT FEED</div>
                <h3 class="mb-4 fw-bold">เลือกกล้อง</h3>

                <button class="btn btn-camera-selector" type="button" data-bs-toggle="collapse"
                    data-bs-target="#cameraDropdown">
                    <i class="fas fa-th-list"></i> เลือกกล้องที่ต้องการแสดงผล
                </button>
                <div id="cameraDropdown" class="collapse"></div>
            </div>

            <!-- Live Stream Feeds -->
            <div class="feeds-section">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div style="width: 4px; height: 24px; background: var(--accent-green); border-radius: 2px;"></div>
                    <h2 class="section-title mb-0" style="font-size: 1.5rem;">Live Streams</h2>
                </div>
                <div id="streamContainer"></div>
            </div>
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