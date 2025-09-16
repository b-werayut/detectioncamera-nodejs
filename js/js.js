// fa-eye toggle password //
$(document).ready(() => {
  $("#toggle-password").click(() => {
    var passwordInput = $("#confirmpassword");
    var passwordInput2 = $("#forgetpassword");
    var icon = $("#toggle-password");

    if (passwordInput.attr("type") == "password") {
      passwordInput.attr("type", "text");
      passwordInput2.attr("type", "text");
      icon.removeClass("fa-eye-slash").addClass("fa-eye");
    } else {
      passwordInput.attr("type", "password");
      passwordInput2.attr("type", "password");
      icon.removeClass("fa-eye").addClass("fa-eye-slash");
    }
  });

  $("#toggle-password2").click(() => {
    var password = $("#passwordregis");
    var icon = $("#toggle-password2");

    if (password.attr("type") == "password") {
      $(password).attr("type", "text");
      icon.removeClass("fa-eye-slash").addClass("fa-eye");
    } else {
      $(password).attr("type", "password");
      icon.removeClass("fa-eye").addClass("fa-eye-slash");
    }
  });

  // fa-eye toggle password //

  //password match//
  $("#confirmpassword").keyup(() => {
    var pass = $("#forgetpassword").val();
    var pass2 = $("#confirmpassword").val();
    if (pass == pass2) {
      $("#match").text("รหัสผ่านทั้งสองช่องตรงกันแล้ว").css("color", "#1cd07c");
    } else {
      $("#match").text("รหัสผ่านทั้งสองช่องไม่ตรงกัน!").css("color", "red");
    }
  });

  $("#forgetpassword").keyup(() => {
    var pass = $("#forgetpassword").val();
    var pass2 = $("#confirmpassword").val();
    if (pass == pass2) {
      $("#match").text("รหัสผ่านทั้งสองช่องตรงกันแล้ว").css("color", "#1cd07c");
    } else {
      $("#match").text("รหัสผ่านทั้งสองช่องไม่ตรงกัน!").css("color", "red");
    }
  });
  // //password match//

  $("#btn-close-register").click(() => {
    $("#usernameregis").val("");
    $("#passwordregis").val("");
    $("#firstname").val("");
    $("#lastname").val("");
    $("#match").text("");
  });

  $("#btn-close-forgetpassword").click(() => {
    $("#forgetusername").val("");
    $("#forgetpassword").val("");
    $("#confirmpassword").val("");
    $("#match").text("");
  });
});
