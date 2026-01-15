<?php
// 1. เริ่ม Session เป็นอย่างแรก
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. ตั้งค่า Timezone และปิด Error (Production)
date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 3. เชื่อมต่อฐานข้อมูล
require_once '../config/db_connection.php';

// --- ส่วนจัดการ Session Timeout (เหมือน index.php) ---
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

// --- ตัวแปร User และ Auth (ปรับให้ตรง DB) ---
$user = $_SESSION['Username'] ?? null;        // [Username]
$roleId = $_SESSION['RoleID'] ?? 0;           // [RoleID]
$userProjectId = $_SESSION['ProjectID'] ?? 0; // [ProjectID]
$auth = $_SESSION['auth'] ?? null;

// ตั้งค่า Link Streaming
if (isset($auth)) {
    $urlstream = '../livenotifyvideo/index.php?auth=' . $auth;
} else {
    $urlstream = '../livenotifyvideo/index.php';
}

// --- Check Authentication ---
if (empty($user) && empty($auth)) {
    header("Location: ../login.php");
    exit();
}

// --- ส่วนดึงข้อมูล Projects และ Cameras ---
$projects = [];
$cameras = [];
$projectDisabled = "disabled";
$selectedProjectID = 0;

try {
    if ($roleId == 1) { // SuperAdmin เห็นทุกโครงการ
        $sqlProj = "SELECT DISTINCT p.ProjectID, p.ProjectName 
                    FROM Project p 
                    INNER JOIN Camera c ON p.ProjectID = c.ProjectID 
                    WHERE c.isActive = 1 
                    ORDER BY p.ProjectName ASC";

        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute();
        $projects = $stmtProj->fetchAll(PDO::FETCH_ASSOC);
        $projectDisabled = "";

        if (isset($_GET['projectid']) && !empty($_GET['projectid'])) {
            $selectedProjectID = $_GET['projectid'];
        } elseif (count($projects) > 0) {
            $selectedProjectID = $projects[0]['ProjectID'];
        }

    } else { // Admin/Viewer เห็นเฉพาะของตัวเอง
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project WHERE ProjectID = ?";
        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute([$userProjectId]);
        $projects = $stmtProj->fetchAll(PDO::FETCH_ASSOC);

        $projectDisabled = "disabled";
        $selectedProjectID = $userProjectId;
    }

    if (!empty($selectedProjectID)) {
        $sqlCam = "SELECT CameraName FROM Camera WHERE isActive = 1 AND ProjectID = ? ORDER BY CameraName ASC";
        $stmtCam = $conn->prepare($sqlCam);
        $stmtCam->execute([$selectedProjectID]);
        $cameras = $stmtCam->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Exception $e) {
    error_log("DB Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ภาพนิ่ง - NetWorklink Co.Ltd.</title>
    <meta name="description" content="ระบบดูภาพนิ่งจากกล้องตรวจจับ NetWorklink">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="fonts/font-kanit.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-green: #0d4d3d;
            --secondary-green: #1a6b54;
            --accent-green: #26d07c;
            --light-green: #e8f5f1;
            --dark-bg: #071e18;
            --success-green: #10b981;
            --warning-yellow: #f59e0b;
            --danger-red: #ef4444;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8fafb 0%, #e8f5f1 100%);
            min-height: 100vh;
            overflow-y: auto;
        }

        /* Header Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--primary-green) 100%);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white !important;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .navbar-brand img {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 0 0.25rem;
        }

        .nav-link:hover {
            background: rgba(38, 208, 124, 0.2);
            color: var(--accent-green) !important;
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: var(--accent-green) !important;
            color: white !important;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            padding: 3rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            background-size: 20px 20px;
            opacity: 0.5;
        }

        .page-header h1 {
            color: var(--accent-green);
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            text-shadow: 0 0 20px rgba(38, 208, 124, 0.5);
            position: relative;
            z-index: 1;
        }

        /* Form Selectors */
        .selector-section {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin: 2rem 0;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(13, 77, 61, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-select {
            border: 2px solid var(--light-green);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: white;
        }

        .form-select:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(38, 208, 124, 0.2);
        }

        .form-select:disabled {
            background-color: #f3f4f6;
            opacity: 0.7;
        }

        /* Content Section */
        .content-section {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin: 2rem 0;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(13, 77, 61, 0.1);
            min-height: 300px;
        }

        .date-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .date-badge i {
            color: var(--accent-green);
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, var(--light-green), var(--accent-green), var(--light-green));
            margin: 1rem 0;
            border-radius: 2px;
        }

        .image-container {
            background: var(--light-green);
            border-radius: 12px;
            padding: 1.5rem;
            min-height: 200px;
        }

        .no-data-message {
            text-align: center;
            padding: 3rem;
            color: var(--primary-green);
        }

        .no-data-message i {
            font-size: 3rem;
            color: var(--accent-green);
            margin-bottom: 1rem;
            display: block;
        }

        .no-data-message h5 {
            font-weight: 600;
            margin: 0;
        }

        /* Image Grid */
        .imgbox {
            list-style: none;
            padding: 0.5rem;
        }

        .imgbox img {
            width: 100%;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .imgbox img:hover {
            transform: scale(1.02);
            border-color: var(--accent-green);
            box-shadow: 0 8px 20px rgba(13, 77, 61, 0.2);
        }

        /* Pagination */
        .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .page-item {
            display: inline-block;
        }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            background: white;
            color: var(--primary-green);
            border: 2px solid var(--light-green);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .page-link:hover {
            background: var(--light-green);
            border-color: var(--accent-green);
            color: var(--primary-green);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            border-color: var(--primary-green);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--primary-green) 100%);
            padding: 1.5rem 0;
            margin-top: 4rem;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3);
        }

        footer p {
            color: white;
            margin: 0;
            letter-spacing: 0.5px;
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid var(--light-green);
            border-top: 3px solid var(--accent-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse-green {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-brand span {
                display: none;
            }

            .navbar-brand img {
                width: 40px;
            }

            .page-header h1 {
                font-size: 1.75rem;
                letter-spacing: 2px;
            }

            .selector-section,
            .content-section {
                padding: 1rem;
                margin: 1rem 0;
            }

            .form-select {
                font-size: 0.9rem;
                padding: 0.6rem 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                padding: 2rem 0;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .date-badge {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }

            .page-link {
                min-width: 35px;
                height: 35px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <?php
    $futuretimecf = 0;
    $configPath = "C:\\inetpub\\wwwroot\\camera\\config.txt";
    if (!file_exists($configPath)) {
        $configPath = "config.txt";
    }

    if (file_exists($configPath)) {
        $myfile = fopen($configPath, "r");
        if ($myfile) {
            $cfraw = fgets($myfile);
            $cfdatas = json_decode($cfraw, true);
            $futuretimecf = $cfdatas['futuretime'] ?? 0;
            fclose($myfile);
        }
    }
    ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container px-lg-5">
            <a class="navbar-brand" href="#!">
                <img src="../snapshot/assets/nwl-logo.png" alt="NetWorklink" width="50">
                <span>NetWorklink Co.Ltd.</span>
            </a>
            <button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $urlstream; ?>">
                            <i class="fas fa-video me-1"></i> สตรีมมิ่ง
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="snappaging_.php">
                            <i class="fas fa-camera me-1"></i> ภาพนิ่ง
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vdopaging_.php">
                            <i class="fas fa-film me-1"></i> วิดีโอ
                        </a>
                    </li>
                    <?php if ($roleId == 1 || $roleId == 2): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../Management/index.php">
                                <i class="fas fa-cog me-1"></i> จัดการระบบ
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($user)): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <h1><i class="fas fa-camera me-3"></i>ภาพนิ่ง</h1>
        </div>
    </header>

    <!-- Main Content -->
    <section class="p-1">
        <div class="container px-lg-5">

            <!-- Selector Section -->
            <div class="selector-section">
                <div class="row g-3 align-items-end">

                    <!-- Project Selector -->
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-building me-1"></i> โครงการ
                        </label>
                        <select id="selectproject" class="form-select" onchange="changeProject()" <?= $projectDisabled ?>>
                            <?php
                            if (count($projects) > 0) {
                                foreach ($projects as $proj) {
                                    $pID = $proj['ProjectID'];
                                    $pName = htmlspecialchars($proj['ProjectName']);
                                    $selected = ($pID == $selectedProjectID) ? 'selected' : '';
                                    echo "<option value='{$pID}' {$selected}>{$pName}</option>";
                                }
                            } else {
                                echo "<option value='' selected>ไม่พบโครงการ</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Camera Selector -->
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-video me-1"></i> กล้อง
                        </label>
                        <select id="selectcam" onchange="selectCam()" class="form-select">
                            <option value="0" selected>เลือกกล้อง</option>
                            <?php
                            if (!empty($cameras)) {
                                foreach ($cameras as $cam) {
                                    $camName = htmlspecialchars($cam['CameraName']);
                                    echo '<option value="' . $camName . '">กล้อง ' . $camName . '</option>';
                                }
                            } else {
                                echo '<option value="" disabled>ไม่มีกล้องในโครงการนี้</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Data Selector -->
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i> ข้อมูล
                        </label>
                        <select id="selectdatas" onchange="selectData()" class="form-select" disabled>
                            <option value="0" selected>กรุณาเลือกกล้องก่อน</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Content Section -->
            <div class="content-section">
                <div id="snappath" class="mb-3" style="display: none;">
                    <span id="filedate" class="date-badge">
                        <i class="fas fa-calendar"></i>
                        <span></span>
                    </span>
                    <div class="divider"></div>
                </div>

                <div class="image-container">
                    <div class="no-data-message" id="nodata">
                        <i class="fas fa-images"></i>
                        <h5 id="nodatah2">กรุณาเลือกข้อมูล</h5>
                    </div>
                    <ul class="imgnamex row" id="imgnamex" style="margin: 0; padding: 0;"></ul>
                    <ul class="imgdisplay row" id="imgdisplay" style="margin: 0; padding: 0;"></ul>
                    <div class="pagination" id="pagination"></div>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="text-center">
                Copyright &copy;
                <?= date('Y'); ?> NetWorklink Co.Ltd. - All Rights Reserved
            </p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $('#selectdatas').attr('disabled', 'disabled');
        let futuretime = '<?= $futuretimecf ?>';
        let snappath = $('#snappath');
        snappath.hide();

        function changeProject() {
            const projectId = document.getElementById('selectproject').value;
            if (projectId) {
                window.location.search = '?projectid=' + projectId;
            }
        }

        function selectCam() {
            const selectcamval = $('#selectcam').val();
            const selectdatasbtn = $('#selectdatas');
            const thaiMonths = [
                "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];

            $('.selectdataoption').remove();

            if (selectcamval == "0" || selectcamval == "") {
                selectdatasbtn.attr('disabled', 'disabled');
            } else {
                selectdatasbtn.removeAttr('disabled');

                $.ajax({
                    url: '/SnapShot/snappagingdata.php',
                    data: `selectcamval=${selectcamval}`,
                    method: 'GET',
                    success: (resp) => {
                        try {
                            let obj = jQuery.parseJSON(resp);
                            if (obj.datas == '') {
                                $('#selectdatas').prop('disabled', true);
                                Swal.fire({
                                    title: "ไม่มีข้อมูล!",
                                    icon: "warning",
                                    confirmButtonText: "ตกลง",
                                    confirmButtonColor: '#0d4d3d'
                                });
                            }

                            $.each(obj.datas, (i, items) => {
                                let datas = items;
                                let datassplit = datas.split("_");
                                if (datassplit.length >= 3) {
                                    let day = datassplit[1].slice(6);
                                    let month = datassplit[1].slice(4, 6);
                                    let monththai = thaiMonths[parseInt(month) - 1];
                                    let year = datassplit[1].slice(0, 4);
                                    let yearthai = parseInt(year) + 543;
                                    let hour = datassplit[2].slice(0, 2);
                                    let minute = datassplit[2].slice(2, 4);
                                    let sec = datassplit[2].slice(4);
                                    let datetimedisplay = `วันที่ ${day} ${monththai} ${yearthai} เวลา ${hour}:${minute}:${sec}`;

                                    selectdatasbtn.append(`<option class="selectdataoption" value="${items}">${datetimedisplay}</option>`);
                                }
                            });
                        } catch (e) {
                            console.error("JSON Error:", e);
                        }
                    },
                    error: (data) => {
                        Swal.fire({
                            icon: "error",
                            title: "เกิดข้อผิดพลาดในการเชื่อมต่อ",
                            confirmButtonColor: '#0d4d3d'
                        });
                    }
                });
            }
        }

        function formatDate(selectdatasdatefm) {
            let ndt = new Date(selectdatasdatefm);
            let year = String(ndt.getFullYear()).padStart(2, '0');
            let month = String(ndt.getMonth() + 1).padStart(2, '0');
            let day = String(ndt.getDate()).padStart(2, '0');
            let hours = String(ndt.getHours()).padStart(2, '0');
            ndt.setMinutes(ndt.getMinutes() + parseInt(futuretime));
            let minutes = String(ndt.getMinutes()).padStart(2, '0');
            let sec = String(ndt.getSeconds()).padStart(2, '0');
            return `${year}${month}${day}${hours}${minutes}${sec}`;
        }

        function selectData() {
            let nodataHtml = '<h5 id="nodatah2">อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>';

            $('#nodatah, #nodatah2, .page-item').remove();

            let selectdatasval = $('#selectdatas').val();
            if (!selectdatasval || selectdatasval === "0") return;

            let camname = selectdatasval.split("_");
            let camnamef = camname[0];

            let selectdatasdt = selectdatasval.slice(13, 29).replaceAll('_', '');
            let selectdatasdatefm = `${selectdatasdt.slice(0, 4)}-${selectdatasdt.slice(4, 6)}-${selectdatasdt.slice(6, 8)} ${selectdatasdt.slice(8, 10)}:${selectdatasdt.slice(10, 12)}:${selectdatasdt.slice(12, 14)}`;
            const futuretimeCalc = formatDate(selectdatasdatefm);

            $('.imgbox, .imgdisplay, .imgnamex').fadeOut(100);

            if (selectdatasval == 0) {
                Swal.fire({
                    icon: "error",
                    title: "กรุณาเลือกข้อมูล!",
                    confirmButtonColor: '#0d4d3d'
                }).then((result) => { if (result.isConfirmed) location.reload(); });
            } else {
                Swal.fire({
                    title: "กำลังดึงข้อมูล...",
                    timer: 2000,
                    didOpen: () => { Swal.showLoading(); },
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        $.ajax({
                            url: '/SnapShot/snappagingdata.php',
                            data: `selectdatas=${selectdatasval}`,
                            method: 'GET',
                            success: (resp) => {
                                let obj = jQuery.parseJSON(resp);
                                if (obj.imgnames == '' && obj.imgnamexs == '') {
                                    $('.page-item').remove();
                                    Swal.fire({
                                        icon: "error",
                                        title: "ไม่พบรูปภาพ",
                                        confirmButtonColor: '#0d4d3d'
                                    });
                                } else {
                                    $('.imgdisplay').fadeIn(200);
                                    $('#filedate').html(`<i class="fas fa-calendar me-2"></i>ข้อมูลวันที่: ${obj.filedates}`);
                                    snappath.fadeIn();

                                    pagingSelectDatas(selectdatasval, obj.imgnames, camnamef);

                                    let imgnamex = $('.imgnamex');
                                    $.each(obj.imgnamexs, function (i, item) {
                                        if (i >= 8) return false;
                                        imgnamex.append(`<li class="imgbox col-md-3 p-0"><img class="img-thumbnail" onclick="showimgx2('${selectdatasval}','${item}','${camnamef}')" src="/eventfolder/${camnamef}/${selectdatasval}/pic/X/${item}"></li>`);
                                    });
                                    imgnamex.fadeIn(400);
                                    $('#nodatah2').remove();
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    icon: "error",
                                    title: "โหลดข้อมูลไม่สำเร็จ!",
                                    confirmButtonColor: '#0d4d3d'
                                });
                            }
                        });
                    }
                });
            }
        }

        // Pagination Logic
        function pagingSelectDatas(path, json, camname) {
            const items = json;
            if (!items) return false;

            const itemsPerPage = 12;
            let currentPage = 1;

            function displayItems2(page) {
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const itemsToDisplay = items.slice(startIndex, endIndex);

                const itemList = document.getElementById('imgdisplay');
                itemList.innerHTML = "";
                let imgdisplay = $('.imgdisplay');

                itemsToDisplay.map(item => {
                    if (item == "X") return false;
                    imgdisplay.append(`<li class="imgbox col-md-3 p-0"><img class="img-thumbnail" onclick="showimg2('${path}', '${item}', '${camname}')" src="/eventfolder/${camname}/${path}/pic/${item}"></li>`);
                });
                imgdisplay.hide().fadeIn(400);
            }

            function displayPagination2() {
                const totalPages = Math.ceil(items.length / itemsPerPage);
                const pagination = document.getElementById('pagination');
                pagination.innerHTML = "";

                if (totalPages > 1) {
                    // Previous Button
                    const prevPage = document.createElement('div');
                    prevPage.className = "page-item";
                    prevPage.innerHTML = '<a class="page-link"><i class="fas fa-chevron-left"></i></a>';
                    prevPage.onclick = function () { if (currentPage > 1) { currentPage--; updatePagination2(); } };
                    pagination.appendChild(prevPage);

                    // Page Numbers
                    for (let i = 1; i <= totalPages; i++) {
                        const page = document.createElement('div');
                        page.className = "page-item" + (i === currentPage ? " active" : "");
                        page.innerHTML = `<a class="page-link">${i}</a>`;
                        page.onclick = function () { currentPage = i; updatePagination2(); };
                        pagination.appendChild(page);
                    }

                    // Next Button
                    const nextPage = document.createElement('div');
                    nextPage.className = "page-item";
                    nextPage.innerHTML = '<a class="page-link"><i class="fas fa-chevron-right"></i></a>';
                    nextPage.onclick = function () { if (currentPage < totalPages) { currentPage++; updatePagination2(); } };
                    pagination.appendChild(nextPage);
                }
            }

            function updatePagination2() {
                displayItems2(currentPage);
                displayPagination2();
            }

            updatePagination2();
        }

        // SweetAlert Image Preview
        function showimg2(path, img, camname) {
            Swal.fire({
                imageUrl: `/eventfolder/${camname}/${path}/pic/${img}`,
                imageWidth: 600,
                imageHeight: 400,
                width: 700,
                showCloseButton: true,
                showConfirmButton: false,
                background: '#fff',
                customClass: {
                    popup: 'rounded-4'
                }
            });
        }

        function showimgx2(path, img, camname) {
            Swal.fire({
                imageUrl: `/eventfolder/${camname}/${path}/pic/X/${img}`,
                imageWidth: 600,
                imageHeight: 400,
                width: 700,
                showCloseButton: true,
                showConfirmButton: false,
                background: '#fff',
                customClass: {
                    popup: 'rounded-4'
                }
            });
        }
    </script>
</body>

</html>