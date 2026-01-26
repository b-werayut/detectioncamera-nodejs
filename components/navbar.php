<?php

// ตั้งค่า default ให้กับตัวแปรที่อาจไม่มี
$urlstream = $urlstream ?? '';
$roleId = $roleId ?? 0;
$user = $user ?? '';
$role = $role ?? '';
$login_time = $login_time ?? '';
$currentPage = $currentPage ?? '';

// กำหนด Active Class สำหรับแต่ละเมนู
$activeStreaming = ($currentPage === 'streaming') ? 'active' : '';
$activeSnapshot = ($currentPage === 'snapshot') ? 'active' : '';
$activeVideo = ($currentPage === 'video') ? 'active' : '';
$activeManage = ($currentPage === 'manage') ? 'active' : '';
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container px-lg-5">
        <a class="navbar-brand" href="#!">
            <img src="../snapshot/assets/nwl-logo.png" alt="NetWorklink" width="50">
            <span class="d-none d-sm-inline">NetWorklink Co.Ltd.</span>
            <span class="d-inline d-sm-none">NWL</span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <!-- สตรีมมิ่ง -->
                <li class="nav-item">
                    <a class="nav-link <?= $activeStreaming; ?>" aria-current="page" href="<?= $urlstream; ?>">
                        <i class="fas fa-video me-1"></i>
                        <span class="nav-text">สตรีมมิ่ง</span>
                    </a>
                </li>

                <!-- ภาพนิ่ง -->
                <li class="nav-item">
                    <a class="nav-link <?= $activeSnapshot; ?>" href="/SnapShot/snappaging_.php">
                        <i class="fas fa-camera me-1"></i>
                        <span class="nav-text">ภาพนิ่ง</span>
                    </a>
                </li>

                <!-- วิดีโอ -->
                <li class="nav-item">
                    <a class="nav-link <?= $activeVideo; ?>" href="/SnapShot/vdopaging_.php">
                        <i class="fas fa-film me-1"></i>
                        <span class="nav-text">วิดีโอ</span>
                    </a>
                </li>

                <!-- จัดการระบบ - SuperAdmin/Admin Only -->
                <?php if ($roleId == 1 || $roleId == 2): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeManage; ?>" href="../Management/index.php">
                            <i class="fas fa-cog me-1"></i>
                            <span class="nav-text">จัดการระบบ</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- User Dropdown -->
                <li class="nav-item d-flex align-items-center">
                    <div class="user-dropdown">
                        <span class="user-badge" role="button" tabindex="0" aria-expanded="false">
                            <span class="user-avatar">
                                <?= strtoupper(substr($user, 0, 1)); ?>
                            </span>
                            <span class="user-info d-none d-md-inline">
                                <span class="user-name">
                                    <?= htmlspecialchars($user); ?>
                                </span>
                                <?php if (!empty($role)): ?>
                                    <span class="user-role">
                                        <?= htmlspecialchars($role); ?>
                                    </span>
                                <?php endif; ?>
                            </span>
                            <i class="fas fa-chevron-down dropdown-arrow d-none d-md-inline"></i>
                        </span>
                        <div class="user-dropdown-menu">
                            <div class="user-dropdown-header">
                                <div class="avatar-large">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="name">
                                    <?= htmlspecialchars($user); ?>
                                </div>
                                <?php if (!empty($role)): ?>
                                    <div class="role-badge">
                                        <?= htmlspecialchars($role); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="user-dropdown-body">
                                <?php if (!empty($login_time)): ?>
                                    <div class="user-dropdown-item">
                                        <i class="fas fa-clock"></i>
                                        <span>เข้าสู่ระบบ:
                                            <?= date('H:i น.', $login_time); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($userRole)): ?>
                                    <div class="user-dropdown-item">
                                        <i class="fas fa-user-shield"></i>
                                        <span>
                                            <?= $userRole; ?>
                                        </span>
                                        <!-- <span>
                                           <?php //$userId; ?>
                                        </span> -->
                                    </div>
                                <?php endif; ?>
                                <div class="user-dropdown-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <span>
                                        <?= date('d/m/Y'); ?>
                                    </span>
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
            </ul>
        </div>
    </div>
</nav>

