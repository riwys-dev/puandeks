<?php
session_start();

// If user logged in
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'user') {
    header('Location: /');
    exit;
}

// Google client 
require_once __DIR__ . '/google-client.php';
$google_login_url = $client->createAuthUrl();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Puandeks - Kullanıcı Girişi</title>

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
    window.history.back();
  }, 400); // 
" 
style="position:fixed; top:20px; right:20px; background:transparent; border:none; cursor:pointer; z-index:9999;">
  <i class="fa-solid fa-xmark" style="font-size:28px; color:#000;"></i>
</button>
<!-- Kapat butonu -->

  
 <!-- Giriş Alanı -->
    <div class="login-container">
      
       
    <div style="text-align:center; margin-bottom:20px;">
        <img src="img/puandeks-logo_2.svg"
             alt="Puandeks Logo"
             style="width:150px; max-width:200px; height:auto; display:block; margin:0 auto;">
    </div>
      
      
        <div class="login-box">
            <h2>Puandeks'e giriş yapın</h2>

          <!-- Social buttons --> 
            <div style="display:flex !important; flex-direction:column !important; gap:10px !important; margin-bottom:20px !important; width:100% !important;">
              <button 
              id="googleLoginBtn"
              style="background:#db4437 !important; color:#fff !important; border:none !important; padding:10px !important; border-radius:6px !important; cursor:pointer !important; display:block !important; width:100% !important;">
              <i class="fa-brands fa-google" style="margin-right:6px !important;"></i> Google ile giriş
            </button>

              <button 
              id="appleLoginBtn"
              style="background:#000 !important; color:#fff !important; border:none !important; padding:12px !important; border-radius:8px !important; cursor:pointer !important; display:flex !important; align-items:center !important; justify-content:center !important; width:100% !important; font-weight:600;">
              <i class="fa-brands fa-apple" style="margin-right:8px !important;"></i> Apple ile giriş
            </button>
              
              <button 
              id="facebookLoginBtn"
              style="background:#3b5998 !important; color:#fff !important; border:none !important; padding:10px !important; border-radius:6px !important; cursor:pointer !important; display:block !important; width:100% !important;">
              <i class="fa-brands fa-facebook-f" style="margin-right:6px !important;"></i> Facebook ile giriş
          </button>
            </div> 
          <!-- Sosyal giriş butonlar -->

        <div style="text-align:center !important; margin:15px 0 !important; font-size:14px !important; color:#888 !important;">
          <span></span> <em>veya</em> <span></span>
        </div>

 <form id="loginForm" style="max-width: 400px; margin: 0 auto;">
    <!-- Email Input -->
    <div class="input-group" style="margin-bottom: 15px;">
        <label for="email">E-posta adresi</label>
        <input type="email" id="email" name="email" placeholder="Örnek: user@example.com" required style="width: 100%; padding: 10px;">
    </div>
    
    <!-- Şifre Input -->
    <div class="input-group" style="margin-bottom: 20px;">
        <label for="password">Şifre</label>
        <input type="password" id="password" name="password" placeholder="Şifrenizi girin" required style="width: 100%; padding: 10px;">
    </div>

    <!-- Forgot Pass -->
   <div style="display:flex !important; justify-content:space-between !important; align-items:center !important; margin-bottom:20px !important; font-size:13px !important; width:100% !important;">
           <a href="javascript:void(0);" id="forgotLink" style="color:#04DA8D !important; text-decoration:none !important; font-weight:600;">
            Şifrenizi mi unuttunuz?
          </a>
          </div>
    <!-- Forgot Pass -->
   
    <!-- Login Button -->
    <button type="submit"
        style="width: 100%; padding: 12px; background-color: #05462F; color: white; border: none; border-radius: 5px; font-weight: 600; cursor: pointer;">
        Giriş yap
    </button>
   <!-- Login Button -->
</form>

<!-- E-Posta mesajı -->
          <?php if (isset($_GET['verified']) && $_GET['verified'] == 1): ?>
            <div style="background:#e8f8f2; color:#05462F; padding:10px 15px; border-radius:6px; margin-bottom:15px; text-align:center;">
               E-posta adresiniz başarıyla doğrulandı. Giriş yapabilirsiniz.
            </div>
          <?php endif; ?>
<!-- E-Posta mesajı -->

<!-- Hata mesajı -->
<div id="loginMessage" style="margin-top:15px; color:red;"></div>
<!-- Hata mesajı -->



<!-- Kayıt Ol Linki -->
            <p class="signup-text">
              Hesabınız yok mu? 
              <a href="register" style="color:#04DA8D !important; text-decoration:none !important; font-weight:bold !important;">
                Hemen ücretsiz kaydolun.
              </a>
            </p>

          
          
</div>



