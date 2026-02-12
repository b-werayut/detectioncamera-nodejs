<?php

// ตั้งค่า default ให้กับตัวแปรที่อาจไม่มี
$urlstream = $urlstream ?? '';
$roleId = $roleId ?? 0;
$user = $user ?? '';
$role = $role ?? '';
$firstname = $firstname ?? '';
$lastname = $lastname ?? '';
$login_time = $login_time ?? '';
$currentPage = $currentPage ?? '';

// กำหนด Active Class สำหรับแต่ละเมนู
$activeStreaming = ($currentPage === 'streaming') ? 'active' : '';
$activeSnapshot = ($currentPage === 'snapshot') ? 'active' : '';
$activeVideo = ($currentPage === 'video') ? 'active' : '';
$activeManage = ($currentPage === 'manage') ? 'active' : '';
?>

<!-- Professional Navbar -->
<nav class="scc-navbar" id="sccNavbar">
    <div class="container px-lg-5 py-2">
        <div class="scc-navbar-inner">
            <!-- Brand -->
            <a class="scc-brand" href="#!">
                <div class="scc-brand-logo">
                    <img src="../snapshot/assets/nwl-logo.png" alt="NetWorklink" width="42">
                </div>
                <div class="scc-brand-text">
                    <span class="scc-brand-name d-none d-sm-block" style="letter-spacing: 1.5px;">NETWORK LINK
                        CO.,LTD.</span>
                    <span class="scc-brand-name d-block d-sm-none">NWL</span>
                    <span class="scc-brand-tagline d-none d-md-block">Streaming Control Center</span>
                </div>
            </a>

            <!-- Mobile Toggle -->
            <button class="scc-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sccNavCollapse"
                aria-controls="sccNavCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="scc-toggle-bar"></span>
                <span class="scc-toggle-bar"></span>
                <span class="scc-toggle-bar"></span>
            </button>

            <!-- Navigation -->
            <div class="collapse navbar-collapse" id="sccNavCollapse">
                <ul class="scc-nav ms-auto">
                    <li class="scc-nav-item">
                        <a class="scc-nav-link <?= $activeStreaming; ?>" href="<?= $urlstream; ?>">
                            <i class="fas fa-satellite-dish"></i>
                            <span>สตรีมมิ่ง</span>
                        </a>
                    </li>
                    <li class="scc-nav-item">
                        <a class="scc-nav-link <?= $activeSnapshot; ?>" href="/SnapShot/snappaging_.php">
                            <i class="fas fa-camera"></i>
                            <span>ภาพนิ่ง</span>
                        </a>
                    </li>
                    <li class="scc-nav-item">
                        <a class="scc-nav-link <?= $activeVideo; ?>" href="/SnapShot/vdopaging_.php">
                            <i class="fas fa-film"></i>
                            <span>วิดีโอ</span>
                        </a>
                    </li>
                    <?php if ($roleId == 1 || $roleId == 2): ?>
                        <li class="scc-nav-item">
                            <a class="scc-nav-link <?= $activeManage; ?>" href="../Management/index.php">
                                <i class="fas fa-sliders-h"></i>
                                <span>จัดการระบบ</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Divider -->
                    <li class="scc-nav-divider"></li>

                    <!-- User Menu -->
                    <li class="scc-nav-item scc-nav-user">
                        <div class="scc-user-dropdown" id="sccUserDropdown">
                            <button class="scc-user-trigger" type="button" aria-expanded="false">
                                <span class="scc-user-avatar">
                                    <?= strtoupper(substr($firstname, 0, 1)); ?>
                                </span>
                                <span class="scc-user-meta d-none d-md-flex">
                                    <span class="scc-user-name"
                                        style="letter-spacing: 1px;"><?= htmlspecialchars($firstname) . " " . htmlspecialchars($lastname); ?></span>
                                </span>
                                <i class="fas fa-chevron-down scc-user-arrow d-none d-md-inline"></i>
                            </button>
                            <div class="scc-user-panel">
                                <div class="scc-user-panel-header">
                                    <div class="scc-user-panel-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="scc-user-panel-name" style="letter-spacing: 1px;">
                                        <?= htmlspecialchars($firstname) . " " . htmlspecialchars($lastname); ?>
                                    </div>
                                    <?php if (!empty($role)): ?>
                                        <div class="scc-user-panel-role"><?= htmlspecialchars($role); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="scc-user-panel-body">
                                    <?php if (!empty($role)): ?>
                                        <div class="scc-user-panel-item">
                                            <i class="fas fa-shield-alt"></i>
                                            <span><?= $role; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($login_time)): ?>
                                        <div class="scc-user-panel-item">
                                            <i class="fas fa-clock"></i>
                                            <span>เข้าสู่ระบบ: <?= date('H:i น.', $login_time); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="scc-user-panel-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><?= date('d/m/Y'); ?></span>
                                    </div>
                                    <div class="scc-user-panel-divider"></div>
                                    <a href="../logout.php" class="scc-user-panel-item scc-logout">
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
    </div>
</nav>

