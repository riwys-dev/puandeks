<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: /login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

/* SORGU */
$stmt = $pdo->prepare("
  SELECT
    c.id,
    c.name,
    c.owner_name,
    ct.name AS city_name,
    c.phone_verified,
    c.category_id,
    c.about,
    c.address,
    c.email,
    c.phone,
    c.phone_prefix,
    c.country,
    c.domain,
    c.website,
    c.linkedin_url,
    c.facebook_url,
    c.instagram_url,
    c.x_url,
    c.youtube_url,
    c.logo,
    c.documents,
    c.city_id,
    c.annual_income,
    c.latitude,
    c.longitude,
    cat.name AS category_name
FROM companies c
LEFT JOIN categories cat ON cat.id = c.category_id
LEFT JOIN cities ct ON ct.id = c.city_id
WHERE c.id = ? AND c.status != 'deleted'
LIMIT 1
");

$stmt->execute([$_SESSION['company_id']]);
$companyData = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$companyData) {
    header("Location: /business-login");
    exit;
}

/*
 |------------------------------------------------------
 | Session business name
 |------------------------------------------------------
*/
if (!isset($_SESSION['company_name'])) {
    $_SESSION['company_name'] = $companyData['name'] ?? 'İşletme';
}

$BUSINESS_NAME = $_SESSION['company_name'];
?>


<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Puandeks - <?= htmlspecialchars($BUSINESS_NAME, ENT_QUOTES, 'UTF-8') ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/business-admin.css" rel="stylesheet">

    <!-- Favicon  -->
    <link rel="icon" href="img/favicon.png">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


<style>
/* =========================
   CONTENT WRAPPER
========================= */

/* Mobil + Tablet */
#content-wrapper {
  padding-left: 24px !important;
}

/* Sadece Desktop */
@media (min-width: 992px) {
  #content-wrapper {
    padding-left: 48px !important;
  }
}

/* =========================
   SETTINGS GRID
========================= */
.settings-grid {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 24px !important;
}

/* =========================
   SETTINGS BOX
========================= */
.settings-box {
  position: relative !important;
  flex: 0 0 calc(33.333% - 16px) !important;
  background: #fff !important;
  border: 1px solid #ddd !important;
  border-radius: 12px !important;
  padding: 20px !important;
  padding-bottom: 72px !important; 
}

/* Tablet */
@media (max-width: 992px) {
  .settings-box {
    flex: 0 0 calc(50% - 12px) !important;
    padding-bottom: 20px !important;
  }
}

/* Telefon */
@media (max-width: 576px) {
  .settings-box {
    flex: 0 0 100% !important;
    padding-bottom: 20px !important;
  }
}

/* =========================
   TEXT
========================= */
.settings-title {
  font-size: 22px;
  font-weight: 500;
  color: #888;
  margin-bottom: 6px;
}

.settings-desc {
  font-size: 14px;
  color: #aaa;
  margin-bottom: 16px;
}

/* =========================
   BUTTONS – DESKTOP
========================= */
@media (min-width: 992px) {
  .settings-btn {
    position: absolute !important;
    bottom: 20px !important;
    left: 20px !important;
    min-width: 160px !important;
    text-align: center !important;
    border-radius: 10px;
  }

  .settings-box .settings-btn + .settings-btn {
    left: 200px !important;
  }
}


/* =========================
   BUTTON COLORS
========================= */
.btn-freeze {
  background-color: #f1c40f !important;
  color: #fff !important;
  border: none !important;
  border-radius: 10px;
}

.btn-delete {
  background-color: #e74c3c !important;
  color: #fff !important;
  border: none !important;
  border-radius: 10px;
}

}

/* =========================
   HESAP DURUMU – ÖZEL DAVRANIŞ
========================= */
.account-actions {
  position: absolute;
  bottom: 20px;
  left: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.account-actions .settings-btn {
  width: 160px;         
}


.account-actions {
  display: flex;
  flex-direction: column;
  gap: 12px; /* butonlar arası boşluk */
}

.account-actions .settings-btn {
  width: 160px !important;  
}


@media (max-width: 991px) {
  .settings-btn {
    width: 160px !important;  
  }
}

/* =========================
   BUTTON RADIUS – GLOBAL FIX
========================= */
.settings-btn {
  border-radius: 10px !important;
}
</style>

<style>
/* =========================
   SETTINGS MODAL
========================= */

#settingsModal.modal-overlay {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  background: rgba(0, 0, 0, 0.55) !important;
  display: none;
  align-items: center !important;
  justify-content: center !important;
  z-index: 9999 !important;
}

#settingsModal .modal-box {
  background: #fff !important;
  width: 100% !important;
  max-width: 520px !important;
  border-radius: 12px !important;
  padding: 24px !important;
  position: relative !important;
  overflow: visible !important; 
}

#settingsModal .modal-close {
  position: absolute !important;
  top: 14px !important;
  right: 14px !important;
  background: none !important;
  border: none !important;
  font-size: 24px !important;
  cursor: pointer !important;
}



</style>
  
</head>

<body id="page-top">
<div id="wrapper">
  
