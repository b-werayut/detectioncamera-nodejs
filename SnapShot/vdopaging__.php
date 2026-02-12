<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', 0);
error_reporting(E_ALL);

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
$userProjectId = $_SESSION['ProjectID'] ?? 0; // [ProjectID]
$auth = $_SESSION['auth'] ?? null;

// ตั้งค่า Link Streaming
if (isset($auth)) {
    // กรณีมาจาก LINE ให้ส่ง auth token กลับไป
    $urlstream = '../livenotifyvideo/index.php?auth=' . $auth;
} else {
    // กรณี Login ปกติ
    $urlstream = '../livenotifyvideo/index.php';
}

// --- Check Authentication ---
if (empty($user) && empty($auth)) {
    header("Location: ../login.php"); 
    exit();
}

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

        // รับค่า Project ที่เลือกจาก URL ถ้าไม่มีให้ใช้ค่าแรก
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
    <title>วิดีโอ - NetWorklink Co.Ltd.</title>
    <meta name="description" content="ระบบดูวิดีโอจากกล้องตรวจจับ NetWorklink">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="fonts/font-kanit.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="./css/snappaging.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    $futuretimecf = 0;
    $beforetime = 0;
    // ตรวจสอบ Config
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
                <div>
                    <h1 class="scc-hero-title">Snap Videos</h1>
                    <p class="scc-hero-subtitle">ระบบดูวิดีโอจากกล้องตรวจจับแบบเรียลไทม์</p>
                </div>
                <div class="scc-hero-meta">
                    <span class="scc-hero-badge">
                        <i class="fas fa-circle"></i> VIDEO SYSTEM
                    </span>
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
                        <select id="selectproject" class="form-select" 
                                onchange="changeProject()" <?= $projectDisabled ?>>
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

                    <div class="col-md-4">
                        <label class="scc-selector-label">
                            <i class="fas fa-video"></i> กล้อง
                        </label>
                        <select id="selectcam" onchange="selectCam()" class="form-select" aria-label="Select Camera">
                            <option value="0" selected="">-- กรุณาเลือกกล้อง --</option>
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

                    <div class="col-md-4">
                        <label class="scc-selector-label">
                            <i class="fas fa-calendar-alt"></i> ข้อมูล
                        </label>
                        <select id="selectdatas" onchange="selectData()" class="form-select" 
                            aria-label="Select Data" disabled>
                            <option value="0" selected="">-- กรุณาเลือกกล้องก่อน --</option>
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
                        <i class="fas fa-video"></i>
                        <h5 id="nodatah2">กรุณาเลือกข้อมูล</h5>
                    </div>
                    <ul class="vdonamex row" id="vdonamex" style="margin: 0; padding:0;"></ul>
                    <ul class="vdodisplay row" id="vdodisplay" style="margin: 0; padding:0;"></ul>
                    <div class="pagination" id="pagination"></div>
                </div>
            </div>

        </div>
    </section>

    <!-- Professional Footer -->
    <footer>
        <div class="container text-center">
            <p>&copy; <?= date('Y'); ?> NetWorklink Co.Ltd. &mdash; Professional Real-time Streaming Solutions</p>
            <div class="scc-footer-version">All Rights Reserved | Intelligent Camera Management System v2.0</div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // --- Global Variables ---
        $('#selectdatas').attr('disabled', 'disabled')
        let futuretime = '<?= $futuretimecf ?>'
        let beforetimeraw = '<?= $beforetime ?>'
        let beforetime = parseInt(beforetimeraw) + 1; 
        let snappath = $('#snappath')
        snappath.hide()

        // --- Function: Change Project ---
        function changeProject() {
            const projectId = document.getElementById('selectproject').value;
            if (projectId) {
                window.location.search = '?projectid=' + projectId;
            }
        }

        // --- Function: Select Camera ---
        function selectCam() {
            const selectcamval = $('#selectcam').val()
            const selectdatasbtn = $('#selectdatas')
            const thaiMonths = [
                "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];

            $('.selectdataoption').remove()

            if (selectcamval == "0" || selectcamval == "") {
                selectdatasbtn.attr('disabled', 'disabled')
            } else {
                selectdatasbtn.removeAttr('disabled', 'disabled')

                $.ajax({
                    url: 'vdopagingdata.php',
                    data: `selectcamval=${selectcamval}`,
                    method: 'GET',
                    success: (resp) => {
                        try {
                            let obj = jQuery.parseJSON(resp)
                            if (obj.datas == '') {
                                $('#selectdatas').prop('disabled', true);
                                Swal.fire({
                                    title: "ไม่มีข้อมูล!",
                                    icon: "warning",
                                    confirmButtonText: "ตกลง",
                                });
                            }
                            
                            $.each(obj.datas, (i, items) => {
                                let datas = items
                                let datassplit = datas.split("_")
                                if(datassplit.length >= 3) {
                                    let day = datassplit[1].slice(6)
                                    let month = datassplit[1].slice(4, 6)
                                    let monththai = thaiMonths[parseInt(month) - 1]
                                    let year = datassplit[1].slice(0, 4)
                                    let yearthai = parseInt(year) + 543
                                    let hour = datassplit[2].slice(0, 2)
                                    let minute = datassplit[2].slice(2, 4)
                                    let sec = datassplit[2].slice(4)
                                    let datetimedisplay = `วันที่ ${day} ${monththai} ${yearthai} เวลา ${hour}:${minute}:${sec}`
                                    
                                    selectdatasbtn.append(`<option class="selectdataoption" value="${items}">${datetimedisplay}</option>`)
                                }
                            })
                        } catch (e) {
                            console.error("JSON Error:", e);
                        }
                    },
                    error: (data) => {
                        Swal.fire({ icon: "error", title: "เกิดข้อผิดพลาดในการเชื่อมต่อ" })
                    }
                })
            }
        }

        // --- Helper: Format Future Time ---
        function formatDatefuturetime(selectdatasdatefm) {
            let ndt = new Date(selectdatasdatefm)
            let year = String(ndt.getFullYear()).padStart(2, '0')
            let month = String(ndt.getMonth() + 1).padStart(2, '0')
            let day = String(ndt.getDate()).padStart(2, '0')
            let hours = String(ndt.getHours()).padStart(2, '0')
            ndt.setMinutes(ndt.getMinutes() + parseInt(futuretime)); 
            ndt.setSeconds(ndt.getSeconds() + 40); 
            let minutes = String(ndt.getMinutes()).padStart(2, '0')
            let sec = String(ndt.getSeconds()).padStart(2, '0')
            return `${year}${month}${day}${hours}${minutes}${sec}`
        }

        // --- Helper: Format Before Time ---
        function formatDatebeforetime(selectdatasdatefm) {
            let ndt = new Date(selectdatasdatefm)
            let year = String(ndt.getFullYear()).padStart(2, '0')
            let month = String(ndt.getMonth() + 1).padStart(2, '0')
            let day = String(ndt.getDate()).padStart(2, '0')
            let hours = String(ndt.getHours()).padStart(2, '0')
            ndt.setMinutes(ndt.getMinutes() - parseInt(beforetime)); 
            ndt.setSeconds(ndt.getSeconds() - 40); 
            let minutes = String(ndt.getMinutes()).padStart(2, '0')
            let sec = String(ndt.getSeconds()).padStart(2, '0')
            return `${year}${month}${day}${hours}${minutes}${sec}`
        }

        // --- Function: Select Data (Load Videos) ---
        function selectData() {
            $('#nodatah, #nodatah2, .page-item').remove()
            let nodata = $("<h5 id='nodatah2'>อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>")
            
            let selectdatasval = $('#selectdatas').val()
            if(!selectdatasval || selectdatasval === "0") return;

            let camname = selectdatasval.split("_");
            let camnamef = camname[0];
            
            // Format Date
            let selectdatasdt = selectdatasval.slice(13, 29).replaceAll('_', '') 
            let selectdatasdty = selectdatasdt.slice(0, 4)
            let selectdatasdtmth = selectdatasdt.slice(4, 6)
            let selectdatasdtd = selectdatasdt.slice(6, 8)
            let selectdatasdth = selectdatasdt.slice(8, 10)
            let selectdatasdtminute = selectdatasdt.slice(10, 12)
            let selectdatasdts = selectdatasdt.slice(12, 14)
            let selectdatasdatefm = `${selectdatasdty}-${selectdatasdtmth}-${selectdatasdtd} ${selectdatasdth}:${selectdatasdtminute}:${selectdatasdts}`

            const futuretime = formatDatefuturetime(selectdatasdatefm)

            $('.vdobox, .vdodisplay, .vdonamex').fadeOut(100);

            if (selectdatasval == 0) {
                Swal.fire({ icon: "error", title: "กรุณาเลือกข้อมูล!" })
                    .then((result) => { if (result.isConfirmed) { nodata.appendTo('#nodata'); } });
            } else {
                Swal.fire({
                    title: "กำลังดึงข้อมูลวิดีโอ!",
                    timer: 2000,
                    didOpen: () => { Swal.showLoading(); },
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        $.ajax({
                            url: 'vdopagingdata.php',
                            data: `selectdatas=${selectdatasval}`,
                            method: 'GET',
                            success: (resp) => {
                                let obj = jQuery.parseJSON(resp)
                                if (obj.vdonames == '') {
                                    Swal.fire({ icon: "error", title: "ไม่พบไฟล์วิดีโอ" })
                                    nodata.appendTo('#nodata')
                                } else {
                                    $('#nodatah2').remove()
                                    snappath.fadeIn(function () {
                                        $('#filedate').text(`ข้อมูลวันที่: ${obj.filedates}`)
                                    })
                                    $('.vdodisplay').fadeIn(200)
                                    
                                    // Pagination & Videos
                                    pagingSelectDatas(selectdatasval, obj.vdonames, camnamef)
                                    
                                    // Highlight Videos (First 5)
                                    let vdonamex = $('.vdonamex')
                                    $.each(obj.vdonamexs, function (i, item) {
                                        if (i >= 5) return false;
                                        vdonamex.append(`<li class="vdobox col-md-3 p-0 text-center" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="/eventfolder/${camnamef}/${selectdatasval}/vdo/x/${item}" type="video/mp4"></video> </li>`);
                                    })
                                    vdonamex.fadeIn(400)
                                }
                            },
                            error: (data) => {
                                Swal.fire({ icon: "error", title: "โหลดข้อมูลไม่สำเร็จ!" })
                                nodata.appendTo('#nodata')
                            }
                        })
                    }
                })
            }
        }

        // --- Pagination Logic (Video) ---
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
                    // Corrected Path: /eventfolder/{camname}/{folder}/{filename}
                    vdodisplay.append(`<li class="vdobox col-md-3 p-0 text-center" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="/eventfolder/${camnamef}/${path}/vdo/${item}" type="video/mp4"></video> </li>`);
                });
                vdodisplay.hide().fadeIn(400);
            }

            function displayPagination2() {
                const totalPages = Math.ceil(items.length / itemsPerPage);
                const pagination = document.getElementById('pagination');
                pagination.innerHTML = "";

                if(totalPages > 1) {
                    const prevPage = document.createElement('div');
                    prevPage.className = "page-item";
                    prevPage.innerHTML = '<a class="page-link">Previous</a>';
                    prevPage.onclick = function() { if (currentPage > 1) { currentPage--; updatePagination2(); } };
                    pagination.appendChild(prevPage);

                    for (let i = 1; i <= totalPages; i++) {
                        const page = document.createElement('div');
                        page.className = "page-item" + (i === currentPage ? " active" : "");
                        page.innerHTML = `<a class="page-link">${i}</a>`;
                        page.onclick = function() { currentPage = i; updatePagination2(); };
                        pagination.appendChild(page);
                    }

                    const nextPage = document.createElement('div');
                    nextPage.className = "page-item";
                    nextPage.innerHTML = '<a class="page-link">Next</a>';
                    nextPage.onclick = function() { if (currentPage < totalPages) { currentPage++; updatePagination2(); } };
                    pagination.appendChild(nextPage);
                }
            }

            function updatePagination2() {
                displayItems2(currentPage);
                displayPagination2();
            }

            updatePagination2();
        }
    </script>
</body>
</html>