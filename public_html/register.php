<?php
session_start();

if (isset($_SESSION['user_id'])) {
  if ($_SESSION['user_role'] === 'user') {
    header("Location: /user");
    exit;
  } elseif ($_SESSION['user_role'] === 'business') {
    header("Location: /business-admin");
    exit;
  } elseif ($_SESSION['user_role'] === 'admin') {
    header("Location: /admin");
    exit;
  }
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Puandeks - Kayıt ol</title>

    <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/vendors.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">

   <!-- font awesome -->
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

   <!-- Page Fade -->
    <style>
    body {
      opacity: 1;
      transition: opacity 0.2s ease;
    }
    body.fade {
      opacity: 0;
    }
    </style>
   <!-- Page Fade -->

</head>

<body>

<!-- Kapat butonu -->
<button onclick="
  document.body.classList.add('fade'); 
  setTimeout(() => {
    var from = sessionStorage.getItem('cameFrom');
    if (from) {
      window.location.href = from;
    } else {
      window.location.href = 'https://puandeks.com';
    }
  }, 250); // transition süresiyle uyumlu
" 
style="position:fixed; top:20px; right:20px; background:transparent; border:none; cursor:pointer; z-index:9999;">
  <i class="fa-solid fa-xmark" style="font-size:28px; color:#000;"></i>
</button>
<!-- Kapat butonu -->




  
 <!-- Giriş Alanı -->
    <div class="login-container" style="padding-top:60px !important;">


      
       <!-- Logo box'ın hemen üstünde -->
    <div style="text-align:center; margin-bottom:20px;">
        <img src="img/puandeks-logo_2.svg"
             alt="Puandeks Logo"
             style="width:150px; max-width:200px; height:auto; display:block; margin:0 auto;">
    </div>
      
      
        <div class="login-box">
            <h2>Puandeks'e kayıt olun</h2>

 <form id="registerForm" style="max-width: 400px; margin: 0 auto;">
   
   <!-- İsim -->
      <div class="input-group" style="margin-bottom: 15px;">
        <label for="name">İsim</label>
        <input type="text" id="name" name="name" placeholder="Adınız" required style="width: 100%; padding: 10px;">
      </div>

    <!-- Soyisim -->
      <div class="input-group" style="margin-bottom: 15px;">
        <label for="surname">Soyisim</label>
        <input type="text" id="surname" name="surname" placeholder="Soyadınız" required style="width: 100%; padding: 10px;">
      </div>
   
   
    <!-- Email Input -->
    <div class="input-group" style="margin-bottom: 15px;">
        <label for="email">E-posta adresi</label>
        <input type="email" id="email" name="email" placeholder="Örnek: user@example.com" required style="width: 100%; padding: 10px;">
    </div>

<!-- User Password -->
<div class="input-group" style="margin-bottom: 20px; position: relative;">
  <label for="password" style="display:flex; align-items:center; gap:5px;">
    Şifre
    <i class="fa-solid fa-circle-info"
       tabindex="0"
       data-bs-toggle="popover"
       data-bs-trigger="focus"
       data-bs-placement="top"
       data-bs-content="Şifre en az 8 karakter, bir büyük harf, bir rakam ve bir özel karakter içermelidir."
       style="color:#888; font-size:15px; cursor:pointer;"></i>
  </label>

  <input type="password" id="password" name="password" placeholder="Şifrenizi girin"
         required style="width: 100%; padding: 10px 40px 10px 10px;">
  <i class="fa-solid fa-eye toggle-password"
     data-target="password"
     style="position:absolute; right:10px; top:36px; cursor:pointer; color:#666;"></i>
</div>

<div class="input-group" style="margin-bottom: 20px; position: relative;">
  <label for="password_confirm">Şifre (Tekrar)</label>
  <input type="password" id="password_confirm" name="password_confirm" placeholder="Şifrenizi tekrar girin"
         required style="width: 100%; padding: 10px 40px 10px 10px;">
  <i class="fa-solid fa-eye toggle-password"
     data-target="password_confirm"
     style="position:absolute; right:10px; top:36px; cursor:pointer; color:#666;"></i>
</div>
<!-- User Password -->




 <!-- Contracts -->
<div style="margin-bottom:20px !important; font-size:13px !important; width:100% !important; line-height:1.5; text-align:left !important; color:#555 !important;">
  Puandeks’e üye olarak 
  <a href="kvkk" target="_blank" style="color:#04DA8D !important; font-weight:600; text-decoration:none;">KVKK Politikası</a>, 
  <a href="privacy-policy" target="_blank" style="color:#04DA8D !important; font-weight:600; text-decoration:none;">Gizlilik Politikası</a> 
  ve 
  <a href="terms-conditions" target="_blank" style="color:#04DA8D !important; font-weight:600; text-decoration:none;">Şartlar & Koşullar</a> 
  metnini kabul etmiş olursunuz.
</div>
 <!-- Contracts -->

    
  <!-- Register button --> 
   <button type="submit" style="width: 100%; padding: 12px; background-color: #05462F; color: white; border: none; border-radius: 5px; font-weight: 600; cursor: pointer;">
  Kayıt Ol 
   </button> 
   <!-- Register button -->

   
</form>

<!-- Hata mesajı alan -->
<div id="loginMessage" style="margin-top:15px; color:red;"></div>

 <!-- Login -->
            <p class="signup-text">
              Bir hesabınız var mı? 
              <a href="login" style="color:#04DA8D !important; text-decoration:none !important; font-weight:bold !important;">
               Giriş Yapın
              </a>
            </p>
 <!-- Login -->
          
          
</div>
</div>

   
<!-- Bootstrap Popover Init -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl)
  })
});
</script>
<!-- Bootstrap Popover Init -->


