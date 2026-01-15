<?php
$user = $_SESSION['Username'] ?? null;        // [Username]
$roleId = $_SESSION['RoleID'] ?? 0;           // [RoleID]
$userProjectID = $_SESSION['ProjectID'] ?? 0; // [ProjectID]
$auth = $_SESSION['auth'] ?? null;
$urlstream = '';
$userRole = $_SESSION['UserRole'] ?? null;
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
                    <a class="nav-link active" aria-current="page" href="<?= $urlstream; ?>">
                        <i class="fas fa-video me-1"></i> สตรีมมิ่ง
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/SnapShot/snappaging_.php">
                        <i class="fas fa-camera me-1"></i> ภาพนิ่ง
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/SnapShot/vdopaging_.php">
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
                <?php //if (empty($auth)): ?>
                <?php //if (empty($auth) && !empty($user)): ?>
                <li class="nav-item d-flex align-items-center">
                    <div class="user-dropdown">
                        <span class="user-badge">
                            <span class="user-avatar"><?= strtoupper(substr($user, 0, 1)); ?></span>
                            <span class="user-info">
                                <span class="user-name"><?= htmlspecialchars($user); ?></span>
                            </span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </span>
                        <div class="user-dropdown-menu">
                            <div class="user-dropdown-header">
                                <div class="avatar-large">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="name"><?= htmlspecialchars($user); ?></div>
                                <?php if (!empty($role)): ?>
                                    <div class="role-badge"><?= htmlspecialchars($role); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="user-dropdown-body">
                                <?php if (!empty($userRole)): ?>
                                    <div class="user-dropdown-item">
                                        <i class="fas fa-user-shield"></i>
                                        <span class="user-role"><?= htmlspecialchars($userRole); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($login_time)): ?>
                                    <div class="user-dropdown-item">
                                        <i class="fas fa-clock"></i>
                                        <span>เข้าสู่ระบบ: <?= date('H:i น.', $login_time); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="user-dropdown-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <span><?= date('d/m/Y'); ?></span>
                                </div>
                                <div class="user-dropdown-divider"></div>
                                <a href="../logout.php" class="user-dropdown-item logout"
                                    style="text-decoration: none;">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>ออกจากระบบ</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <?php //endif; ?>
                <!-- <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
                        </a>
                    </li> -->
                <?php //endif; ?>
            </ul>
        </div>
    </div>
</nav>