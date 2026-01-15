<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันตัวตน | Identity Verification</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-orange: #E67E22;
            --primary-orange-light: #F39C12;
            --primary-orange-dark: #D35400;
            --accent-maroon: #8B3A3A;
            --dark-bg: #1A1A2E;
            --dark-bg-light: #25253D;
            --text-light: #F8F9FA;
            --text-muted: #ADB5BD;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --success-green: #28A745;
            --gradient-orange: linear-gradient(135deg, #E67E22 0%, #F5B041 50%, #E67E22 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Prompt', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1A1A2E 0%, #2D2D44 50%, #1A1A2E 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 20% 20%, rgba(230, 126, 34, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(230, 126, 34, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 40% 60%, rgba(230, 126, 34, 0.03) 0%, transparent 30%);
            animation: floatBackground 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes floatBackground {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            33% {
                transform: translate(30px, -30px) rotate(1deg);
            }

            66% {
                transform: translate(-20px, 20px) rotate(-1deg);
            }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--primary-orange);
            border-radius: 50%;
            opacity: 0.3;
            animation: float 15s infinite;
        }

        .particle:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
            animation-duration: 20s;
        }

        .particle:nth-child(2) {
            left: 20%;
            animation-delay: 2s;
            animation-duration: 25s;
        }

        .particle:nth-child(3) {
            left: 30%;
            animation-delay: 4s;
            animation-duration: 18s;
        }

        .particle:nth-child(4) {
            left: 40%;
            animation-delay: 1s;
            animation-duration: 22s;
        }

        .particle:nth-child(5) {
            left: 50%;
            animation-delay: 3s;
            animation-duration: 19s;
        }

        .particle:nth-child(6) {
            left: 60%;
            animation-delay: 5s;
            animation-duration: 21s;
        }

        .particle:nth-child(7) {
            left: 70%;
            animation-delay: 2.5s;
            animation-duration: 24s;
        }

        .particle:nth-child(8) {
            left: 80%;
            animation-delay: 1.5s;
            animation-duration: 17s;
        }

        .particle:nth-child(9) {
            left: 90%;
            animation-delay: 3.5s;
            animation-duration: 23s;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.3;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                transform: translateY(-100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .main-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 520px;
        }

        .verification-card {
            background: linear-gradient(145deg, rgba(45, 45, 68, 0.95) 0%, rgba(26, 26, 46, 0.98) 100%);
            border-radius: 24px;
            padding: 50px 45px;
            box-shadow:
                0 25px 80px rgba(0, 0, 0, 0.5),
                0 10px 40px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1),
                0 0 0 1px rgba(230, 126, 34, 0.2);
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        /* Decorative corner accents */
        .verification-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(230, 126, 34, 0.25) 0%, transparent 50%);
            border-radius: 24px 0 0 0;
        }

        .verification-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(315deg, rgba(230, 126, 34, 0.2) 0%, transparent 50%);
            border-radius: 0 0 24px 0;
        }

        .card-content {
            position: relative;
            z-index: 1;
        }

        /* Logo/Icon Section */
        .logo-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .shield-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-orange);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow:
                0 10px 30px rgba(230, 126, 34, 0.4),
                0 5px 15px rgba(230, 126, 34, 0.3);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 10px 30px rgba(230, 126, 34, 0.4), 0 5px 15px rgba(230, 126, 34, 0.3);
            }

            50% {
                box-shadow: 0 15px 40px rgba(230, 126, 34, 0.5), 0 8px 20px rgba(230, 126, 34, 0.4);
            }
        }

        .shield-icon i {
            font-size: 36px;
            color: var(--dark-bg);
        }

        .header-title {
            color: var(--text-light);
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .header-subtitle {
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 300;
            line-height: 1.6;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            color: var(--text-light);
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            color: var(--primary-orange);
            font-size: 16px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px;
            padding-left: 50px;
            background: rgba(255, 255, 255, 0.05) !important;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: #ffffff !important;
            font-family: 'Prompt', sans-serif;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        /* Fix for autofill background and text color */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus,
        .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px rgba(45, 45, 68, 1) inset !important;
            -webkit-text-fill-color: #ffffff !important;
            caret-color: #ffffff !important;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-orange);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(230, 126, 34, 0.2);
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 18px;
            color: var(--primary-orange);
            font-size: 18px;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-control:focus+.input-icon,
        .form-control:not(:placeholder-shown)+.input-icon {
            color: var(--primary-orange-light);
        }

        /* Validation States */
        .form-control.is-valid {
            border-color: var(--success-green);
        }

        .form-control.is-invalid {
            border-color: #DC3545;
        }

        .invalid-feedback {
            color: #DC3545;
            font-size: 13px;
            margin-top: 8px;
            display: none;
        }

        .form-control.is-invalid~.invalid-feedback {
            display: block;
        }

        /* Submit Button */
        .btn-verify {
            width: 100%;
            padding: 18px 30px;
            background: var(--gradient-orange);
            border: none;
            border-radius: 14px;
            color: #ffffff;
            font-family: 'Prompt', sans-serif;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 35px;
        }

        .btn-verify::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(230, 126, 34, 0.5);
        }

        .btn-verify:hover::before {
            left: 100%;
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .btn-verify i {
            font-size: 20px;
        }

        /* Security Badge */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .security-badge i {
            color: var(--success-green);
            font-size: 16px;
        }

        .security-badge span {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 300;
        }

        /* Trust Indicators */
        .trust-indicators {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .trust-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .trust-item i {
            color: var(--primary-orange);
            font-size: 20px;
        }

        .trust-item span {
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Loading State */
        .btn-verify.loading {
            pointer-events: none;
        }

        .btn-verify.loading .btn-text {
            opacity: 0;
        }

        .spinner {
            display: none;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            position: absolute;
        }

        .btn-verify.loading .spinner {
            display: block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Success Animation */
        .success-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(26, 26, 46, 0.98);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .success-overlay.show {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .success-checkmark {
            width: 100px;
            height: 100px;
            background: var(--gradient-orange);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            animation: scaleIn 0.5s ease 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-checkmark i {
            font-size: 48px;
            color: #ffffff;
        }

        .success-title {
            color: var(--text-light);
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
            animation: slideUp 0.5s ease 0.4s both;
        }

        .success-message {
            color: var(--text-muted);
            font-size: 16px;
            animation: slideUp 0.5s ease 0.5s both;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 576px) {
            body {
                padding: 15px;
            }

            .verification-card {
                padding: 35px 20px;
                border-radius: 18px;
            }

            .shield-icon {
                width: 65px;
                height: 65px;
            }

            .shield-icon i {
                font-size: 28px;
            }

            .header-title {
                font-size: 22px;
            }

            .header-subtitle {
                font-size: 14px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-label {
                font-size: 13px;
                margin-bottom: 8px;
            }

            .form-control {
                padding: 14px 18px;
                padding-left: 45px;
                font-size: 14px;
            }

            .input-icon {
                left: 15px;
                top: 17px;
                font-size: 16px;
            }

            .invalid-feedback {
                font-size: 12px;
                margin-top: 6px;
            }

            .btn-verify {
                padding: 16px 25px;
                font-size: 16px;
                margin-top: 25px;
            }

            .security-badge {
                flex-direction: column;
                gap: 5px;
                margin-top: 25px;
                padding-top: 20px;
            }

            .security-badge span {
                font-size: 11px;
                text-align: center;
            }

            .trust-indicators {
                gap: 15px;
                margin-top: 15px;
            }

            .trust-item i {
                font-size: 18px;
            }

            .trust-item span {
                font-size: 9px;
            }

            .success-checkmark {
                width: 80px;
                height: 80px;
            }

            .success-checkmark i {
                font-size: 38px;
            }

            .success-title {
                font-size: 24px;
            }

            .success-message {
                font-size: 14px;
            }
        }

        /* Medium screens */
        @media (max-width: 768px) and (min-width: 577px) {
            .verification-card {
                padding: 45px 35px;
            }

            .header-title {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>
    <!-- Floating Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="main-container">
        <div class="verification-card">
            <div class="card-content">
                <!-- Logo Section -->
                <div class="logo-section">
                    <div class="shield-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h1 class="header-title">ยืนยันตัวตน</h1>
                    <p class="header-subtitle">
                        กรุณากรอกข้อมูลของท่านเพื่อยืนยันตัวตน<br>ข้อมูลของท่านจะถูกเก็บรักษาอย่างปลอดภัย</p>
                </div>

                <!-- Verification Form -->
                <form id="verificationForm" novalidate>
                    <!-- Last Name -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person"></i>
                            นามสกุล
                        </label>
                        <div class="input-wrapper">
                            <input type="text" class="form-control" id="lastName" name="lastName"
                                placeholder="กรุณากรอกนามสกุล" required>
                            <i class="bi bi-person-fill input-icon"></i>
                            <div class="invalid-feedback">กรุณากรอกนามสกุลของท่าน</div>
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-telephone"></i>
                            เบอร์โทรศัพท์
                        </label>
                        <div class="input-wrapper">
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="0XX-XXX-XXXX"
                                pattern="[0-9]{10}" required>
                            <i class="bi bi-telephone-fill input-icon"></i>
                            <div class="invalid-feedback">กรุณากรอกเบอร์โทรศัพท์ 10 หลัก</div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-envelope"></i>
                            อีเมล
                        </label>
                        <div class="input-wrapper">
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="example@email.com" required>
                            <i class="bi bi-envelope-fill input-icon"></i>
                            <div class="invalid-feedback">กรุณากรอกอีเมลที่ถูกต้อง</div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-verify" id="submitBtn">
                        <span class="btn-text">ยืนยันตัวตน</span>
                        <i class="bi bi-arrow-right-circle-fill btn-text"></i>
                        <div class="spinner"></div>
                    </button>
                </form>

                <!-- Security Badge -->
                <div class="security-badge">
                    <i class="bi bi-lock-fill"></i>
                    <span>ข้อมูลของท่านได้รับการเข้ารหัสและปกป้องอย่างปลอดภัย</span>
                </div>

                <!-- Trust Indicators -->
                <div class="trust-indicators">
                    <div class="trust-item">
                        <i class="bi bi-shield-lock"></i>
                        <span>ปลอดภัย</span>
                    </div>
                    <div class="trust-item">
                        <i class="bi bi-patch-check"></i>
                        <span>น่าเชื่อถือ</span>
                    </div>
                    <div class="trust-item">
                        <i class="bi bi-clock-history"></i>
                        <span>รวดเร็ว</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Overlay -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-checkmark">
            <i class="bi bi-check-lg"></i>
        </div>
        <h2 class="success-title">ยืนยันตัวตนสำเร็จ!</h2>
        <p class="success-message">ขอบคุณสำหรับการยืนยันตัวตน</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('verificationForm');
            const submitBtn = document.getElementById('submitBtn');
            const successOverlay = document.getElementById('successOverlay');

            // Phone number formatting
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) {
                    value = value.slice(0, 10);
                }
                e.target.value = value;
            });

            // Real-time validation
            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('blur', function () {
                    validateField(this);
                });

                input.addEventListener('input', function () {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                });
            });

            function validateField(field) {
                let isValid = true;

                if (field.id === 'lastName') {
                    isValid = field.value.trim().length >= 2;
                } else if (field.id === 'phone') {
                    isValid = /^[0-9]{10}$/.test(field.value);
                } else if (field.id === 'email') {
                    isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value);
                }

                if (isValid) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                } else {
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                }

                return isValid;
            }

            // Form submission
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                let isFormValid = true;
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isFormValid = false;
                    }
                });

                if (isFormValid) {
                    // Show loading state
                    submitBtn.classList.add('loading');

                    // Simulate API call
                    setTimeout(() => {
                        submitBtn.classList.remove('loading');
                        successOverlay.classList.add('show');

                        // Collect form data
                        const formData = {
                            lastName: document.getElementById('lastName').value,
                            phone: document.getElementById('phone').value,
                            email: document.getElementById('email').value
                        };

                        console.log('Verification Data:', formData);

                        // Hide success overlay after 3 seconds
                        setTimeout(() => {
                            successOverlay.classList.remove('show');
                            form.reset();
                            inputs.forEach(input => {
                                input.classList.remove('is-valid');
                            });
                        }, 3000);
                    }, 2000);
                }
            });
        });
    </script>
</body>

</html>
</CodeContent>
<parameter name="EmptyFile">false