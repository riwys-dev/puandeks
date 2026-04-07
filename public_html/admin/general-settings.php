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
    <title>Puandeks Admin - Ayarlar</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png">
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 

 <style> 
   .password-wrapper {position: relative;}
   .password-wrapper input {padding-right: 40px !important;}
   .password-wrapper i {position: absolute;right: 12px;top: 50%;transform: translateY(-50%);cursor: pointer;color: #888;font-size: 16px;line-height: 1;z-index: 10;}
  </style>
  
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

<!-- Account Settings Page Content -->
<div class="container-fluid" style="max-width: 720px; margin: 0 auto;">
  <h1 class="h3 mb-4 text-gray-800">Ayarlar</h1>

  <form id="settingsForm" method="POST" enctype="multipart/form-data">
    <div style="background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
      <h5 style="font-weight: bold; margin-bottom: 20px;">Profil Bilgileri</h5>
      
      <!-- Avatar -->
    <div style="margin-bottom: 16px;">
      <label>Profil Fotoğrafı</label><br>

      <img 
        id="adminAvatarPreview" 
        src="" 
        alt="Profil Foto"
        style="width: 100px; border-radius: 50%; margin-bottom: 8px; display:none;"
      >

      <input type="file" name="admin_avatar" accept="image/png, image/jpeg">
    </div>

      <!-- Full Name -->
      <div style="margin-bottom: 16px;">
        <label>Ad Soyad</label>
        <input type="text" class="form-control" name="admin_name" placeholder="Ad Soyad">
      </div>
      
      <!-- Role -->
      <div style="margin-bottom: 16px;">
        <label>Yetki</label>
        <input type="text" class="form-control" value="SuperAdmin" readonly>
      </div>

      <!-- Email -->
      <div style="margin-bottom: 16px;">
        <label>Giriş E-Posta</label>
        <input type="email" class="form-control" name="admin_email" value="" readonly>
      </div>

    <!-- Password Update -->
      <div style="margin-bottom: 16px;">
        <label>Şifre Güncelle</label>

        <!-- Eski Şifre -->
        <div class="password-wrapper">
          <input type="password" class="form-control" name="old_password" placeholder="Eski Şifre">
          <i class="fas fa-eye toggle-password" data-target="old_password"></i>
        </div>

        <!-- Yeni Şifre -->
        <div class="password-wrapper" style="margin-top: 10px;">
          <input type="password" class="form-control" name="new_password" placeholder="Yeni Şifre">
          <i class="fas fa-eye toggle-password" data-target="new_password"></i>
        </div>

        <small style="color:#777; font-size:12px; display:block; margin-top:6px;">
          En az 8 karakter, büyük harf, küçük harf, rakam ve özel karakter içermelidir.
        </small>
      </div>
   <!-- Password Update -->


      <button class="btn btn-primary">Kaydet</button>
    </div>
  </form>


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



<script>
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("settingsForm");
  const nameInput = document.querySelector('[name="admin_name"]');
  const emailInput = document.querySelector('[name="admin_email"]');
  const avatarImg = document.getElementById("adminAvatarPreview");
  const avatarInput = document.querySelector('[name="admin_avatar"]');

  // Admin bilgilerini çek
  fetch("api/get-admin-user.php")
    .then(res => res.json())
    .then(data => {
      if (data.success) {
      const admin = data.data;

      nameInput.value = admin.full_name;
      emailInput.value = admin.email;

      avatarImg.src = (admin.avatar_url ? "/" + admin.avatar_url : "img/placeholder/admin-user.png");
      avatarImg.style.display = "block";
    }
    });

  // FORM SUBMIT (şifre konatrol + update)
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    const newPass = formData.get("new_password");

    // Eğer yeni şifre girildiyse zorunlu kontrol
    if (newPass) {
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        if (!strongRegex.test(newPass)) {
            alert("Yeni şifre güçlü değil. Lütfen kriterlere uygun bir şifre girin.");
            return;
        }
    }

    // API'ye gönder
    fetch("api/update-admin-user.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Profil güncellendi.");
        } else {
            alert("Hata: " + (data.error || "Güncelleme başarısız."));
        }
    });
  });

  // Avatar önizleme
  avatarInput.addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = e => avatarImg.src = e.target.result;
      reader.readAsDataURL(file);
    }
  });

  // ŞİFRE GÖSTER / GİZLE (Göz ikonları)
  document.querySelectorAll(".toggle-password").forEach(icon => {
      icon.addEventListener("click", function () {

          const targetName = this.getAttribute("data-target");
          const input = document.querySelector(`input[name="${targetName}"]`);

          if (!input) return;

          if (input.type === "password") {
              input.type = "text";
              this.classList.remove("fa-eye");
              this.classList.add("fa-eye-slash");
          } else {
              input.type = "password";
              this.classList.remove("fa-eye-slash");
              this.classList.add("fa-eye");
          }
      });
  });

});
</script>



</body>
</html>
