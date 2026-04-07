<?php
session_start();

require_once('/home/puandeks.com/backend/config.php');

// Eğer işletme zaten giriş yaptıysa → direkt /home
if (isset($_SESSION['company_id'])) {
    header("Location: /home");
    exit;
}

// Claim verify sonrası banner göstermek için işaret
$claimBanner = false;
if (isset($_SESSION['claim_verified']) && $_SESSION['claim_verified'] === true) {
    $claimBanner = true;
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Puandeks İşletme</title>

    <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="https://puandeks.com/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://puandeks.com/css/style.css" rel="stylesheet">
    <link href="https://puandeks.com/css/vendors.css" rel="stylesheet">
    <link href="https://puandeks.com/css/business-login.css" rel="stylesheet">

    <script>
    window.PHONE_VERIFIED = <?php echo $phone_verified; ?>;
    </script>
</head>

<body>

<!-- Close Page -->  
<script>
document.addEventListener("DOMContentLoaded", () => {
    const ref = document.referrer;

    if (ref && !ref.includes('business.puandeks.com/register')) {
        sessionStorage.setItem("cameFrom", ref);
    }
});
</script>

<button onclick="
  document.body.classList.add('fade'); 
  setTimeout(() => {
    var from = sessionStorage.getItem('cameFrom');
    if (from) {
      window.location.href = from;
    } else {
      window.location.href = 'https://business.puandeks.com/';
    }
  }, 250);
" 
style="position:fixed; top:20px; right:20px; background:transparent; border:none; cursor:pointer; z-index:9999;">
  <i class="fa-solid fa-xmark" style="font-size:28px; color:#000;"></i>
</button>
<!-- Close Page -->

    <!-- Isletme Giris Alanı -->
    <div class="business-login-container">

    <div style="text-align:center; margin-bottom:20px;">
        <img src="https://puandeks.com/img/puandeks-logo_2.svg"
             alt="Puandeks Logo"
             style="width:150px; max-width:200px; height:auto; display:block; margin:0 auto;">
    </div>
      
      
        <div class="login-box">
            <h2>Puandeks İşletme'ye giriş yapın</h2>

 <form id="loginForm" style="max-width: 400px; margin: 0 auto;">
    <!-- Email Input -->
    <div class="input-group" style="margin-bottom: 15px;">
        <label for="business-email">İşletme E-posta adresi</label>
        <input type="email" id="business-email" name="email" placeholder="Örnek: info@example.com" required style="width: 100%; padding: 10px;">
    </div>
    
    <!-- ifre Input -->
    <div class="input-group" style="margin-bottom: 20px;">
        <label for="business-password">Şifre</label>
        <input type="password" id="business-password" name="password" placeholder="Şifrenizi girin" required style="width: 100%; padding: 10px;">
    </div>

    <p style="margin-top:15px; text-align:left;">
        <a href="#" id="forgotPasswordLink" style="color:#05462F; font-weight:500;">
          Şifrenizi mi unuttunuz?
        </a>
      </p>

    
    <!-- Login Buto -->

    <button id="loginBtn" type="submit"
        style="width: 100%; padding: 12px; background-color: #05462F; color: white; border: none; border-radius: 5px; font-weight: 600; cursor: pointer;">
        Giriş yap
    </button>
    

</form>

<!-- Hata mesajı alan -->
<div id="loginMessage" style="margin-top:15px; color:red;"></div>




  
  

            <!-- Kayt Ol Linki -->
            <p class="signup-text">İşletme hesabınız yok mu? <a href="https://business.puandeks.com/register">Hemen ücretsiz
                    kaydolun.</a>
              </p>
          
          
              </div>
              
              </div>


<!-- Forgot pass popup --> 
<div id="forgotModal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); 
            z-index:99999; justify-content:center; align-items:center;">

    <div style="
        background:#fff;
        width:90%; max-width:420px;
        padding:32px 28px;
        border-radius:12px;
        box-shadow:0 4px 20px rgba(0,0,0,0.15);
        position:relative;
        font-family: 'Roboto', sans-serif;
    ">

        <!-- X (kapat) -->
        <span id="closeForgot"
              style="position:absolute; right:20px; top:18px; font-size:22px; cursor:pointer; color:#444;">×</span>

        <h2 style="font-size:26px; font-weight:600; color:#063;">
            Şifrenizi mi unuttunuz?
        </h2>

        <p style="font-size:14px; color:#666; margin-top:6px; margin-bottom:18px;">
            E-posta adresinizi girin, size geçici bir şifre gönderelim.
        </p>

        <input type="email" id="resetEmail"
               placeholder="E-posta adresiniz"
               style="width:100%; padding:12px; border:1px solid #ccc; border-radius:8px; font-size:15px;">

        <button id="sendReset"
                style="margin-top:18px; width:100%; padding:12px; 
                       background:#05462F; color:white; border:none;
                       font-size:16px; font-weight:600; border-radius:8px; cursor:pointer;">
            Gnder
        </button>

        <p id="resetMessage" style="margin-top:12px; color:red; font-size:14px;"></p>

    </div>
</div>
<!-- Forgot pass popup -->   

<!-- OTP popup -->  
<div id="businessOtpModal" style="
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.5);
  align-items:center;
  justify-content:center;
  z-index:9999;
