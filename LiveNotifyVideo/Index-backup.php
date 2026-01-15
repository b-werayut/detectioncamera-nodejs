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
    } catch (Exception $e) { }
}

$projectOptions = "";
$isProjectDisabled = "";

try {
    if ($roleId == 1) { // SuperAdmin
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project ORDER BY ProjectName ASC";
        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute();
        
        $projectOptions .= '<option value="" selected disabled>เลือกโครงการ</option>';
        while ($row = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
            $projectOptions .= '<option value="'.$row['ProjectID'].'">'.$row['ProjectName'].'</option>';
        }
        $isProjectDisabled = ""; 

    } else { // Admin/Viewer
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project WHERE ProjectID = ?";
        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute([$userProjectID]);
        
        if ($row = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
            $projectOptions .= '<option value="'.$row['ProjectID'].'" selected>'.$row['ProjectName'].'</option>';
        } else {
            $projectOptions .= '<option value="" selected disabled>ไม่พบข้อมูลโครงการ</option>';
        }
        $isProjectDisabled = "disabled"; 
    }

} catch (Exception $e) {
    $projectOptions = '<option value="">Error Loading Projects</option>';
}

$getparam = $_GET['auth'] ?? ''; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Streaming : NetWorklink.Co.Ltd,</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="fonts/font-kanit.css" />
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/snappaging_.css">
    <script src="js/jquery-3.7.1.min.js"></script>
</head>

