<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once("/home/puandeks.com/backend/config.php");

$businessId = $_SESSION["company_id"] ?? null;
if (!$businessId) {
    header("Location: /login");
    exit;
}

$BUSINESS_NAME = $_SESSION["company_name"] ?? "İşletme";


$stmt = $pdo->prepare("
    SELECT p.slug
    FROM company_subscriptions cs
    JOIN packages p ON p.id = cs.package_id
    WHERE cs.company_id = ?
      AND cs.status IN ('active','trial')
    ORDER BY cs.id DESC
    LIMIT 1
");
$stmt->execute([$businessId]);

$currentPackage = $stmt->fetchColumn();
?>


<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Puandeks - <?= htmlspecialchars($BUSINESS_NAME, ENT_QUOTES, 'UTF-8') ?> | Widgets</title>
   
    <!-- Favicon  -->
    <link rel="icon" href="img/favicon.png">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/business-admin.css" rel="stylesheet">


    <style>
/* ANA KUTU */
.widget-package-box{
  background:#ffffff !important;
  border-radius:12px !important;
  padding:32px !important;
  box-shadow:0 1px 3px rgba(0,0,0,0.06) !important;
  margin-bottom:24px !important;
  width:100% !important;
  box-sizing:border-box !important;
}

/* İÇ YERLEŞİM */
.widget-package-inner{
  display:flex !important;
  gap:24px !important;
  align-items:flex-start !important;
  flex-wrap:wrap !important;
}

/* WIDGET KART */
.widget-card{
  width:240px !important;
  min-width:240px !important;
  background:#fff !important;
  border:1px solid #e5e5e5 !important;
  border-radius:16px !important;
  overflow:hidden !important;
  display:flex !important;
  flex-direction:column !important;
}
.widget-title{
  height:52px !important;
  display:flex !important;
  align-items:center !important;
  padding:0 16px !important;
  font-size:22px !important;
  font-weight:600 !important;
  color:#1C1C1C !important;
}

/* ÖNİZLEME */
.widget-preview{
  height:140px !important;
  background:#f4f6f8 !important;
  padding:20px !important;
  display:flex !important;
  align-items:center !important;
  justify-content:center !important;
}

.widget-preview img{
  max-width:100% !important;
  max-height:100% !important;
  object-fit:contain !important;
  border:1px solid #e5e5e5 !important;
}

/* SAĞ TARAF */
.widget-package-inner > div:last-child{
  flex:1 !important;
  min-width:260px !important;
}

/* BUTONLAR */
.widget-package-inner button{
  cursor:pointer !important;
  transition:all .2s ease !important;
}

.widget-package-inner button:hover{
  background:#04DA8D !important;
  color:#ffffff !important;
}

@media (max-width: 1024px){

  .widget-package-inner{
    flex-direction:column !important;
  }

  .widget-package-inner > div:last-child{
    width:100% !important;
    min-width:100% !important;
  }

  .widget-package-inner > div:last-child > div{
    display:block !important;
    width:100% !important;
  }

  .widget-package-inner button{
    display:block !important;
    width:100% !important;
    margin:8px 0 16px 0 !important;
    padding:10px 12px !important;
    box-sizing:border-box !important;
  }
}
@media (max-width: 420px){

  /* flex overflow KÖK ÇÖZÜM */
  .widget-package-inner,
  .widget-package-inner *{
    box-sizing:border-box !important;
  }

  .widget-package-inner > div{
    min-width:0 !important;
    max-width:100% !important;
  }

  /* ölçü + buton satırlarını kır */
  .widget-package-inner > div:last-child{
    word-break:break-word !important;
    overflow-wrap:break-word !important;
  }

  /* buton padding taşmasını kes */
  .widget-package-inner button{
    max-width:100% !important;
    padding:10px !important;
  }
}
@media (max-width: 480px){

  html, body{
    width:100% !important;
    max-width:100% !important;
    overflow-x:hidden !important;
  }

  #wrapper{
    width:100% !important;
    max-width:100% !important;
    overflow-x:hidden !important;
  }

  #content-wrapper{
    margin-left:0 !important;
    min-width:0 !important;
    width:100% !important;
    max-width:100% !important;
  }

  .container-fluid{
    padding-left:16px !important;
    padding-right:16px !important;
    min-width:0 !important;
    width:100% !important;
  }

  /* FLEX CHILD DARALMA ZORLAMASI */
  .d-flex > *{
    min-width:0 !important;
  }
}
</style>

<style>
.widget-grid{
  display:flex;
  flex-wrap:wrap;
  gap:16px;
  justify-content:flex-start;
}

.widget-item{
  width:360px;
}

/* tablet */
@media (max-width:1024px){
  .widget-grid{
    justify-content:center;
  }
}

