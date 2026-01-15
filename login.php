<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./assets/favicon/favicon.ico" />
    <link rel="stylesheet" href="./css/css.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css" />
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="./css/sweetalert2.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Login</title>
</head>

<body>
    <section class="vh-100 main-bg">
        <div class="blur-content h-100">
            <div class="container h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col col-xl-5">
                        <div class="mb-3 pb-1 d-flex justify-content-center text-center logo">
                            <div class="logo-circle">
                                <img src="./assets/brand/nwl-logo.png" alt="" width="50">
                            </div>
                        </div>
                        <div class="card"
                            style="border-radius: 15px; overflow: hidden; box-shadow: 5px 10px 10px #1e1e1e; top:-100px;">
                            <div class="card-body p-4 p-lg-5 text-black">
                                <form style="margin-top: 6rem;">
                                    <div class="inputGroup py-2">
                                        <input type="text" required autocomplete="off" class="form-control-lg"
                                            id="username">
                                        <label for="username">ชื่อผู้ใช้</label>
                                    </div>

                                    <div class="inputGroup">
                                        <input type="password" required autocomplete="off" class="form-control-lg"
                                            id="password">
                                        <label for="password">รหัสผ่าน</label>
                                        <i class="togglepass-login toggle-password fa-regular fa-eye-slash"></i>
                                    </div>

                                    <div class="pt-1 mb-4">
                                        <button class="login-btn" type="button" onclick="login()">เข้าสู่ระบบ</button>
                                    </div>

                                    <!-- <div class="d-flex justify-content-end">
                                        <a href="#" style="color: #666; font-size: 0.9rem;" data-bs-toggle="modal" data-bs-target="#forgetpasswordmodal">ลืมรหัสผ่าน?</a>
                                    </div> -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="forgetpasswordmodal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card text-center" style="width: 100%;">
                    <div class="card-header h5 text-white d-flex p-3"
                        style="justify-content: center; align-items: center; background-color: #1A4D2E;">
                        <span style="flex: 1;">รีเซ็ทรหัสผ่าน</span>
                        <button type="button" id="btn-close-forgetpassword" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close" style="font-size: 14px;"></button>
                    </div>
                    <div class="card-body px-5 py-5">
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <img class="icon-secure" src="./assets/icon/security_icon.png" width="60" />
                        </div>
                        <form id="forgetpassForm">
                            <div class="inputGroup">
                                <input type="text" required autocomplete="off" class="form-control-lg"
                                    id="forgetusername">
                                <label for="forgetusername">Username</label>
                            </div>
                            <div class="inputGroups">
                                <input type="email" required autocomplete="off" class="form-control-lg"
                                    id="forgetemail">
                                <label for="forgetemail">E-mail</label>
                            </div>
                            <div class="inputGroup">
                                <input type="password" required autocomplete="off" class="form-control-lg"
                                    id="forgetpassword">
                                <label for="forgetpassword">New Password</label>
                            </div>
                            <div class="inputGroup mb-3">
                                <input type="password" required autocomplete="off" class="form-control-lg"
                                    id="confirmpassword">
                                <label for="confirmpassword">Confirm Password</label>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Reset</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            $(".togglepass-login").click(function () {
                let inputpass = $("#password");
                let icon = $(this);
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