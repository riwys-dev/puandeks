<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: https://business.puandeks.com");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
  
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>İşletme Kaydınızı Tamamlayın - Puandeks</title>
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

<div id="verifyBanner" 
     style="
        width:100%;
        background:#54D18C;
        color:#1C1C1C;
        padding:16px;
        text-align:center;
        font-size:15px;
        font-weight:600;
        position:fixed;
        top:0;
        left:0;
        z-index:9999;
        opacity:1;
        transition:opacity 0.6s ease;
     ">
  E-posta adresiniz başarıyla doğrulandı. Lütfen üyelik bilgilerinizi tamamlayın.
</div>



  <div class="background"></div>
  <div class="container">
    <div class="info-section">
      
      <div style="display:flex; align-items:center; gap:18px; margin-bottom:40px;">
        <a href="https://business.puandeks.com" style="display:flex; align-items:center;">
          <img src="img/logo-p-business_2.svg" alt="puandeks" style="height:38px;">
        </a>
      </div>
      
      <h2>Markanzın Gerçek Hikâyesini Dinleyin</h2>
      <p>Tüketici deneyimleriyle markanızın değerini güçlendirin.</p>
      <h2>Güven, Başarının Anahtarıdır</h2>
      <p>Kullanıcı yorumlarıyla yeni tüketicilere ulaşın.</p>
      <h2>İşletmenizi Yorumlarla Büyütün</h2>
      <p>Değerlendirmelerle gelişim frsatlarını keşfedin.</p>
    </div>
    
    

<div class="form-section">
  <div class="form-container">
    <h2>İşletme Bilgilerinizi tamamlayın</h2>
    <br>
    
   <form action="/api/business-register-complete.php" method="POST" id="registerForm">
    <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">


  <!-- İşletme Kategorisi -->
  <label for="category" style="font-weight: bold; display:block; margin-bottom:6px;">İşletme Kategorisi</label>
  <div class="filter_type" style="margin-top: 5px;">
    <div id="customCategoryDropdown" style="position: relative;">
      <span id="categorySelected" style="display: block; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; background-color: #fff;">
        Kategori seçin
      </span>
      <ul id="categoryOptions" style="display: none; list-style: none; margin: 0; padding: 0; border: 1px solid #ddd; border-radius: 5px; background-color: #fff; max-height: 180px; overflow-y: auto; position: absolute; width: 100%; z-index: 999;">
        <?php
          require_once('/home/puandeks.com/backend/config.php');
          $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
          while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<li data-value="' . htmlspecialchars($category['id']) . '" style="padding: 10px; cursor: pointer;">' . htmlspecialchars($category['name']) . '</li>';
          }
        ?>
      </ul>
      <input type="hidden" name="category_id" id="categoryInput">
    </div>
  </div>

  <br>


  <!-- Gelir -->
  <label for="annual_income" style="font-weight: bold;">Yıllık Gelir (₺)</label>
  <input type="number" step="0.01" name="annual_income" placeholder="Örn: 250000" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; margin-bottom:20px;">

  <!-- Buton -->
      <button type="submit" id="submitBtn"
        style="width: 100%; background-color: #0C7C59 !important; color:white; padding: 12px; 
               border-radius:5px; font-size:16px; opacity:0.5; cursor:not-allowed; 
               outline:none !important; border:none !important;"
        disabled>
    Kaydı Tamamla
  </button>
</form>

    
  </div>
</div>
    
        
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

  /* =========================================================
     ELEMENT SEÇİMLERİ
  ========================================================= */
  const form = document.getElementById("registerForm");
  const submitBtn = document.getElementById("submitBtn");
  const dropdown = document.getElementById("customCategoryDropdown");
  const options = document.getElementById("categoryOptions");
  const selected = document.getElementById("categorySelected");
  const hiddenInput = document.getElementById("categoryInput");
  const incomeInput = document.querySelector('input[name="annual_income"]');

  /* =========================================================
     BUTON DURUMU GÜNCELLEME
  ========================================================= */
  function updateSubmitState(isValid) {
    submitBtn.disabled = !isValid;
    submitBtn.style.opacity = isValid ? "1" : "0.5";
    submitBtn.style.cursor = isValid ? "pointer" : "not-allowed";
  }

  /* =========================================================
     KATEGORİ DROPDOWN
  ========================================================= */
  selected.addEventListener("click", () => {
    options.style.display = (options.style.display === "block") ? "none" : "block";
  });

  options.querySelectorAll("li").forEach(item => {
    item.addEventListener("click", () => {
      selected.textContent = item.textContent;
      hiddenInput.value = item.dataset.value;
      options.style.display = "none";
      checkFormValidity();
    });
  });

  document.addEventListener("click", (e) => {
    if (!dropdown.contains(e.target)) options.style.display = "none";
  });

/* =========================================================
   FORM KONTROL
========================================================= */
function checkFormValidity() {
  const categoryFilled = hiddenInput.value.trim() !== "";
  const incomeFilled = incomeInput.value.trim() !== "";
  updateSubmitState(categoryFilled && incomeFilled);
}

/* =========================================================
   GELİR ALANI KONTROLÜ
========================================================= */
incomeInput.addEventListener("input", checkFormValidity);


  /* =========================================================
     İLK DURUM
  ========================================================= */
  checkFormValidity();
});
</script>



<!-- Complete --> 
<script>
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registerForm");

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    try {
      const response = await fetch("api/business-register-complete.php", {
        method: "POST",
        body: formData
      });

      const result = await response.json();

      if (result.success) {
        alert(result.message);
        // Başarlysa otomatik yönlendir
        window.location.href = result.redirect || "https://business.puandeks.com/login";
      } else {
        alert("Hata: " + result.message);
      }
    } catch (err) {
      alert("Sunucu hatası: " + err.message);
    }
  });
});
</script>
<!-- Complete --> 

<!-- Alert Closing --> 
<script>
document.addEventListener("DOMContentLoaded", function () {
    const banner = document.getElementById("verifyBanner");

    setTimeout(() => {
        banner.style.opacity = "0";

        // tamamen kaldr
        setTimeout(() => {
            banner.style.display = "none";
        }, 600);

    }, 10000); // 10 saniye
});
</script>
<!-- Alert Closing --> 

</body>
</html>
