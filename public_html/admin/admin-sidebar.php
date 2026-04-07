<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #1C1C1C !important; background-image: none !important;">

  <!-- Logo -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="https://puandeks.com/admin/">
    <div class="sidebar-brand-icon"><img src="img/logo.svg" height="26"></div>
    <div class="sidebar-brand-text mx-3"><img src="img/admin-logo.svg" height="16px"></div>
  </a>

  <hr class="sidebar-divider my-0">

  <li class="nav-item <?= ($currentPage == 'https://puandeks.com/admin/') ? 'active' : '' ?>">
    <a class="nav-link" href="https://puandeks.com/admin/"><span> Ana Sayfa</span></a>
  </li>
  <li class="nav-item <?= ($currentPage == 'user-list.php') ? 'active' : '' ?>">
    <a class="nav-link" href="user-list"><i class="fas fa-users-cog"></i><span> Kullanıcılar </span></a>
  </li>
  <li class="nav-item <?= ($currentPage == 'businesses.php') ? 'active' : '' ?>">
    <a class="nav-link" href="businesses"><i class="fas fa-store"></i><span> İşletmeler</span></a>
  </li>
  <li class="nav-item <?= ($currentPage == 'categories.php') ? 'active' : '' ?>">
    <a class="nav-link" href="categories"><i class="fas fa-tags"></i><span> Kategoriler</span></a>
  </li>
  <li class="nav-item <?= ($currentPage == 'reviews.php') ? 'active' : '' ?>">
    <a class="nav-link" href="reviews"><i class="fas fa-comments"></i><span> İncelemeler</span></a>
  </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBlog"
         aria-expanded="false" aria-controls="collapseBlog">
        <i class="fas fa-blog"></i>
        <span>Blog Yönetimi</span>
      </a>

      <div id="collapseBlog"
           class="collapse <?= ($currentPage == 'blog-management' || $currentPage == 'blog-management-business') ? 'show' : '' ?>"
           aria-labelledby="headingBlog"
           data-parent="#accordionSidebar"
           style="background:transparent !important;">

        <div class="py-2 collapse-inner rounded"
             style="background:transparent !important;">

          <!-- Normal Blog Yönetimi -->
          <a class="collapse-item <?= ($currentPage == 'blog-management') ? 'active' : '' ?>"
             href="blog-management"
             style="color:#d1d3e2 !important; background:transparent !important;">
            Blog Yönetimi
          </a>

          <!-- İşletme Blog Yönetimi -->
          <a class="collapse-item <?= ($currentPage == 'blog-management-business') ? 'active' : '' ?>"
             href="blog-management-business"
             style="color:#d1d3e2 !important; background:transparent !important;">
            İşletme Blog Yönetimi
          </a>

        </div>
      </div>
    </li>

    <li class="nav-item <?= ($currentPage == 'seo-settings.php') ? 'active' : '' ?>">
  <a class="nav-link" href="seo-settings">
    <i class="fas fa-search"></i>
    <span> SEO Ayarları</span>
  </a>
</li>

  
<hr class="sidebar-divider my-0">
  
  <li class="nav-item <?= ($currentPage == 'package-list.php') ? 'active' : '' ?>">
    <a class="nav-link" href="package-list"><i class="fas fa-box-open"></i><span> Paket Yönetimi</span></a>
  </li>
  <li class="nav-item <?= ($currentPage == 'subscriptions.php') ? 'active' : '' ?>">
    <a class="nav-link" href="subscriptions"><i class="fas fa-file-contract"></i><span> Abonelikler</span></a>
  </li>
  <li class="nav-item <?= ($currentPage == 'integrations.php') ? 'active' : '' ?>">
    <a class="nav-link" href="integrations"><i class="fas fa-plug"></i><span> Entegrasyonlar</span></a>
  </li>
  
<hr class="sidebar-divider my-0">

  <li class="nav-item <?= ($currentPage == 'reports.php') ? 'active' : '' ?>">
    <a class="nav-link" href="reports"><i class="fas fa-chart-bar"></i><span> Raporlar</span></a>
  </li>
  <li class="nav-item <?= ($currentPage == 'general-settings.php') ? 'active' : '' ?>">
    <a class="nav-link" href="general-settings"><i class="fas fa-cogs"></i><span> Genel Ayarlar</span></a>
  </li>

  <hr class="sidebar-divider d-none d-md-block">

  <li class="nav-item">
    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i><span> Çıkış</span></a>
  </li>
</ul>
<!-- End Sidebar -->
