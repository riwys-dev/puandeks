<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

/* =============================
   ADMIN INFO
============================= */
$admin_id = $_SESSION['admin_id'];

$stmt = $pdo->prepare("SELECT full_name FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$admin_name = $admin['full_name'] ?? 'Admin';

// unread notifications
$notifStmt = $pdo->query("
    SELECT COUNT(*) 
    FROM admin_notifications 
    WHERE is_read = 0
");
$unreadCount = $notifStmt->fetchColumn();

/* =============================
   SAVE PACKAGE (POST)
   - SADECE plans editörü
============================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['package_id'])) {

    $package_id = (int)$_POST['package_id'];

   $price_monthly = isset($_POST['price_monthly']) && $_POST['price_monthly'] !== '' 
    ? (float)$_POST['price_monthly'] 
    : null;

  $price_yearly = isset($_POST['price_yearly']) && $_POST['price_yearly'] !== '' 
      ? (float)$_POST['price_yearly'] 
      : null;

    // features (textarea → json)
    $featuresRaw  = trim($_POST['features'] ?? '');
    $featuresArr  = array_filter(array_map('trim', explode("\n", $featuresRaw)));
    $featuresJson = json_encode(array_values($featuresArr), JSON_UNESCAPED_UNICODE);

    $stmt = $pdo->prepare("
        UPDATE packages
        SET
            price_monthly = ?,
            price_yearly  = ?,
            features      = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $price_monthly,
        $price_yearly,
        $featuresJson,
        $package_id
    ]);

    header("Location: package-list.php?saved=1");
    exit;
}

/* =============================
   PACKAGES (READ)
   - ENTERPRISE HARİÇ
============================= */
$packagesStmt = $pdo->query("
    SELECT *
    FROM packages
    WHERE is_active = 1
    ORDER BY sort_order ASC
");

$packages = $packagesStmt->fetchAll(PDO::FETCH_ASSOC);
?>




<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - Paketleri yönet</title>
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

<div class="container-fluid" style="max-width:900px;margin:0 auto;padding:40px 20px;">

  <h1 class="h3 mb-4 text-gray-800">Paketleri Yönet</h1>

  <!-- PERİYOT SEÇİMİ (SADECE UI) -->
  <div class="form-group mb-4" style="max-width:420px;">
    <label><strong>Periyot Seç</strong></label>
    <select class="form-control" id="periodSelect">
      <option value="monthly" selected>Aylık</option>
      <option value="yearly">Yıllık</option>
    </select>
  </div>

  <!-- PAKET SEÇİMİ -->
  <div class="form-group mb-4" style="max-width:420px;">
    <label><strong>Paket Seç</strong></label>
    <select class="form-control" id="packageSelect">
      <?php foreach ($packages as $i => $p): ?>
        <option
          value="<?= $p['id'] ?>"
          <?= $i === 0 ? 'selected' : '' ?>
        >
          <?= htmlspecialchars($p['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <?php foreach ($packages as $i => $p): ?>
    <?php $isEnterprise = !empty($p['is_enterprise']); ?>

    <form method="post"
          class="package-form"
          data-id="<?= $p['id'] ?>"
          style="<?= $i === 0 ? '' : 'display:none;' ?>border:1px solid #ddd;padding:20px;border-radius:8px;">

      <input type="hidden" name="package_id" value="<?= $p['id'] ?>">

      <!-- AYLIK FİYAT -->
      <div class="form-group">
        <label>Aylık Fiyat</label>
        <input class="form-control"
            name="price_monthly"
            value="<?= htmlspecialchars($p['price_monthly']) ?>"
            <?= $isEnterprise ? 'disabled' : '' ?>>
      </div>

      <!-- YILLIK FİYAT -->
      <div class="form-group">
        <label>Yıllık Fiyat</label>
        <input class="form-control"
            name="price_yearly"
            value="<?= htmlspecialchars($p['price_yearly']) ?>"
            <?= $isEnterprise ? 'disabled' : '' ?>>
      </div>

      <!-- FEATURES -->
      <div class="form-group">
        <label>İçerik (her satır 1 madde)</label>
        <textarea class="form-control"
                  rows="6"
                  name="features"><?=
          htmlspecialchars(implode("\n", json_decode($p['features'], true) ?? []))
        ?></textarea>
      </div>

      <button class="btn btn-primary">Değişiklikleri Kaydet</button>

    </form>

  <?php endforeach; ?>

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

<script>
const packageSelect = document.getElementById('packageSelect');
const forms = document.querySelectorAll('.package-form');

function showForm(id) {
  forms.forEach(f => f.style.display = 'none');

  const form = document.querySelector('.package-form[data-id="' + id + '"]');
  if (form) form.style.display = 'block';
}

// ilk yükleme
showForm(packageSelect.value);

// paket değişince
packageSelect.addEventListener('change', e => {
  showForm(e.target.value);
});
</script>




</body>
</html>
