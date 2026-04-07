<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

// Admin adı
$admin_id = $_SESSION['admin_id'];
$admin_name = 'Admin';
$stmt = $pdo->prepare("SELECT full_name FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    $admin_name = $admin['full_name'];
}

// Bildirim sayısı
$notifStmt = $pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0");
$unreadCount = $notifStmt->fetchColumn();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Silme işlemi (sadece users tablosu için)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: user-list");
    exit;
}

// Filtreler
$search = $_GET['search'] ?? '';
$type = 'user';
$date = $_GET['date'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

$params = [];
$filterSql = "";

// Arama
if (!empty($search)) {
    if ($type === 'business') {
        $filterSql .= " AND name LIKE :search";
    } else {
        $filterSql .= " AND (name LIKE :search OR surname LIKE :search OR email LIKE :search)";
    }
    $params['search'] = '%' . $search . '%';
}

// Tarih
if (!empty($date)) {
    $filterSql .= " AND DATE(created_at) = :date";
    $params['date'] = $date;
}

if ($type === 'business') {
    $queryBase = "SELECT id, name, email, created_at FROM companies WHERE 1";
    $countQuery = "SELECT COUNT(*) FROM companies WHERE 1";
} else {
    $queryBase = "SELECT id, name, surname, email, created_at, status FROM users WHERE 1";
    $countQuery = "SELECT COUNT(*) FROM users WHERE 1";
}

// Sayı hesapla
$stmtCount = $pdo->prepare($countQuery . $filterSql);
$stmtCount->execute($params);
$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Listeyi çek (LIMIT ve OFFSET sabit olarak eklenir!)
$query = $queryBase . $filterSql . " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);

// Parametre bağla (limit-offset bind yapılmaz!)
foreach ($params as $key => $val) {
    $stmt->bindValue(':' . $key, $val);
}
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - Kullanıcılar </title>
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

<div class="container-fluid" style="max-width: 1200px; margin: 0 auto; padding: 24px;">
    <h1 class="h3 mb-4 text-gray-800" style="font-weight: bold;">Kullanıcılar</h1>
  
<!-- Filtre Alan -->
<form method="GET" style="display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
  <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="İsim veya E-posta" style="max-width: 250px;">
  <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="form-control" style="max-width: 180px;">
  <button class="btn btn-primary" style="padding: 8px 24px;">Filtrele</button>
  <a href="user-list" class="btn btn-secondary" style="padding: 8px 24px;">Filtreyi Temizle</a>
</form>
<!-- Filtre Alan -->

<!-- Kullanıcı Tablosu -->
<div class="card shadow mb-4">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead style="background: #f8f9fc;">
          <tr>
            <th>Ad Soyad</th>
            <th>E-posta</th>
            <th>Profil</th>
            <th>Kayt Tarihi</th>
            <th>Durum</th>
            <th>İşlem</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr><td colspan="6" class="text-center">Kayıt bulunamadı.</td></tr>
          <?php else: ?>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?= htmlspecialchars($user['name'] . ' ' . $user['surname']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><a href="/user-profile?id=<?= $user['id'] ?>" target="_blank">Profili Aç</a></td>
                <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                <td>
                  <?php if ($user['status'] === 'active'): ?>
                    <span class="badge badge-success">Aktif</span>
                  <?php else: ?>
                    <span class="badge badge-secondary">Pasif</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="?delete=<?= $user['id'] ?>" 
                     onclick="return confirm('Bu kullanıcyı silmek istediğinize emin misiniz?')" 
                     class="btn btn-sm btn-danger">Kullanıcıyı Sil</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Sayfalama -->
    <nav>
      <ul class="pagination justify-content-center">
        <?php
          $isDisabled = ($totalPages <= 1);
          $maxVisible = min(4, $totalPages);
        ?>
        <li class="page-item <?= $isDisabled ? 'disabled' : '' ?>">
          <a class="page-link" href="<?= $isDisabled ? '#' : '?search=' . urlencode($search) . '&type=' . urlencode($type) . '&date=' . urlencode($date) . '&page=1' ?>">İlk</a>
        </li>

        <?php for ($i = 1; $i <= $maxVisible; $i++): ?>
          <li class="page-item <?= ($i == $page) ? 'active' : '' ?> <?= $isDisabled ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $isDisabled ? '#' : '?search=' . urlencode($search) . '&type=' . urlencode($type) . '&date=' . urlencode($date) . '&page=' . $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <li class="page-item <?= ($totalPages > 4) ? '' : 'disabled' ?>">
          <a class="page-link" href="<?= ($totalPages > 4) ? '?search=' . urlencode($search) . '&type=' . urlencode($type) . '&date=' . urlencode($date) . '&page=' . $totalPages : '#' ?>">Son</a>
        </li>
      </ul>
    </nav>

      </div> <!-- /card-body -->
    </div> <!-- /card -->
  </div> <!-- /container-fluid -->

</div> <!-- /#content -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
  <div class="container my-auto">
    <div class="copyright text-center my-auto">
      <span>© <?php echo date('Y'); ?> Puandeks</span>
    </div>
  </div>
</footer>

</div> <!-- /#content-wrapper -->
</div> <!-- /#wrapper -->

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>


          

                
  