<!-- Sidebar -->
<?php include("inc/sidebar.php"); ?>
<!-- Sidebar -->
<div id="sidebar-overlay"></div>

<!-- Content -->
<div id="content-wrapper" class="d-flex flex-column" style="margin-left:280px; margin-top:100px; padding:24px; min-height:calc(100vh - 100px); background:#f9fafb;">

<div id="content">

<!-- Topbar -->
    <?php include("inc/topbar.php"); ?>
<!-- Topbar -->


<!-- Main Content -->
<h1 class="h3 mb-4 text-gray-800">Ayarlar</h1>
  
<div class="settings-grid">

  <!-- Profil Bilgileri -->
  <div class="settings-box">
    <div class="settings-title">
      <i class="fas fa-user fa-lg settings-icon"></i>
      Profil Bilgileri
    </div>
    <div class="settings-desc">
      Yetkili adı, e-posta, ülke ve telefon bilgileri
    </div>
    <button class="btn btn-success btn-sm settings-btn" data-modal="profile">
      Düzenle
    </button>
  </div>

  <!-- İşletme Logosu -->
  <div class="settings-box">
    <div class="settings-title">
      <i class="fas fa-image fa-lg settings-icon"></i>
      İşletme Logosu
    </div>
    <div class="settings-desc">
      Profil sayfanızda görünen logo
    </div>
    <button class="btn btn-success btn-sm settings-btn" data-modal="logo">
      Logoyu Güncelle
    </button>
  </div>

  <!-- Şifre Değitir -->
  <div class="settings-box">
    <div class="settings-title">
      <i class="fas fa-lock fa-lg settings-icon"></i>
      Şifre Değiştir
    </div>
    <div class="settings-desc">
      Hesap güvenliği
    </div>
    <button class="btn btn-success btn-sm settings-btn" data-modal="password">
      Şifreyi Değiştir
    </button>
  </div>

  <!-- İsletme Kategorisi -->
  <div class="settings-box">
    <div class="settings-title">
      <i class="fas fa-building fa-lg settings-icon"></i>
      İşletme Kategorisi
    </div>
    <div class="settings-desc">
      Kategorinizi değiştirin
    </div>
    <button class="btn btn-success btn-sm settings-btn" data-modal="category">
      Düzenle
    </button>
  </div>

  <!-- Hakkımızda -->
  <div class="settings-box">
    <div class="settings-title">
      <i class="fas fa-info-circle fa-lg settings-icon"></i>
      Hakkımızda
    </div>
    <div class="settings-desc">
      İşletme açıklaması
    </div>
    <button class="btn btn-success btn-sm settings-btn" data-modal="business-info">
      Düzenle
    </button>
  </div>

  <!-- Adres -->
  <div class="settings-box">
    <div class="settings-title">
      <i class="fas fa-info-circle fa-lg settings-icon"></i>
      Adres bilgileri
    </div>
    <div class="settings-desc">
      İşletme adresi detayları 
    </div>
    <button class="btn btn-success btn-sm settings-btn" data-modal="adress-info">
      Düzenle
    </button>
  </div>

  <!-- Web & Sosyal Medya -->
  <div class="settings-box">
    <div class="settings-title">
      <i class="fas fa-share-nodes fa-lg settings-icon"></i>
      Web & Sosyal Medya
    </div>
    <div class="settings-desc">
      Web sitesi ve sosyal medya linkleri
    </div>
    <button class="btn btn-success btn-sm settings-btn" data-modal="social">
      Düzenle
    </button>
  </div>


  <!-- Doğrulama Belgeleri -->  
  <div class="settings-box">
    <div class="settings-title">
      <i class="fas fa-file-alt fa-lg settings-icon"></i>
      Doğrulama Belgeleri
    </div>
    <div class="settings-desc">
      İşletmenize ait resmi belgeler
    </div>
    <button class="btn btn-success btn-sm settings-btn" data-modal="documents">
      Belgeleri Yönet
    </button>
  </div>

 <!-- Hesap Durumu -->
<div class="settings-box danger-box">
  <div class="settings-title" style="color:#c0392b;">
    <i class="fas fa-exclamation-triangle fa-lg settings-icon" style="color:#e74c3c; margin-right:8px;"></i>
    Hesap Durumu
  </div>

  <div class="settings-desc"> 
    Hesabınızı geçici olarak dondurabilir veya kalıcı olarak silebilirsiniz
  </div>

  <!-- BUTON GRUBU -->
  <div class="account-actions">
    <button class="btn btn-sm settings-btn btn-freeze" data-modal="freeze">
      Hesabı Dondur
    </button>

    <button class="btn btn-sm settings-btn btn-delete" data-modal="delete">
      Üyeliğimi Sil
    </button>
  </div>
</div>



</div>
<!-- Main Content -->
          
</div>


  

  
<!-- SETTINGS MODAL -->
<div id="settingsModal" class="modal-overlay" style="display:none;">
  <div class="modal-box">

    <h3 id="modalTitle"></h3>
    <div id="modalBody"></div>
  </div>
</div>
  
