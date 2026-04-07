<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

// Sayfa açıldığında tüm bildirimleri okundu yap
$pdo->query("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");

$stmt = $pdo->prepare("SELECT * FROM admin_notifications ORDER BY created_at DESC");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks - Admin</title>
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
  <h1 class="h3 mb-4 text-gray-800">Bildirimler</h1>

  <button class="btn btn-danger btn-sm mb-4" onclick="deleteAllNotifications()">Tmünü Sil</button>

  <?php if (empty($notifications)): ?>
    <div class="alert alert-info">Hiç bildirim yok.</div>
  <?php else: ?>
    <?php foreach ($notifications as $n): ?>
      <div style="background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px; padding: 16px; margin-bottom: 16px; position: relative;" id="notif-<?= $n['id'] ?>">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <strong style="color: #004085;">📣 <?= htmlspecialchars($n['title']) ?></strong>
            <p style="margin-top: 8px; color: #333;">
              <?= nl2br(htmlspecialchars($n['content'])) ?>
            </p>
          </div>
          <div style="text-align: right;">
            <span style="font-size: 0.9rem; color: #666;"><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></span><br>
            <button class="btn btn-sm btn-light" style="margin-top: 8px;" onclick="deleteNotification(<?= $n['id'] ?>)">❌</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

      


  
<!-- =================================== -->

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

<!-- DELETE -->
<script>
function deleteNotification(id) {
  if (!confirm("Bu bildirimi silmek istiyor musunuz?")) return;

  fetch('api/delete-admin-notification.php?id=' + id)
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        const el = document.getElementById("notif-" + id);
        if (el) el.remove();
      } else {
        alert("Silinemedi: " + data.message);
      }
    })
    .catch(() => alert("Sunucuya bağlanılamadı."));
}
</script>
<!-- DELETE -->

<!-- DELETE ALL -->
<script>
function deleteAllNotifications() {
  if (!confirm("Tüm bildirimleri silmek istiyor musunuz?")) return;

  fetch('api/delete-all-admin-notifications.php')
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        location.reload();
      } else {
        alert("Silinemedi: " + data.message);
      }
    })
    .catch(() => alert("Sunucuya bağlanılamadı."));
}
</script>
<!-- DELETE ALL -->


</body>
</html>
