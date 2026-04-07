<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: /login");
    exit;
}

if (!isset($_SESSION['company_name'])) {
    require_once('/home/puandeks.com/backend/config.php');
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
  <title>Puandeks - <?= htmlspecialchars($BUSINESS_NAME, ENT_QUOTES, 'UTF-8') ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/business-admin.css" rel="stylesheet">

    <!-- Favicon  -->
    <link rel="icon" href="img/favicon.png">
  
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

    <h1 class="h3 mb-4 text-gray-800">Paket ve Faturalandırma</h1>

      <?php
      $company_id = $_SESSION['company_id'] ?? null;
      $subscription_data = null;
      $package_name = 'Free';
      $start_date = '-';
      $end_date = '-';

      if ($company_id) {
        $api_url = "https://business.puandeks.com/api/get-company-subscription.php?company_id=" . $company_id;
        $response = file_get_contents($api_url . '&t=' . time());
        $decoded = json_decode($response, true);

        if ($decoded && $decoded['success'] && !empty($decoded['data'])) {
          $data = $decoded['data'];

          if (in_array($data['status'], ['active','trial'])) {
            $package_name = $data['package_name'];
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];
          }
        }
      }
      ?>

<!-- Aktif Paket Bilgisi -->
<div class="card mb-4" style="border:4px solid #9FF6D3; box-shadow:none; border-radius:12px; background-color:white; max-width:300px;">
  <div class="card-body">

    <div class="d-flex justify-content-between align-items-start mb-3">
  
        <div>
          <h5 class="mb-0" style="font-weight: bold; color: #4b4f56;">
            Aktif Paket
          </h5>

          <div style="margin-top:8px;">
            <span style="background-color: #9FF6D3; color:#1C1C1C; padding:5px 15px; border-radius:4px;">
              <?= htmlspecialchars($package_name) ?>
              <?php if (($data['status'] ?? '') === 'trial'): ?>
                <span style="margin-left:6px; font-size:12px; color:#059669;">
                  (7 Gün Deneme)
                </span>
              <?php endif; ?>
            </span>
          </div>
        </div>

      </div>

    <p class="mb-1"><strong>Başlangıç:</strong>
      <?= htmlspecialchars($start_date) ?>
    </p>

    <p class="mb-1"><strong>Bitiş:</strong>
      <?= htmlspecialchars($end_date) ?>
    </p>

    <div class="mt-4 text-left">
      <a href="plans" class="btn btn-success">
        <i class="fas fa-arrow-right"></i> Paketleri gör
      </a>
    </div>

  </div>
</div>
              
<!-- Fatura History -->
<div style="
  border:1px solid #e0e0e0;
  border-radius:12px;
  background:#ffffff;
  padding:20px;
  max-width:600px;
">

  <h3 style="margin:0 0 16px 0; font-weight:600; color:#4b4f56;">
    Fatura Geçmişi
  </h3>

  <div id="invoice-list"></div>

</div>
<!-- Fatura History -->

      
    </div><!-- /.container-fluid -->

  </div><!-- /#content -->



  
  
</div><!-- /#content-wrapper -->

</div><!-- /#wrapper -->

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

<!-- Invoices -->
 <script id="invjs">
document.addEventListener("DOMContentLoaded", function () {

  fetch("api/get-company-invoices.php")
    .then(res => res.json())
    .then(data => {

      const container = document.getElementById("invoice-list");
      container.innerHTML = "";

      if (!data.success || data.data.length === 0) {
        container.innerHTML = "<small style='color:#6b7280;'>Fatura bulunamadı</small>";
        return;
      }

      data.data.forEach(inv => {

        const item = document.createElement("div");

        item.style = `
          padding:12px;
          border:1px solid #e5e7eb;
          border-radius:10px;
          display:flex;
          justify-content:space-between;
          align-items:center;
          margin-bottom:10px;
        `;

        item.innerHTML = `
          <div>
            <strong style="color:#1f2937;">Fatura #${inv.invoice_number}</strong><br>
            <small style="color:#6b7280;">${inv.issue_date}</small>
          </div>

          ${inv.pdf_url ? `
            <a href="${inv.pdf_url}" target="_blank"
              style="color:#2563eb; text-decoration:none; font-weight:500;">
              PDF
            </a>
          ` : `
            <span style="color:#9ca3af;">PDF yok</span>
          `}
        `;

        container.appendChild(item);
      });

    });

});
</script>
<!-- Invoices -->

</body>
</html>