<script>
window.COMPANY_OWNER_NAME = <?= json_encode($companyData['owner_name'] ?? '') ?>;
window.COMPANY_EMAIL = <?= json_encode($companyData['email'] ?? '') ?>;
window.COMPANY_PHONE = <?= json_encode($companyData['phone'] ?? '') ?>;
window.COMPANY_PHONE_PREFIX = <?= json_encode($companyData['phone_prefix'] ?? '90') ?>;
window.COMPANY_PHONE_VERIFIED = <?= (int)($companyData['phone_verified'] ?? 0) ?>;

window.COMPANY_CATEGORY_ID = <?= (int)$companyData['category_id'] ?>;

window.COMPANY_LOGO = <?= json_encode($companyData['logo']) ?>;

window.COMPANY_COUNTRY = <?= json_encode($companyData['country']) ?>;

window.COMPANY_ABOUT = <?= json_encode($companyData['about'] ?? '') ?>;

window.COMPANY_ADDRESS = <?= json_encode($companyData['address'] ?? '') ?>;
window.COMPANY_CITY = <?= json_encode($companyData['city_id']) ?>;
window.COMPANY_CITY_NAME = <?= json_encode($companyData['city_name'] ?? '') ?>;
window.COMPANY_LATITUDE  = <?= json_encode($companyData['latitude'] ?? null) ?>;
window.COMPANY_LONGITUDE = <?= json_encode($companyData['longitude'] ?? null) ?>;

window.COMPANY_WEBSITE = <?= json_encode($companyData['domain']) ?>;

window.COMPANY_LINKEDIN  = <?= json_encode($companyData['linkedin_url'] ?? '') ?>;
window.COMPANY_FACEBOOK  = <?= json_encode($companyData['facebook_url'] ?? '') ?>;
window.COMPANY_INSTAGRAM = <?= json_encode($companyData['instagram_url'] ?? '') ?>;
window.COMPANY_X         = <?= json_encode($companyData['x_url'] ?? '') ?>;
window.COMPANY_YOUTUBE   = <?= json_encode($companyData['youtube_url'] ?? '') ?>;

window.COMPANY_DOCUMENTS = <?= json_encode($companyData['documents']) ?>;
window.COMPANY_ANNUAL_INCOME = <?= json_encode($companyData['annual_income'] ?? '') ?>;

</script>

 
<!-- JAVASCRIPT -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>


<script src="js/settings-ui.js"></script>
<script src="js/settings-modals.js"></script>
<script src="js/settings-logo-handler.js"></script>



<!-- Notif count -->
<script>
function updateNotificationCount() {
  fetch('api/get-company-unread-count.php')
    .then(res => res.json())
    .then(data => {
      const count = parseInt(data.count || 0);
      const el = document.getElementById('notification-count');
      if (el) {
        el.innerText = count;
        el.style.display = count > 0 ? 'inline-block' : 'none';
      }
    });
}

updateNotificationCount();
setInterval(updateNotificationCount, 30000);
</script>
<!-- Notif count -->

<!-- Sidebar open / close -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.getElementById("menuToggle");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebar-overlay");

  if (menuToggle && sidebar && overlay) {
    menuToggle.addEventListener("click", function () {
      sidebar.classList.toggle("open");
      overlay.classList.toggle("active");
    });

    overlay.addEventListener("click", function () {
      sidebar.classList.remove("open");
      overlay.classList.remove("active");
    });
  }
});
</script>
<!-- Sidebar open / close -->

<!-- Map -->
<script>
let map;
let marker;

window.initMap = function () {

  let defaultLocation = { lat: 41.0082, lng: 28.9784 }; // default

  // pinned location -> DB 
  if (window.COMPANY_LATITUDE && window.COMPANY_LONGITUDE) {
    defaultLocation = {
      lat: parseFloat(window.COMPANY_LATITUDE),
      lng: parseFloat(window.COMPANY_LONGITUDE)
    };
  }

  map = new google.maps.Map(document.getElementById("map"), {
    center: defaultLocation,
    zoom: 15
  });

  // Eğer kayıtlı pin varsa göster
  if (window.COMPANY_LATITUDE && window.COMPANY_LONGITUDE) {
    marker = new google.maps.Marker({
    position: defaultLocation,
    map: map,
    icon: {
      url: "https://puandeks.com/img/core/puandeks-pin.png",
      scaledSize: new google.maps.Size(40, 40)
    }
  });

    document.getElementById("latitude").value  = window.COMPANY_LATITUDE;
    document.getElementById("longitude").value = window.COMPANY_LONGITUDE;
  }

  map.addListener("click", function (e) {
    placeMarker(e.latLng);
  });
};

function placeMarker(location) {
  if (marker) {
    marker.setPosition(location);
  } else {
    marker = new google.maps.Marker({
    position: location,
    map: map,
    icon: {
      url: "https://puandeks.com/img/core/puandeks-pin.png",
      scaledSize: new google.maps.Size(40, 40)
    }
  });
  }

  document.getElementById("latitude").value = location.lat();
  document.getElementById("longitude").value = location.lng();
}
</script>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA2ttyxRIB7WeGPdukqe3TVM6pd8rhkVEM&callback=initMap"> </script>
<!-- Map -->

  
</body>
</html>
