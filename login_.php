<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./assets/favicon/favicon.ico" />
    <link rel="stylesheet" href="./css/css.css" >
    <link rel="stylesheet" href="./css/bootstrap.min.css"/>
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="./css/sweetalert2.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- <link rel="stylesheet" href="./css/font/kanit.css"/> -->
    <title>Login</title>
</head>
<style>
  .card-body{background-color: #424242;}
</style>
<body>
  
    <!-- <section class="vh-100" style="background-color: #9A616D;"> --> <!-- bg pink -->
  <section class="vh-100" style="background-color: #FAEDCE;">
  <div class="container h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-xl-8">
        <div class="card" style="border-radius: 15px; overflow: hidden; box-shadow: 5px 10px 10px #1e1e1e;">
          <div class="row g-0">
            <div class="col-md-6 col-lg-5 d-none d-md-block index-img">
              <img src="./assets/img/ip-camera.png" />
            </div>
            <div class="col-md-6 col-lg-7 d-flex" style="background-color: #424242;">
              <div class="d-flex flex-column justify-content-between w-100">
              <div class="card-body p-4 p-lg-5 text-black">
                <form>
                  <div class="mb-3 pb-1 text-center">
                  <!-- <img src="./assets/brand/nwl-logo.png" alt=""> -->
                    <h1 class="fw-medium mb-0"
                        style="
                            font-size: clamp(33px, 2.5vw, 36px);
                            letter-spacing: 1px;
                            color: #ffb100;
                        ">
                        Sign in
                    </h1>
                  </div>
                  <hr class="text-white">
                  <div class="inputGroup py-2">
                    <input type="text" required="" autocomplete="off" class="form-control-lg" id="username">
                    <label for="username">Username</label>
                  </div>

                  <div class="inputGroup">
                    <input type="password" required="" autocomplete="off" class="form-control-lg" id="password">
                    <label for="password">Password</label>
                    <i class="togglepass-login toggle-password fa-regular fa-eye-slash" ></i>
                  </div>

                  <div class="pt-1 mb-4">
                    <button style="width: 100%; box-shadow: 5px 4px 6px #1e1e1e; letter-spacing: 1px; font-size: 14px;" data-mdb-button-init data-mdb-ripple-init class="btn btn-dark btn-lg btn-block" type="button" onclick="login()">Login</button>
                  </div>

                      <p><a data-bs-toggle="modal" data-bs-target="#forgetpasswordmodal" href="#!" style="color: #dfdfdf;">ลืมรหัสผ่าน?</a></p>
                      <p style="color: #B0B0B0;">ถ้ายังไม่มีชื่อผู้ใช้งาน <a data-bs-toggle="modal" data-bs-target="#registermodal" href="#" style="color: #00C49A;">คลิกที่นี่เพื่อสมัครชื่อผู้ใช้งาน</a></p>

                </form>
              </div>
              <div>
                <div class="float-end py-3 px-5">
                  <img src="./assets/brand/nwl-logo.png" width=50>
                    <span class="fw-medium mb-0 text-white" style=" font-size: clamp(12px, 2.5vw, 14px);  letter-spacing: 1px; color: #dfdfdf;">NetWorklink.Co.Ltd,</span>
                  </div>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Modal Forgetpass start -->
<div class="modal fade" id="forgetpasswordmodal">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="card text-center" style="width: 100%;">
    <div class="text-uppercase card-header h5 text-white  d-flex p-3" style="justify-content: center; align-items: center; background-color: #dba200;">
    <span style="flex: 1;">รีเซ็ทรหัสผ่าน</span>
    <button type="button" id="btn-close-forgetpassword" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 14px;"></button>
    </div>
    <div class="card-body px-5 py-5" style="padding-top: 1rem!important;">
        <div data-mdb-input-init class="form-outline">
            <form id="forgetpassForm">

                      <div class="inputGroup">
                        <input type="text" required="" autocomplete="off" class="form-control-lg" id="forgetusername">
                        <label for="forgetusername">Username</label>
                      </div>
   
                      <div class="inputGroups">
                      <input type="email" required autocomplete="off" class="form-control-lg" id="forgetemail" placeholder=" " aria-describedby="emailHelp">
                      <label for="forgetemail">E-mail</label>
                    </div>

                      <div class="inputGroup">
                        <input type="password" required="" autocomplete="off" class="form-control-lg" id="forgetpassword">
                        <label for="forgetpassword">Password</label>
                      </div>
         
                      <div class="inputGroup" style="margin-bottom: 0;">
                        <input type="password" required="" autocomplete="off" class="form-control-lg" id="confirmpassword">
                        <label for="confirmpassword">Confirm password</label>
                        <i id="toggle-password" class="toggle-password fa-regular fa-eye-slash"></i>
                      </div>
            </div>
            <div class="match">
            <span id="match"></span>
            </div>
        <button href="#" type="submit" data-mdb-ripple-init class="btn btn-success w-100">Reset</button>
        </form>
    </div>
</div>
    </div>
  </div>
</div>
<!--Modal Forgetpass End-->

<!-- Modal Register start -->
<div class="modal fade" id="registermodal">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="card text-center" style="width: 100%;">
    <div class="card-header h5 text-white d-flex p-3" style="justify-content: center; align-items: center; background-color: #dba200;">
    <span class="" style="flex: 1;">สมัครชื่อผู้ใช้งาน</span>
    <button type="button" id="btn-close-register" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 14px;"></button>
    </div>
    <div class="card-body px-5 py-5" style="padding-top: 1rem!important;">
            
                <form id="registerForm">
              <div class="inputGroup">
                    <input type="text" required="" autocomplete="off" class="form-control-lg" id="usernameregis">
                    <label for="usernameregis">Username</label>
                  </div>
                
            
                <div class="inputGroup">
                    <input type="password" required="" autocomplete="off" class="form-control-lg" id="passwordregis">
                    <label for="passwordregis">Password</label>
                    <i id="toggle-password2" class="toggle-password fa-regular fa-eye-slash"></i>
                  </div>
               

            
                <div class="inputGroups">
                  <input type="email" required autocomplete="off" class="form-control-lg" id="emailregis" placeholder=" " aria-describedby="emailHelp">
                  <label for="emailregis">E-mail</label>
                </div>
                      
                <!-- <div class="inputGroup">
                        <input type="text" required="" autocomplete="off" class="form-control-lg" id="lastname">
                        <label for="lastname">Lastname</label>
                   </div>
            
                   <div class="inputGroup">
                        <input type="text" required="" autocomplete="off" class="form-control-lg" id="telephonenumber">
                        <label for="lastname">Telephonenumber</label>
                   </div> -->

              <button  type="submit" data-mdb-ripple-init class="btn btn-success w-100">Register</button>
          </form>
        <div class="d-flex justify-content-between mt-4 d-none">
            <a style="color: rgb(137, 170, 223);" class="" href="#">Login</a>
            <a style="color: rgb(137, 170, 223);" class="" href="#">Register</a>
        </div>
    </div>
</div>
    </div>
  </div>
</div>
<!--Modal Register End-->

<script src="./js/bootstrap.bundle.min.js"></script>
<script src="./js/ajax-jquery.min.js"></script>
<script src="./js/js.js"></script>
</body>
</html>

<script>

$("#forgetpassForm").on('submit', function(e) {
    e.preventDefault();
    resetPass();
});

const inputs = document.querySelectorAll('input[required]');
inputs.forEach(input => {
  input.addEventListener('input', () => {
    input.setCustomValidity('');
  });

  input.addEventListener('invalid', () => {
    if (input.validity.valueMissing) {
      input.setCustomValidity('กรุณากรอกข้อมูลในช่องนี้');
    } else {
      input.setCustomValidity('');
    }
  });
});


const emailInput = document.getElementById('emailregis');
emailInput.addEventListener('invalid', function (e) {
  if (emailInput.validity.typeMismatch) {
    emailInput.setCustomValidity("กรุณากรอกอีเมลให้ถูกต้อง เช่น example@domain.com");
  } else {
    emailInput.setCustomValidity('');
  }
});

emailInput.addEventListener('input', function () {
  emailInput.setCustomValidity('');
});

const forgetemailinput = document.getElementById('forgetemail');
forgetemailinput.addEventListener('invalid', function (e) {
  if (forgetemailinput.validity.typeMismatch) {
    forgetemailinput.setCustomValidity("กรุณากรอกอีเมลให้ถูกต้อง เช่น example@domain.com");
  } else {
    forgetemailinput.setCustomValidity('');
  }
});

forgetemailinput.addEventListener('input', function () {
  forgetemailinput.setCustomValidity('');
});

  $('#btn-close-register').click(function() {
    debugRegisModal()
  });

  function debugRegisModal(){
    $("#usernameregis").val('');
        $("#passwordregis").val('');
        $("#emailregis").val('');

        $('body').attr('tabindex', -1).focus();

        var myModalEl = document.getElementById('registermodal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        if (!modal) {
          modal = new bootstrap.Modal(myModalEl);
        }
        modal.hide();
  }

  $('#btn-close-forgetpassword').click(function() {
    debugForgetpassModal()
  });

  function debugForgetpassModal(){
        $("#forgetusername").val('');
        $("#forgetemail").val('');
        $("#forgetpassword").val('');
        $("#confirmpassword").val('');

        $('body').attr('tabindex', -1).focus();

        var myModalEl = document.getElementById('forgetpasswordmodal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        if (!modal) {
          modal = new bootstrap.Modal(myModalEl);
        }
        modal.hide();
  }

    $("#usernameregis").keypress((e)=>{

      if(e.key === "Enter"){
        register()
      }
    })

    $("#passwordregis").keypress((e)=>{

      if(e.key === "Enter"){
        register()
      }

    })

    $("#emailregis").keypress((e)=>{

      if(e.key === "Enter"){
        register()
      }

      })

        
    $(".togglepass-login").click(()=>{
      let inputpass = $("#password");
      let icon = $(".togglepass-login");
      if(inputpass.attr("type") == "password"){
          inputpass.attr("type","text");
          icon.removeClass("fa-eye-slash").addClass("fa-eye");
      }else{
          inputpass.attr("type","password");
          icon.removeClass("fa-eye").addClass("fa-eye-slash");
      }
    })

    let inputusername = $("#username");
    let inputpassword = $("#password");

    inputpassword.keypress((enter)=>{
      if(enter.key === "Enter"){
          login()
      }
    })

    inputusername.keypress((enter)=>{
      if(enter.key === "Enter"){
          login()
      }
    })

    function login(){
    const username = $("#username").val().trim();
    const password = $("#password").val().trim();
    const datastr = 'username='+username+'&password='+password;

    if(username == "" || password == ""){
        Swal.fire({
        title: "ไม่มีข้อมูล!",
        text: "กรุณากรอกข้อมูลให้ครบถ้วนก่อนดำเนินการ",
        icon: "warning",
        position: "center",
        background: "#fffbea",
        iconColor: "#f39c12",
        customClass: {
            title: "swal2-title-custom",
            popup: "swal2-popup-custom",
            confirmButton: "swal2-confirm-custom"
        },
        showClass: {
            popup: "animate__animated animate__shakeY"
        },
        confirmButtonText: "ตกลง",
    });
    }else{
    $.ajax({
        url: 'loginfunction.php',
        type: 'POST',
        data: datastr,
        success: (result) => {

        const baseConfig = {
        position: "center",
        background: '#fefefe',
        customClass: {
            title: 'swal2-title-custom',
            popup: 'swal2-popup-custom',
            confirmButton: 'swal2-confirm-custom'
        },
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        },
        showConfirmButton: false,
    };

    if (result == 1) {
        Swal.fire({
        ...baseConfig,
        title: "เข้าสู่ระบบสำเร็จ!",
        text: "กำลังนำคุณเข้าสู่ระบบ...",
        icon: "success",
        timer: 3500
    });

    setTimeout(() => {
        location.href = "/LiveNotifyVideo/index.php";
    }, 2000);

    } else if (result == 0) {
        Swal.fire({
        ...baseConfig,
        title: "เข้าสู่ระบบสำเร็จ!",
        text: "กำลังนำคุณเข้าสู่ระบบ...",
        icon: "success",
        timer: 3500
    });

    setTimeout(() => {
        location.href = "/LiveNotifyVideo/index.php";
    }, 2000);

    } else if (result == 2) {
        Swal.fire({
            icon: "error",
            title: "ชื่อผู้ใช้งานไม่ถูกต้อง",
            position: "center",
            background: "#fffbea",
            iconColor: "#f39c12",
            customClass: {
                title: "swal2-title-custom",
                popup: "swal2-popup-custom",
                confirmButton: "swal2-confirm-custom"
            },
            showClass: {
                popup: "animate__animated animate__shakeX"
            },
            confirmButtonText: "ตกลง",
        });
    } else if (result == 3) {
        Swal.fire({
            icon: "error",
            title: "รหัสผ่านไม่ถูกต้อง",
            position: "center",
            background: "#fffbea",
            iconColor: "#f39c12",
            customClass: {
                title: "swal2-title-custom",
                popup: "swal2-popup-custom",
                confirmButton: "swal2-confirm-custom"
            },
            showClass: {
                popup: "animate__animated animate__shakeX"
            },
            confirmButtonText: "ตกลง",
        });
    }
}
  })
  
}
}