<!-- Page Fade -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  // Page open fade-in
  document.body.classList.add("fade");
  setTimeout(() => {
    document.body.classList.remove("fade");
  }, 50);

  // Sadece login/register linklerine tıklanınca fade-out uygula
  document.addEventListener("click", (e) => {
    const link = e.target.closest("a");
    if (link && (link.href.includes("login") || link.href.includes("register"))) {
      e.preventDefault();
      document.body.classList.add("fade");
      setTimeout(() => {
        window.location.href = link.href;
      }, 400); // transition süresi ile aynı
    }
  });
});
</script>
<!-- Page Fade -->

<!-- User Password  -->
<script>
document.querySelectorAll(".toggle-password").forEach(icon => {
  icon.addEventListener("click", function() {
    const targetInput = document.getElementById(this.dataset.target);
    const type = targetInput.getAttribute("type") === "password" ? "text" : "password";
    targetInput.setAttribute("type", type);
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });
});
</script>
<!-- User Password  -->

<script>
document.querySelectorAll('.password-info').forEach(icon => {
  const tooltip = icon.nextElementSibling;

  // Hover (masaüstü)
  icon.addEventListener('mouseenter', () => {
    tooltip.style.display = 'block';
  });
  icon.addEventListener('mouseleave', () => {
    tooltip.style.display = 'none';
  });

  // Tıklama (mobil)
  icon.addEventListener('click', (e) => {
    e.stopPropagation();
    tooltip.style.display = tooltip.style.display === 'block' ? 'none' : 'block';
  });

  // Başka yere dokununca kapansın (mobil)
  document.addEventListener('click', (e) => {
    if (!icon.contains(e.target)) {
      tooltip.style.display = 'none';
    }
  });
});
</script>

<!-- User Register -->
<script>
document.getElementById("registerForm").addEventListener("submit", async function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  const messageBox = document.getElementById("loginMessage");

  messageBox.style.color = "#333";
  messageBox.innerText = "İşlem yapılıyor...";

  try {
    const response = await fetch("api/user-register.php", {
      method: "POST",
      body: formData
    });

    const result = await response.json();

    if (result.status === "success") {
      // Başarılı kayıt -> verify-pending.php sayfasna ynlendir
      window.location.href = "verify-pending";
    } else {
      messageBox.style.color = "red";
      messageBox.innerText = result.message;
    }
  } catch (error) {
    messageBox.style.color = "red";
    messageBox.innerText = "Sunucuya bağlanılamadı. Lütfen daha sonra tekrar deneyin.";
  }
});
</script>
<!-- /User Register -->


</body>
</html>
