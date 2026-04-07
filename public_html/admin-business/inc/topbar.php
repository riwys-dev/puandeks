<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once('/home/puandeks.com/backend/config.php');

$ownerName = 'Yetkili';
$logoPath  = 'img/business-logo.png';

if (!empty($_SESSION['company_id'])) {
  $stmt = $pdo->prepare("SELECT owner_name, logo FROM companies WHERE id = ?");
  $stmt->execute([$_SESSION['company_id']]);
  $company = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($company) {
    if (!empty($company['owner_name'])) {
      $ownerName = $company['owner_name'];
    }

    if (!empty($company['logo'])) {
      $logoPath = $company['logo']; 
    }
  }
}
?>


<style>
.topbar {
  position: fixed;
  top: 0;
  left: 280px; 
  right: 0;
  height: 100px;
  background-color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  z-index: 997; 
}


/* Sağ kısmı */
.topbar .right-items {
  display: flex;
  align-items: center;
  gap: 24px;
}

.notif-icon {
  position: relative;
  display: inline-block;
}

.notif-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  background: #e53935;
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  border-radius: 9px;
  line-height: 18px;
  text-align: center;
  box-sizing: border-box;
}


/* Kullanıcı bilgisi */
.topbar .user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}

.topbar .user-info span {
  font-family: Arial, sans-serif;
  font-size: 14px;
  font-weight: normal;
  color: #444;
}

.topbar .user-info img {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
}
</style>

<div class="topbar">

  <!-- Hamburger  -->
  <div id="menuToggle" class="menu-toggle d-md-none">
    <img src="img/icons/mobile-menu.svg" alt="Menü" width="24" height="24">
  </div>

  
  <div></div>

  <!-- Right -->
  <div class="right-items">
   <a href="business-notification" class="notif-icon">
      <img src="img/icons/notif-icon.svg" alt="Bildirim">
      <span id="notifBadge" class="notif-badge" style="display:none;"></span>
    </a>


    <div class="user-info">
      <span><?= htmlspecialchars($ownerName) ?></span>
      <img src="<?= htmlspecialchars($logoPath) ?>" alt="Profil">

    </div>
  </div>
</div>





<script>
// Notif icon
document.addEventListener("DOMContentLoaded", function () {
  fetch("api/get-company-unread-count.php")
    .then(res => res.json())
    .then(data => {
      const count = parseInt(data.count || 0);
      const badge = document.getElementById("notifBadge");

      if (badge && count > 0) {
        badge.innerText = count > 99 ? "99+" : count;
        badge.style.display = "block";
      }
    });
});
</script>

