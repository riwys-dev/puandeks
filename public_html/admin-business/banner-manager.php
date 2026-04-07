<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: /login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

/* Paket durumu */
$PACKAGE_STATUS = 'free';

$api_url = "https://business.puandeks.com/api/get-company-subscription.php?company_id=" . $_SESSION['company_id'];
$response = @file_get_contents($api_url);
$decoded = json_decode($response, true);

if ($decoded && $decoded['success'] && !empty($decoded['data'])) {
    $PACKAGE_STATUS = $decoded['data']['status'];
}

/* Banner kullanabilir mi */
$isAllowed = in_array($PACKAGE_STATUS, ['trial','active']);

/* işletme adı */
if (!isset($_SESSION['company_name'])) {
    $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
    $stmt->execute([$_SESSION['company_id']]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['company_name'] = $company['name'] ?? 'İşletme';
}

$BUSINESS_NAME = $_SESSION['company_name'] ?? 'İşletme';
?>



<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Puandeks - <?= htmlspecialchars($BUSINESS_NAME, ENT_QUOTES, 'UTF-8') ?> | Banner yönetimi </title>
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link href="css/business-admin.css" rel="stylesheet">
  <link rel="icon" href="img/favicon.png">

<style>

/* Banner Box */
.banner-box {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 24px;
  max-width: 800px;
}

/* Başlık */
.banner-box h2 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 16px;
  color: #444;
}

/* Uyarı */
.banner-warning {
  background: #fee2e2;
  color: #991b1b;
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-size: 14px;
}

/* Form alanları */
.banner-field {
  margin-bottom: 20px;
}

.banner-field label {
  font-weight: 600;
  margin-bottom: 6px;
  display: block;
}

.banner-field input {
  border-radius: 10px;
  height: 44px;
}

/* Preview */
.banner-preview {
  width: 100%;
  aspect-ratio: 1200 / 550;
  background: #f3f4f6;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  font-size: 14px;
}

/* Button */
.banner-btn {
  border-radius: 10px;
  padding: 10px 18px;
  font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
  .banner-box {
    padding: 20px;
  }
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
<div class="container-fluid">

<h1 class="h3 mb-4 text-gray-800">Banner Yönetimi</h1>

<div class="banner-box">

  <h2>Banner Ayarları</h2>

  <!-- Paket uyarı -->
 <?php if (!in_array($PACKAGE_STATUS, ['trial','active'])): ?>
  <div class="banner-warning">
    Bu özellik mevcut paketinize dahil değildir. Kullanmak için paketinizi yükseltin.
  </div>
<?php endif; ?>

<!-- FORM -->
<?php $isAllowed = in_array($PACKAGE_STATUS, ['trial','active']); ?>
<form id="bannerForm" enctype="multipart/form-data">

  <!-- Banner görsel -->
  <div class="banner-field">
    <label>Banner Görseli</label>
    <input type="file" name="banner" class="form-control" <?= !$isAllowed ? 'disabled' : '' ?> required>
  </div>

  <!-- Banner link -->
  <div class="banner-field">
    <label>Yönlendirme Linki</label>
    <input type="text" name="banner_link" class="form-control" placeholder="https://..." <?= !$isAllowed ? 'disabled' : '' ?>>
  </div>

  <!-- Önizleme -->
  <div class="banner-field">
    <label>Önizleme</label>

<div class="banner-preview" id="bannerPreview">
<?php
$stmt = $pdo->prepare("SELECT banner_url FROM companies WHERE id = ?");
$stmt->execute([$_SESSION['company_id']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($data['banner_url'])) {
    echo '<img src="' . htmlspecialchars($data['banner_url']) . '" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">';
} else {
    echo 'Banner önizleme alanı';
}
?>
</div>

  </div>

  <!-- Kurallar -->
  <div style="margin-top:10px; margin-bottom:10px; background:#fef3c7; color:#92400e; padding:10px 14px; border-radius:10px; font-size:13px;">
    ⚠️ Banner boyutu 1200x550 olmalıdır <br>
    Maksimum 2MB, format: JPG / PNG / WEBP
</div>

  <!-- Kaydet -->
  <button type="submit" class="btn btn-success banner-btn"
    <?= !$isAllowed ? 'disabled style="opacity:0.6;cursor:not-allowed;"' : '' ?>>
    <i class="fas fa-save"></i> Banner Kaydet
  </button>

</form>
<!-- FORM -->

</div>
<!-- /Content -->

</div>
<!-- / container-fluid -->




<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>


<script>
// Notif count
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


<!-- Sidebar Aç/Kapat -->
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
<!-- Sidebar Aç/Kapat -->


<!-- Banner upload  -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  const form = document.getElementById("bannerForm");
  const fileInput = document.querySelector('input[name="banner"]');
  if (!fileInput) return;
  const preview = document.getElementById("bannerPreview");

  /* PREVIEW */
  fileInput.addEventListener("change", function () {

    const file = this.files[0];
    if (!file) return;

    const img = new Image();

    img.onload = function () {

      if (this.width !== 1200 || this.height !== 550) {
        alert("Banner boyutu 1200x550 olmalıdır");
        fileInput.value = "";
        preview.innerHTML = "Banner önizleme alanı";
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">';
      };
      reader.readAsDataURL(file);

    };

    img.src = URL.createObjectURL(file);

  });

  /* UPLOAD */
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const file = fileInput.files[0];
    if (!file) {
      alert("Lütfen bir görsel seçin");
      return;
    }

    const img = new Image();
    img.onload = function () {

      if (this.width !== 1200 || this.height !== 550) {
        alert("Banner boyutu 1200x550 olmalıdır");
        return;
      }

      const formData = new FormData(form);

      fetch('https://business.puandeks.com/api/upload-banner.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
      })
      .then(async res => {
        const data = await res.json();

        if (data.status === 'success') {
          alert("Banner başarıyla yüklendi");
        } else {
          alert(data.message || "Hata oluştu");
        }
      })
      .catch(err => {
        console.error(err);
        alert("Sunucu hatası");
      });

    };

    img.src = URL.createObjectURL(file);

  });

});
</script>


</body>
</html>