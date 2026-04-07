<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

if (isset($_GET['from']) && $_GET['from'] === 'register-button') {
  if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $giris_turu = ($_SESSION['role'] === 'business') ? 'işletme' : 'tüketici';
    $uyari = "Şu anda $giris_turu olarak giriş yaptınız. Yeni bir işletme üyeliği için önce çıkış yapmalısınz.";
    echo "<script>alert('$uyari');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="tr">
  
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>İşletme Kayıt - Puandeks</title>
  <link rel="stylesheet" href="https://puandeks.com/css/styles.css">
  
    <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
   

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: Arial, sans-serif;
    background-color: #fcfbf3;
    min-height: 100vh;
    width: 100%;
    margin: 0;
    padding: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }

  .background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
  }

  .container {
    display: flex;
    max-width: 1100px;
    width: 100%;
    margin: 0 auto;
    overflow: hidden;
    position: relative;
  }

  .info-section {
    position: sticky;
    top: 40px;
    align-self: flex-start;
    text-align: left;
    max-width: 500px;
    padding: 40px 20px 0 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
  }

  .info-logo {
    display: block;
    margin-bottom: 40px;
    width: 180px;
    margin-left: 0;
    text-align: left;
  }

  .info-section h2 {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 10px;
  }

  .info-section p {
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 20px;
  }

  /* FORM */
  .form-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px;
    background: transparent;
    margin-top: 50px;
  }

  .form-container {
    width: 100%;
    max-width: 430px;
    background: white;
    padding: 20px;
    border-radius: 10px;
  }

  .google-signup {
    width: 100%;
    padding: 10px;
    background: #4285f4;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    margin-bottom: 10px;
    margin-top: 10px;
  }

  .divider {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 20px 0;
  }

  .divider-line {
    flex-grow: 1;
    height: 1px;
    background-color: #ccc;
    margin: 0 10px;
  }

  .divider-text {
    font-size: 14px;
    font-weight: normal;
    color: #555;
  }

  .email-signup-title {
    font-size: 16px;
    font-weight: bold;
    text-align: left;
    margin-bottom: 10px;
  }

  form input,
  form select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 14px;
  }

  .input-group {
    display: flex;
    gap: 10px;
  }

  .submit-btn {
    width: 100%;
    padding: 12px;
    background: #0c7c59 !important;
    color: white;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
  }

  .checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    margin-right: 0;
  }

  .checkbox label {
    flex-grow: 1;
    white-space: normal;
    word-break: break-word;
    font-size: 12px;
    line-height: 12px;
  }

  .back-link {
    position: absolute;
    top: 20px;
    left: 20px;
    display: flex;
    align-items: center;
    text-decoration: none;
    font-size: 16px;
    font-weight: normal;
    color: #333;
    transition: color 0.3s ease-in-out;
  }

  .back-icon {
    width: 18px;
    height: 18px;
    margin-right: 8px;
  }

  .back-link:hover {
    color: #0044cc;
  }

  .confirmation-message {
    display: none;
    background: #fff;
    padding: 40px;
    border-radius: 10px;
    width: 600px;
    max-width: 90%;
    margin: 50px auto 0;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-align: left;
    font-size: 16px;
  }

  .confirmation-message h2 {
    font-size: 22px;
    margin-bottom: 15px;
  }

  .confirmation-message p {
    margin-bottom: 10px;
  }

  .confirmation-message a {
    color: #0044cc;
    text-decoration: underline;
  }

    /* 768px  */
@media (max-width: 1024px) {

  .container {
    flex-direction: column !important;
    align-items: center !important;
  }

  .info-section {
    position: relative !important;
    top: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
    padding: 40px 20px 0 20px !important;
    text-align: center !important;
    align-items: center !important;
  }

  .form-section {
    width: 100% !important;
    max-width: 500px !important;
    margin-top: 20px !important;
    padding: 20px !important;
  }

  .form-container {
    width: 100% !important;
    max-width: 100% !important;
  }

  .info-logo {
    margin: 0 auto 30px auto !important;
    text-align: center !important;
  }
}


  /* 480px */
  @media (max-width: 480px) {
    .form-section {
      width: 100%;
      max-width: 360px;
      margin: 30px auto 0;
      padding: 10px;
    }
    .confirmation-message {
      margin: 30px auto 0;
    }
  }

  /* 769px – 1024px  */
  @media (max-width: 1024px) and (min-width: 769px) {
    .container {
      flex-direction: column !important;
      align-items: center !important;
    }

    .info-section {
      position: relative !important;
      align-self: center !important;
      width: 100% !important;
      max-width: 100% !important;
      padding: 80px 20px 20px 20px !important;
      text-align: center !important;
    }

    .info-logo {
      margin: 0 auto 30px auto !important;
      text-align: center !important;
    }
  }
