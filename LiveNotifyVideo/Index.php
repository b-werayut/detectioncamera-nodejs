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
        // ===== MediaMTX Camera Monitor Configuration =====
        const getparams = '<?php echo $getparam ?? ''; ?>';

        // MediaMTX API Configuration
        const MEDIAMTX_HOST = '127.0.0.1';
        const MEDIAMTX_API_PORT = 9997;
        const MEDIAMTX_API_URL = `http://${MEDIAMTX_HOST}:${MEDIAMTX_API_PORT}/v3/paths/list`;

        // Streaming Configuration
        const STREAMING_HOST = '0.0.0.0';
        const STREAMING_PORT = 8889; // WebRTC port

        // Monitor Intervals
        const CHECK_INTERVAL = 10000; // 10 seconds
        const FREEZE_TIMEOUT = 15000; // 15 seconds (no bytes change = frozen)

        // Camera tracking state
        let cameraStates = {};
        let cameraByteHistory = {}; // Track bytesReceived for freeze detection

        // Status constants
        const STATUS = {
            ONLINE: 'ONLINE',
            OFFLINE: 'OFFLINE',
            FROZEN: 'FROZEN',
            UNKNOWN: 'UNKNOWN'
        };

        /**
         * Fetch paths list from MediaMTX API
         */
        async function fetchMediaMTXPaths() {
            try {
                const response = await fetch(`${MEDIAMTX_API_URL}?_=${Date.now()}`, {
                    headers: { 'cache-control': 'no-cache' }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                return data.items || [];
            } catch (error) {
                console.error('❌ Cannot connect to MediaMTX API:', error);
                return null; // Return null to indicate API error
            }
        }

        /**
         * Determine camera status based on MediaMTX path info
         */
        function determineCameraStatus(pathInfo, cameraName) {
            const now = Date.now();

            // Initialize byte history if not exists
            if (!cameraByteHistory[cameraName]) {
                cameraByteHistory[cameraName] = {
                    lastBytes: 0,
                    lastBytesTime: now,
                    status: STATUS.UNKNOWN
                };
            }

            const history = cameraByteHistory[cameraName];

            // ===== OFFLINE CHECK =====
            if (!pathInfo ||
                pathInfo.ready !== true ||
                !pathInfo.source ||
                pathInfo.source.type !== 'rtmpConn') {
                history.status = STATUS.OFFLINE;
                history.lastBytes = 0;
                history.lastBytesTime = now;
                return STATUS.OFFLINE;
            }

            // ===== FROZEN CHECK =====
            const currentBytes = pathInfo.bytesReceived || 0;

            // First time seeing this camera online
            if (history.lastBytes === 0) {
                history.lastBytes = currentBytes;
                history.lastBytesTime = now;
                history.status = STATUS.ONLINE;
                return STATUS.ONLINE;
            }

            // Bytes increased = camera is streaming data
            if (currentBytes > history.lastBytes) {
                history.lastBytes = currentBytes;
                history.lastBytesTime = now;
                history.status = STATUS.ONLINE;
                return STATUS.ONLINE;
            }

            // Bytes haven't changed for too long = FROZEN
            if (now - history.lastBytesTime > FREEZE_TIMEOUT) {
                history.status = STATUS.FROZEN;
                return STATUS.FROZEN;
            }

            // Still considered online (within freeze timeout window)
            return history.status === STATUS.FROZEN ? STATUS.FROZEN : STATUS.ONLINE;
        }

        /**
         * Process MediaMTX paths and return camera status array
         */
        async function fetchCameraStatusesFromAPI() {
            const items = await fetchMediaMTXPaths();

            // API connection error
            if (items === null) {
                return [];
            }

            const cameraStats = [];

            items.forEach(pathInfo => {
                const cameraName = pathInfo.name;
                const status = determineCameraStatus(pathInfo, cameraName);

                cameraStats.push({
                    camera: cameraName,
                    path: pathInfo.name,
                    status: status,
                    bytesReceived: pathInfo.bytesReceived || 0,
                    bytesSent: pathInfo.bytesSent || 0,
                    readyTime: pathInfo.readyTime || null,
                    tracks: pathInfo.tracks || [],
                    readers: pathInfo.readers?.length || 0,
                    sourceType: pathInfo.source?.type || 'unknown'
                });
            });

            return cameraStats;
        }

        /**
         * Update dashboard statistics
         */
        function updateDashboardStats(cameraData) {
            const total = cameraData.length;
            const online = cameraData.filter(cam => cam.status === STATUS.ONLINE).length;
            const offline = cameraData.filter(cam => cam.status === STATUS.OFFLINE).length;
            const frozen = cameraData.filter(cam => cam.status === STATUS.FROZEN).length;
            const selected = document.querySelectorAll('.form-check-input:checked').length;

            document.getElementById('totalCameras').textContent = total;
            document.getElementById('onlineCameras').textContent = online;
            document.getElementById('offlineCameras').textContent = offline;
            document.getElementById('frozenCameras').textContent = frozen;
            document.getElementById('selectedCameras').textContent = selected;
        }

        /**
         * Get status display properties
         */
        function getStatusDisplay(status) {
            switch (status) {
                case STATUS.ONLINE:
                    return { class: 'online', text: 'ออนไลน์', icon: '🟢', color: '#10b981' };
                case STATUS.OFFLINE:
                    return { class: 'offline', text: 'ออฟไลน์', icon: '🔴', color: '#ef4444' };
                case STATUS.FROZEN:
                    return { class: 'frozen', text: 'ภาพค้าง', icon: '🟡', color: '#f59e0b' };
                default:
                    return { class: 'unknown', text: 'ไม่ทราบ', icon: '⚪', color: '#6b7280' };
            }
        }

        /**
         * Update camera status table
         */
        function updateCameraTable(cameraData) {
            const tbody = document.getElementById('cameraTableBody');

            if (cameraData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: #6b7280;">
                            <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                            ไม่พบกล้องจาก MediaMTX API<br>
                            <small>กรุณาตรวจสอบการเชื่อมต่อ</small>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = cameraData.map(cam => {
                const statusDisplay = getStatusDisplay(cam.status);
                const streamUrl = `http://${STREAMING_HOST}:${STREAMING_PORT}/${cam.path}`;
                const currentTime = new Date().toLocaleTimeString('th-TH');
                const bytesFormatted = formatBytes(cam.bytesReceived);

                return `
                    <tr>
                        <td>
                            <strong>${cam.camera}</strong>
                            <br><small style="color: #6b7280; font-size: 0.75rem;">
                                ${cam.tracks.join(', ')} • ${cam.readers} viewers
                            </small>
                        </td>
                        <td>
                            <span class="table-status-badge ${statusDisplay.class}" style="background-color: ${statusDisplay.color}15; color: ${statusDisplay.color}; border: 1px solid ${statusDisplay.color}30;">
                                <span class="table-status-dot" style="background-color: ${statusDisplay.color};"></span>
                                ${statusDisplay.text}
                            </span>
                        </td>
                        <td style="font-size: 0.8rem; color: #6b7280;">
                            ${bytesFormatted} received
                        </td>
                        <td>${currentTime}</td>
                    </tr>
                `;
            }).join('');
        }

        /**
         * Format bytes to human readable string
         */
        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        /**
         * Render camera selection dropdown
         */
        async function renderCameraDropdown() {
            const dropdown = document.getElementById('cameraDropdown');
            dropdown.innerHTML = '<div style="text-align: center; padding: 1rem;"><div class="loading-spinner"></div></div>';

            const apiCameraStats = await fetchCameraStatusesFromAPI();

            // Update dashboard stats and table
            updateDashboardStats(apiCameraStats);
            updateCameraTable(apiCameraStats);

            dropdown.innerHTML = '';

            if (apiCameraStats.length === 0) {
                dropdown.innerHTML = `
                    <div style="text-align: center; padding: 1rem; color: #6b7280;">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i>
                        ไม่พบกล้องจาก MediaMTX
                    </div>
                `;
                return;
            }

            apiCameraStats.forEach((cameraData, index) => {
                const cameraName = cameraData.camera;
                const statusDisplay = getStatusDisplay(cameraData.status);

                // Generate WebRTC streaming URL
                const FULL_STREAMING_URL = `http://${STREAMING_HOST}:${STREAMING_PORT}/${cameraData.path}`;

                const htmlId = `camera-${cameraName.replace(/[^a-zA-Z0-9]/g, '-')}`;
                const isChecked = cameraData.status !== STATUS.OFFLINE ? 'checked' : '';

                const cameraItem = document.createElement('div');
                cameraItem.className = 'camera-item';
                cameraItem.innerHTML = `
                    <input class="form-check-input"
                        type="checkbox"
                        id="${htmlId}"
                        data-camera-name="${cameraName}"
                        data-camera-path="${cameraData.path}"
                        data-stream-url="${FULL_STREAMING_URL}"
                        data-status="${cameraData.status}"
                        ${isChecked}>
                    <label class="camera-label" for="${htmlId}">
                        <i class="fas fa-video me-2"></i>${cameraName}
                    </label>
                    <span id="${htmlId}-status" class="camera-status ${statusDisplay.class}" 
                          style="background-color: ${statusDisplay.color};" 
                          title="${statusDisplay.text}"></span>
                `;

                dropdown.appendChild(cameraItem);
            });

            const checkboxes = document.querySelectorAll('.form-check-input');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateStreams);
            });
        }

        /**
         * Update video streams based on selection
         */
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

            // Remove unselected cameras
            document.querySelectorAll('.video-wrapper').forEach(wrapper => {
                const wrapperId = wrapper.id.replace('stream-', '');
                if (!selectedIds.has(wrapperId)) {
                    wrapper.remove();
                    delete cameraStates[wrapperId];
                }
            });

            // Add/update selected cameras
            selectedCheckboxes.forEach(checkbox => {
                const stream = {
                    id: checkbox.id,
                    name: checkbox.dataset.cameraName,
                    path: checkbox.dataset.cameraPath,
                    streamUrl: checkbox.dataset.streamUrl,
                    status: checkbox.dataset.status
                };

                let videoWrapper = document.querySelector(`#stream-${CSS.escape(stream.id)}`);
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

        /**
         * Create video wrapper element
         */
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

        /**
         * Update camera display based on status
         */
        function updateCameraDisplay(wrapper, camera) {
            const statusDisplay = getStatusDisplay(camera.status);
            const statusClass = `status-badge-${statusDisplay.class}`;

            if (camera.status === STATUS.OFFLINE) {
                wrapper.innerHTML = `
                    <div class="video-header">
                        <div class="video-name">
                            <i class="fas fa-video"></i>
                            ${camera.name}
                        </div>
                        <div class="video-status-badge ${statusClass}" style="background-color: ${statusDisplay.color}20; color: ${statusDisplay.color};">
                            <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                            ${statusDisplay.text}
                        </div>
                    </div>
                    <div class="video-content">
                        <div class="video-placeholder">
                            <i class="fas fa-video-slash" style="color: ${statusDisplay.color};"></i>
                            <p>กล้องไม่พร้อมใช้งาน</p>
                            <small>Camera is offline - No RTMP connection</small>
                        </div>
                    </div>
                `;
            } else if (camera.status === STATUS.FROZEN) {
                wrapper.innerHTML = `
                    <div class="video-header">
                        <div class="video-name">
                            <i class="fas fa-video"></i>
                            ${camera.name}
                        </div>
                        <div class="video-status-badge ${statusClass}" style="background-color: ${statusDisplay.color}20; color: ${statusDisplay.color};">
                            <i class="fas fa-snowflake" style="font-size: 0.6rem;"></i>
                            ${statusDisplay.text}
                        </div>
                    </div>
                    <div class="video-content">
                        <div class="video-placeholder" style="background: linear-gradient(135deg, #fef3c720 0%, #fbbf2420 100%);">
                            <i class="fas fa-snowflake" style="color: ${statusDisplay.color}; animation: spin 3s linear infinite;"></i>
                            <p style="color: ${statusDisplay.color};">ภาพค้าง - ไม่มีข้อมูลใหม่</p>
                            <small>Stream frozen - No new data received</small>
                        </div>
                    </div>
                `;
            } else {
                // ONLINE - Show iframe
                wrapper.innerHTML = `
                    <div class="video-header">
                        <div class="video-name">
                            <i class="fas fa-video"></i>
                            ${camera.name}
                        </div>
                        <div class="video-status-badge ${statusClass}" style="background-color: ${statusDisplay.color}20; color: ${statusDisplay.color};">
                            <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                            ${statusDisplay.text}
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

        // ===== PERIODIC STATUS CHECK =====
        setInterval(async () => {
            console.log('================================');
            console.log('=== MediaMTX RTMP PUSH MONITOR ===');
            console.log(new Date().toLocaleString('th-TH'));

            const apiCameraStats = await fetchCameraStatusesFromAPI();
            let hasChanged = false;

            // Update dashboard stats and table
            updateDashboardStats(apiCameraStats);
            updateCameraTable(apiCameraStats);

            // Log status for each camera
            apiCameraStats.forEach(cam => {
                const statusDisplay = getStatusDisplay(cam.status);
                console.log(`${statusDisplay.icon} ${cam.camera} (${cam.path}) ${cam.status}`);
            });

            // Check for status changes in checkboxes
            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                const cameraName = checkbox.dataset.cameraName;
                const cameraInfo = apiCameraStats.find(cam => cam.camera === cameraName);

                if (cameraInfo) {
                    const newStatus = cameraInfo.status;
                    const currentStatus = checkbox.dataset.status;

                    if (currentStatus !== newStatus) {
                        hasChanged = true;
                        checkbox.dataset.status = newStatus;

                        const statusDisplay = getStatusDisplay(newStatus);
                        const statusElement = document.querySelector(`#${CSS.escape(checkbox.id)}-status`);
                        if (statusElement) {
                            statusElement.className = `camera-status ${statusDisplay.class}`;
                            statusElement.style.backgroundColor = statusDisplay.color;
                            statusElement.title = statusDisplay.text;
                        }

                        // Alert on status change
                        console.log(`⚡ Status changed: ${cameraName} ${currentStatus} → ${newStatus}`);
                    }
                }
            });

            if (hasChanged) {
                await updateStreams();
            }
        }, CHECK_INTERVAL);

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