">
  <div style="
    background:#fff;
    width:100%;
    max-width:400px;
    padding:24px;
    border-radius:12px;
    text-align:center;
  ">
    <h4 style="margin-bottom:10px;">Giriş Doğrulama</h4>
    <p style="font-size:14px;color:#555;margin-bottom:15px;">
      Telefonunuza gönderilen 6 haneli kodu giriniz.
    </p>

    <input type="text"
           id="businessOtpInput"
           maxlength="6"
           placeholder="******"
           style="width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;margin-bottom:8px;text-align:center;font-size:18px;">

    <!-- Hata mesajı -->
    <div id="otpError" style="color:red;font-size:13px;display:none;margin-bottom:8px;">
      Kod hatalı, tekrar giriniz.
    </div>

    <!-- KVKK -->
    <div style="margin-top:10px; text-align:left;">
      <label style="font-size:14px;color:#555;">
        <input type="checkbox" id="kvkkCheck">
        <a href="https://puandeks.com/kvkk" target="_blank">
          KVKK metnini
        </a> okudum, kabul ediyorum
      </label>
    </div>

    <!-- BUTON -->
    <button id="verifyOtpBtn"
      disabled
      style="margin-top:12px;width:100%;padding:12px;background:#ccc;color:#fff;border:none;border-radius:6px;cursor:not-allowed;">
      Doğrula ve Giriş Yap
    </button>

    <div id="businessCountdownText" style="font-size:14px;color:#999;margin-top:10px;">
      60 saniye kaldı
    </div>
  </div>
</div>
<!-- OTP popup -->  



<!-- SCRIPTS START -->  
<script>
// =======================
// ELEMENTS
// =======================
const loginForm = document.getElementById("loginForm");
const loginMessage = document.getElementById("loginMessage");

const loginBtn = document.getElementById("loginBtn");
const emailInput = document.getElementById("business-email");
const passwordInput = document.getElementById("business-password");

loginBtn.disabled = true;
loginBtn.style.background = "#ccc";
loginBtn.style.cursor = "not-allowed";

function checkInputs() {
    if (emailInput.value.trim() !== "" && passwordInput.value.trim() !== "") {
        loginBtn.disabled = false;
        loginBtn.style.background = "#05462F";
        loginBtn.style.cursor = "pointer";
    } else {
        loginBtn.disabled = true;
        loginBtn.style.background = "#ccc";
        loginBtn.style.cursor = "not-allowed";
    }
}

emailInput.addEventListener("input", checkInputs);
passwordInput.addEventListener("input", checkInputs);

const forgotModal = document.getElementById("forgotModal");
const otpModal = document.getElementById("businessOtpModal");

const countdownText = document.getElementById("businessCountdownText");


