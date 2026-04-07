<?php
require_once('/home/puandeks.com/backend/config.php');

$admin_id = $_SESSION['admin_id'] ?? null;
$admin_name = 'Admin';
$admin_avatar = "img/placeholder/admin-user.png"; // varsayılan

if ($admin_id) {
    // avatar + full_name birlikte çekiliyor
    $stmt = $pdo->prepare("SELECT full_name, avatar FROM admin_users WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $admin_name = $admin['full_name'];

        // avatar varsa uploads klasöründen al
        if (!empty($admin['avatar'])) {
            $admin_avatar = "/uploads/admin/" . $admin['avatar'];
        }
    }
}

// bildirim sayısı
$notifStmt = $pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0");
$unreadCount = $notifStmt->fetchColumn();
?>


<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
  <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
    <i class="fa fa-bars"></i>
  </button>

  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown no-arrow mx-1">
      <a class="nav-link" href="admin-notification">
        <i class="fas fa-bell fa-fw"></i>
        <?php if ($unreadCount > 0): ?>
          <span class="badge badge-danger badge-counter"><?= $unreadCount ?></span>
        <?php endif; ?>
      </a>
    </li>

    <div class="topbar-divider d-none d-sm-block"></div>

    <li class="nav-item">
      <a class="nav-link" href="general-settings">
        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($admin_name) ?></span>
        <img class="img-profile rounded-circle" src="<?= htmlspecialchars($admin_avatar) ?>">
      </a>
    </li>
  </ul>
</nav>