<!-- Şifre Sıfırlama Popup -->
<div id="forgotPopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
  <div style="background:#fff; padding:30px; border-radius:10px; width:90%; max-width:400px; box-shadow:0 5px 20px rgba(0,0,0,0.2); text-align:center; position:relative;">
    <h3 style="margin-bottom:15px; color:#05462F;">Şifrenizi mi unuttunuz?</h3>
    <p style="font-size:14px; color:#666; margin-bottom:20px;">E-posta adresinizi girin, size geçici bir şifre gönderelim.</p>
    
    <form id="forgotForm">
      <input type="email" id="forgotEmail" name="email" placeholder="E-posta adresiniz" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">
      <button type="submit" style="width:100%; padding:10px; background-color:#05462F; color:#fff; border:none; border-radius:6px; font-weight:600; cursor:pointer;">Gönder</button>
    </form>

    <div id="forgotMessage" style="margin-top:10px; font-size:13px; color:#05462F;"></div>

    <button id="closeForgot" style="position:absolute; top:10px; right:10px; background:none; border:none; font-size:18px; cursor:pointer; color:#666;">✕</button>
  </div>
</div>

<script>
document.getElementById('forgotLink').addEventListener('click', function() {
  document.getElementById('forgotPopup').style.display = 'flex';
});

document.getElementById('closeForgot').addEventListener('click', function() {
  document.getElementById('forgotPopup').style.display = 'none';
});

document.getElementById('forgotForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const email = document.getElementById('forgotEmail').value.trim();
  if (!email) return;

  const formData = new FormData();
  formData.append('email', email);

  document.getElementById('forgotMessage').innerText = "İleniyor...";

  try {
    const response = await fetch('api/forgot-password-handler.php', {
      method: 'POST',
      body: formData
    });
    const result = await response.json();

    if (result.status === 'success') {
      document.getElementById('forgotMessage').style.color = '#05462F';
    } else {
      document.getElementById('forgotMessage').style.color = 'red';
    }

    document.getElementById('forgotMessage').innerText = result.message;

  } catch (err) {
    document.getElementById('forgotMessage').style.color = 'red';
    document.getElementById('forgotMessage').innerText = 'Sunucuya bağlanılamadı.';
  }
});
</script>
<!-- /ifre Sıfırlama Popup -->


   
<!-- SCRIPTS START -->

<!-- google -->
<script>
document.getElementById("googleLoginBtn").addEventListener("click", function() {
    const url = <?php echo json_encode($google_login_url); ?>;
    window.location.href = url;
});
</script>
<!-- google -->

<!-- apple -->
<script>
document.getElementById("appleLoginBtn").addEventListener("click", function() {
    window.location.href = "/apple/apple-login.php";
});
</script>
<!-- apple -->

<!-- facebook -->
<script>
document.getElementById("facebookLoginBtn").addEventListener("click", function() {
    window.location.href = "/facebook-client.php";
});
</script>
<!-- facebook -->

<script>
document.getElementById("loginForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
        const response = await fetch("api/login-handler.php", {
            method: "POST",
            body: formData,
            credentials: "include"
        });

        const result = await response.json();

        if (result.status === "success") {
            document.getElementById("loginMessage").innerText = result.message;
            // Fade animasyonu için body class ekle
            document.body.classList.add("fade");
            setTimeout(() => {
                window.location.href = result.redirect || "https://puandeks.com";
            }, 150);
        } else {
            document.getElementById("loginMessage").innerText = result.message;
        }

    } catch (error) {
        document.getElementById("loginMessage").innerText = "Sunucuya bağlanılamad.";
    }
});
</script>

  
  


<!-- Page Fade -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  // Sayfa ilk yüklendiğinde fade-in
  document.body.classList.add("fade");
  setTimeout(() => {
    document.body.classList.remove("fade");
  }, 50);

  // Fade-out sadece login/register geçişlerinde çalışsın
  document.addEventListener("click", (e) => {
    const link = e.target.closest("a");

    // Link varsa ve login/register sayfalarına gidiyorsa
    if (link && (link.href.includes("login") || link.href.includes("register"))) {
      e.preventDefault();
      document.body.classList.add("fade");
      setTimeout(() => {
        window.location.href = link.href;
      }, 250);
    }
  });

  // Kapat butonu (üstteki X) için fade-out davranış
  const closeBtn = document.querySelector("button[onclick]");
  if (closeBtn) {
    closeBtn.addEventListener("click", (e) => {
      e.preventDefault();
      document.body.classList.add("fade");
      setTimeout(() => {
        var from = sessionStorage.getItem('cameFrom');
        if (from) {
          window.location.href = from;
        } else {
          window.location.href = 'https://puandeks.com';
        }
      }, 250);
    });
  }
});
</script>
<!-- /Page Fade -->


</body>
</html>