</style>

  
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
      window.location.href = 'https://business.puandeks.com';
    }
  }, 250); 
" 
style="position:fixed; top:20px; right:20px; background:transparent; border:none; cursor:pointer; z-index:9999;">
  <i class="fa-solid fa-xmark" style="font-size:28px; color:#000;"></i>
</button>
<!-- Close Page -->

  <div class="background"></div>
  <div class="container">
    <div class="info-section">
      <div style="display:flex; align-items:center; gap:18px; margin-bottom:40px;">
        <a href="https://business.puandeks.com" style="display:flex; align-items:center;">
          <img src="img/logo-p-business_2.svg" alt="puandeks" style="height:38px;">
        </a>
      </div>



      <h2>Markanızın Gerçek Hikâyesini Dinleyin</h2>
      <p>Tüketici deneyimleriyle markanızın değerini güçlendirin.</p>
      <h2>Güven, Başarının Anahtarıdır</h2>
      <p>Kullanıcı yorumlarıyla yeni tüketicilere ulaşın.</p>
      <h2>İşletmenizi Yorumlarla Büyütün</h2>
      <p>Değerlendirmelerle gelişim fırsatlarını keşfedin.</p>
    </div>
    
    

<div class="form-section">
  <div class="form-container">
    <h2>Ücretsiz Hesap Oluştur</h2>
    <br>
    <form action="api/business-register.php" method="POST" id="registerForm">
      <input type="text" name="business_name" placeholder="İşletme Adı" required>
      <input type="text" name="full_name" placeholder="Yetkili İsim Soyisim" required>
      
      <!-- Website - Mail -->
      <input type="text" id="businessWebsite" name="website" placeholder="İşletme Websitesi (Örnek: firmaadi.com)" required>


      <input type="email" id="businessEmail" name="email" placeholder="İşletme E-posta adresi" required>
        <small id="emailInfo" style="display:block; color:#777; font-size:12px; margin-bottom:10px;">
          Kurumsal e-posta adresinizi girin (örnek: <b>info@firmaadi.com</b>).<br>
          Gmail, Outlook gibi genel adreslerle kayıt yapılamaz.
        </small>
        <div id="emailWarning" style="display:none; color:#d9534f; font-size:12px; margin-bottom:10px;">
          Lütfen domain adınız ile uyumlu bir e-posta adresi kullanın.
        </div>
     <!-- Website - Mail -->
      

    <!-- Country Select -->
    <div class="filter_type" style="margin-top: 12px;">
      <div id="customCountryDropdown" style="position: relative;">
        <span id="countrySelected" style="display: block; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; background-color: #fff;">
          Ülke seçin
        </span>
        <ul id="countryOptions" style="display: none; list-style: none; margin: 0; padding: 0; border: 1px solid #ddd; border-radius: 5px; background-color: #fff; max-height: 180px; overflow-y: auto; position: absolute; width: 100%; z-index: 999;">
          <?php
            $stmtTr = $pdo->prepare("SELECT id, name, phone_prefix FROM countries WHERE code = 'TR' LIMIT 1");
            $stmtTr->execute();
            $turkey = $stmtTr->fetch(PDO::FETCH_ASSOC);

            if ($turkey) {
              echo '<li 
                      data-value="' . htmlspecialchars($turkey['id']) . '" 
                      data-prefix="' . htmlspecialchars($turkey['phone_prefix']) . '" 
                      data-enabled="1"
                      style="padding:10px; cursor:pointer; font-weight:bold;">
                      ' . htmlspecialchars($turkey['name']) . '
                    </li>';
            }

            $stmt = $pdo->query("SELECT id, name, phone_prefix FROM countries WHERE code != 'TR' ORDER BY name ASC");
            while ($country = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo '<li 
                      data-value="' . htmlspecialchars($country['id']) . '" 
                      data-prefix="' . htmlspecialchars($country['phone_prefix']) . '" 
                      data-enabled="0"
                      style="padding:10px; color:#bbb; cursor:not-allowed;">
                      ' . htmlspecialchars($country['name']) . '
                    </li>';
            }
            ?>
        </ul>
        <input type="hidden" name="country" id="countryInput">
      </div>
    </div>
      
       <br>

    <!-- Telefon -->
    <div class="input-group">
      <input type="text" id="phonePrefix" readonly style="width: 80px;" name="phone_prefix">
      <input type="tel" name="phone" placeholder="Telefon Numarası" required>
    </div>
    <!-- Telefon -->

      <br>

      <!-- Şifre -->
      <small style="display:block; color:#777; font-size:12px; margin-bottom:5px;">
        Şifre en az 8 karakter olmalı. Büyük harf, küçük harf, rakam ve özel karakter içermelidir.
      </small>

      <div style="position: relative; margin-bottom: 10px;">
        <input type="password" id="password" name="password" placeholder="Şifre belirleyin" required minlength="8" style="width:100%; padding-right:40px;">
        <i class="fa-solid fa-eye toggle-password" data-target="password" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; color:#555;"></i>
      </div>

      <div style="position: relative; margin-bottom: 10px;">
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Şifreyi tekrar girin" required style="width:100%; padding-right:40px;">
        <i class="fa-solid fa-eye toggle-password" data-target="confirmPassword" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; color:#555;"></i>
      </div>

      <small id="passwordError" style="color:#d9534f; font-size:12px; display:none;">
        Şifreler eşleşmiyor veya kurallara uymuyor.
      </small>
      <!-- Şifre -->

      
     <!-- Onay -->
      <div class="checkbox" style="margin-top: 20px;">
        <input type="checkbox" name="agreement" id="kvkk" onchange="checkFormValidity()">
        <label for="kvkk">
          <a href="https://puandeks.com/kvkk" target="_blank">KVKK</a>, 
          <a href="https://puandeks.com/privacy-policy" target="_blank"> Gizlilik politikası</a>,
          <a href="https://business.puandeks.com/system-files/isletme_uyelik_sozlesmesi.pdf" target="_blank"> İşletme üyelik sözleşmesi</a>
          ve
          <a href="https://puandeks.com/terms-conditions" target="_blank"> Şartlar ve koşullar</a> 
          metinlerini okudum, kabul ediyorum.
        </label>
      </div>
      <!-- Onay -->

      <br>
      <button type="submit" id="submitBtn" style="width: 100%; background-color: #0C7C59 !important; color:white; padding: 12px; border-radius:5px; font-size:16px; opacity:0.5; 
                     cursor:not-allowed; outline: none !important; border: none !important;" disabled>
        Ücretsiz Hesap Oluştur
      </button>
    </form>
  </div>
