<?php
session_start();

$mode  = $_SESSION['register_mode'] ?? 'normal';
$email = isset($_SESSION['pending_business_email'])
    ? htmlspecialchars($_SESSION['pending_business_email'])
    : null;
?>


<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Puandeks – E-posta Doğrulama</title>

  <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">

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
      text-align:center;
      max-width:400px;
      background:#fff;
      padding:40px;
      border-radius:10px;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);
    }

    .timer { 
      font-size:38px; 
      font-weight:700; 
      color:#05462F; 
      margin:20px 0 25px;
    }

    .resend-btn {
      background:#ccc;
      color:#fff;
      border:none;
      border-radius:5px;
      padding:12px 28px;
      font-weight:600;
      font-size:15px;
      cursor:not-allowed;
      transition:0.2s;
    }

    .resend-btn.active {
      background:#05462F;
      cursor:pointer;
    }

    .warning {
      color:#C0392B;
      font-size:14px;
      margin-bottom:10px;
    }
  </style>
</head>

<body>


<button onclick="
  document.body.classList.add('fade');
  setTimeout(() => {
    <?php if ($mode === 'claim'): ?>
      window.location.href = 'https://business.puandeks.com/login';
    <?php else: ?>
      window.location.href = 'https://business.puandeks.com/login';
    <?php endif; ?>
  }, 250);
"
style="position:fixed; top:20px; right:20px; background:transparent; border:none; cursor:pointer; z-index:99;">
  <i style='font-size:28px; color:#000;'>&times;</i>
</button>


<div class="verify-box">
  <img src="https://puandeks.com/img/puandeks-logo_2.svg" style="width:140px; margin-bottom:20px;">

  <h2 style="font-weight:600; margin-bottom:10px;">Kayıt Başarılı!</h2>

  <p class="warning">
    Lütfen kayıt srasında kullandığınız e-posta adresinizi kontrol edin.<br>
    E-posta gelene kadar bu sayfayı kapatmayın.
  </p>

  <?php if($email): ?>
    <p style="margin-top:-5px; font-size:13px; color:#333;">
      Gönderilen adres:<br>
      <b style="color:#05462F;"><?= $email ?></b>
    </p>

   <input type="hidden" id="pendingEmail" value="<?= $email ?>">
  <?php endif; ?>

  <div class="timer" id="timer">03:00</div>

  <button class="resend-btn" id="resendBtn" disabled>E-postayı tekrar gönder</button>
</div>

<script>
// Countdown
let timeLeft = 180;
const timerEl = document.getElementById("timer");
const resendBtn = document.getElementById("resendBtn");

const countdown = setInterval(() => {
  const m = String(Math.floor(timeLeft / 60)).padStart(2,"0");
  const s = String(timeLeft % 60).padStart(2,"0");
  timerEl.textContent = `${m}:${s}`;

  if (timeLeft <= 0) {
    clearInterval(countdown);
    resendBtn.disabled = false;
    resendBtn.classList.add("active");
  } else timeLeft--;

}, 1000);

// === RESEND MAIL ===
const mode = "<?= $mode ?>";
const email = document.getElementById("pendingEmail").value;

let api = "/admin-business/api/business-resend-verification.php";
if (mode === "claim") {
    api = "/admin-business/api/business-resend-claim-verification.php";
}

resendBtn.addEventListener("click", () => {
  if (!resendBtn.classList.contains("active")) return;

  fetch(api, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: email })
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert("Doğrulama e-postası tekrar gönderildi.");
        location.reload();
      } else {
        alert(data.message);
      }
    })
    .catch(() => alert("Sunucu hatası."));
});
</script>


</body>
</html>