<style>
    /* ================================================================
   SCC NAVBAR — Streaming Control Center Professional Navigation
   ================================================================ */
    .scc-navbar {
        background: linear-gradient(135deg, #061a14 0%, #0a3d2e 50%, #0d4d3d 100%);
        padding: 0;
        position: sticky;
        top: 0;
        z-index: 1100;
        box-shadow: 0 2px 24px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.03) inset;
        border-bottom: 1px solid rgba(38, 208, 124, 0.1);
    }

    .scc-navbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 64px;
    }

    /* --- Brand --- */
    .scc-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        flex-shrink: 0;
    }

    .scc-brand-logo {
        position: relative;
    }

    .scc-brand-logo img {
        filter: drop-shadow(0 2px 8px rgba(38, 208, 124, 0.2));
        transition: transform 0.3s ease;
    }

    .scc-brand:hover .scc-brand-logo img {
        transform: scale(1.06);
    }

    .scc-brand-text {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
    }

    .scc-brand-name {
        color: #fff;
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .scc-brand-tagline {
        color: rgba(38, 208, 124, 0.7);
        font-size: 0.65rem;
        font-weight: 500;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-top: 1px;
    }

    /* --- Mobile Toggle --- */
    .scc-toggle {
        display: flex;
        flex-direction: column;
        gap: 5px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .scc-toggle:hover,
    .scc-toggle:focus {
        background: rgba(38, 208, 124, 0.15);
        border-color: rgba(38, 208, 124, 0.3);
        outline: none;
    }

    .scc-toggle-bar {
        width: 22px;
        height: 2px;
        background: rgba(255, 255, 255, 0.85);
        border-radius: 2px;
        transition: all 0.3s ease;
    }

    /* --- Mobile Collapse --- */
    .scc-navbar .navbar-collapse {
        background: linear-gradient(180deg, rgba(10, 61, 46, 0.98) 0%, rgba(6, 26, 20, 0.98) 100%);
        border-radius: 0 0 20px 20px;
        margin: 0 -12px;
        padding: 1rem 1.25rem 1.25rem;
        backdrop-filter: blur(20px);
        border-top: 1px solid rgba(38, 208, 124, 0.1);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
    }

    /* --- Nav Items --- */
    .scc-nav {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .scc-nav-item {
        width: 100%;
    }

    .scc-nav-link {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.75rem 1rem;
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .scc-nav-link i {
        width: 20px;
        text-align: center;
        font-size: 0.88rem;
        transition: transform 0.25s ease;
    }

    .scc-nav-link:hover {
        color: #fff;
        background: rgba(38, 208, 124, 0.1);
        transform: translateX(4px);
    }

    .scc-nav-link:hover i {
        transform: scale(1.15);
        color: #26d07c;
    }

    .scc-nav-link.active {
        color: #fff;
        background: linear-gradient(135deg, rgba(38, 208, 124, 0.2) 0%, rgba(38, 208, 124, 0.08) 100%);
        border: 1px solid rgba(38, 208, 124, 0.2);
        font-weight: 600;
    }

    .scc-nav-link.active i {
        color: #26d07c;
    }

    .scc-nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 60%;
        background: #26d07c;
        border-radius: 0 4px 4px 0;
        box-shadow: 0 0 8px rgba(38, 208, 124, 0.5);
    }

    /* --- Nav Divider --- */
    .scc-nav-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.08);
        margin: 0.5rem 0;
        list-style: none;
    }

    /* --- User Dropdown --- */
    .scc-user-dropdown {
        width: 100%;
        position: relative;
    }

    .scc-user-trigger {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.6rem 0.9rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        color: #fff;
        font-family: inherit;
        font-size: inherit;
    }

    .scc-user-trigger:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(38, 208, 124, 0.2);
    }

    .scc-user-dropdown.is-open .scc-user-trigger {
        background: rgba(38, 208, 124, 0.12);
        border-color: rgba(38, 208, 124, 0.25);
    }

    .scc-user-avatar {
        width: 34px;
        height: 34px;
        min-width: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #26d07c, #0d8f5f);
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        box-shadow: 0 2px 8px rgba(38, 208, 124, 0.3);
    }

    .scc-user-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.2;
        flex: 1;
    }

    .scc-user-name {
        font-size: 0.82rem;
        font-weight: 600;
        color: #fff;
    }

    .scc-user-role {
        font-size: 0.68rem;
        color: rgba(255, 255, 255, 0.5);
        font-weight: 400;
    }

    .scc-user-arrow {
        font-size: 0.6rem;
        color: rgba(255, 255, 255, 0.4);
        transition: transform 0.3s ease;
    }

    .scc-user-dropdown.is-open .scc-user-arrow {
        transform: rotate(180deg);
    }

    /* --- User Panel (Dropdown Menu) --- */
    .scc-user-panel {
        display: none;
        position: static;
        background: #fff;
        border-radius: 14px;
        margin-top: 0.5rem;
        overflow: hidden;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.25);
        animation: sccPanelIn 0.25s ease;
    }

    .scc-user-dropdown.is-open .scc-user-panel {
        display: block;
    }

    @keyframes sccPanelIn {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(0.97);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .scc-user-panel-header {
        padding: 1.25rem 1rem;
        background: linear-gradient(135deg, #0d4d3d 0%, #1a6b54 100%);
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .scc-user-panel-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(38, 208, 124, 0.12) 0%, transparent 60%);
        pointer-events: none;
    }

    .scc-user-panel-avatar {
        width: 52px;
        height: 52px;
        background: rgba(255, 255, 255, 0.12);
        border: 2px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin: 0 auto 0.6rem;
        position: relative;
    }

    .scc-user-panel-name {
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: 0.2px;
        position: relative;
    }

    .scc-user-panel-role {
        display: inline-block;
        padding: 0.2rem 0.75rem;
        background: rgba(38, 208, 124, 0.2);
        border: 1px solid rgba(38, 208, 124, 0.2);
        border-radius: 20px;
        font-size: 0.7rem;
        margin-top: 0.4rem;
        letter-spacing: 0.5px;
        font-weight: 500;
        position: relative;
    }

    .scc-user-panel-body {
        padding: 0.5rem;
    }

    .scc-user-panel-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 1rem;
        color: #4b5563;
        font-size: 0.85rem;
        border-radius: 10px;
        transition: all 0.2s ease;
        cursor: default;
        text-decoration: none;
    }

    .scc-user-panel-item:hover {
        background: #f0faf6;
    }

    .scc-user-panel-item i {
        width: 18px;
        text-align: center;
        color: #0d4d3d;
        font-size: 0.85rem;
    }

    .scc-user-panel-divider {
        height: 1px;
        background: #f0f0f0;
        margin: 0.35rem 0.5rem;
    }

    .scc-logout {
        color: #ef4444 !important;
        cursor: pointer;
    }

    .scc-logout i {
        color: #ef4444 !important;
    }

    .scc-logout:hover {
        background: #fef2f2 !important;
    }

    /* ================================================================
   DESKTOP (min-width: 992px)
   ================================================================ */
    @media (min-width: 992px) {
        .scc-navbar {
            padding: 0;
        }

        .scc-navbar-inner {
            min-height: 68px;
        }

        .scc-toggle {
            display: none;
        }

        .scc-navbar .navbar-collapse {
            display: flex !important;
            background: transparent;
            margin: 0;
            padding: 0;
            border-radius: 0;
            border-top: none;
            box-shadow: none;
            backdrop-filter: none;
        }

        .scc-nav {
            flex-direction: row;
            align-items: center;
            gap: 6px;
        }

        .scc-nav-item {
            width: auto;
        }

        .scc-nav-link {
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            gap: 0.5rem;
        }

        .scc-nav-link:hover {
            transform: translateY(-2px);
        }

        .scc-nav-link.active {
            background: linear-gradient(135deg, rgba(38, 208, 124, 0.18) 0%, rgba(38, 208, 124, 0.06) 100%);
            box-shadow: 0 0 12px rgba(38, 208, 124, 0.15);
        }

        .scc-nav-link.active::before {
            left: 50%;
            top: auto;
            bottom: -2px;
            transform: translateX(-50%);
            width: 60%;
            height: 2px;
            border-radius: 4px 4px 0 0;
        }

        .scc-nav-divider {
            width: 1px;
            height: 28px;
            margin: 0 8px;
            background: rgba(255, 255, 255, 0.1);
        }

        /* User Dropdown Desktop */
        .scc-user-dropdown {
            width: auto;
        }

        .scc-user-trigger {
            width: auto;
            padding: 0.45rem 0.85rem;
            border-radius: 50px;
        }

        .scc-user-avatar {
            width: 30px;
            height: 30px;
            min-width: 30px;
            font-size: 0.78rem;
            border-radius: 8px;
        }

        .scc-user-panel {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 260px;
            z-index: 1100;
        }
    }

    /* ================================================================
   LARGE DESKTOP (min-width: 1200px)
   ================================================================ */
    @media (min-width: 1200px) {
        .scc-brand-name {
            font-size: 1.1rem;
        }

        .scc-nav-link {
            padding: 0.5rem 1.15rem;
        }
    }

    /* ================================================================
   SMALL MOBILE (max-width: 576px)
   ================================================================ */
    @media (max-width: 576px) {
        .scc-brand-logo img {
            width: 36px;
        }

        .scc-brand-name {
            font-size: 0.9rem;
        }

        .scc-nav-link {
            padding: 0.65rem 0.85rem;
            font-size: 0.88rem;
        }

        .scc-user-avatar {
            width: 30px;
            height: 30px;
            min-width: 30px;
        }
    }
</style>

<script>
    (function () {
        var dropdown = document.getElementById('sccUserDropdown');
        if (!dropdown) return;
        var trigger = dropdown.querySelector('.scc-user-trigger');

        function toggle(open) {
            dropdown.classList.toggle('is-open', open);
            if (trigger) trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        if (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggle(!dropdown.classList.contains('is-open'));
            });
        }

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target)) toggle(false);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') toggle(false);
        });
    })();
</script>