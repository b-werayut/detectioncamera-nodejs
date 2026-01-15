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

        $projectOptions .= '<option value="" selected disabled>เลือกโครงการ</option>';
        while ($row = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
            $projectOptions .= '<option value="' . $row['ProjectID'] . '">' . $row['ProjectName'] . '</option>';
        }
        $isProjectDisabled = "";

    } else { // Admin/Viewer
        $sqlProj = "SELECT ProjectID, ProjectName FROM Project WHERE ProjectID = ?";
        $stmtProj = $conn->prepare($sqlProj);
        $stmtProj->execute([$userProjectID]);

        if ($row = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
            $projectOptions .= '<option value="' . $row['ProjectID'] . '" selected>' . $row['ProjectName'] . '</option>';
        } else {
            $projectOptions .= '<option value="" selected disabled>ไม่พบข้อมูลโครงการ</option>';
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
                </div>
                <div class="table-responsive">
                    <table class="professional-table" id="cameraStatusTable">
                        <thead>
                            <tr>
                                <th>ชื่อกล้อง</th>
                                <th>สถานะ</th>
                                <th>URL สตรีม</th>
                                <th>อัพเดทล่าสุด</th>
                            </tr>
                        </thead>
                        <tbody id="cameraTableBody">
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem;">
                                    <div class="loading-spinner"></div>
                                    <p style="margin-top: 1rem; color: #6b7280;">กำลังโหลดข้อมูลกล้อง...</p>
                                </td>
                            </tr>
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
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <script>
        let roundcheck = 0;
        let currentStatus = {};

        const getparams = '<?php echo $getparam ?? ''; ?>';
        const BASE_URL = 'http://85.204.247.82';
        const PORT = '26300';
        const API_PATH = 'api/getCameraStat';
        const CAMERA_STATS_API_URL = `${BASE_URL}:${PORT}/${API_PATH}?v=${Date.now()}`;

        let toggleState = 0;
        let cameraStates = {};

        async function fetchCameraStatusesFromAPI() {
            try {
                const response = await fetch(CAMERA_STATS_API_URL, {
                    headers: {
                        'cache-control': 'no-cache'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.msg === "Success" && Array.isArray(data.cameraStat)) {
                    return data.cameraStat;
                } else {
                    console.error('API returned an unexpected format or error:', data);
                    return [];
                }
            } catch (error) {
                console.error('Error fetching camera stats from API:', error);
                return [];
            }
        }

        function updateDashboardStats(cameraData) {
            const total = cameraData.length;
            const online = cameraData.filter(cam => parseInt(cam.status, 10) === 1).length;
            const offline = total - online;
            const selected = document.querySelectorAll('.form-check-input:checked').length;

            document.getElementById('totalCameras').textContent = total;
            document.getElementById('onlineCameras').textContent = online;
            document.getElementById('offlineCameras').textContent = offline;
            document.getElementById('selectedCameras').textContent = selected;
        }

        function updateCameraTable(cameraData) {
            const tbody = document.getElementById('cameraTableBody');

            if (cameraData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: #6b7280;">
                            ไม่มีข้อมูลกล้อง
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = cameraData.map(cam => {
                const status = parseInt(cam.status, 10);
                const statusClass = status === 1 ? 'online' : 'offline';
                const statusText = status === 1 ? 'ออนไลน์' : 'ออฟไลน์';
                const STREAMING_PORT = 26080;
                const streamUrl = `${BASE_URL}:${STREAMING_PORT}/detectionstreaming/${cam.camera}`;
                const currentTime = new Date().toLocaleTimeString('th-TH');

                return `
                    <tr>
                        <td><strong>${cam.camera}</strong></td>
                        <td>
                            <span class="table-status-badge ${statusClass}">
                                <span class="table-status-dot ${statusClass}"></span>
                                ${statusText}
                            </span>
                        </td>
                        <td style="font-size: 0.8rem; color: #6b7280; word-break: break-all;">${streamUrl}</td>
                        <td>${currentTime}</td>
                    </tr>
                `;
            }).join('');
        }

        async function renderCameraDropdown() {
            const dropdown = document.getElementById('cameraDropdown');
            dropdown.innerHTML = '';

            const apiCameraStats = await fetchCameraStatusesFromAPI();

            // Update dashboard stats and table
            updateDashboardStats(apiCameraStats);
            updateCameraTable(apiCameraStats);

            apiCameraStats.forEach((cameraData, index) => {
                const cameraName = cameraData.camera;
                const apiStatus = parseInt(cameraData.status, 10);

                const STREAMING_PORT = 26080;
                const STREAMING_BASE_URL = `${BASE_URL}:${STREAMING_PORT}/detectionstreaming`;
                const FULL_STREAMING_URL = `${STREAMING_BASE_URL}/${cameraName}`;

                currentStatus[index] = {
                    name: cameraName.trim(),
                    streamUrl: FULL_STREAMING_URL,
                    apiStatus
                };

                if (!FULL_STREAMING_URL) {
                    console.warn(`No stream URL defined for camera: ${cameraName}. Skipping.`);
                    return;
                }

                const htmlId = `camera-${cameraName.replace(/[^a-zA-Z0-9]/g, '-')}`;
                const statusClass = apiStatus === 1 ? 'online' : 'offline';
                const isChecked = 'checked';

                const cameraItem = document.createElement('div');
                cameraItem.className = 'camera-item';
                cameraItem.innerHTML = `
                    <input class="form-check-input"
                        type="checkbox"
                        id="${htmlId}"
                        data-camera-name="${cameraName}"
                        data-stream-url="${FULL_STREAMING_URL}"
                        data-status="${apiStatus}"
                        ${isChecked}>
                    <label class="camera-label" for="${htmlId}">
                        <i class="fas fa-video me-2"></i>${cameraName}
                    </label>
                    <span id="${htmlId}-status" class="camera-status ${statusClass}"></span>
                `;

                dropdown.appendChild(cameraItem);
            });

            const checkboxes = document.querySelectorAll('.form-check-input');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateStreams);
            });
        }

        async function updateStreams() {
            const streamContainer = document.getElementById('streamContainer');
            const selectedCheckboxes = Array.from(document.querySelectorAll('.form-check-input:checked'));
            const selectedIds = new Set(selectedCheckboxes.map(cb => cb.id));

            // Update selected cameras count
            document.getElementById('selectedCameras').textContent = selectedIds.size;

            if (selectedIds.size === 0) {
                streamContainer.innerHTML = `
                    <div id="no-camera-message">
                        <i class="fas fa-video-slash"></i>
                        <div>กรุณาเลือกกล้องอย่างน้อย 1 ตัว</div>
                        <small style="color: #6b7280;">Please select at least one camera</small>
                    </div>
                `;
                cameraStates = {};
                return;
            } else {
                const message = document.getElementById('no-camera-message');
                if (message) message.remove();
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
            wrapper.className = 'video-wrapper';
            document.getElementById('streamContainer').appendChild(wrapper);

            setTimeout(() => {
                wrapper.classList.add('show');
            }, 50);

            return wrapper;
        }

        function updateCameraDisplay(wrapper, camera) {
            const statusClass = camera.status === 1 ? 'status-badge-online' : 'status-badge-offline';
            const statusText = camera.status === 1 ? 'ออนไลน์' : 'ออฟไลน์';
            const statusIcon = camera.status === 1 ? 'fa-circle' : 'fa-circle';

            if (camera.status === 0) {
                wrapper.innerHTML = `
                    <div class="video-header">
                        <div class="video-name">
                            <i class="fas fa-video"></i>
                            ${camera.name}
                        </div>
                        <div class="video-status-badge ${statusClass}">
                            <i class="fas ${statusIcon}"></i>
                            ${statusText}
                        </div>
                    </div>
                    <div class="video-content">
                        <div class="video-placeholder">
                            <i class="fas fa-video-slash"></i>
                            <p>กล้องไม่พร้อมใช้งาน</p>
                            <small>Camera is not available</small>
                        </div>
                    </div>
                `;
            } else {
                wrapper.innerHTML = `
                    <div class="video-header">
                        <div class="video-name">
                            <i class="fas fa-video"></i>
                            ${camera.name}
                        </div>
                        <div class="video-status-badge ${statusClass}">
                            <i class="fas ${statusIcon}"></i>
                            ${statusText}
                        </div>
                    </div>
                    <div class="video-content">
                        <iframe src="${camera.streamUrl}?t=${Date.now()}"
                                width="100%"
                                height="100%"
                                frameborder="0"
                                allowfullscreen
                                sandbox="allow-scripts allow-same-origin"
                                style="opacity: 0; transition: opacity 0.5s ease;"
                                onload="this.style.opacity = 1;"></iframe>
                    </div>
                `;
            }
        }

        // Update camera status every 3 seconds
        setInterval(async () => {
            const apiCameraStats = await fetchCameraStatusesFromAPI();
            let hasChanged = false;

            // Update dashboard stats and table
            updateDashboardStats(apiCameraStats);
            updateCameraTable(apiCameraStats);

            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                const cameraName = checkbox.dataset.cameraName;
                const cameraInfo = apiCameraStats.find(cam => cam.camera === cameraName);

                if (cameraInfo) {
                    const newStatus = parseInt(cameraInfo.status, 10);
                    const currentStatus = parseInt(checkbox.dataset.status, 10);

                    if (currentStatus !== newStatus) {
                        hasChanged = true;
                        checkbox.dataset.status = newStatus;

                        const statusElement = document.querySelector(`#${checkbox.id}-status`);
                        if (statusElement) {
                            statusElement.className = 'camera-status ' + (newStatus === 1 ? 'online' : 'offline');
                        }
                    }
                }
            });

            if (hasChanged) {
                await updateStreams();
            }
        }, 3000);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', async function () {
            const dropdownButton = document.querySelector('[data-bs-target="#cameraDropdown"]');
            const dropdownMenu = document.getElementById('cameraDropdown');

            document.addEventListener('click', function (event) {
                const isClickInside = dropdownMenu.contains(event.target) ||
                    dropdownButton.contains(event.target);

                if (!isClickInside && dropdownMenu.classList.contains('show')) {
                    const collapseInstance = bootstrap.Collapse.getInstance(dropdownMenu);
                    if (collapseInstance) {
                        collapseInstance.hide();
                    }
                }
            });

            await renderCameraDropdown();
            await updateStreams();
        });

        // Auth check
        function getCookie(name) {
            const cookies = document.cookie.split('; ');
            for (let cookie of cookies) {
                const [key, value] = cookie.split('=');
                if (key === name) return value;
            }
            return null;
        }

        function checkAuth() {
            const token = getCookie('token');
            const urlParams = new URLSearchParams(window.location.search);
            const authParam = urlParams.get('auth');

            if (!token && authParam !== '1') {
                window.location.href = '../login.php';
            }

            if (authParam === '1') {
                const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }
        }

        //window.addEventListener('DOMContentLoaded', checkAuth);
    </script>
</body>

</html>