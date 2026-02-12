<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/db_connection.php';

if (isset($_SESSION['UserId'])) {
    $timeout_duration = 3600;

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

$user = $_SESSION['Username'] ?? null;
$firstname = $_SESSION['Firstname'] ?? null;        // [Firstname]
$lastname = $_SESSION['Lastname'] ?? null;        // [Lastname]
$roleId = $_SESSION['RoleID'] ?? 0;
$userProjectId = $_SESSION['ProjectID'] ?? 0;
$auth = $_SESSION['auth'] ?? null;
$role = $_SESSION['UserRole'];

if (isset($auth)) {
    $urlstream = '../livenotifyvideo/index.php?auth=' . $auth;
} else {
    $urlstream = '../livenotifyvideo/index.php';
}

if (empty($user) && empty($auth)) {
    header("Location: ../login.php");
    exit();
}

$projects = [];
$cameras = [];
$projectDisabled = "disabled";
$selectedProjectID = 0;

try {
    if ($roleId == 1) {
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project ORDER BY ProjectName ASC";

        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute();
        $projects = $stmtProj->fetchAll(PDO::FETCH_ASSOC);
        $projectDisabled = "";

        if (isset($_GET['projectid']) && !empty($_GET['projectid'])) {
            $selectedProjectID = $_GET['projectid'];
        } elseif (count($projects) > 0) {
            $selectedProjectID = $projects[0]['ProjectID'];
        }

    } else {
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project WHERE ProjectID = ?";
        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute([$userProjectId]);
        $projects = $stmtProj->fetchAll(PDO::FETCH_ASSOC);

        $projectDisabled = "disabled";
        $selectedProjectID = $userProjectId;
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
    <title>วิดีโอ - NetWorklink Co.Ltd.</title>
    <meta name="description" content="ระบบดูวิดีโอจากกล้องตรวจจับ NetWorklink">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="fonts/font-kanit.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="./css/snappaging.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="js/jquery-3.7.1.min.js"></script>
    <style>
        /* Critical inline CSS — prevent white flash on navigation */
        body {
            background: #f8fafb;
            background-image:
                radial-gradient(ellipse at 0% 0%, rgba(38, 208, 124, 0.04) 0%, transparent 60%),
                radial-gradient(ellipse at 100% 0%, rgba(13, 77, 61, 0.03) 0%, transparent 60%);
            margin: 0;
            opacity: 0;
            animation: pageEntrance 0.4s ease-out 0.05s forwards;
        }

        @keyframes pageEntrance {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <?php
    $futuretimecf = 0;
    $beforetime = 0;
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
            $beforetime = $cfdatas['beforetime'] ?? 0;
            fclose($myfile);
        }
    }

    $currentPage = 'video';
    $role = $_SESSION['UserRole'] ?? '';
    $login_time = $_SESSION['LAST_ACTIVITY'] ?? '';
    ?>

    <?php include_once '../components/navbar.php'; ?>

    <!-- Page Hero -->
    <div class="scc-page-hero">
        <div class="container px-lg-5">
            <div class="scc-hero-content">
                <div class="scc-hero-icon">
                    <i class="fas fa-film"></i>
                </div>
                <div class="d-flex justify-content-center">
                    <div>
                        <h1 class="scc-hero-title m-0 text-uppercase" style="letter-spacing: 1.5px;">Video Viewer</h1>
                        <p class="scc-hero-subtitle m-0" style="letter-spacing: 1px;">
                            ระบบดูวิดีโอจากกล้องตรวจจับ</p>
                    </div>
                </div>
                <div class="scc-hero-meta">
                    <div class="current-time-badge shadow-sm">
                        <i class="far fa-clock me-2 text-success"></i>
                        <span id="liveTime">
                            <?= date('H:i'); ?>
                        </span> น.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-4">
        <div class="container px-lg-5">

            <!-- Selector Section -->
            <div class="scc-selector-section scc-animate">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="scc-selector-label">
                            <i class="fas fa-building"></i> โครงการ
                        </label>
                        <?php
                        $selectedProjectID = isset($_GET['projectId']) ? (int) $_GET['projectId'] : 0;
                        ?>
                        <select id="selectproject" class="form-select" onchange="changeProject()">

                            <?php
                            $projectList = [];

                            if ($roleId == 1) {
                                echo '<option value="0"' . ($selectedProjectID == 0 ? ' selected' : '') . '>-- กรุณาเลือกโครงการ --</option>';

                                $sql = "SELECT ProjectID, ProjectName FROM [NWL_Detected].[dbo].[Project]";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $projectList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } else {
                                $projectList = $projects ?? [];
                            }

                            foreach ($projectList as $project) {
                                $projectID = (int) $project['ProjectID'];
                                $projectName = htmlspecialchars($project['ProjectName'], ENT_QUOTES, 'UTF-8');
                                $selected = ($projectID == $selectedProjectID) ? 'selected' : '';

                                echo "<option value=\"{$projectID}\" {$selected}>{$projectName}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="scc-selector-label">
                            <i class="fas fa-video"></i> กล้อง
                        </label>
                        <select id="selectcam" onchange="selectCam()" class="form-select">
                            <option value="0" selected>-- กรุณาเลือกกล้อง --</option>
                            <?php
                            // if (!empty($cameras)) {
                            //     foreach ($cameras as $cam) {
                            //         $camName = htmlspecialchars($cam['CameraName']);
                            //         echo '<option value="' . $camName . '">กล้อง ' . $camName . '</option>';
                            //     }
                            // } else {
                            //     echo '<option value="" disabled>ไม่มีกล้องในโครงการนี้</option>';
                            // }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="scc-selector-label">
                            <i class="fas fa-calendar-alt"></i> ข้อมูล
                        </label>
                        <select id="selectdatas" onchange="selectData()" class="form-select" disabled>
                            <option value="0" selected>กรุณาเลือกกล้องก่อน</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Content Section -->
            <div class="scc-content-section scc-animate scc-animate-d1">
                <div id="snappath" class="mb-3" style="display: none;">
                    <span id="filedate" class="scc-date-badge">
                        <i class="fas fa-calendar-alt"></i>
                        <span></span>
                    </span>
                    <div class="scc-divider"></div>
                </div>

                <div class="scc-media-container">
                    <div class="scc-no-data" id="nodata">
                        <i class="fas fa-film"></i>
                        <h5 id="nodatah2">กรุณาเลือกข้อมูล</h5>
                    </div>
                    <ul class="vdonamex row" id="vdonamex" style="margin: 0; padding: 0;"></ul>
                    <ul class="vdodisplay row" id="vdodisplay" style="margin: 0; padding: 0;"></ul>
                    <div class="pagination" id="pagination"></div>
                </div>
            </div>

        </div>
    </section>

    <!-- Professional Footer -->
    <footer>
        <div class="container text-center">
            <p>&copy; <?= date('Y'); ?> NetWorklink Co.Ltd. &mdash; Professional Real-time Streaming Solutions</p>
            <div class="scc-footer-version">All Rights Reserved | Intelligent Camera Management System</div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const API_URL = 'http://www.centrecities.com:26300/api/getallcamera';

        init();

        function init() {
            const projectId = $('#selectproject').val();
            if (projectId && projectId !== '0') {
                changeProject();
            }
        }

        async function changeProject() {
            const projectId = $('#selectproject').val();
            const $selectCam = $('#selectcam');
            const $selectDatasBtn = $('#selectdatas');

            resetCameraSelect();

            if (!projectId || projectId === '0') return;

            try {
                const cameras = await fetchCameras(projectId);
                const activeCams = cameras.filter(cam => cam.isActive);

                if (!activeCams.length) {
                    showAlert(
                        'warning',
                        'ไม่พบข้อมูลกล้อง',
                        'โครงการนี้ยังไม่มีการตั้งค่ากล้อง'
                    );
                    return;
                }

                populateCameraSelect(activeCams);
                $selectCam.prop('disabled', false);

            } catch (error) {
                console.error('โหลดกล้องไม่สำเร็จ:', error);

                showAlert(
                    'error',
                    'เกิดข้อผิดพลาด',
                    'ไม่สามารถโหลดข้อมูลกล้องได้'
                );
            }

            function resetCameraSelect() {
                $selectCam
                    .empty()
                    .append('<option value="0" selected>-- กรุณาเลือกกล้อง --</option>')
                    .prop('disabled', true);

                $selectDatasBtn.prop('disabled', true);
            }

            function populateCameraSelect(cameras) {
                cameras.forEach(({ CameraName }) => {
                    $selectCam.append(
                        `<option value="${CameraName}">${CameraName}</option>`
                    );
                });
            }
        }

        async function fetchCameras(projectId) {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ projectID: projectId })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            return response.json();
        }

        function showAlert(icon, title, text) {
            Swal.fire({
                icon,
                title,
                text,
                confirmButtonText: 'ตกลง'
            });
        }

        async function selectCam() {
            const selectcamval = $('#selectcam').val();
            const selectdatasbtn = $('#selectdatas');
            const thaiMonths = [
                "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];

            $('.selectdataoption').remove();


            selectdatasbtn.removeAttr('disabled');

            $.ajax({
                url: '/SnapShot/vdopagingdata.php',
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


        async function selectData() {
            let nodata = $("<h5 id='nodatah2'>อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>");

            $('.page-item, #snappath').hide();
            $('#nodatah2, #nodata').show();

            let selectdatasval = $('#selectdatas').val();
            if (!selectdatasval || selectdatasval === "0") return;

            let camname = selectdatasval.split("_");
            let camnamef = camname[0];

            let selectdatasdt = selectdatasval.slice(13, 29).replaceAll('_', '');
            let selectdatasdatefm = `${selectdatasdt.slice(0, 4)}-${selectdatasdt.slice(4, 6)}-${selectdatasdt.slice(6, 8)} ${selectdatasdt.slice(8, 10)}:${selectdatasdt.slice(10, 12)}:${selectdatasdt.slice(12, 14)}`;

            const futuretimeCalc = formatDatefuturetime(selectdatasdatefm);

            $('.vdobox, .vdodisplay, .vdonamex').fadeOut(100);

            if (selectdatasval == 0) {
                Swal.fire({
                    icon: "error",
                    title: "กรุณาเลือกข้อมูล!",
                    confirmButtonColor: '#0d4d3d'
                })
            } else {
                Swal.fire({
                    title: "กำลังดึงข้อมูลวิดีโอ!",
                    timer: 2000,
                    didOpen: () => { Swal.showLoading(); },
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        $.ajax({
                            url: '/SnapShot/vdopagingdata.php',
                            data: `selectdatas=${selectdatasval}`,
                            method: 'GET',
                            success: (resp) => {
                                let obj = jQuery.parseJSON(resp);
                                if (obj.vdonames == '') {
                                    $('#nodata').show();
                                    $('.page-item, #snappath').hide();
                                    Swal.fire({
                                        icon: "error",
                                        title: "ไม่พบไฟล์วิดีโอ",
                                        confirmButtonColor: '#0d4d3d'
                                    });
                                } else {
                                    $('#nodatah2').remove();
                                    snappath.fadeIn(function () {
                                        $('#filedate').html(`<i class="fas fa-calendar me-2"></i>ข้อมูลวันที่: ${obj.filedates}`);
                                    });
                                    $('.vdodisplay').fadeIn(200);

                                    pagingSelectDatas(selectdatasval, obj.vdonames, camnamef);

                                    let vdonamex = $('.vdonamex');
                                    $.each(obj.vdonamexs, function (i, item) {
                                        if (i >= 5) return false;
                                        vdonamex.append(`<li class="vdobox col-md-3 p-2 text-center"><video width="100%" muted controls class="img-thumbnail"><source src="/eventfolder/${camnamef}/${selectdatasval}/vdo/x/${item}" type="video/mp4"></video></li>`);
                                    });
                                    vdonamex.fadeIn(400);
                                    $('#nodata, #nodatah2').hide();
                                    $('.page-item').show();
                                }
                            },
                            error: (data) => {
                                Swal.fire({
                                    icon: "error",
                                    title: "โหลดข้อมูลไม่สำเร็จ!",
                                    confirmButtonColor: '#0d4d3d'
                                });
                                nodata.appendTo('#nodata');
                            }
                        });
                    }
                });
            }
        }

        $('#selectdatas').attr('disabled', 'disabled');
        let futuretime = '<?= $futuretimecf ?>';
        let beforetimeraw = '<?= $beforetime ?>';
        let beforetime = parseInt(beforetimeraw) + 1;
        let snappath = $('#snappath');
        snappath.hide();

        function formatDatefuturetime(selectdatasdatefm) {
            let ndt = new Date(selectdatasdatefm);
            let year = String(ndt.getFullYear()).padStart(2, '0');
            let month = String(ndt.getMonth() + 1).padStart(2, '0');
            let day = String(ndt.getDate()).padStart(2, '0');
            let hours = String(ndt.getHours()).padStart(2, '0');
            ndt.setMinutes(ndt.getMinutes() + parseInt(futuretime));
            ndt.setSeconds(ndt.getSeconds() + 40);
            let minutes = String(ndt.getMinutes()).padStart(2, '0');
            let sec = String(ndt.getSeconds()).padStart(2, '0');
            return `${year}${month}${day}${hours}${minutes}${sec}`;
        }

        function formatDatebeforetime(selectdatasdatefm) {
            let ndt = new Date(selectdatasdatefm);
            let year = String(ndt.getFullYear()).padStart(2, '0');
            let month = String(ndt.getMonth() + 1).padStart(2, '0');
            let day = String(ndt.getDate()).padStart(2, '0');
            let hours = String(ndt.getHours()).padStart(2, '0');
            ndt.setMinutes(ndt.getMinutes() - parseInt(beforetime));
            ndt.setSeconds(ndt.getSeconds() - 40);
            let minutes = String(ndt.getMinutes()).padStart(2, '0');
            let sec = String(ndt.getSeconds()).padStart(2, '0');
            return `${year}${month}${day}${hours}${minutes}${sec}`;
        }

        function pagingSelectDatas(path, json, camnamef) {
            const items = json;
            const itemsPerPage = 20;
            let currentPage = 1;

            function displayItems2(page) {
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const itemsToDisplay = items.slice(startIndex, endIndex);

                const itemList = document.getElementById('vdodisplay');
                itemList.innerHTML = "";
                let vdodisplay = $('.vdodisplay');

                itemsToDisplay.map(item => {
                    if (item == "X") return false;
                    vdodisplay.append(`<li class="vdobox col-md-3 p-2 text-center"><video width="100%" muted controls class="img-thumbnail"><source src="/eventfolder/${camnamef}/${path}/vdo/${item}" type="video/mp4"></video></li>`);
                });
                vdodisplay.hide().fadeIn(400);
            }

            function displayPagination2() {
                const totalPages = Math.ceil(items.length / itemsPerPage);
                const pagination = document.getElementById('pagination');
                pagination.innerHTML = "";

                if (totalPages > 1) {
                    const prevPage = document.createElement('div');
                    prevPage.className = "page-item";
                    prevPage.innerHTML = '<a class="page-link"><i class="fas fa-chevron-left"></i></a>';
                    prevPage.onclick = function () { if (currentPage > 1) { currentPage--; updatePagination2(); } };
                    pagination.appendChild(prevPage);

                    for (let i = 1; i <= totalPages; i++) {
                        const page = document.createElement('div');
                        page.className = "page-item" + (i === currentPage ? " active" : "");
                        page.innerHTML = `<a class="page-link">${i}</a>`;
                        page.onclick = function () { currentPage = i; updatePagination2(); };
                        pagination.appendChild(page);
                    }

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

        $(document).ready(function () {
            toggleControls();

            $('#selectproject').on('change', function () {
                toggleControls();
            });

            $('#selectcam').on('change', function () {
                toggleControls();
            });

            function toggleControls() {
                const projectVal = $('#selectproject').val();
                const camVal = $('#selectcam').val();

                const selectcam = $('#selectcam');
                const selectdatasbtn = $('#selectdatas');

                $('.selectdataoption').remove();

                if (projectVal == "0" || projectVal == "") {
                    selectcam.val("0").attr('disabled', 'disabled');
                    selectdatasbtn.attr('disabled', 'disabled');

                } else {
                    // 
                    selectcam.removeAttr('disabled');

                    if (camVal == "0" || camVal == "") {
                        selectdatasbtn.attr('disabled', 'disabled');
                    } else {
                        selectdatasbtn.removeAttr('disabled');
                    }
                }
            }

        })

        document.addEventListener("DOMContentLoaded", async function () {
            // Initialize clock (Start immediately)
            updateLiveTime();
            setInterval(updateLiveTime, 1000);
        })

        function updateLiveTime() {
            const timeElement = document.getElementById("liveTime");
            if (!timeElement) return;

            const now = new Date();
            const hours = String(now.getHours()).padStart(2, "0");
            const minutes = String(now.getMinutes()).padStart(2, "0");
            const timeString = `${hours}:${minutes}`;

            if (timeElement.textContent !== timeString) {
                timeElement.textContent = timeString;
            }
        }
    </script>
</body>

</html>