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
?>



<!DOCTYPE html>
<html lang="tr">
  
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - İncelemeler</title>
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
          
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">İncelemeler</h1>

  <!-- Sekmeler -->
  <ul class="nav nav-tabs mb-4" id="reviewTabs" role="tablist" style="border-bottom: 2px solid #ccc;">

    <li class="nav-item">
      <a class="nav-link active show" id="pending-tab" data-toggle="tab" href="#pending" role="tab" style="font-weight:bold;">Yeni İncelemeler</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab" style="font-weight:bold;">Onaylananlar</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab" style="font-weight:bold;">Reddedilenler</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="reports-tab" data-toggle="tab" href="#reports" role="tab" style="font-weight:bold;">
        Kullanıcı Şikayetleri
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" id="business-reports-tab" data-toggle="tab" href="#business-reports" role="tab" style="font-weight:bold;">
        İşletme Şikayetleri
      </a>
    </li>


  </ul>

  <div class="tab-content" id="reviewTabsContent">

    <!-- Yeni İncelemeler -->
    <div class="tab-pane fade show active" id="pending" role="tabpanel">
      <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning">
          <h6 class="m-0 font-weight-bold text-white">Yeni İncelemeler</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
              <thead class="thead-light">
                <tr>
                  <th>Tarih</th>
                  <th>İşletme</th>
                  <th>Kullanıcı</th>
                  <th>Yorum</th>
                  <th>Puan</th>
                  <th>İşlem</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
            <div class="pagination-area mt-3 text-center" id="pagination-pending"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Onaylananlar -->
    <div class="tab-pane fade" id="approved" role="tabpanel">
      <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success">
          <h6 class="m-0 font-weight-bold text-white">Onaylanan İncelemeler</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
              <thead class="thead-light">
                <tr>
                  <th>Tarih</th>
                  <th>İşletme</th>
                  <th>Kullanıcı</th>
                  <th>Yorum</th>
                  <th>Puan</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
            <div class="pagination-area mt-3 text-center" id="pagination-approved"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reddedilenler -->
    <div class="tab-pane fade" id="rejected" role="tabpanel">
      <div class="card shadow mb-4">
        <div class="card-header py-3 bg-danger">
          <h6 class="m-0 font-weight-bold text-white">Reddedilen İncelemeler</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
              <thead class="thead-light">
                <tr>
                  <th>Tarih</th>
                  <th>İşletme</th>
                  <th>Kullanıcı</th>
                  <th>Yorum</th>
                  <th>Puan</th>
                  <th>İşlem</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
            <div class="pagination-area mt-3 text-center" id="pagination-rejected"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Şikayetler -->
    <div class="tab-pane fade" id="reports" role="tabpanel">
      <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#1c1c1c;">
          <h6 class="m-0 font-weight-bold text-white">Şikayet Edilen Yorumlar</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
              <thead class="thead-light">
                <tr>
                  <th>Tarih</th>
                  <th>İşletme</th>
                  <th>Yorumu Yazan</th>
                  <th>Şikayet Eden</th>
                  <th>Şikayet İçeriği</th>
                  <th>İşlem</th>
                </tr>
              </thead>
              <tbody id="reports-body">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>


    <!-- İşletme Şikayetleri -->
    <div class="tab-pane fade" id="business-reports" role="tabpanel">
      <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#05462F;">
          <h6 class="m-0 font-weight-bold text-white">İşletme Tarafından Şikayet Edilen Yorumlar</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
              <thead class="thead-light">
                <tr>
                  <th>Tarih</th>
                  <th>İşletme</th>
                  <th>Yorumu Yazan</th>
                  <th>Şikayet Nedeni</th>
                  <th>Durum</th>
                  <th>İşlem</th>
                </tr>
              </thead>
              <tbody id="business-reports-body">
                <!-- frontend dummy satırlar eklenecek -->
              </tbody>
            </table>
          </div>
        </div>
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
<script src="js/admin-reviews.js"></script>




</body>
</html>
