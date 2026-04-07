<?php
$current_page = basename($_SERVER['PHP_SELF'], ".php");
?>

<!-- Overlay -->
<div id="sidebar-overlay"></div>

<style>
  html, body {
  overflow-x: hidden;
}

/* Sidebar */
.sidebar-nav {
  width: 280px;
  max-width: 100%;
  background-color: #1C1C1C;
  font-family: Arial, sans-serif;
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  z-index: 9998;
  transition: transform 0.3s ease;
  overflow-x: hidden;
}

/* Scroll */
.sidebar-nav::-webkit-scrollbar {
  width: 6px;
}
.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.2);
  border-radius: 10px;
}

.sidebar-nav ul.menu-list {
  padding-bottom: 120px;
}

/* Mobil */
@media (max-width: 768px) {
  .sidebar-nav {
    transform: translateX(-100%);
  }
  .sidebar-nav.open {
    transform: translateX(0);
  }

  #sidebar-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.4);
    z-index: 100;
  }

  #sidebar-overlay.active {
    display: block;
  }
}

/* Logo */
.sidebar-nav .logo {
  display: flex;
  align-items: center;
  padding: 0 24px;
  margin-bottom: 44px;
  margin-top: 20px;   
}
.sidebar-nav .logo img {
  height: 30px;
}

/* İşletme adı */
.sidebar-nav .business-name {
  color: #fff;
  font-size: 18px;
  padding: 0 24px;
  margin-bottom: 32px;
}

/* Menü */
.sidebar-nav ul.menu-list {
  list-style: none;
  margin: 0;
  padding: 0 0 100px 0; /* logout için boşluk */
  display: flex;
  flex-direction: column;
}

/* Menü item */
.sidebar-nav li {
  height: 72px;
  display: flex;
  align-items: center;
}

/* Link */
.sidebar-nav li a {
  display: flex;
  align-items: center;
  gap: 16px;
  width: 100%;
  height: 100%;
  padding: 0 24px;
  color: #fff;
  font-size: 15px;
  text-decoration: none;
  transition: all 0.25s ease;
}

/* Hover */
.sidebar-nav li:not(.active) a:hover {
  background: rgba(255,255,255,0.05);
}

/* Active */
.sidebar-nav li.active a {
  background-color: #9FF6D3;
  color: #1C1C1C;
}
.sidebar-nav li.active a img {
  filter: none;
}

/* Icon */
.sidebar-nav li:not(.active) a img {
  filter: brightness(0) invert(1);
  opacity: 0.8;
}
.sidebar-nav li:not(.active) a:hover img {
  opacity: 1;
}

/* Logout sabit */
.sidebar-logout {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 280px;

  background-color: #1C1C1C;
  padding: 16px 24px;
  z-index: 10000;
}

@media (max-width: 768px) {
  .sidebar-logout {
    left: 0;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }

  .sidebar-nav.open ~ .sidebar-logout {
    transform: translateX(0);
  }
}

/* Üst çizgi */
.sidebar-logout::before {
  content: "";
  display: block;
  height: 1px;
  background: rgba(255,255,255,0.08);
  margin-bottom: 12px;
}

/* Logout link */
.sidebar-logout a {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #fff;
  font-size: 15px;
  text-decoration: none;
  opacity: 0.6;
  transition: 0.2s;
}
.sidebar-logout a:hover {
  opacity: 1;
}

.sidebar-logout a img {
  filter: brightness(0) invert(1);
  height: 20px;
}
</style>

<div class="sidebar-nav">

  <!-- Logo -->
  <div class="logo">
    <a href="https://business.puandeks.com">
      <img src="https://puandeks.com/img/core/logo-p-business.svg" alt="Puandeks İşletme Logo">
    </a>
  </div>

<div class="business-name">
 <a href="https://puandeks.com/company/<?=
htmlspecialchars(
    $_SESSION['company_slug'] ?? 
    strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_SESSION['company_name'] ?? ''))),
    ENT_QUOTES,
    'UTF-8'
)
?>" target="_blank" style="color:#fff; text-decoration:none;">
  <?= htmlspecialchars($_SESSION['company_name'] ?? 'İşletme') ?>
</a>
</div>


  <!-- Menü -->
  <ul class="menu-list">
    <li class="<?= ($current_page == 'home') ? 'active' : '' ?>">
      <a href="home">
        <img src="img/icons/<?= ($current_page == 'home') ? 'home-highlight.svg' : 'home.svg' ?>" height="20">
        Ana Sayfa
      </a>
    </li>

    <li class="<?= ($current_page == 'reviews') ? 'active' : '' ?>">
      <a href="reviews">
        <img src="img/icons/<?= ($current_page == 'reviews') ? 'reviews-highlight.svg' : 'reviews.svg' ?>" height="20">
        İncelemeler
      </a>
    </li>

    <li class="<?= ($current_page == 'automations') ? 'active' : '' ?>">
      <a href="automations">
        <img src="img/icons/<?= ($current_page == 'automations') ? 'automations-highlight.svg' : 'automations.svg' ?>" height="20">
        Otomasyonlar
      </a>
    </li>

    <li class="<?= ($current_page == 'integrations') ? 'active' : '' ?>">
      <a href="integrations">
        <img src="img/icons/<?= ($current_page == 'integrations') ? 'integrations-highlight.svg' : 'integrations.svg' ?>" height="20">
        Entegrasyonlar
      </a>
    </li>

     <li class="<?= ($current_page == 'widget-manager') ? 'active' : '' ?>">
      <a href="widget-manager">
        <img src="img/icons/<?= ($current_page == 'widget-manager') ? 'widget-manager-highlight.svg' : 'widget-manager.svg' ?>" height="20">
        Widgetlar
      </a>
    </li>

    <li class="<?= ($current_page == 'banner-manager') ? 'active' : '' ?>">
  <a href="banner-manager">
    <img src="img/icons/<?= ($current_page == 'banner-manager') ? 'banner-highlight.svg' : 'banner.svg' ?>" height="20">
    Banner Yönetimi
  </a>
</li>

   

    <li class="<?= ($current_page == 'pricing') ? 'active' : '' ?>">
      <a href="pricing">
        <img src="img/icons/<?= ($current_page == 'pricing') ? 'pricing-highlight.svg' : 'pricing.svg' ?>" height="20">
        Paket ve Faturalandırma
      </a>
    </li>


    <li class="<?= ($current_page == 'settings') ? 'active' : '' ?>">
      <a href="settings">
        <img src="img/icons/<?= ($current_page == 'settings') ? 'settings-highlight.svg' : 'settings.svg' ?>" height="20">
        Ayarlar
      </a>
    </li>
  </ul>

</div>

  <!-- Cikis-->
  <div class="sidebar-logout">
    <a href="logout.php">
      <img src="img/icons/logout.svg" alt="Çkış">
      Çıkış
    </a>
  </div>


<!-- JavaScript ile toggle -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("menuToggle");
  const sidebar = document.querySelector(".sidebar-nav");
  const overlay = document.getElementById("sidebar-overlay");

  toggleBtn?.addEventListener("click", function () {
    sidebar?.classList.add("open");
    overlay?.classList.add("active");
  });

  overlay?.addEventListener("click", function () {
    sidebar?.classList.remove("open");
    overlay?.classList.remove("active");
  });
});
</script>
