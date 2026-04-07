<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: /admin/");
    exit;
}
unset($_SESSION['admin_menus'], $_SESSION['admin_role']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Puandeks - Admin</title>

      <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
  
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  
</head>
  
  
<body class="bg-light d-flex align-items-center" style="min-height:100vh; font-family:sans-serif;">

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div style="background:white; border-radius:12px; padding:40px 30px; border:1px solid #e0e0e0;">
        
        <!-- Logo/Başlık -->
        <div style="text-align:center; margin-bottom:30px;">
          <img style="margin-bottom:18px" src="https://puandeks.com/img/puandeks-logo_2.svg">
          <span style="text-align:center; color:#333; font-size:18px; font-weight:600; margin-left:6px;">Admin</span>
        </div>
        
        

        <form id="loginForm">
          <div style="margin-bottom:20px;">
              <label for="email" style="display:block; margin-bottom:8px; color:#555; font-weight:500;">
                  Yönetici E-posta adresi
              </label>

              <input type="email"
               id="email"
               name="email"
               placeholder="örnek: admin@puandeks.com"
               required
               pattern="^[a-zA-Z0-9._%+-]+@puandeks\.com$"
               title="Sadece @puandeks.com e-posta adresi kabul edilir."
               style="width:100%; padding:12px 15px; border:1px solid #ddd; border-radius:6px; font-size:14px; background:#fafafa;">

          </div>


          <div style="margin-bottom:25px; position:relative;">
              <label for="password" style="display:block; margin-bottom:8px; color:#555; font-weight:500;">
                  Şifre
              </label>

              <input type="password"
                     id="password"
                     name="password"
                     placeholder="Şifrenizi girin"
                     required
                     style="width:100%; padding:12px 45px 12px 15px; border:1px solid #ddd; border-radius:6px; font-size:14px; background:#fafafa;">

              <!-- Font Awesome Eye Icon -->
              <i id="togglePassword"
                 class="fa-solid fa-eye"
                 style="position:absolute; right:12px; top:42px; cursor:pointer; font-size:18px; color:#666;">
              </i>
          </div>

          <button id="loginBtn" disabled style="width:100%; height:48px; background:#0b6b2a; border:none; color:#fff; 
                      font-weight:600; border-radius:8px; cursor:not-allowed; opacity:0.6;">
              Giriş yap
          </button>

        </form>

        <div id="loginMessage" style="color:#d32f2f; text-align:center; margin-top:15px;"></div>
      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ------------------------------------------------------
       ELEMENTS
    ------------------------------------------------------ */
    const loginForm    = document.getElementById("loginForm");
    const loginMessage = document.getElementById("loginMessage");
    const emailInput   = document.getElementById("email");
    const loginBtn     = document.getElementById("loginBtn");
    const togglePass   = document.getElementById("togglePassword");
    const passwordInp  = document.getElementById("password");


    /* ------------------------------------------------------
       1) PASSWORD SHOW / HIDE TOGGLE
    ------------------------------------------------------ */
    if (togglePass) {
        togglePass.onclick = function () {
            const type = passwordInp.type === "password" ? "text" : "password";
            passwordInp.type = type;

            // Switch icon
            if (type === "password") {
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            } else {
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            }
        };
    }


    /* ------------------------------------------------------
       2) EMAIL DOMAIN VALIDATION + DISABLE/ENABLE BUTTON
    ------------------------------------------------------ */
    const emailPattern = /^[a-zA-Z0-9._%+-]+@puandeks\.com$/;

    emailInput.addEventListener("input", function () {

        // Valid puandeks.com email → enable button
        if (emailPattern.test(emailInput.value.trim())) {
            loginBtn.disabled = false;
            loginBtn.style.opacity = "1";
            loginBtn.style.cursor = "pointer";

        } else {
            // Invalid email → disable button
            loginBtn.disabled = true;
            loginBtn.style.opacity = "0.6";
            loginBtn.style.cursor = "not-allowed";
        }
    });


    /* ------------------------------------------------------
       3) AJAX LOGIN REQUEST
    ------------------------------------------------------ */
    loginForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        // Double-check email domain before sending
        if (!emailPattern.test(emailInput.value.trim())) {
            loginMessage.innerText = "Only @puandeks.com email addresses are allowed.";
            return;
        }

        const formData = new FormData(loginForm);

        try {
            const response = await fetch("/api/admin-login.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();

            if (result.status === "success") {
                window.location.href = "/admin/";
            } else {
                loginMessage.innerText = result.message || "Login failed.";
            }
        } catch (error) {
            loginMessage.innerText = "Connection error.";
        }
    });

});
</script>


</body>
</html>
