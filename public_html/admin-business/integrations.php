<?php
session_start();
require_once("/home/puandeks.com/backend/config.php");

$businessId = $_SESSION["company_id"] ?? null;
if (!$businessId) {
  header("Location: /business-login");
  exit;
}

$BUSINESS_NAME = $_SESSION['company_name'] ?? 'İşletme';
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Puandeks - <?= htmlspecialchars($BUSINESS_NAME, ENT_QUOTES, 'UTF-8') ?> | Entegrasyonlar</title>
   
    <!-- Favicon  -->
    <link rel="icon" href="img/favicon.png">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/business-admin.css" rel="stylesheet">

<style>
/* =========================
   GRID WRAPPERS
========================= */
.integration-section {
  margin-bottom: 48px;
}

.integration-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

/* =========================
   WIDGET BOX 
========================= */
.widget-box {
  position: relative;
  flex: 0 0 calc(25% - 15px);
  background: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 16px;
  overflow: hidden;

  display: flex;
  flex-direction: column;
}

/* =========================
   WIDGET PREVIEW ALANI 
========================= */
.widget-preview {
  height: 140px;
  background: #f4f6f8;
  padding: 20px;

  display: flex;
  align-items: center;
  justify-content: center;
}

.widget-preview img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.widget-preview i {
  font-size: 52px;
  color: #444;
}

/* =========================
   TITLE 
========================= */
.widget-title {
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: flex-start;

  padding: 0 16px;
  font-size: 16px;
  font-weight: 500;
  color: #111;
}

/* =========================
   BADGE – YAKINDA
========================= */
.badge-soon {
  position: absolute;
  top: 12px;
  right: 12px;
  background: #f1c40f;
  color: #fff;
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 20px;
  z-index: 2;
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 992px) {
  .widget-box {
    flex: 0 0 calc(50% - 10px);
  }
}

@media (max-width: 576px) {
  .widget-box {
    flex: 0 0 100%;
  }
}

/* =========================
   TAB HEADER
========================= */
.integration-tabs {
  display: flex;
  background: #e0e0e0;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 28px;
  max-width: 420px;
}

.tab-btn {
  flex: 1;
  padding: 12px 16px;
  font-size: 14px;
  font-weight: 500;
  border: none;
  background: transparent;
  cursor: pointer;
  color: #1c1c1c;
}

.tab-btn.active {
  background: #9FF6D3;
  color: #1C1C1C;
  font-weight: 600;
}

/* =========================
   TAB CONTENT
========================= */
.tab-content {
  display: none;
}

.tab-content.active {
  display: block;
}

/* PLATFORM – TITLE hide */
#tab-platform .widget-title {
  display: none;
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

            <h1 class="h3 mb-4 text-gray-800">Entegrasyonlar</h1>


           <div class="integration-tabs">
            <button class="tab-btn active" data-tab="platform">Platformlar</button>
            <button class="tab-btn" data-tab="website">Widget Türleri</button>
          </div>
  


              <div class="tab-content active" id="tab-platform">
              <div class="integration-section">
                <div class="integration-grid">

                  <!-- AKTİF -->
                   <div class="widget-box">
                    <div class="widget-preview">
                      <i class="fas fa-globe"></i>
                    </div>
                    <div class="widget-title"></div>
                  </div>

                  <div class="widget-box">
                    <div class="widget-preview">
                      <img src="https://puandeks.com/img/brands/widget-brands/woocommerce-logo.svg">
                    </div>
                    <div class="widget-title"></div>
                  </div>

                  <div class="widget-box">
                    <div class="widget-preview">
                      <img src="https://puandeks.com/img/brands/widget-brands/shopify-logo.svg">
                    </div>
                    <div class="widget-title"></div>
                  </div>

                  <div class="widget-box">
                    <div class="widget-preview">
                      <img src="https://puandeks.com/img/brands/widget-brands/ideasoft-logo.svg">
                    </div>
                    <div class="widget-title"></div>
                  </div>

                  <div class="widget-box">
                    <div class="widget-preview">
                      <img src="https://puandeks.com/img/brands/widget-brands/ticimax-logo.svg">
                    </div>
                    <div class="widget-title"></div>
                  </div>

                  <div class="widget-box">
                    <div class="widget-preview">
                      <img src="https://puandeks.com/img/brands/widget-brands/tsoft-logo.svg">
                    </div>
                    <div class="widget-title"></div>
                  </div>

                  <div class="widget-box">
                    <div class="widget-preview">
                      <img src="https://puandeks.com/img/brands/widget-brands/ikas-logo.svg">
                    </div>
                    <div class="widget-title"></div>
                  </div>

                  <!-- YAKINDA -->
                  <div class="widget-box">
                    <span class="badge-soon">Yakında</span>
                    <div class="widget-preview">
                      <img src="https://puandeks.com/img/brands/widget-brands/shopier-logo.svg">
                    </div>
                    <div class="widget-title"></div>
                  </div>

                  <div class="widget-box">
                    <span class="badge-soon">Yakında</span>
                    <div class="widget-preview">
                      <img src="https://puandeks.com/img/brands/widget-brands/prestashop-logo.svg">
                    </div>
                    <div class="widget-title"></div>
                  </div>

                  <div class="widget-box">
                    <span class="badge-soon">Yakında</span>
                    <div class="widget-preview">
                      <img src="https://puandeks.com/img/brands/widget-brands/wix-logo.svg">
                    </div>
                    <div class="widget-title"></div>
                  </div>

                </div>
              </div>
            </div>
              
              
           <div class="tab-content" id="tab-website">
            <div class="integration-section">
              <div class="integration-grid">

                <div class="widget-box">
                  <div class="widget-preview">
                    <img src="https://puandeks.com/img/brands/widget-brands/Carousel.svg">
                  </div>
                  <div class="widget-title">Carousel</div>
                </div>

                <div class="widget-box">
                  <div class="widget-preview">
                    <img src="https://puandeks.com/img/brands/widget-brands/Slider.svg">
                  </div>
                  <div class="widget-title">Slider</div>
                </div>

                <div class="widget-box">
                  <div class="widget-preview">
                    <img src="https://puandeks.com/img/brands/widget-brands/List.svg">
                  </div>
                  <div class="widget-title">List</div>
                </div>

                <div class="widget-box">
                  <div class="widget-preview">
                    <img src="https://puandeks.com/img/brands/widget-brands/Flex-carousel.svg">
                  </div>
                  <div class="widget-title">Flex</div>
                </div>

              </div>
            </div>
          </div>


  


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

<!-- Sekmeler -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  document.querySelectorAll(".tab-btn").forEach(btn => {
    btn.addEventListener("click", function () {

      // buton aktifliği
      document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
      this.classList.add("active");

      // içerik geçişi
      const tab = this.dataset.tab;
      document.querySelectorAll(".tab-content").forEach(c => c.classList.remove("active"));
      document.getElementById("tab-" + tab).classList.add("active");

    });
  });

});
</script>
<!-- Sekmeler -->

</body>
</html>