$("#registerForm").on('submit', function(e) {
    e.preventDefault();
    register();
});

function register(){
  const username = $("#usernameregis").val().trim();
  const password = $("#passwordregis").val().trim();
  const email = $("#emailregis").val().trim();
  const datastr = new URLSearchParams({
  username,
  password,
  email,
}).toString();

  if(username == '' || password == '' || email == ''){
    Swal.fire({
        title: "ไม่มีข้อมูล!",
        text: "กรุณากรอกข้อมูลให้ครบถ้วนก่อนดำเนินการ",
        icon: "warning",
        position: "center",
        background: "#fffbea",
        iconColor: "#f39c12",
        customClass: {
            title: "swal2-title-custom",
            popup: "swal2-popup-custom",
            confirmButton: "swal2-confirm-custom"
        },
        showClass: {
            popup: "animate__animated animate__shakeY"
        },
        confirmButtonText: "ตกลง",
    });
  }else{
  $.ajax({
    url: "registerfunction.php",
    type: "POST",
    data: datastr,
    success: (result) => {
    const baseConfig = {
        background: '#fefefe',
        customClass: {
            title: 'swal2-title-custom',
            popup: 'swal2-popup-custom',
            confirmButton: 'swal2-confirm-custom'
        },
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    };

    if(result == 1 || result == 0){
        Swal.fire({
            icon: "error",
            title: "ชื่อผู้ใช้งานถูกใช้แล้ว",
            text: "กรุณาใช้ชื่อผู้ใช้งานอื่น",
            icon: "warning",
            position: "center",
            background: "#fffbea",
            iconColor: "#f39c12",
            customClass: {
                title: "swal2-title-custom",
                popup: "swal2-popup-custom",
                confirmButton: "swal2-confirm-custom"
            },
            showClass: {
                popup: "animate__animated animate__shakeX"
            },
            confirmButtonText: "ตกลง",
        });
    } else if(result == 2){
        Swal.fire({
          ...baseConfig,
      title: "สมัครสมาชิกสำเร็จ!",
      text: "ระบบจะปิดหน้าต่างนี้โดยอัตโนมัติ",
      icon: "success",
      showConfirmButton: false,
      timer: 1800,
      timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    $('#registermodal').modal('hide')
    $("#usernameregis").val('');
    $("#passwordregis").val('');
    $("#emailregis").val('');
    } else if(result == 3){
        Swal.fire({
            icon: "error",
            title: "เกิดข้อผิดพลาด",
            text: "ไม่สามารถสมัครสมาชิกได้",
            footer: '<a href="#">ติดต่อผู้ดูแลระบบ</a>',
            position: "center",
            background: "#fffbea",
            iconColor: "#f39c12",
            customClass: {
                title: "swal2-title-custom",
                popup: "swal2-popup-custom",
                confirmButton: "swal2-confirm-custom"
            },
            showClass: {
                popup: "animate__animated animate__shakeX"
            },
            confirmButtonText: "ตกลง",
        });
    } else if(result == 4){
        Swal.fire({
            icon: "error",
            title: "อีเมลนี้ถูกใช้งานแล้ว",
            text: "กรุณาใช้อีเมลอื่น",
                position: "center",
            background: "#fffbea",
            iconColor: "#f39c12",
            customClass: {
                title: "swal2-title-custom",
                popup: "swal2-popup-custom",
                confirmButton: "swal2-confirm-custom"
            },
            showClass: {
                popup: "animate__animated animate__shakeX"
            },
            confirmButtonText: "ตกลง",
        });
    }
}
  })
}
}

function resetPass(){
  const username = $("#forgetusername").val().trim();
  const email = $('#forgetemail').val().trim();
  const password = $("#forgetpassword").val().trim();
  const passwordcf = $("#confirmpassword").val().trim();
  const datastr = new URLSearchParams({
  username,
  email,
  password,
  passwordcf
}).toString();

  if(username == "" && password == "" && passwordcf == ""){
        $("#forgetpasswordmodal").modal("hide");
  }else{
  if(username == "" || password == "" || passwordcf == ""){
    Swal.fire({
      title: "Please insert all information!",
      text: "",
      icon: "warning"
    });
  }else if(password != passwordcf){
    Swal.fire({
      title: "รหัสผ่านทั้งสองช่องไม่ตรงกัน กรุณาตรวจสอบอีกครั้ง!",
      icon: "warning",
      position: "center",
            customClass: {
                title: "swal2-title-custom",
                popup: "swal2-popup-custom",
                confirmButton: "swal2-confirm-custom"
            },
            showClass: {
                popup: "animate__animated animate__shakeX"
            },
            confirmButtonText: "ตกลง",
    });
  }else{
    $.ajax({
      url: "forgotpasswordfunction.php",
      type: "POST",
      data: datastr,
      success: (result) => {
  if (result == 1) {
    Swal.fire({
      title: "ชื่อผู้ใช้งานไม่ถูกต้อง",
      text: "กรุณาตรวจสอบชื่อผู้ใช้งานของคุณอีกครั้ง",
      icon: "warning",
      confirmButtonText: "ตกลง",
      confirmButtonColor: "#f6c23e"
    });
  } else if (result == 2) {
  Swal.fire({
      title: "อีเมลไม่ถูกต้อง",
      text: "กรุณาตรวจสอบอีเมลของคุณอีกครั้ง",
      icon: "warning",
      confirmButtonText: "ตกลง",
      confirmButtonColor: "#f6c23e"
    });
  }else if (result == 3) {
    debugForgetpassModal()
    Swal.fire({
      customClass: {
            title: 'swal2-title-custom',
            popup: 'swal2-popup-custom',
            confirmButton: 'swal2-confirm-custom'
        },
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        },
      title: "รีเซ็ตรหัสผ่านสำเร็จ!",
      text: "ระบบจะปิดหน้าต่างนี้โดยอัตโนมัติ",
      icon: "success",
      showConfirmButton: false,
      timer: 1800,
      timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading();
      }
    });
  }
}
    })
  }
}
}
</script>

