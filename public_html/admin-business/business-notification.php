<?php
session_start();
require_once('../../backend/config.php');

$company_id = $_SESSION['company_id'] ?? null;

if (!$company_id) {
    die("Giriş yapılmamış.");
}

$pdo->prepare("
  UPDATE company_notifications
  SET is_read = 1
  WHERE company_id = ?
")->execute([$company_id]);

// Şirket adı session'da yoksa al
if (!isset($_SESSION['company_name'])) {
    try {
        $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
        $stmt->execute([$company_id]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['company_name'] = $company['name'] ?? 'İşletme';
    } catch (PDOException $e) {
        $_SESSION['company_name'] = 'İşletme';
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM company_notifications WHERE company_id = ? ORDER BY created_at DESC");
    $stmt->execute([$company_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veri çekme hatası: " . $e->getMessage());
}

$BUSINESS_NAME = $_SESSION['company_name'] ?? 'İşletme';
?>



<!DOCTYPE html>
<html>
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
            <div class="container-fluid" id="notificationContainer">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                  <h1 class="h3 mb-4 text-gray-800">Bildirimler</h1>

                <button id="deleteAllNotifications"
                  style="background:#dc3545; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;"
                  <?= empty($notifications) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>>
                  Hepsini Sil
                </button>
                </div>


    <?php if (!empty($notifications)): ?>
  <?php foreach ($notifications as $notif): ?>
    <div class="notification-item"
         style="background:#f0f7ff; border:1px solid #cce5ff; border-radius:8px; padding:16px; margin-bottom:16px; position:relative;">

      <!-- X Silme Butonu -->
      <button
        type="button"
        class="delete-notif-btn"
        data-id="<?= $notif['id'] ?>"
        style="
          position:absolute;
          top:8px;
          right:8px;
          border:none;
          background:transparent;
          font-size:16px;
          color:red;
          cursor:pointer;
        ">
        <img src="https://business.puandeks.com/img/icons/close.svg">
      </button>
      <br>

      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div style="padding-right:24px;">
          <strong style="color:#004085;">
            <?= htmlspecialchars($notif['title']) ?>
          </strong>

          <p style="margin-top:8px; color:#333;">
            <?= nl2br(htmlspecialchars($notif['content'])) ?>
          </p>
        </div>

        <div style="text-align:right; white-space:nowrap;">
          <span style="font-size:0.9rem; color:#666;">
            <?= date("d.m.Y", strtotime($notif['created_at'])) ?>
          </span>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <div style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:24px; text-align:center;">
    <h5 style="margin-bottom:8px; color:#111;">Henüz bildiriminiz bulunmuyor</h5>
    <p style="color:#6b7280; margin:0;">
      Yeni aktiviteler ve sistem güncellemeleri burada görüntülenecektir.
    </p>
  </div>
<?php endif; ?>

</div>
<!--container-fluid  -->


            
</div>


        
</div>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

  // Bildirim sil (X)
  document.querySelectorAll('.delete-notif-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;

      fetch('api/delete-company-notification.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + encodeURIComponent(id)
      })
      .then(res => res.text())
      .then(response => {
        if (response === 'OK') {
          this.closest('.notification-item').remove();
          updateTopbarNotificationBadge();
        }
      });
    });
  });

  // Topbar badge güncelle
  function updateTopbarNotificationBadge() {
    fetch('api/get-company-unread-count.php')
      .then(res => res.json())
      .then(data => {
        const count = parseInt(data.count || 0);
        const badge = document.getElementById('notifBadge');

        if (!badge) return;

        if (count > 0) {
          badge.innerText = count > 99 ? '99+' : count;
          badge.style.display = 'block';
        } else {
          badge.style.display = 'none';
        }
      });
  }

});
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



<!-- TOPLU SİL -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  const deleteAllBtn = document.getElementById("deleteAllNotifications");

  if (!deleteAllBtn) return;

  function updateButtonState() {
    const hasNotifications = document.querySelectorAll('.notification-item').length > 0;

    if (!hasNotifications) {
      deleteAllBtn.disabled = true;
      deleteAllBtn.style.opacity = "0.5";
      deleteAllBtn.style.cursor = "not-allowed";
    } else {
      deleteAllBtn.disabled = false;
      deleteAllBtn.style.opacity = "1";
      deleteAllBtn.style.cursor = "pointer";
    }
  }

  function renderEmptyStateIfNeeded() {
    const container = document.getElementById("notificationContainer");
    if (!container) return;

    const items = container.querySelectorAll('.notification-item');

    if (items.length === 0) {
      container.innerHTML = `
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:24px; text-align:center;">
          <h5 style="margin-bottom:8px; color:#111;">Henüz bildiriminiz bulunmuyor</h5>
          <p style="color:#6b7280; margin:0;">
            Yeni aktiviteler ve sistem güncellemeleri burada görüntülenecektir.
          </p>
        </div>
      `;
    }
  }

  updateButtonState();

  deleteAllBtn.addEventListener("click", function () {

    if (deleteAllBtn.disabled) return;

    if (!confirm("Tüm bildirimler silinecek. Emin misin?")) return;

    fetch('api/delete-all-company-notifications.php', {
      method: 'POST'
    })
    .then(res => res.text())
    .then(response => {
      if (response === 'OK') {

        document.querySelectorAll('.notification-item').forEach(el => el.remove());

        renderEmptyStateIfNeeded();
        updateButtonState();

        if (typeof updateTopbarNotificationBadge === "function") {
          updateTopbarNotificationBadge();
        }
      }
    });

  });

});
</script>
  
  
</body>
</html>