/* mobil */
@media (max-width:600px){
  .widget-grid{
    flex-direction:column;
    align-items:center;
  }

  .widget-item{
    width:100%;
    max-width:360px;
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

            <h1 class="h3 mb-4 text-gray-800">Widgetlar</h1>


         

              <button type="button" class="btn" style="padding:10px 24px;border-radius:8px;background:#FAE108;
               color:#1c1c1c;border:none;margin-bottom:20px;transition:all .2s ease;" onmouseover="this.style.background='#FEED5A'" 
               onmouseout="this.style.background='#FAE108'" onclick="window.location.href='/widget-select'">
              <img src="img/icons/widget-manager-highlight.svg"> Widgetlerı Gör
            </button>


          
<div style="background:#ffffff;border-radius:12px;padding:32px;margin-bottom:20px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06);">

<?php if (!$currentPackage): ?>
  <p style="color:#6b7280;margin-bottom:24px;">
    Henüz bir paketiniz yok. Widgetları kullanabilmek için lütfen paket satın alın.
  </p>

<?php elseif ($currentPackage === 'enterprise'): ?>
  <p style="color:#047857;margin-bottom:24px;font-weight:500;">
    Şu anda Puandeks'in özel paketi "Enterprise" a sahipsiniz.
  </p>

<?php else: ?>
  <p style="color:#6b7280;margin-bottom:24px;">
    Mevcut paketinizi yükselterek daha fazla widget özelliği açabilirsiniz.
  </p>
<?php endif; ?>

  <button
    type="button"
    class="btn"
    style="padding:10px 24px;border-radius:8px;background:#04DA8D;color:#fff;border:none;"
    onclick="window.location.href='/plans'">
    Paketleri gör
  </button>

</div>

<!-- Widget Boxes -->
<div class="widget-grid">

  <!-- FLEX -->
  <div class="widget-item">
    <div style="background:#fff;border-radius:16px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);">

      <div style="font-size:18px;font-weight:600;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;">
        Flex
        <span onclick="toggleInfo(this)" style="cursor:pointer;font-size:14px;background:#f3f4f6;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;">?</span>
      </div>

      <div class="widget-info" style="display:none;font-size:13px;color:#6b7280;margin-bottom:10px;">
        Minimum genişlik: 320px  <br>
        Önerilen genişlik: 360–420px  <br>
        Yükseklik: İçeriğe göre otomatik
      </div>

      <div style="background:#f4f6f8;border-radius:10px;padding:10px;margin-bottom:12px;">
        <img src="https://puandeks.com/img/brands/widget-brands/Flex-carousel.svg" style="width:100%;border-radius:8px;">
      </div>

      <?php $flexAllowed = in_array($currentPackage, ['plus','premium','advanced','enterprise']); ?>

      <button <?= $flexAllowed ? "onclick=\"copyEmbed('flex')\"" : "disabled" ?>
        style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;border-radius:10px;
        border:1px solid <?= $flexAllowed ? '#04DA8D' : '#e5e7eb' ?>;
        background:<?= $flexAllowed ? '#E8FBF3' : '#f3f4f6' ?>;
        color:<?= $flexAllowed ? '#047857' : '#9ca3af' ?>;">
        <i class="fas fa-code"></i> Embed al
      </button>

      

      <?php if (!$flexAllowed): ?>
      <div style="margin-top:10px;font-size:13px;color:#6b7280;display:flex;gap:6px;">
        <i class="fas fa-lock"></i> Paket yükseltin
      </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- CAROUSEL -->
  <div class="widget-item">
    <div style="background:#fff;border-radius:16px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);">

          <div style="font-size:18px;font-weight:600;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;">
            Carousel
            <span onclick="toggleInfo(this)" style="cursor:pointer;font-size:14px;background:#f3f4f6;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;">?</span>
          </div>

          <div class="widget-info" style="display:none;font-size:13px;color:#6b7280;margin-bottom:10px;">
              Minimum genişlik: 320px  <br>
              Önerilen genişlik: 400px+  <br>
              Yükseklik: İçeriğe göre otomatik
          </div>

      <div style="background:#f4f6f8;border-radius:10px;padding:10px;margin-bottom:12px;">
        <img src="https://puandeks.com/img/brands/widget-brands/Carousel.svg" style="width:100%;border-radius:8px;">
      </div>

      <?php $carouselAllowed = in_array($currentPackage, ['premium','advanced','enterprise']); ?>

      <button <?= $carouselAllowed ? "onclick=\"copyEmbed('carousel')\"" : "disabled" ?>
        style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;border-radius:10px;
        border:1px solid <?= $carouselAllowed ? '#04DA8D' : '#e5e7eb' ?>;
        background:<?= $carouselAllowed ? '#E8FBF3' : '#f3f4f6' ?>;
        color:<?= $carouselAllowed ? '#047857' : '#9ca3af' ?>;">
        <i class="fas fa-code"></i> Embed al
      </button>

      <?php if (!$carouselAllowed): ?>
      <div style="margin-top:10px;font-size:13px;color:#6b7280;display:flex;gap:6px;">
        <i class="fas fa-lock"></i> Paket yükseltin
      </div>
      <?php endif; ?>

    </div>
  </div>


  <!-- SLIDER -->
  <div class="widget-item">
    <div style="background:#fff;border-radius:16px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);">

     <div style="font-size:18px;font-weight:600;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;">
        Slider
        <span onclick="toggleInfo(this)" style="cursor:pointer;font-size:14px;background:#f3f4f6;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;">?</span>
      </div>

      <div class="widget-info" style="display:none;font-size:13px;color:#6b7280;margin-bottom:10px;">
            Minimum genişlik: 320px  <br>
            Önerilen genişlik: 400px+  <br>
            Yükseklik: İçeriğe göre otomatik
      </div>

      <div style="background:#f4f6f8;border-radius:10px;padding:10px;margin-bottom:12px;">
        <img src="https://puandeks.com/img/brands/widget-brands/Slider.svg" style="width:100%;border-radius:8px;">
      </div>

      <?php $sliderAllowed = in_array($currentPackage, ['premium','advanced','enterprise']); ?>

      <button <?= $sliderAllowed ? "onclick=\"copyEmbed('slider')\"" : "disabled" ?>
        style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;border-radius:10px;
        border:1px solid <?= $sliderAllowed ? '#04DA8D' : '#e5e7eb' ?>;
        background:<?= $sliderAllowed ? '#E8FBF3' : '#f3f4f6' ?>;
        color:<?= $sliderAllowed ? '#047857' : '#9ca3af' ?>;">
        <i class="fas fa-code"></i> Embed al
      </button>

      <?php if (!$sliderAllowed): ?>
      <div style="margin-top:10px;font-size:13px;color:#6b7280;display:flex;gap:6px;">
        <i class="fas fa-lock"></i> Paket yükseltin
      </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- LIST -->
  <div class="widget-item">
    <div style="background:#fff;border-radius:16px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);">

      <div style="font-size:18px;font-weight:600;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;">
          List
          <span onclick="toggleInfo(this)" style="cursor:pointer;font-size:14px;background:#f3f4f6;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;">?</span>
        </div>

        <div class="widget-info" style="display:none;font-size:13px;color:#6b7280;margin-bottom:10px;">
              Minimum genişlik: 320px  <br>
              Önerilen genişlik: 400px+  <br>
              Yükseklik: İçeriğe göre otomatik
        </div>

      <div style="background:#f4f6f8;border-radius:10px;padding:10px;margin-bottom:12px;">
        <img src="https://puandeks.com/img/brands/widget-brands/List.svg" style="width:100%;border-radius:8px;">
      </div>

      <?php $listAllowed = in_array($currentPackage, ['enterprise']); ?>
      
      <button <?= $listAllowed ? "onclick=\"copyEmbed('list')\"" : "disabled" ?>
        style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;border-radius:10px;
        border:1px solid <?= $listAllowed ? '#04DA8D' : '#e5e7eb' ?>;
        background:<?= $listAllowed ? '#E8FBF3' : '#f3f4f6' ?>;
        color:<?= $listAllowed ? '#047857' : '#9ca3af' ?>;">
        <i class="fas fa-code"></i> Embed al
      </button>

      <?php if (!$listAllowed): ?>
      <div style="margin-top:10px;font-size:13px;color:#6b7280;display:flex;gap:6px;">
        <i class="fas fa-lock"></i> Paket yükseltin
      </div>
      <?php endif; ?>

    </div>
  </div>

</div>

<!-- Widget boxes -->



</div>
</div>          
</div>
      
 

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>


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


<!-- Sidebar open/close -->
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
<!-- Sidebar open/close -->

<script>
const COMPANY_ID = <?= (int)$businessId ?>;
</script>


<!-- Copy Embed -->
<script>
function copyEmbed(type){
  fetch(`/api/get-widget-embed.php?type=${type}`, {
  credentials: 'include'
})
    .then(r => {
      if (!r.ok) throw new Error('Kod alınamadı');
      return r.text();
    })
    .then(code => {

      // modern clipboard
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(code);
      } else {
        // fallback
        const textarea = document.createElement("textarea");
        textarea.value = code;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand("copy");
        textarea.remove();
      }

      alert("Embed kodu kopyalandı");
    })
    .catch(() => {
      alert("Embed kodu alınamadı");
    });
}
</script>
<!-- Copy Embed -->

<!-- Widget Info -->
 <script>
function toggleInfo(el){
  const info = el.parentElement.nextElementSibling;
  info.style.display = (info.style.display === "none") ? "block" : "none";
}
</script>
<!-- Widget Info -->


</body>
</html>