</div>
        
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

  /* =========================================================
     ELEMENT Selections
  ========================================================= */
  const form = document.getElementById("registerForm");
  const submitBtn = document.getElementById("submitBtn");
  const kvkk = document.getElementById("kvkk");
  const phoneInput = document.querySelector('input[name="phone"]');
  const businessName = document.querySelector('input[name="business_name"]');
  const fullName = document.querySelector('input[name="full_name"]');
  const websiteInput = document.getElementById("businessWebsite");
  const emailInput = document.getElementById("businessEmail");
  const emailWarning = document.getElementById("emailWarning");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirmPassword");
  const passwordError = document.getElementById("passwordError");
  const toggleIcons = document.querySelectorAll(".toggle-password");
  const dropdown = document.getElementById("customCountryDropdown");
  const options = document.getElementById("countryOptions");
  const selected = document.getElementById("countrySelected");
  const hiddenInput = document.getElementById("countryInput");
  const phonePrefix = document.getElementById("phonePrefix");

  /* =========================================================
     GENEL YARDIMCI: BUTON DURUMU GUNCELLEME
  ========================================================= */
  function updateSubmitState(isValid) {
    submitBtn.disabled = !isValid;
    submitBtn.style.opacity = isValid ? "1" : "0.5";
    submitBtn.style.cursor = isValid ? "pointer" : "not-allowed";
  }

  /* =========================================================
     FORM CONTROL
  ========================================================= */
  function checkFormValidity() {
    const requiredFields = form.querySelectorAll("input[required], select[required]");
    let allFilled = true;
    requiredFields.forEach(field => {
      if (!field.value.trim()) allFilled = false;
    });
    updateSubmitState(allFilled && kvkk.checked);
  }

  kvkk.addEventListener("change", checkFormValidity);
  form.querySelectorAll("input[required], select[required]").forEach(el => {
    el.addEventListener("input", checkFormValidity);
  });

     /* =========================================================
       COUNTRY (SADECE TÜRKİYE AKTF)
    ========================================================= */
    selected.addEventListener("click", () => {
      options.style.display = (options.style.display === "block") ? "none" : "block";
    });

    /* Click logic */
    options.querySelectorAll("li").forEach(item => {
      item.addEventListener("click", () => {

        if (item.dataset.enabled !== "1") {
          return;
        }

        selected.textContent = item.textContent;
        hiddenInput.value = item.dataset.value;
        phonePrefix.value = item.getAttribute("data-prefix");
        options.style.display = "none";
        checkFormValidity();
      });
    });

    const turkeyItem = options.querySelector('li[data-enabled="1"]');
    if (turkeyItem) {
      selected.textContent = turkeyItem.textContent;
      hiddenInput.value = turkeyItem.dataset.value;
      phonePrefix.value = turkeyItem.getAttribute("data-prefix");
    }

    document.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) options.style.display = "none";
    });


  /* =========================================================
     E-POSTA
  ========================================================= */
  const blockedDomains = [
    "gmail.com", "outlook.com", "hotmail.com", "yahoo.com",
    "yandex.com", "icloud.com", "aol.com", "protonmail.com"
  ];

  function validateEmail() {
    const email = emailInput.value.trim().toLowerCase();
    const domain = email.substring(email.lastIndexOf("@") + 1);
    const website = websiteInput.value.trim().toLowerCase()
      .replace(/^https?:\/\//, '')
      .replace(/^www\./, '')
      .split('/')[0];

    const mainDomain = website.split('.')[0];
    const valid = !blockedDomains.includes(domain) && domain.includes(mainDomain);

    emailWarning.style.display = valid ? "none" : "block";
    checkFormValidity();
    if (!valid) updateSubmitState(false);
  }

  emailInput.addEventListener("input", validateEmail);
  websiteInput.addEventListener("input", validateEmail);

  /* =========================================================
     TELEFON 
  ========================================================= */
  phoneInput.addEventListener("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });

  /* =========================================================
     iSiM / FiRMA ALANLARi (SADECE HARF)
  ========================================================= */
  [businessName, fullName].forEach(input => {
    input.addEventListener("input", function () {
      this.value = this.value.replace(/[^a-zA-ZüşıöĞÜŞ\s]/g, "");
    });
  });

  /* =========================================================
     WEBSITE
  ========================================================= */
  websiteInput.addEventListener("input", function () {
    this.value = this.value.replace(/^https?:\/\//i, "").replace(/[^a-zA-Z0-9.\-]/g, "");
    const pattern = /^[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*\.[a-zA-Z]{2,}$/;
    this.style.borderColor = pattern.test(this.value) ? "#ddd" : "#e67e22";
  });

  /* =========================================================
     SIFRE HIDE / SHOW
  ========================================================= */
  toggleIcons.forEach(icon => {
    icon.addEventListener("click", function () {
      const target = document.getElementById(this.dataset.target);
      const isPassword = target.type === "password";
      target.type = isPassword ? "text" : "password";
      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  });

  /* =========================================================
     SIFRE
  ========================================================= */
  function validatePasswordStrength(pass) {
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/.test(pass);
  }

  function validatePasswords() {
    const valid = validatePasswordStrength(password.value);
    const match = password.value === confirmPassword.value;
    passwordError.style.display = (!valid || !match) ? "block" : "none";
    checkFormValidity();
    if (!valid || !match) updateSubmitState(false);
  }

  password.addEventListener("input", validatePasswords);
  confirmPassword.addEventListener("input", validatePasswords);

  /* =========================================================
     STATE CONTROL
  ========================================================= */
  checkFormValidity();
});
</script>

<!-- Business Register – AJAX Submit -->
<script>
document.getElementById("registerForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    formData.set('password', document.getElementById('password').value);
    formData.set('confirmPassword', document.getElementById('confirmPassword').value);

    fetch("https://business.puandeks.com/api/business-register.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            window.location.href = "/verify-pending";
        } else {
            alert(data.message);
        }

    })
    .catch(() => {
        alert("Sunucuya bağlanırken bir hata oluştu.");
    });
});
</script>
<!-- Business Register – AJAX Submit -->


</body>
</html>