// =======================
// LOGIN + OTP
// =======================
loginForm.addEventListener("submit", async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
        const response = await fetch("api/business-login.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            window.location.href = '/home';
        }

        else if (result.status === 'otp_required') {

            // OTP modal aç
            document.getElementById('businessOtpModal').style.display = 'flex';

            // inputları resetle
            document.getElementById('businessOtpInput').value = '';
            document.getElementById('kvkkCheck').checked = false;

            // hata mesajını gizle
            document.getElementById('otpError').style.display = 'none';

            // butonu pasif yap
            const btn = document.getElementById('verifyOtpBtn');
            btn.disabled = true;
            btn.style.background = '#ccc';
            btn.style.cursor = 'not-allowed';

            // countdown başlat
            let timeLeft = 60;
            countdownText.textContent = timeLeft + " saniye kaldı";

            const timer = setInterval(() => {
                timeLeft--;
                countdownText.textContent = timeLeft + " saniye kaldı";

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    countdownText.textContent = "Kod süresi doldu";
                }
            }, 1000);

        } 

        else {
            loginMessage.innerText = result.message;
        }

    } catch (error) {
        loginMessage.innerText = "Sunucuya bağlanamadı.";
    }
});


// =======================
// OTP INPUT CONTROL
// =======================
const otpInput = document.getElementById('businessOtpInput');
const kvkkCheck = document.getElementById('kvkkCheck');
const verifyBtn = document.getElementById('verifyOtpBtn');

function toggleOtpButton() {
    if (otpInput.value.length === 6 && kvkkCheck.checked) {
        verifyBtn.disabled = false;
        verifyBtn.style.background = '#05462F';
        verifyBtn.style.cursor = 'pointer';
    } else {
        verifyBtn.disabled = true;
        verifyBtn.style.background = '#ccc';
        verifyBtn.style.cursor = 'not-allowed';
    }
}

otpInput.addEventListener('input', toggleOtpButton);
kvkkCheck.addEventListener('change', toggleOtpButton);


// =======================
// OTP VERIFY BUTTON
// =======================
verifyBtn.addEventListener('click', function() {

    const otp = otpInput.value.trim();

    fetch('api/business-verify-otp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'otp=' + encodeURIComponent(otp)
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === 'success') {
            window.location.href = '/home';
        } else {
            document.getElementById('otpError').style.display = 'block';
        }

    })
    .catch(() => {
        alert('Sunucu hatası');
    });

});

// =======================
// OTP MODAL CLOSE
// =======================
otpModal.addEventListener("click", function(e) {
  if (e.target === otpModal) {
    otpModal.style.display = "none";
  }
});

document.addEventListener("keydown", function(e) {
  if (e.key === "Escape") {
    otpModal.style.display = "none";
    forgotModal.style.display = "none";
  }
});


// =======================
// FORGOT PASSWORD MODAL
// =======================
document.getElementById("forgotPasswordLink").onclick = function(e) {
    e.preventDefault();
    forgotModal.style.display = "flex";
};

document.getElementById("closeForgot").onclick = function() {
    forgotModal.style.display = "none";
};


// =======================
// PASSWORD RESET
// =======================
document.getElementById("sendReset").onclick = async function() {

    const email = document.getElementById("resetEmail").value.trim();
    const messageBox = document.getElementById("resetMessage");

    if (email === '') {
        messageBox.style.color = "red";
        messageBox.innerText = "Lütfen e-posta adresinizi girin.";
        return;
    }

    const formData = new FormData();
    formData.append('email', email);

    try {
        const response = await fetch("api/business-forgot-password.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.status === "success") {
            messageBox.style.color = "green";
            messageBox.innerText = "Geçici şifre e-posta adresinize gönderildi.";
        } else {
            messageBox.style.color = "red";
            messageBox.innerText = result.message;
        }

    } catch (err) {
        messageBox.style.color = "red";
        messageBox.innerText = "Sunucuya bağlanılamadı.";
    }
};
</script>


</body>

</html>