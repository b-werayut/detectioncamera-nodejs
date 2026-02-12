<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ — Streaming Control Center</title>
    <meta name="description" content="ระบบติดตามและบริหารจัดการกล้องแบบเรียลไทม์ โดย NetWorklink Co.Ltd.">
    <link rel="icon" type="image/x-icon" href="./assets/favicon/favicon.ico" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="./js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ============================================================
           LOGIN — Streaming Control Center
           Premium Authentication Interface
           ============================================================ */
        :root {
            --primary-green: #0d4d3d;
            --secondary-green: #1a6b54;
            --accent-green: #26d07c;
            --light-green: #edf7f5;
            --pale-green: #f0faf6;
            --dark-bg: #071e18;
            --darker-bg: #030f0b;
            --surface-primary: #ffffff;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #9ca3af;
            --border-light: rgba(13, 77, 61, 0.08);
            --card-shadow: 0 8px 40px rgba(0, 0, 0, 0.08), 0 2px 12px rgba(13, 77, 61, 0.06);
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --transition-smooth: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Kanit', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--darker-bg);
            -webkit-font-smoothing: antialiased;
        }

        ::selection {
            background: rgba(38, 208, 124, 0.25);
            color: var(--primary-green);
        }

        /* --- Layout: Split Screen --- */
        .login-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* --- Left: Brand Showcase Panel --- */
        .brand-panel {
            flex: 1;
            background: linear-gradient(160deg, var(--dark-bg) 0%, var(--primary-green) 50%, var(--secondary-green) 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(38, 208, 124, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(38, 208, 124, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(13, 77, 61, 0.1) 0%, transparent 70%);
            z-index: 1;
        }

        /* Animated grid pattern */
        .brand-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(38, 208, 124, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(38, 208, 124, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 1;
            animation: gridDrift 20s linear infinite;
        }

        @keyframes gridDrift {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(60px, 60px);
            }
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 480px;
        }

        .brand-logo-wrap {
            width: 150px;
            height: 150px;
            /* background: rgba(255, 255, 255, 0.1); */
            background-color: white;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: floatIn 0.8s ease-out both;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .brand-logo-wrap img {
            width: 85%;
            height: auto;
            filter: brightness(1.1);
        }

        .brand-title {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
            animation: fadeUp 0.7s ease-out 0.2s both;
        }

        .brand-subtitle {
            font-size: 0.95rem;
            font-weight: 300;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.7;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.7s ease-out 0.35s both;
        }

        /* Feature pills */
        .brand-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            justify-content: center;
            animation: fadeUp 0.7s ease-out 0.5s both;
        }

        .feature-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 50px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.78rem;
            font-weight: 400;
            transition: all 0.3s ease;
        }

        .feature-pill i {
            color: var(--accent-green);
            font-size: 0.7rem;
        }

        .feature-pill:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            transform: translateY(-1px);
        }

        /* Floating decorative elements */
        .deco-orb {
            position: absolute;
            border-radius: 50%;
            z-index: 0;
            opacity: 0.5;
        }

        .deco-orb-1 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(38, 208, 124, 0.12), transparent 70%);
            top: -80px;
            left: -80px;
            animation: pulse 6s ease-in-out infinite;
        }

        .deco-orb-2 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(38, 208, 124, 0.08), transparent 70%);
            bottom: -60px;
            right: -60px;
            animation: pulse 8s ease-in-out infinite reverse;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.15);
                opacity: 0.7;
            }
        }

        /* --- Right: Login Form Panel --- */
        .form-panel {
            width: 520px;
            min-width: 420px;
            background: var(--surface-primary);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 3.5rem;
            position: relative;
            overflow-y: auto;
        }

        .form-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(180deg, var(--accent-green), var(--primary-green), transparent);
        }

        .form-header {
            margin-bottom: 2.5rem;
            animation: fadeUp 0.5s ease-out 0.3s both;
        }

        .form-header .greeting {
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--accent-green);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-header .greeting::before {
            content: '';
            width: 20px;
            height: 2px;
            background: var(--accent-green);
            border-radius: 2px;
        }

        .form-header h2 {
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.3px;
            margin-bottom: 0.4rem;
        }

        .form-header p {
            font-size: 0.88rem;
            color: var(--text-muted);
            font-weight: 300;
        }

        /* Input fields */
        .scc-field {
            position: relative;
            margin-bottom: 1.4rem;
            animation: fadeUp 0.5s ease-out both;
        }

        .scc-field:nth-child(1) {
            animation-delay: 0.4s;
        }

        .scc-field:nth-child(2) {
            animation-delay: 0.5s;
        }

        .scc-field-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            letter-spacing: 0.3px;
        }

        .scc-input-wrap {
            position: relative;
        }

        .scc-input-wrap .field-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.9rem;
            transition: color var(--transition-smooth);
            z-index: 2;
        }

        .scc-input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.8rem;
            font-family: 'Kanit', sans-serif;
            font-size: 0.92rem;
            font-weight: 400;
            color: var(--text-primary);
            background: var(--surface-primary);
            border: 1.5px solid rgba(13, 77, 61, 0.12);
            border-radius: var(--radius-md);
            outline: none;
            transition: all var(--transition-smooth);
        }

        .scc-input::placeholder {
            color: transparent;
        }

        .scc-input:hover {
            border-color: rgba(13, 77, 61, 0.25);
        }

        .scc-input:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(38, 208, 124, 0.1);
            background: #fff;
        }

        .scc-input:focus~.field-icon {
            color: var(--accent-green);
        }

        .toggle-pass {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.9rem;
            transition: color 0.2s ease;
            z-index: 2;
            background: none;
            border: none;
            padding: 0.25rem;
        }

        .toggle-pass:hover {
            color: var(--primary-green);
        }

        /* Login Button */
        .scc-login-btn {
            width: 100%;
            padding: 0.9rem;
            font-family: 'Kanit', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #fff;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all var(--transition-smooth);
            box-shadow: 0 4px 16px rgba(13, 77, 61, 0.25);
            animation: fadeUp 0.5s ease-out 0.6s both;
        }

        .scc-login-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .scc-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(13, 77, 61, 0.35);
        }

        .scc-login-btn:hover::before {
            transform: translateX(100%);
        }

        .scc-login-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(13, 77, 61, 0.3);
        }

        .scc-login-btn i {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .scc-login-btn:hover i {
            transform: translateX(3px);
        }

        /* Divider */
        .form-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
            animation: fadeUp 0.5s ease-out 0.65s both;
        }

        .form-divider::before,
        .form-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border-light), transparent);
        }

        .form-divider span {
            font-size: 0.72rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 500;
        }

        /* System status badge */
        .system-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.72rem;
            color: var(--text-muted);
            animation: fadeUp 0.5s ease-out 0.7s both;
        }

        .system-badge .dot {
            width: 6px;
            height: 6px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        /* Footer copyright */
        .login-footer {
            margin-top: 2.5rem;
            text-align: center;
            animation: fadeUp 0.5s ease-out 0.75s both;
        }

        .login-footer p {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 300;
        }

        .login-footer a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
        }

        /* --- Forgot Password Modal --- */
        .modal-content {
            border: none;
            border-radius: var(--radius-lg) !important;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        }

        .modal-header-scc {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header-scc h5 {
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .modal-header-scc .btn-close-modal {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease;
            font-size: 0.85rem;
        }

        .modal-header-scc .btn-close-modal:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .modal-body-scc {
            padding: 2rem 2rem 1.5rem;
        }

        .modal-body-scc .scc-field {
            animation: none;
        }

        .modal-body-scc .security-icon {
            width: 56px;
            height: 56px;
            background: var(--light-green);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }

        .modal-body-scc .security-icon i {
            font-size: 1.4rem;
            color: var(--primary-green);
        }

        .modal-body-scc .scc-reset-btn {
            width: 100%;
            padding: 0.8rem;
            font-family: 'Kanit', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all var(--transition-smooth);
            box-shadow: 0 4px 12px rgba(13, 77, 61, 0.2);
        }

        .modal-body-scc .scc-reset-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(13, 77, 61, 0.3);
        }

        /* --- SweetAlert2 Theme Override --- */
        .swal2-popup {
            border-radius: 20px !important;
            font-family: 'Kanit', sans-serif !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
        }

        .swal2-title {
            font-weight: 700 !important;
            font-size: 1.25rem !important;
        }

        .swal2-html-container {
            font-size: 0.9rem !important;
        }

        .swal2-confirm {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)) !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 0.6rem 2rem !important;
            box-shadow: 0 4px 12px rgba(13, 77, 61, 0.25) !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(38, 208, 124, 0.3) !important;
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: var(--accent-green) !important;
        }

        .swal2-timer-progress-bar {
            background: linear-gradient(90deg, var(--primary-green), var(--accent-green)) !important;
        }

        /* --- Animations --- */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatIn {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* --- Scrollbar (modal) --- */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(13, 77, 61, 0.15);
            border-radius: 10px;
        }

        /* --- Login Card (visible on mobile) --- */
        .login-card {
            position: relative;
        }

        .login-card-icon {
            display: none;
        }

        /* --- Responsive --- */
        @media (max-width: 992px) {
            html, body {
                overflow-y: auto;
            }

            .brand-panel {
                display: none;
            }

            .login-wrapper {
                flex-direction: column;
                background: linear-gradient(180deg, #f0faf6 0%, #ffffff 40%, #ffffff 100%);
                min-height: 100vh;
                height: auto;
            }

            .form-panel {
                width: 100%;
                min-width: unset;
                padding: 2rem 1.75rem 2.5rem;
                background: transparent;
                flex: 1;
            }

            .form-panel::before {
                display: none;
            }

            /* Mobile Brand Card */
            .mobile-brand {
                display: flex !important;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 0;
                margin: 0 0 1.5rem;
                padding: 2rem 1.5rem 1.5rem;
                background: linear-gradient(160deg, var(--dark-bg) 0%, var(--primary-green) 50%, var(--secondary-green) 100%);
                border-radius: 24px;
                position: relative;
                overflow: hidden;
                animation: fadeUp 0.4s ease-out both;
                box-shadow:
                    0 8px 32px rgba(13, 77, 61, 0.18),
                    0 2px 8px rgba(0, 0, 0, 0.06);
                border: 1px solid rgba(38, 208, 124, 0.12);
            }

            .mobile-brand::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(circle at 20% 80%, rgba(38, 208, 124, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(38, 208, 124, 0.1) 0%, transparent 50%);
                pointer-events: none;
            }

            .mobile-brand::after {
                content: '';
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(38, 208, 124, 0.04) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(38, 208, 124, 0.04) 1px, transparent 1px);
                background-size: 40px 40px;
                pointer-events: none;
                animation: gridDrift 20s linear infinite;
            }

            /* Glow orb decoration */
            .mobile-brand-glow {
                position: absolute;
                top: -30px;
                right: -30px;
                width: 120px;
                height: 120px;
                background: radial-gradient(circle, rgba(38, 208, 124, 0.2), transparent 70%);
                border-radius: 50%;
                z-index: 0;
                animation: pulse 6s ease-in-out infinite;
            }

            .mobile-brand .mobile-logo {
                width: 72px;
                height: 72px;
                background: rgba(255, 255, 255, 0.95);
                border-radius: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                margin-bottom: 0.85rem;
                position: relative;
                z-index: 1;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                animation: floatIn 0.6s ease-out 0.1s both;
            }

            .mobile-brand .mobile-logo img {
                width: 68%;
            }

            .mobile-brand .mobile-text {
                position: relative;
                z-index: 1;
            }

            .mobile-brand .mobile-text h3 {
                font-size: 1.05rem;
                font-weight: 700;
                color: #ffffff;
                margin: 0 0 0.2rem;
                letter-spacing: 1px;
                text-transform: uppercase;
                animation: fadeUp 0.5s ease-out 0.2s both;
            }

            .mobile-brand .mobile-text p {
                font-size: 0.75rem;
                color: rgba(255, 255, 255, 0.55);
                margin: 0;
                letter-spacing: 0.5px;
                font-weight: 400;
                animation: fadeUp 0.5s ease-out 0.25s both;
            }

            /* Brand tagline */
            .mobile-brand-tagline {
                position: relative;
                z-index: 1;
                margin-top: 0.6rem;
                padding: 0 1rem;
                animation: fadeUp 0.5s ease-out 0.3s both;
            }

            .mobile-brand-tagline span {
                font-size: 0.7rem;
                color: rgba(255, 255, 255, 0.45);
                font-weight: 300;
                line-height: 1.5;
                letter-spacing: 0.3px;
            }

            /* Mobile feature pills */
            .mobile-brand .mobile-features {
                display: flex;
                flex-wrap: wrap;
                gap: 0.4rem;
                justify-content: center;
                margin-top: 0.85rem;
                position: relative;
                z-index: 1;
                animation: fadeUp 0.5s ease-out 0.35s both;
            }

            .mobile-brand .mobile-features .m-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.3rem 0.7rem;
                background: rgba(255, 255, 255, 0.07);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 50px;
                color: rgba(255, 255, 255, 0.7);
                font-size: 0.65rem;
                font-weight: 400;
                transition: all 0.25s ease;
            }

            .mobile-brand .mobile-features .m-pill i {
                color: var(--accent-green);
                font-size: 0.58rem;
            }

            /* Brand status badge */
            .mobile-brand-status {
                display: flex;
                align-items: center;
                gap: 0.4rem;
                margin-top: 1rem;
                padding: 0.3rem 0.85rem;
                background: rgba(38, 208, 124, 0.1);
                border: 1px solid rgba(38, 208, 124, 0.15);
                border-radius: 50px;
                font-size: 0.62rem;
                font-weight: 500;
                color: rgba(38, 208, 124, 0.85);
                letter-spacing: 0.8px;
                text-transform: uppercase;
                position: relative;
                z-index: 1;
                animation: fadeUp 0.5s ease-out 0.4s both;
            }

            .mobile-brand-status .brand-dot {
                width: 5px;
                height: 5px;
                background: var(--accent-green);
                border-radius: 50%;
                animation: blink 2s ease-in-out infinite;
                box-shadow: 0 0 6px rgba(38, 208, 124, 0.5);
            }

            /* Mobile Login Card */
            .login-card {
                background: #ffffff;
                border-radius: 24px;
                padding: 2rem 1.5rem 1.75rem;
                box-shadow:
                    0 4px 24px rgba(13, 77, 61, 0.08),
                    0 1px 4px rgba(0, 0, 0, 0.04);
                border: 1px solid rgba(13, 77, 61, 0.06);
                position: relative;
                animation: fadeUp 0.5s ease-out 0.3s both;
            }

            .login-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 60px;
                height: 3px;
                background: linear-gradient(90deg, var(--accent-green), var(--primary-green));
                border-radius: 0 0 4px 4px;
            }

            .login-card-icon {
                display: flex !important;
                width: 48px;
                height: 48px;
                background: linear-gradient(135deg, var(--light-green), #e0f5ec);
                border: 2px solid rgba(38, 208, 124, 0.15);
                border-radius: 16px;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.25rem;
                animation: floatIn 0.5s ease-out 0.35s both;
            }

            .login-card-icon i {
                font-size: 1.1rem;
                color: var(--primary-green);
            }

            .form-header {
                margin-bottom: 1.5rem;
                text-align: center;
            }

            .form-header .greeting {
                justify-content: center;
            }

            .form-header .greeting::before {
                display: none;
            }

            .login-footer {
                margin-top: 1.25rem;
                animation: fadeUp 0.5s ease-out 0.5s both;
            }
        }

        @media (max-width: 480px) {
            .form-panel {
                padding: 1.25rem !important;
            }

            .mobile-brand {
                padding: 1.5rem 1.25rem 1.25rem !important;
                margin: 0 0 1.25rem !important;
                border-radius: 20px !important;
            }

            .mobile-brand .mobile-logo {
                width: 60px;
                height: 60px;
                border-radius: 14px;
            }

            .mobile-brand .mobile-text h3 {
                font-size: 0.92rem;
            }

            .mobile-brand-tagline span {
                font-size: 0.65rem;
            }

            .login-card {
                padding: 1.5rem 1.25rem 1.5rem;
                border-radius: 20px;
            }

            .form-header h2 {
                font-size: 1.35rem;
            }
        }

        @media (min-width: 993px) {
            .mobile-brand {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">

        <!-- Left: Brand Showcase Panel -->
        <div class="brand-panel">
            <div class="deco-orb deco-orb-1"></div>
            <div class="deco-orb deco-orb-2"></div>

            <div class="brand-content">
                <div class="brand-logo-wrap">
                    <img src="./assets/brand/nwl-logo.png" alt="NetWorklink Logo">
                </div>
                <h1 class="brand-title text-uppercase" style="letter-spacing: 1.5px;">Streaming Control Center</h1>
                <p class="brand-subtitle" style="letter-spacing: 1.5px;">
                    ระบบติดตามและบริหารจัดการกล้องอัจฉริยะแบบเรียลไทม์<br>
                    โดย NetWorklink Co.Ltd.
                </p>
                <div class="brand-features">
                    <span class="feature-pill"><i class="fas fa-video"></i> Real-time Streaming</span>
                    <span class="feature-pill"><i class="fas fa-shield-halved"></i> Secure Monitoring</span>
                    <span class="feature-pill"><i class="fas fa-chart-line"></i> Smart Analytics</span>
                    <span class="feature-pill"><i class="fas fa-bell"></i> Instant Alerts</span>
                </div>
            </div>
        </div>

        <!-- Right: Login Form Panel -->
        <div class="form-panel">

            <!-- Mobile-only brand card -->
            <div class="mobile-brand">
                <div class="mobile-brand-glow"></div>
                <div class="mobile-logo">
                    <img src="./assets/brand/nwl-logo.png" alt="Logo">
                </div>
                <div class="mobile-text">
                    <h3>Streaming Control Center</h3>
                    <p>NetWorklink Co.Ltd.</p>
                </div>
                <div class="mobile-brand-tagline">
                    <span>ระบบติดตามและบริหารจัดการกล้องอัจฉริยะแบบเรียลไทม์</span>
                </div>
                <div class="mobile-features">
                    <span class="m-pill"><i class="fas fa-video"></i> Streaming</span>
                    <span class="m-pill"><i class="fas fa-shield-halved"></i> Secure</span>
                    <span class="m-pill"><i class="fas fa-chart-line"></i> Analytics</span>
                    <span class="m-pill"><i class="fas fa-bell"></i> Alerts</span>
                </div>
                <div class="mobile-brand-status">
                    <span class="brand-dot"></span> System Online
                </div>
            </div>

            <!-- Login Card -->
            <div class="login-card">
                <div class="login-card-icon">
                    <i class="fas fa-lock"></i>
                </div>

                <div class="form-header">
                    <div class="greeting">AUTHENTICATION</div>
                    <h2>เข้าสู่ระบบ</h2>
                    <p>กรุณากรอกข้อมูลเพื่อเข้าใช้งานระบบ</p>
                </div>

                <form id="loginForm" autocomplete="off">
                    <div class="scc-field">
                        <label class="scc-field-label" for="username">ชื่อผู้ใช้</label>
                        <div class="scc-input-wrap">
                            <i class="fas fa-user field-icon"></i>
                            <input type="text" class="scc-input" id="username" required placeholder="กรอกชื่อผู้ใช้">
                        </div>
                    </div>

                    <div class="scc-field">
                        <label class="scc-field-label" for="password">รหัสผ่าน</label>
                        <div class="scc-input-wrap">
                            <i class="fas fa-lock field-icon"></i>
                            <input type="password" class="scc-input" id="password" required placeholder="กรอกรหัสผ่าน">
                            <button type="button" class="toggle-pass togglepass-login" tabindex="-1">
                                <i class="fa-regular fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <button class="scc-login-btn" type="button" onclick="login()">
                        เข้าสู่ระบบ <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="form-divider"><span>SECURED CONNECTION</span></div>

                <div class="system-badge">
                    <span class="dot"></span>
                    ระบบพร้อมใช้งาน &mdash; SSL Encrypted
                </div>
            </div>

            <div class="login-footer">
                <p>&copy; <?= date('Y'); ?> <a href="#">NetWorklink Co.Ltd.</a> &mdash; Intelligent Camera Management
                    System</p>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgetpasswordmodal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-scc">
                    <h5><i class="fas fa-key"></i> รีเซ็ทรหัสผ่าน</h5>
                    <button type="button" class="btn-close-modal" id="btn-close-forgetpassword" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body-scc">
                    <div class="security-icon">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <form id="forgetpassForm">
                        <div class="scc-field">
                            <label class="scc-field-label" for="forgetusername">ชื่อผู้ใช้</label>
                            <div class="scc-input-wrap">
                                <i class="fas fa-user field-icon"></i>
                                <input type="text" class="scc-input" id="forgetusername" required
                                    placeholder="กรอกชื่อผู้ใช้">
                            </div>
                        </div>
                        <div class="scc-field">
                            <label class="scc-field-label" for="forgetemail">อีเมล</label>
                            <div class="scc-input-wrap">
                                <i class="fas fa-envelope field-icon"></i>
                                <input type="email" class="scc-input" id="forgetemail" required placeholder="กรอกอีเมล">
                            </div>
                        </div>
                        <div class="scc-field">
                            <label class="scc-field-label" for="forgetpassword">รหัสผ่านใหม่</label>
                            <div class="scc-input-wrap">
                                <i class="fas fa-lock field-icon"></i>
                                <input type="password" class="scc-input" id="forgetpassword" required
                                    placeholder="กรอกรหัสผ่านใหม่">
                            </div>
                        </div>
                        <div class="scc-field">
                            <label class="scc-field-label" for="confirmpassword">ยืนยันรหัสผ่าน</label>
                            <div class="scc-input-wrap">
                                <i class="fas fa-lock field-icon"></i>
                                <input type="password" class="scc-input" id="confirmpassword" required
                                    placeholder="กรอกรหัสผ่านอีกครั้ง">
                            </div>
                        </div>
                        <button type="submit" class="scc-reset-btn">
                            <i class="fas fa-rotate-right me-2"></i> รีเซ็ทรหัสผ่าน
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            $(".togglepass-login").click(function () {
                let inputpass = $("#password");
                let icon = $(this).find('i');
                if (inputpass.attr("type") == "password") {
                    inputpass.attr("type", "text");
                    icon.removeClass("fa-eye-slash").addClass("fa-eye");
                } else {
                    inputpass.attr("type", "password");
                    icon.removeClass("fa-eye").addClass("fa-eye-slash");
                }
            });

            $("#username, #password").keypress((e) => {
                if (e.key === "Enter") login();
            });

            window.login = function () {
                const username = $("#username").val().trim();
                const password = $("#password").val().trim();

                if (username === "" || password === "") {
                    Swal.fire({
                        title: "ข้อมูลไม่ครบถ้วน",
                        text: "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน",
                        icon: "warning",
                        confirmButtonText: "ตกลง"
                    });
                    return;
                }

                $.ajax({
                    url: 'loginroutes.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ username, password }),
                    success: (result) => {
                        const baseConfig = {
                            position: "center",
                            background: '#fefefe',
                            showConfirmButton: false,
                            customClass: {
                                title: 'swal2-title-custom',
                                popup: 'swal2-popup-custom'
                            }
                        };

                        if (result.val == 3) {
                            Swal.fire({
                                ...baseConfig,
                                icon: "success",
                                title: "เข้าสู่ระบบสำเร็จ",
                                text: "กำลังพาท่านเข้าสู่ระบบ...",
                                timer: 2000
                            }).then(() => {
                                window.location.href = "/LiveNotifyVideo/index.php";
                            });
                        } else if (result.val == 1) {
                            Swal.fire({ icon: "error", title: "ไม่พบชื่อผู้ใช้งานนี้", confirmButtonText: "ตกลง" });
                        } else if (result.val == 2) {
                            Swal.fire({ icon: "error", title: "รหัสผ่านไม่ถูกต้อง", confirmButtonText: "ตกลง" });
                        } else {
                            Swal.fire({ icon: "error", title: "เกิดข้อผิดพลาด", text: result.message, confirmButtonText: "ตกลง" });
                        }
                    },
                    error: (err) => {
                        console.error(err);
                        Swal.fire({ icon: "error", title: "เชื่อมต่อเซิร์ฟเวอร์ไม่ได้" });
                    }
                });
            };

            $("#forgetpassForm").on('submit', function (e) {
                e.preventDefault();
                resetPass();
            });

            $('#btn-close-forgetpassword').click(function () {
                $("#forgetpassForm")[0].reset();
            });

            function resetPass() {
                const username = $("#forgetusername").val().trim();
                const email = $('#forgetemail').val().trim();
                const password = $("#forgetpassword").val().trim();
                const passwordcf = $("#confirmpassword").val().trim();

                if (password !== passwordcf) {
                    Swal.fire({ icon: "warning", title: "รหัสผ่านไม่ตรงกัน", confirmButtonText: "ตกลง" });
                    return;
                }

                $.ajax({
                    url: "forgotpasswordfunction.php",
                    type: "POST",
                    data: { username, email, password, passwordcf },
                    success: (result) => {
                        if (result == 3) {
                            Swal.fire({
                                icon: "success",
                                title: "รีเซ็ตรหัสผ่านสำเร็จ",
                                text: "ระบบจะปิดหน้าต่างนี้โดยอัตโนมัติ",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#forgetpasswordmodal').modal('hide');
                            $("#forgetpassForm")[0].reset();
                        } else if (result == 1) {
                            Swal.fire({ icon: "warning", title: "ชื่อผู้ใช้งานไม่ถูกต้อง" });
                        } else if (result == 2) {
                            Swal.fire({ icon: "warning", title: "อีเมลไม่ถูกต้อง" });
                        } else {
                            Swal.fire({ icon: "error", title: "เกิดข้อผิดพลาด" });
                        }
                    },
                    error: (err) => {
                        console.error(err);
                        Swal.fire({ icon: "error", title: "เกิดข้อผิดพลาดในการเชื่อมต่อ" });
                    }
                });
            }

            const inputs = document.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('invalid', () => {
                    input.setCustomValidity('กรุณากรอกข้อมูลในช่องนี้');
                });
                input.addEventListener('input', () => {
                    input.setCustomValidity('');
                });
            });

            const forgetEmailInput = document.getElementById('forgetemail');
            if (forgetEmailInput) {
                forgetEmailInput.addEventListener('invalid', function (e) {
                    if (forgetEmailInput.validity.typeMismatch) {
                        forgetEmailInput.setCustomValidity("กรุณากรอกอีเมลให้ถูกต้อง");
                    } else {
                        forgetEmailInput.setCustomValidity('');
                    }
                });
                forgetEmailInput.addEventListener('input', function () {
                    forgetEmailInput.setCustomValidity('');
                });
            }

        });
    </script>
</body>

</html>