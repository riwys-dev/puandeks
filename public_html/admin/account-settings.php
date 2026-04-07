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
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #1C1C1C !important; background-image: none !important;">

    <!-- Logo -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index">
        <div class="sidebar-brand-icon"><img src="img/logo.svg" height="26"></div>
        <div class="sidebar-brand-text mx-3"><img src="img/admin-logo.svg" height="16px"></div>
    </a>

    <hr class="sidebar-divider my-0">
    <li class="nav-item"><a class="nav-link" href="index"><span> Ana Sayfa</span></a></li>

    <!-- Genel Yönetim -->
    <li class="nav-item"><a class="nav-link" href="user-list"><i class="fas fa-users-cog"></i><span> Kullanıcı yönetimi</span></a></li>

    <li class="nav-item"><a class="nav-link" href="businesses"><i class="fas fa-store"></i><span> İşletmeler</span></a></li>


    <!-- İçerik -->
    <li class="nav-item"><a class="nav-link" href="reviews"><i class="fas fa-comments"></i><span> İncelemeler</span></a></li>

    <!-- Paketler -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePackages" aria-expanded="true">
            <i class="fas fa-box-open"></i><span> Paket Yönetimi</span>
        </a>
        <div id="collapsePackages" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="package-list">Paket Listesi</a>
                <a class="collapse-item" href="package-create">Yeni Paket</a>
            </div>
        </div>
    </li>

   <!-- Blog yönetimi -->
      <li class="nav-item">
      <a class="nav-link" href="blog-management"><i class="fas fa-blog"></i><span>Blog Yönetimi</span></a></li>


    <li class="nav-item"><a class="nav-link" href="subscriptions"><i class="fas fa-file-contract"></i><span> Abonelikler</span></a></li>

    <!-- Ayarlar -->
    <li class="nav-item"><a class="nav-link" href="general-settings"><i class="fas fa-cogs"></i><span> Genel Ayarlar</span></a></li>
    <li class="nav-item"><a class="nav-link" href="integrations"><i class="fas fa-plug"></i><span> Entegrasyonlar</span></a></li>

    <!-- Raporlar -->
    <li class="nav-item"><a class="nav-link" href="reports"><i class="fas fa-chart-bar"></i><span> Raporlar</span></a></li>

    <!-- Güvenlik -->
    <li class="nav-item"><a class="nav-link" href="security"><i class="fas fa-user-shield"></i><span> Güvenlik</span></a></li>

    <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item"><a class="nav-link" href="../admin-login"><i class="fas fa-sign-out-alt"></i><span> Çıkış</span></a></li>
</ul>
<!-- End Sidebar -->


    <!-- Content -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link" href="admin-notification">
                            <i class="fas fa-bell fa-fw"></i>
                            <span class="badge badge-danger badge-counter">3</span>
                        </a>
                    </li>
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Yetkili Adı</span>
                            <img class="img-profile rounded-circle" src="img/placeholder/admin-user.png">
                        </a>
                    </li>
                </ul>
            </nav>

<!-- =================================== -->

<!-- Account Settings Page Content -->
<div class="container-fluid" style="max-width: 720px; margin: 0 auto;">
  <h1 class="h3 mb-4 text-gray-800">Hesap Ayarları</h1>

  <!-- Profil Bilgileri -->
  <div style="background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
    <h5 style="font-weight: bold; margin-bottom: 20px;">Profil Bilgileri</h5>

    <div style="margin-bottom: 16px;">
      <label>Ad Soyad</label>
      <input type="text" class="form-control" placeholder="Adınızı girin">
      <!-- Backend: Kullanıcı adı dinamik olarak getirilip güncellenebilir -->
    </div>

    <div style="margin-bottom: 16px;">
      <label>Profil Fotoğrafı</label><br>
      <img src="img/placeholder/admin-user.png" alt="Profil Foto" style="width: 100px; border-radius: 50%; margin-bottom: 8px;">
      <input type="file" accept="image/png, image/jpeg">
      <!-- Backend: Profil resmi yüklenir ve kullanıcı verisine kaydedilir -->
    </div>

    <div style="margin-bottom: 16px;">
      <label>Giriş E-Posta</label>
      <input type="email" class="form-control" placeholder="ornek@eposta.com">
      <!-- Backend: Email adresi güncellenebilir -->
    </div>

    <div style="margin-bottom: 16px;">
      <label>Şifre Güncelle</label>
      <input type="password" class="form-control" placeholder="Eski Şifre">
      <input type="password" class="form-control mt-2" placeholder="Yeni Şifre">
      <!-- Backend: Şifre doğrulama ve güncelleme yapılır -->
    </div>

    <div style="margin-bottom: 24px;">
      <label>Tema Tercihi</label>
      <select class="form-control">
        <option>Açık Tema</option>
        <option>Koyu Tema</option>
      </select>
      <!-- Dummy olarak gösterilir, backend'e bağlanmaz -->
    </div>

    <button class="btn btn-primary">Kaydet</button>
  </div>

  <!-- Yalnızca SuperAdmin İçin Görünen Alan -->
  <div style="background: #f8f9fc; border-radius: 8px; padding: 24px; margin-top: 40px;">
    <h5 style="font-weight: bold; margin-bottom: 20px;">Yönetici Yetkileri (Sadece SuperAdmin)</h5>

    <div style="margin-bottom: 16px;">
      <label>Yeni Admin Ekle</label>
      <input type="email" class="form-control" placeholder="admin@site.com">
      <button class="btn btn-success mt-2">Ekle</button>
      <!-- Backend: Yeni kullanıcı admin rolü ile eklenir -->
    </div>

    <div style="margin-bottom: 16px;">
      <label>Rol Ataması</label>
      <select class="form-control">
        <option>Super Admin</option>
        <option>Admin</option>
        <option>Editor</option>
        <option>Sınırlı Yetkili</option>
      </select>
      <!-- Backend: Kullanıcı rolleri burada atanabilir -->
    </div>

    <div style="margin-bottom: 16px;">
      <label>İki Adımlı Giriş (2FA)</label><br>
      <input type="checkbox" id="2faToggle"> <label for="2faToggle">Aktif</label>
      <!-- Backend: 2FA aktif/pasif durumu saklanır -->
    </div>

    <div style="margin-bottom: 16px;">
      <label>Giriş Kayıtları</label>
      <ul style="list-style: disc; padding-left: 20px;">
        <li>15.04.2025 - 13:45 - Chrome - İstanbul</li>
        <li>14.04.2025 - 09:10 - Mobile - İzmir</li>
      </ul>
      <!-- Backend: Login log'ları son 7 gün için gösterilir -->
    </div>
  </div>
</div>

<!-- Backend Notları:
- Ad, soyad, email, şifre gibi bilgiler kullanıcı tablosuna bağlıdır.
- Rol ataması sadece SuperAdmin erişimiyle yapılabilir.
- 2FA backend'e entegre edilecek (dummy toggle şimdilik görsel).
- Login logları sistem tablosundan çekilecektir.
-->

  
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




</body>
</html>