<style>
    /* ===============================================
   Responsive Navbar Styles
   =============================================== */

    /* Mobile First - Base Styles */
    .navbar {
        padding: 0.5rem 0;
    }

    .navbar-brand img {
        width: 40px;
        height: auto;
    }

    .navbar-brand span {
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Navbar Toggler Styling */
    .navbar-toggler {
        border: none;
        padding: 0.5rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .navbar-toggler:focus {
        box-shadow: none;
        outline: none;
        background: rgba(255, 255, 255, 0.2);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.85%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        width: 24px;
        height: 24px;
    }

    /* Mobile Menu Collapse */
    .navbar-collapse {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%);
        border-radius: 0 0 16px 16px;
        margin: 0.5rem -1rem -0.5rem;
        padding: 1rem;
        backdrop-filter: blur(10px);
    }

    /* Nav Items */
    .navbar-nav {
        gap: 0.25rem;
    }

    .nav-item {
        width: 100%;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem !important;
        border-radius: 10px;
        transition: all 0.3s ease;
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        color: #fff !important;
        transform: translateX(5px);
    }

    .nav-link i {
        width: 20px;
        text-align: center;
    }

    /* User Dropdown Mobile */
    .user-dropdown {
        width: 100%;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .user-dropdown.is-open .user-badge {
        background: rgba(255, 255, 255, 0.2);
    }

    .user-dropdown.is-open .dropdown-arrow {
        transform: rotate(180deg);
    }

    .user-dropdown-menu {
        display: none;
        position: static;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        margin-top: 0.5rem;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: fadeInUp 0.3s ease;
    }

    .user-dropdown.is-open .user-dropdown-menu {
        display: block;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user-dropdown-header {
        padding: 1.25rem;
        text-align: center;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
    }

    .user-dropdown-header .avatar-large {
        width: 50px;
        height: 50px;
        margin: 0 auto 0.75rem;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .user-dropdown-header .name {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .user-dropdown-header .role-badge {
        font-size: 0.75rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        display: inline-block;
    }

    .user-dropdown-body {
        padding: 0.75rem;
    }

    .user-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #374151;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .user-dropdown-item:hover {
        background: #f3f4f6;
    }

    .user-dropdown-item i {
        width: 18px;
        text-align: center;
        color: #6b7280;
    }

    .user-dropdown-item.logout {
        color: #ef4444;
    }

    .user-dropdown-item.logout i {
        color: #ef4444;
    }

    .user-dropdown-item.logout:hover {
        background: #fef2f2;
    }

    .user-dropdown-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 0.5rem 0;
    }

    /* ===============================================
   Tablet & Desktop Styles (min-width: 992px)
   =============================================== */
    @media (min-width: 992px) {
        .navbar {
            padding: 0.75rem 0;
        }

        .navbar-brand img {
            width: 50px;
        }

        .navbar-brand span {
            font-size: 1rem;
        }

        .navbar-collapse {
            background: transparent;
            margin: 0;
            padding: 0;
            border-radius: 0;
        }

        .navbar-nav {
            flex-direction: row;
            gap: 0.5rem;
        }

        .nav-item {
            width: auto;
        }

        .nav-link {
            padding: 0.5rem 1rem !important;
        }

        .nav-link:hover,
        .nav-link.active {
            transform: translateY(-2px);
        }

        /* User Dropdown Desktop */
        .user-dropdown {
            width: auto;
            margin-top: 0;
            padding-top: 0;
            border-top: none;
            position: relative;
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            min-width: 260px;
            margin-top: 0.5rem;
            z-index: 1050;
        }
    }

    /* ===============================================
   Large Desktop Styles (min-width: 1200px)
   =============================================== */
    @media (min-width: 1200px) {
        .navbar-brand span {
            font-size: 1.1rem;
        }

        .nav-link {
            padding: 0.5rem 1.25rem !important;
        }
    }

    /* ===============================================
   Small Mobile Styles (max-width: 576px)
   =============================================== */
    @media (max-width: 576px) {
        .navbar-brand img {
            width: 35px;
        }

        .navbar-brand span {
            font-size: 0.85rem;
            font-weight: 700;
        }

        .nav-link {
            padding: 0.65rem 0.75rem !important;
            font-size: 0.9rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            min-width: 32px;
            font-size: 0.8rem;
        }

        .user-dropdown-header {
            padding: 1rem;
        }

        .user-dropdown-header .avatar-large {
            width: 45px;
            height: 45px;
            font-size: 1rem;
        }
    }
</style>

<script>
    (function () {
        function setOpen(dropdown, open) {
            if (!dropdown) return;
            dropdown.classList.toggle('is-open', open);
            const badge = dropdown.querySelector('.user-badge');
            if (badge) badge.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        function closeAll(except) {
            document.querySelectorAll('.user-dropdown.is-open').forEach(function (dd) {
                if (except && dd === except) return;
                setOpen(dd, false);
            });
        }

        document.addEventListener('click', function (event) {
            const dropdown = event.target.closest('.user-dropdown');
            if (!dropdown) {
                closeAll();
                return;
            }

            const badge = event.target.closest('.user-badge');
            if (badge && dropdown.contains(badge)) {
                event.preventDefault();
                const willOpen = !dropdown.classList.contains('is-open');
                closeAll(dropdown);
                setOpen(dropdown, willOpen);
                return;
            }
        });

        document.addEventListener('keydown', function (event) {
            const badge = event.target.closest && event.target.closest('.user-badge');
            if (!badge) return;
            const dropdown = badge.closest('.user-dropdown');
            if (!dropdown) return;

            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                const willOpen = !dropdown.classList.contains('is-open');
                closeAll(dropdown);
                setOpen(dropdown, willOpen);
            }

            if (event.key === 'Escape') {
                setOpen(dropdown, false);
                badge.blur();
            }
        });
    })();
</script>