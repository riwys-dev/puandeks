<?php
session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Puandeks – E-posta Doğrulama</title>

  <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      display:flex; align-items:center; justify-content:center;
      height:100vh; background:#f9f9f9;
      font-family:'Roboto',sans-serif;
      margin:0;
      opacity:1;
      transition:opacity 0.2s ease;
    }
    body.fade { opacity:0; }

    .verify-box {
      text-align:center; max-width:400px;
      background:#fff; padding:40px;
      border-radius:10px;
    }

    .timer {
      font-size:38px;
      font-weight:700;
      color:#05462F;
      margin:20px 0 25px;
    }

    .resend-btn {
      display:inline-block;
      background:#ccc;
      color:#fff;
      border:none;
      border-radius:5px;
      padding:12px 28px;
      font-weight:600;
      font-size:15px;
      cursor:not-allowed;
      transition:background 0.3s ease;
    }

    .resend-btn.active {
      background:#05462F;
      cursor:pointer;
    }

    .warning {
      color:#777;
      font-size:14px;
      margin-bottom:10px;
    }
  </style>
</head>

<body>

  <!-- Kapat butonu -->
  <button onclick="
    document.body.classList.add('fade'); 
    setTimeout(() => {
      window.location.href = 'https://puandeks.com';
    }, 250);
  " 
  style="position:fixed; top:20px; right:20px; background:transparent; border:none; cursor:pointer; z-index:9999;">
    <i class="fa-solid fa-xmark" style="font-size:28px; color:#000;"></i>
  </button>
  <!-- Kapat butonu -->

  <div class="verify-box">
    <img src="img/puandeks-logo_2.svg" alt="Puandeks" style="width:140px; margin-bottom:20px;">

    <h2 style="font-weight:600; margin-bottom:10px;">Kayıt Başarılı!</h2>
      <p class="warning" style="color:#C0392B; font-size:13px; margin-bottom:10px;">
        Lütfen kayıt aşamasında kullandığınız E-Posta adresinizi kontrol edin. E-posta gelene kadar bu sayfayı kapatmayın.
      </p>

    <div class="timer" id="timer">03:00</div>
    <button class="resend-btn" id="resendBtn" disabled>E-postayı Yeniden Gönder</button>
  </div>

  <!-- Font Awesome -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>

  <!-- Countown -->
  <script>
  let timeLeft = 180; 
  const timer = document.getElementById("timer");
  const resendBtn = document.getElementById("resendBtn");

  const countdown = setInterval(() => {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timer.textContent = `${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

    if (timeLeft <= 0) {
      clearInterval(countdown);
      resendBtn.disabled = false;
      resendBtn.classList.add("active");
    } else {
      timeLeft--;
    }
  }, 1000);

  resendBtn.addEventListener("click", () => {
    resendBtn.disabled = true;
    resendBtn.classList.remove("active");
    resendBtn.style.background = "#ccc";
    timer.textContent = "03:00";
    timeLeft = 180;

    setTimeout(() => {
      const countdownRestart = setInterval(() => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timer.textContent = `${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        if (timeLeft <= 0) {
          clearInterval(countdownRestart);
          resendBtn.disabled = false;
          resendBtn.classList.add("active");
        } else {
          timeLeft--;
        }
      }, 1000);
    }, 1000);
  });
  </script>

</body>
</html>
