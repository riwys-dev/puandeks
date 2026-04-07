<?php
// cookie-settings.php
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Çerez Tercihleri - Puandeks </title>
	<!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
  
   <link rel="stylesheet" href="css/cookie.css">
  
</head>
  
  
<body style="margin:0; font-family:Arial, sans-serif; background:#f9f9f9;">

  <div class="cookie-page">
    
    <!-- Üst kısım: Logo + X -->
    <div class="cookie-page-header" style="display:flex; align-items:center; justify-content:space-between; padding:15px 20px; background:#fff; border-bottom:1px solid #eee;">
      <img src="img/puandeks-logo_2.svg" alt="Logo" style="height:28px;">
      <button onclick="window.history.back()" style="background:none; border:none; font-size:28px; cursor:pointer;">&times;</button>
    </div>

    <!-- İçerik -->
    <div class="cookie-modal-body" style="max-width:600px; margin:20px auto; background:#fff; padding:20px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

      <h2>Çerez tercihlerinizi yönetin</h2>
      <p>
        Web sitemizde belirli çerezler, sizin deneyiminizi ve web faaliyetimizi geliştirmek için kullanılmaktadır. 
        Buradan hangi çerezleri aktif/pasif edebileceğinizi seçebilirsiniz. 
        <a href="cookie-policy" target="_blank">Daha fazla bilgi</a>
      </p>

      <!-- Kesinlikle gerekli -->
      <div class="cookie-option">
        <h4>Kesinlikle gerekli çerezler <em style="color:#32a067;">(Zorunlu)</em></h4>
        <p>Zorunlu çerezler, sitenin temel işlevlerini sağlamak için gereklidir ve kapatılamaz.</p>
      </div>

      <!-- Performans -->
      <div class="cookie-option">
        <div class="cookie-option-row">
          <div>
            <h4>Performans çerezleri</h4>
            <p>Bu çerezler, genel site performansnı ölçmemize ve iyileştirmemize olanak sağlar.</p>
          </div>
          <label class="switch">
            <input type="checkbox" id="performanceCookies" checked />
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <!-- İşlevsellik -->
      <div class="cookie-option">
        <div class="cookie-option-row">
          <div>
            <h4>İşlevsellik çerezleri</h4>
            <p>Bu çerezler, site kullanımınızı kişiselleştirmemizi sağlar.</p>
          </div>
          <label class="switch">
            <input type="checkbox" id="functionalCookies" checked />
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <!-- Pazarlama -->
      <div class="cookie-option">
        <div class="cookie-option-row">
          <div>
            <h4>Pazarlama çerezleri</h4>
            <p>Bu çerezler, reklam etkinliğini ölçmemizi ve ilgili reklamlar göstermemizi sağlar.</p>
          </div>
          <label class="switch">
            <input type="checkbox" id="marketingCookies" />
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <!-- Kaydet Butonu -->
      <div class="cookie-modal-footer" style="margin-top:20px; text-align:right;">
        <button id="saveCookiePreferences" class="save-cookie-btn">Tercihleri kaydet</button>
      </div>

    </div>
  </div>

  <script src="js/cookie-settings.js"></script>
</body>
</html>
