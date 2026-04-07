<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

// Admin adı ve bildirim sayısı
$admin_id = $_SESSION['admin_id'];
$admin_name = 'Admin';
$stmt = $pdo->prepare("SELECT full_name FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    $admin_name = $admin['full_name'];
}
$notifStmt = $pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0");
$unreadCount = $notifStmt->fetchColumn();

// Integrations list (admin source of truth)
$stmt = $pdo->query("
    SELECT *
    FROM integrations
    ORDER BY type ASC, sort_order ASC
");
$integrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - Entegrasyonlar </title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png">
</head>
<body id="page-top">
<div id="wrapper">

<!-- Sidebar -->
<?php include('admin-sidebar.php'); ?>
<!-- Sidebar -->

<!-- Content -->
<div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

<?php include('includes/topbar.php'); ?>


<!-- =================================== -->
<div class="container-fluid" style="margin-bottom:20px;">


  <h1 class="h3 mb-4">Entegrasyonlar</h1>

  <!-- PLATFORM INTEGRATIONS -->
  <div style="
    border:1px solid #e3e6f0;
    padding:20px;
    border-radius:10px;
    margin-bottom:40px;
    background:#ffffff;
  ">
    <h4 style="margin-bottom:20px;">Platform Entegrasyonları</h4>

    <div style="display:flex; flex-wrap:wrap; gap:20px;">

      <?php foreach ($integrations as $row): ?>
        <?php if ($row['type'] !== 'platform') continue; ?>

        <div style="
          width:280px;
          border:1px solid #dee2e6;
          padding:20px;
          border-radius:8px;
          background:#f8f9fc;
          text-align:center;
        ">

          <?php if (!empty($row['logo_path'])): ?>
            <div style="
              height:70px;
              display:flex;
              align-items:center;
              justify-content:center;
              margin-bottom:10px;
            ">
              <img
                src="<?= htmlspecialchars($row['logo_path']) ?>"
                alt="<?= htmlspecialchars($row['name']) ?>"
                style="max-height:55px; max-width:160px;"
              >
            </div>
          <?php endif; ?>

          <?php if ($row['status'] === 'api_ready'): ?>
            <span style="
              display:inline-block;
              padding:4px 10px;
              font-size:12px;
              border-radius:6px;
              background:#36b9cc;
              color:#fff;
              margin-bottom:8px;
            ">API Hazır</span>

          <?php elseif ($row['status'] === 'infra_ready'): ?>
            <span style="
              display:inline-block;
              padding:4px 10px;
              font-size:12px;
              border-radius:6px;
              background:#f6c23e;
              color:#fff;
              margin-bottom:8px;
            ">Altyapı Hazır</span>

          <?php else: ?>
            <span style="
              display:inline-block;
              padding:4px 10px;
              font-size:12px;
              border-radius:6px;
              background:#858796;
              color:#fff;
              margin-bottom:8px;
            ">Planlandı</span>
          <?php endif; ?>

          <?php if (!empty($row['description'])): ?>
            <p style="font-size:14px; margin-top:10px; color:#5a5c69;">
              <?= htmlspecialchars($row['description']) ?>
            </p>
          <?php endif; ?>

        </div>

      <?php endforeach; ?>

    </div>
  </div>





  <hr style="margin:40px 0;">
  
  
  

  <!-- WEBSITE WIDGETLARI -->
<div style="
  border:1px solid #e3e6f0;
  padding:20px;
  border-radius:10px;
  background:#ffffff;
">

  <h4 style="margin-bottom:20px;">Website Widgetları</h4>

  <div style="display:flex; flex-wrap:wrap; gap:20px;">

    <?php foreach ($integrations as $row): ?>
      <?php if ($row['type'] !== 'widget' || !$row['is_active']) continue; ?>

      <div style="
        width:280px;
        border:1px solid #dee2e6;
        padding:20px;
        border-radius:8px;
        background:#f8f9fc;
        text-align:center;
      ">

        <?php if (!empty($row['logo_path'])): ?>
          <div style="
            height:80px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin-bottom:12px;
          ">
            <img
              src="<?= htmlspecialchars($row['logo_path']) ?>"
              alt="<?= htmlspecialchars($row['name']) ?>"
              style="
                max-height:60px;
                max-width:180px;
              "
            >
          </div>
        <?php endif; ?>

        <h5 style="font-weight:bold; margin-bottom:6px;">
          <?= htmlspecialchars($row['name']) ?>
        </h5>

        <span style="
          display:inline-block;
          padding:4px 10px;
          font-size:12px;
          border-radius:6px;
          background:#1cc88a;
          color:#fff;
        ">Widget</span>

        <?php if (!empty($row['description'])): ?>
          <p style="
            font-size:14px;
            margin-top:10px;
            color:#5a5c69;
          ">
            <?= htmlspecialchars($row['description']) ?>
          </p>
        <?php endif; ?>

      </div>

    <?php endforeach; ?>

  </div>

</div>

</div>

  



</div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>© <?php echo date('Y'); ?> Puandeks</span>
                </div>
            </div>
        </footer>
    </div>
</div>



<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