<style>
    html, body {
        height: 100%;
        overflow-y: auto;
    }
    .video-wrapper {
        width: 390px;
        height: 310px;
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 10px;
        position: relative;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    .video-wrapper.show {
        opacity: 1;
    }
    
    #cameraDropdown {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: white;
        width: 100%;
        position: absolute;
        z-index: 1000;
        padding: 10px;
    }

    .camera-status {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 8px;
    }
    .online { background-color: #28a745; }
    .offline { background-color: #dc3545; }
    
    .camera-item {
        display: flex;
        align-items: center;
        padding: 5px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .camera-item:last-child {
        border-bottom: none;
    }
    .camera-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        cursor: pointer;
    }
    .camera-label {
        flex-grow: 1;
        font-weight: 500;
        cursor: pointer;
        margin-bottom: 0;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg digital-bg">
        <div class="container px-lg-5">
            <img src="../snapshot/assets/nwl-logo.png" alt="NetWorklink" width="50">
            <span style="letter-spacing: 1px;" class="text-white" href="#!">NetWorklink.Co.Ltd,</span>
            <button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active text-light" aria-current="page"
                            href="<?= $urlstream; ?>">Streamimg</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="/SnapShot/snappaging_.php">Snapshot</a>
                    </li>
                    <li class="nav-item"><a class="nav-link text-light" href="/SnapShot/vdopaging_.php">Snap Videos</a>
                    </li>
                    
                    <?php if ($roleId == 1 || $roleId == 2): ?>
                        <li class="nav-item"><a class="nav-link text-light" href="../Management/index.php">Management</a></li>
                    <?php endif; ?>

                    <?php
                    // แก้ไข: ใช้ !empty($user) แทน empty($auth)
                    // เพราะถ้า Login ปกติ $user จะมีค่าเสมอ
                    if (!empty($user)) {
                        echo "<li class='nav-item'><a class='nav-link text-light' href='../logout.php'>Logout</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <header class="py-2 ">
        <div class="container px-lg-5 ">
            <div class="p-4 p-lg-5 rounded-3 text-center digital-style">
                <div class="">
                    <h1 class="display-5 fw-bold text-white text-uppercase" style="letter-spacing: 10px">Streaming</h1>
                </div>
            </div>
    </header>
    
    <section class="p-1 main-content">
        <div class="container mt-3 px-lg-5">
            <div class="row g-2">
                
                <div class="col-md-6">
                    <label for="projectSelect" class="form-label fw-bold mb-1" style="font-size: 0.9rem;">
                        โครงการ
                    </label>
                    <select class="form-select shadow-sm" id="projectSelect" style="border-radius: 10px;" <?= $isProjectDisabled ?>>
                        <?= $projectOptions ?>
                    </select>
                </div>

                <div class="col-md-6 position-relative">
                    <label class="form-label fw-bold mb-1" style="font-size: 0.9rem;">
                        กล้อง
                    </label>
                    <button class="btn btn-outline-secondary w-100 shadow-sm text-start d-flex justify-content-between align-items-center" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#cameraDropdown"
                            style="border-radius: 10px; background-color: white; color: #333;">
                        <span>เลือกกล้อง</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    
                    <div id="cameraDropdown" class="collapse shadow-sm">
                        <div class="text-center py-2 text-muted">กำลังโหลดข้อมูล...</div>
                    </div>
                </div>

            </div>

            <div id="streamContainer" class="mt-4 row g-3 flex justify-content-center">
            </div>
        </div>

        <div style="height: 80px;"></div>
    </section>

    <script>
        let roundcheck = 0;
        let currentStatus = {};

        const getparams = '<?= $getparam; ?>'; 
        const BASE_URL = 'http://85.204.247.82';
        const PORT = '26300';
        const API_PATH = 'api/getCameraStat';
        const CAMERA_STATS_API_URL = `${BASE_URL}:${PORT}/${API_PATH}?v=${Date.now()}`;

        let toggleState = 0;

        async function fetchCameraStatusesFromAPI() {
            try {
                const response = await fetch(CAMERA_STATS_API_URL, {
                    headers: { 'cache-control': 'no-cache' }
                });
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();

                if (data.msg === "Success" && Array.isArray(data.cameraStat)) {
                    return data.cameraStat;
                } else {
                    console.error('API returned an unexpected format:', data);
                    return [];
                }
            } catch (error) {
                console.error('Error fetching camera stats:', error);
                return [];
            }
        }

        async function renderCameraDropdown() {
            const dropdown = document.getElementById('cameraDropdown');
            dropdown.innerHTML = ''; 

            const apiCameraStats = await fetchCameraStatusesFromAPI();

            if (apiCameraStats.length === 0) {
                dropdown.innerHTML = '<div class="text-center py-2 text-muted">ไม่พบกล้องในระบบ</div>';
                return;
            }

            apiCameraStats.forEach((cameraData, index) => {
                const cameraName = cameraData.camera;
                const apiStatus = parseInt(cameraData.status, 10);
                const STREAMING_PORT = 26080;
                const STREAMING_BASE_URL = `${BASE_URL}:${STREAMING_PORT}/detectionstreaming`;
                const FULL_STREAMING_URL = `${STREAMING_BASE_URL}/${cameraName}`;

                const isChecked = 'checked'; 

                const htmlId = `camera-${cameraName.replace(/[^a-zA-Z0-9]/g, '-')}`;
                const statusClass = apiStatus === 1 ? 'online' : 'offline';

                const cameraItem = document.createElement('div');
                cameraItem.className = 'camera-item';
                cameraItem.innerHTML = `
                    <div class="form-check w-100 m-0">
                        <input class="form-check-input" type="checkbox" id="${htmlId}"
                            data-camera-name="${cameraName}"
                            data-stream-url="${FULL_STREAMING_URL}"
                            data-status="${apiStatus}"
                            ${isChecked}>
                        <label class="form-check-label camera-label w-100" for="${htmlId}">
                            ${cameraName}
                            <span id="${htmlId}-status" class="camera-status ${statusClass}"></span>
                        </label>
                    </div>
                `;
                dropdown.appendChild(cameraItem);
            });

            const checkboxes = document.querySelectorAll('.form-check-input');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateStreams);
            });

            updateStreams();
        }

        document.getElementById('projectSelect').addEventListener('change', async function() {
            document.getElementById('streamContainer').innerHTML = '<div class="text-center w-100 py-5">กำลังโหลดกล้อง...</div>';
            await renderCameraDropdown();
        });

        let cameraStates = {};

        async function updateStreams() {
            const streamContainer = document.getElementById('streamContainer');
            const selectedCheckboxes = Array.from(document.querySelectorAll('.form-check-input:checked'));
            const selectedIds = new Set(selectedCheckboxes.map(cb => cb.id));

            if (selectedCheckboxes.length === 0) {
                streamContainer.innerHTML = `
                <div id="no-camera-message" style="padding: 20px; color: #555; font-weight: bold; text-align: center; width: 100%;">
                    กรุณาเลือกกล้องที่ต้องการดู
                </div>`;
                cameraStates = {};
                return;
            } else {
                const message = document.getElementById('no-camera-message');
                if (message) message.remove();
                if (streamContainer.innerHTML.includes('กำลังโหลด')) streamContainer.innerHTML = '';
            }

            document.querySelectorAll('.video-wrapper').forEach(wrapper => {
                const wrapperId = wrapper.id.replace('stream-', '');
                if (!selectedIds.has(wrapperId)) {
                    wrapper.remove();
                    delete cameraStates[wrapperId];
                }
            });

            selectedCheckboxes.forEach(checkbox => {
                const stream = {
                    id: checkbox.id,
                    name: checkbox.dataset.cameraName,
                    streamUrl: checkbox.dataset.streamUrl,
                    status: parseInt(checkbox.dataset.status, 10)
                };

                let videoWrapper = document.querySelector(`#stream-${stream.id}`);
                let isNewWrapper = !videoWrapper;

                if (isNewWrapper) {
                    videoWrapper = createVideoWrapper(stream.id, stream.name);
                }

                if (cameraStates[stream.id] !== stream.status || isNewWrapper) {
                    cameraStates[stream.id] = stream.status;
                    updateCameraDisplay(videoWrapper, stream);
                }
            });
        }

        function createVideoWrapper(id, name) {
            const wrapper = document.createElement('div');
            wrapper.id = `stream-${id}`;
            wrapper.className = 'video-wrapper position-relative';
            wrapper.style.flex = '0 0 auto';
            document.getElementById('streamContainer').appendChild(wrapper);
            return wrapper;
        }

        function updateCameraDisplay(wrapper, camera) {
            if (camera.status === 0) {
                wrapper.innerHTML = `
                <div style="position: absolute; top: 0; left: 0;
                                background-color: rgba(0,0,0,0.7); color: white;
                                padding: 2px 5px; font-size: 12px; z-index: 10;">
                        ${camera.name}
                        <span class="camera-status offline" style="display: inline-block; margin-left: 5px;"></span>
                    </div>
                    <div style="width:100%; height:100%; background:#ccc; display:flex; 
                                    justify-content:center; align-items:center; color:#333;">
                        Camera is not available
                    </div>`;
                wrapper.classList.add('show');
            } else {
                wrapper.innerHTML = `
                    <div style="position: absolute; top: 0; left: 0;
                    background-color: rgba(0,0,0,0.7); color: white;
                    padding: 2px 5px; font-size: 12px; z-index: 10;">
                        ${camera.name}
                        <span class="camera-status online" style="display: inline-block; margin-left: 5px;"></span>
                    </div>
                    <iframe src="${camera.streamUrl}?t=${Date.now()}"
                            width="100%" height="100%" frameborder="0" allowfullscreen
                            sandbox="allow-scripts allow-same-origin"
                            style="display: block; opacity: 0; transition: opacity 0.5s ease;"
                            onload="this.style.opacity = 1;"></iframe>`;
                wrapper.classList.add('show');
            }
        }

        setInterval(async () => {
            const apiCameraStats = await fetchCameraStatusesFromAPI();
            
            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                const cameraName = checkbox.dataset.cameraName;
                const cameraInfo = apiCameraStats.find(cam => cam.camera === cameraName);

                if (cameraInfo) {
                    const newStatus = parseInt(cameraInfo.status, 10);
                    const currentStatus = parseInt(checkbox.dataset.status, 10);

                    if (currentStatus !== newStatus) {
                        checkbox.dataset.status = newStatus;
                        const statusElement = document.querySelector(`#${checkbox.id}-status`);
                        if (statusElement) {
                            statusElement.className = 'camera-status ' + (newStatus === 1 ? 'online' : 'offline');
                        }
                        updateStreams();
                    }
                }
            });
        }, 3000);

        document.addEventListener('DOMContentLoaded', async function() {
            await renderCameraDropdown();
        });        
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
<footer class="py-2 digital-bg">
    <div class="container">
        <p class="m-0 text-center text-white" style="letter-spacing: 1px;">
            Copyright &copy; NetWorklink.Co.Ltd,
        </p>
    </div>
</footer>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

</html>