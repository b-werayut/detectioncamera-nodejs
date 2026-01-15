<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['Username'] ?? '';
$userRoleName = "User";

if (!empty($user) && isset($conn)) {
    try {
        $sqlGetRole = "SELECT r.UserRole 
                       FROM Users u 
                       INNER JOIN Role r ON u.RoleID = r.RoleID 
                       WHERE u.Username = ?";
        $stmtRole = $conn->prepare($sqlGetRole);
        $stmtRole->execute([$user]);
        $roleResult = $stmtRole->fetch(PDO::FETCH_ASSOC);

        if ($roleResult && !empty($roleResult['UserRole'])) {
            $userRoleName = ucfirst($roleResult['UserRole']);
        }
    } catch (PDOException $e) {
        // Error handling
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-logo text-center">
        <img src="https://itp1.itopfile.com/ImageServer/z_itp_11032024vrm0/0/0/wwwz-z732105416303.png" 
             alt="NetworkLink Logo" 
             class="img-fluid logo-image w-75">
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="project.php" class="nav-link <?= $current_page == 'project.php' ? 'active' : '' ?>">
                <i class="fas fa-folder"></i> โครงการ
            </a>
        </li>
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-map-marker-alt"></i> สถานที่ติดตั้ง
            </a>
        </li>
        <li class="nav-item">
            <a href="camera.php" class="nav-link <?= $current_page == 'camera.php' ? 'active' : '' ?>">
                <i class="fas fa-camera"></i> กล้อง
            </a>
        </li>
        
        <?php if (isset($_SESSION['RoleID']) && ($_SESSION['RoleID'] == 1 || $_SESSION['RoleID'] == 2)): ?> 
        <li class="nav-item">
            <a href="users.php" class="nav-link <?= $current_page == 'users.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> ผู้ใช้งาน
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
            <a href="alert.php" class="nav-link <?= $current_page == 'alert.php' ? 'active' : '' ?>">
                <i class="fas fa-bell"></i> การแจ้งเตือน
            </a>
        </li>
    </ul>

    <div>
        <a href="../livenotifyvideo/index.php" class="btn btn-outline-light w-100">
            <i class="fas fa-video me-2"></i> กลับไปหน้าดูวิดีโอ
        </a>
    </div>

    <div class="user-profile shadow-sm">
        <div class="d-flex align-items-center">
            <div class="bg-green text-white rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 35px; height: 35px; text-transform: uppercase; font-weight: bold;">
                 <?= mb_substr($user, 0, 1) ?>
            </div>
            
            <div class="ms-2">
                <span class="d-block fw-bold" style="font-size: 0.9rem;">
                    <?= htmlspecialchars($user) ?>
                </span>
                
                <span class="text-muted" style="font-size: 0.7rem;">
                    <i class="fas fa-user-shield me-1"></i><?= htmlspecialchars($userRoleName) ?>
                </span>
            </div>
        </div>
        
        <a href="../logout.php" class="text-danger" onclick="return confirm('ยืนยันการออกจากระบบ?');">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</nav>