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
$roleId = $_SESSION['RoleID'] ?? 0;
$userProjectId = $_SESSION['ProjectID'] ?? 0;
$auth = $_SESSION['auth'] ?? null;
$userRole = $_SESSION['UserRole'];

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

    } else {
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
    <link href="./css/vdopaging.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // กำหนดหน้าปัจจุบันสำหรับ Navbar Active State
    $currentPage = 'video';
    ?>

    <?php include_once '../components/navbar.php'; ?>

    <header class="page-header">
        <div class="container">
            <h1><i class="fas fa-film me-3"></i>วิดีโอ</h1>
        </div>
    </header>

    <section class="p-1">
        <div class="container px-lg-5">

            <div class="selector-section">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-building me-1"></i> โครงการ
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
                        <label class="form-label">
                            <i class="fas fa-video me-1"></i> กล้อง
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
                        <label class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i> ข้อมูล
                        </label>
                        <select id="selectdatas" onchange="selectData()" class="form-select" disabled>
                            <option value="0" selected>กรุณาเลือกกล้องก่อน</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="content-section">
                <div id="snappath" class="mb-3" style="display: none;">
                    <span id="filedate" class="date-badge">
                        <i class="fas fa-calendar"></i>
                        <span></span>
                    </span>
                    <div class="divider"></div>
                </div>

                <div class="video-container">
                    <div class="no-data-message" id="nodata">
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
        async function changeProject() {
            const projectId = $('#selectproject').val();
            const selectcam = $('#selectcam');
            const selectdatasbtn = $('#selectdatas');

            selectcam
                .empty()
                .append('<option value="0" selected>-- กรุณาเลือกกล้อง --</option>')
                .prop('disabled', true);

            selectdatasbtn.prop('disabled', true);

            if (projectId == "0" || projectId == "") return;

            try {
                const response = await fetch('http://www.centrecities.com:26300/api/getallcamera', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        projectID: projectId
                    })
                });

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                const cameras = await response.json();

                const activeCams = cameras.filter(cam => cam.isActive === true);

                if (activeCams.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ไม่พบข้อมูลกล้อง',
                        text: 'โครงการนี้ยังไม่มีการตั้งค่ากล้อง',
                        confirmButtonText: 'ตกลง'
                    });

                    selectcam.prop('disabled', true);
                    return;
                }

                activeCams.forEach(cam => {
                    selectcam.append(
                        `<option value="${cam.CameraID}">${cam.CameraName}</option>`
                    );
                });

                selectcam.prop('disabled', false);

            } catch (err) {
                console.error('โหลดกล้องไม่สำเร็จ:', err);

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถโหลดข้อมูลกล้องได้',
                    confirmButtonText: 'ตกลง'
                });

                selectcam.prop('disabled', true);
            }
        }

        $(document).ready(function () {
            toggleControls(); // ตอนโหลดหน้า

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

            $('#selectdatas').attr('disabled', 'disabled');
            let futuretime = '<?= $futuretimecf ?>';
            let beforetimeraw = '<?= $beforetime ?>';
            let beforetime = parseInt(beforetimeraw) + 1;
            let snappath = $('#snappath');
            snappath.hide();

            function changeProject() {
                const projectId = document.getElementById('selectproject').value;
                window.location.href = "?projectId=" + projectId;
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
            }

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

            function selectData() {
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
        })
    </script>
</body>

</html>