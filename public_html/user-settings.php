<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

// Kullanıcı giriş yapmamışsa veya rol hatalıysa login sayfasına yönlendir
if (!isset($_SESSION['user_id']) || 
   (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'user')) {
  header('Location: login');
  exit;
}

// Kullanıcı bilgilerini çek 
$stmt = $pdo->prepare("SELECT name, surname FROM users WHERE id = ? AND status = 'active'");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login");
    exit;
}

$name = htmlspecialchars($user['name']);
$surname = htmlspecialchars($user['surname']);

// Bildirim sayısı
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND status = 'unread'");
$stmt->execute([$_SESSION['user_id']]);
$unreadNotificationCount = (int) $stmt->fetchColumn();

// Bildirim kontrolü (ikinci kez hesaplama)
$unreadNotificationCount = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND status = 'unread'");
$stmt->execute([$_SESSION['user_id']]);
$unreadNotificationCount = (int)$stmt->fetchColumn();


// ---------------------------------------------
// Bildirim tercihleri
// ---------------------------------------------
$stmt = $pdo->prepare("SELECT marketing, recommend, updates, features, feedback 
                       FROM users 
                       WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$prefs = $stmt->fetch(PDO::FETCH_ASSOC);

// Değer yoksa sıfırla
$prefs = $prefs ?: [
  "marketing" => 0,
  "recommend" => 0,
  "updates" => 0,
  "features" => 0,
  "feedback" => 0
];
?>


<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Puandeks- <?php echo htmlspecialchars($name . ' ' . $surname); ?></title>


  <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/vendors.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
    <link href="css/contact.css" rel="stylesheet">


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


</head>

<body>

<?php include 'header-main.php'; ?>
      
<!-- USER MENU -->
<div id="userMenuContainer" style="background-color:#f9f7f3; padding:20px 20px; text-align:center; margin-top:70px;">

<!-- Masaüstü Menü -->
<nav class="user-menu-desktop"
     style="display:flex; justify-content:center; gap:40px; flex-wrap:wrap;
            margin-bottom:5px; margin-top:10px;">

  <a href="/user" class="menu-item"
     style="text-decoration:none; color:#333; font-weight:600;">
    <i class="fa-solid fa-user me-1"></i> Profilim
  </a>

  <a href="user-reviews" class="menu-item"
     style="text-decoration:none; color:#333; font-weight:600;">
    <i class="fa-solid fa-star me-1"></i> İncelemelerim
  </a>

<a href="/user-notifications" class="menu-item"
   style="text-decoration:none; color:#333; font-weight:600; position:relative;">
  <i class="fa-solid fa-bell me-1"></i> Bildirimlerim
  <?php if ($unreadNotificationCount > 0): ?>
    <span style="
      position:absolute;
      top:-5px;
      right:-8px;
      width:10px;
      height:10px;
      background-color:red;
      border-radius:50%;
      display:inline-block;
    "></span>
  <?php endif; ?>
</a>



  <a href="/user-settings" class="menu-item active"
     style="text-decoration:none; color:#1b7d2f; font-weight:600;">
    <i class="fa-solid fa-gear me-1"></i> Ayarlar
  </a>

  <a href="logout.php" class="menu-item logout"
     style="text-decoration:none; color:#D5332E; font-weight:600;">
    <i class="fa-solid fa-right-from-bracket me-1"></i> Çıkış
  </a>
</nav>


<!-- Mobil Accordion Men -->
<div class="user-menu-mobile"
     style="display:none; max-width:280px; margin:0 auto; border:1px solid #ccc;
            border-radius:8px; background:#fff; overflow:hidden;">

  <!-- Başlık (menü başlığı) -->
  <div id="mobileMenuHeader"
       style="padding:12px 15px; display:flex; align-items:center; justify-content:space-between;
              cursor:pointer; font-weight:600; color:#1b7d2f; background:#f9f9f9;">
    <span><i class="fa-solid fa-bars me-2"></i> Menü</span>
    <i class="fa-solid fa-chevron-down" style="transition:transform 0.3s;"></i>
  </div>

  <!-- Mobil Menü ierikleri -->
  <div id="mobileMenuContent"
       style="max-height:0; overflow:hidden; transition:max-height 0.4s ease;
              border-top:1px solid #ddd;">

    <a href="/user"
       style="display:block; padding:12px 18px; text-decoration:none; text-align:left; color:#333; border-bottom:1px solid #eee;">
      <i class="fa-solid fa-user me-1"></i> Profilim
    </a>
    <a href="/user-reviews"
       style="display:block; padding:12px 18px; text-decoration:none; text-align:left; color:#333; border-bottom:1px solid #eee;">
      <i class="fa-solid fa-star me-1"></i> İncelemelerim
    </a>
<a href="/user-notifications"
   style="display:block; padding:12px 18px; text-decoration:none; text-align:left; color:#333; border-bottom:1px solid #eee; position:relative;">
  <i class="fa-solid fa-bell me-1"></i> Bildirimlerim
  <?php if ($unreadNotificationCount > 0): ?>
    <span style="
      position:absolute;
      top:10px;
      right:18px;
      width:10px;
      height:10px;
      background-color:red;
      border-radius:50%;
      display:inline-block;
    "></span>
  <?php endif; ?>
</a>

    <a href="/user-settings"
       style="display:block; padding:12px 18px; text-decoration:none; text-align:left; color:#1b7d2f; border-bottom:1px solid #eee;">
      <i class="fa-solid fa-gear me-1"></i> Ayarlar
    </a>
    <a href="logout"
       style="display:block; padding:12px 18px; text-decoration:none; text-align:left; color:#D5332E;">
      <i class="fa-solid fa-right-from-bracket me-1"></i> Çıkış
    </a>

  </div>
</div>




</div>
<!-- /USER MENU -->


<main style="background-color:#f9f7f3; padding:60px 0; min-height:80vh; font-family:'Montserrat',sans-serif;">

  <div style="max-width:720px; margin:0 auto; background:#fff; border-radius:14px; box-shadow:0 4px 14px rgba(0,0,0,0.05); padding:40px;">

    <!-- Şifre Değiştir -->
<section style="margin-bottom:50px;">
  <h3 style="font-weight:700; color:#1c1c1c; font-size:22px; margin-bottom:20px;">
    <i class="fa-solid fa-lock" style="color:#1b7d2f; margin-right:8px;"></i> Şifre Değiştir
  </h3>

  <form action="/api/change-user-password.php" method="POST" id="changePassForm">

    <!-- Eski Şifre -->
    <label style="font-weight:600; font-size:15px; display:block; margin-bottom:8px;">Eski Şifre</label>
    <div style="position:relative; margin-bottom:20px;">
      <input type="password" name="old_pass" id="old_pass"
             placeholder="•••••"
             style="width:100%; padding:12px 40px 12px 12px; border:1px solid #ddd; border-radius:8px; font-size:15px;">
      <i class="fa-solid fa-eye" id="toggleOldPass"
         style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; color:#888;"></i>
    </div>

    <!-- Yeni ifre -->
    <label style="font-weight:600; font-size:15px; display:block; margin-bottom:8px;">Yeni Şifre</label>
    <div style="position:relative; margin-bottom:20px;">
      <input type="password" name="new_pass" id="new_pass"
             placeholder="Yeni şifre"
             style="width:100%; padding:12px 40px 12px 12px; border:1px solid #ddd; border-radius:8px; font-size:15px;">
      <i class="fa-solid fa-eye" id="toggleNewPass"
         style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; color:#888;"></i>
    </div>

    <button type="submit"
      style="background:#1b7d2f; color:#fff; border:none; border-radius:8px; padding:12px 24px; font-weight:600; cursor:pointer;">
      Değiştir
    </button>
  </form>
</section>


<!-- Bildirim Tercihleri -->
<section id="notification-preferences" style="margin-bottom:50px;">
  <h3 style="font-weight:700; color:#1c1c1c; font-size:22px; margin-bottom:20px;">
    <i class="fa-solid fa-bell" style="color:#1b7d2f; margin-right:8px;"></i> Bildirim Tercihleri
  </h3>
  <p style="font-size:15px; color:#444; margin-bottom:25px;">
    Hangi tür e-postaları almak istediğinizi seçin. <br>
    <em style="color:#777;">Hesabınızla ilgili önemli e-postalar her zaman etkindir.</em>
  </p>

  <!-- Pazarlama -->
  <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
    <div>
      <strong>Pazarlama</strong>
      <p style="margin:4px 0 0; font-size:14px; color:#666;">
        Bunlar için açılış oranlarını takip ediyoruz. 
        <a href="privacy-policy" style="color:#1b7d2f;">Daha fazla bilgi edinin</a>
      </p>
    </div>

    <label style="position:relative; width:46px; height:24px;">
      <input 
        type="checkbox"
        <?= !empty($prefs['marketing']) ? 'checked' : '' ?>
        style="opacity:0; width:0; height:0;">
      <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0;
                   background-color:#ccc; border-radius:24px; transition:0.3s;"></span>
      <span style="position:absolute; height:18px; width:18px; left:3px; bottom:3px;
                   background-color:white; border-radius:50%; transition:0.3s;"></span>
    </label>
  </div>

  <!-- Kişiselleştirilmiş öneriler -->
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div>
      <strong>Kişiselleştirilmiş öneriler</strong>
      <p style="margin:4px 0 0; font-size:14px; color:#666;">Tercihlerinize göre içerik sunar.</p>
    </div>

    <label style="position:relative; width:46px; height:24px;">
      <input 
        type="checkbox"
        <?= !empty($prefs['recommend']) ? 'checked' : '' ?>
        style="opacity:0; width:0; height:0;">
      <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0;
                   background-color:#ccc; border-radius:24px; transition:0.3s;"></span>
      <span style="position:absolute; height:18px; width:18px; left:3px; bottom:3px;
                   background-color:white; border-radius:50%; transition:0.3s;"></span>
    </label>
  </div>

  <!-- Son Gelimeler -->
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div>
      <strong>Son Gelişmeler</strong>
      <p style="margin:4px 0 0; font-size:14px; color:#666;">Haber bülteni, duyurular, ipuçları.</p>
    </div>

    <label style="position:relative; width:46px; height:24px;">
      <input 
        type="checkbox"
        <?= !empty($prefs['updates']) ? 'checked' : '' ?>
        style="opacity:0; width:0; height:0;">
      <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0;
                   background-color:#ccc; border-radius:24px; transition:0.3s;"></span>
      <span style="position:absolute; height:18px; width:18px; left:3px; bottom:3px;
                   background-color:white; border-radius:50%; transition:0.3s;"></span>
    </label>
  </div>

  <!-- zellik güncellemeleri -->
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div>
      <strong>Özellik güncellemeleri</strong>
      <p style="margin:4px 0 0; font-size:14px; color:#666;">Yeni özellikler veya değişiklikler.</p>
    </div>

    <label style="position:relative; width:46px; height:24px;">
      <input 
        type="checkbox"
        <?= !empty($prefs['features']) ? 'checked' : '' ?>
        style="opacity:0; width:0; height:0;">
      <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0;
                   background-color:#ccc; border-radius:24px; transition:0.3s;"></span>
      <span style="position:absolute; height:18px; width:18px; left:3px; bottom:3px;
                   background-color:white; border-radius:50%; transition:0.3s;"></span>
    </label>
  </div>

  <!-- Performans geri bildirimi -->
  <div style="display:flex; align-items:center; justify-content:space-between;">
    <div>
      <strong>Performans geri bildirimi</strong>
      <p style="margin:4px 0 0; font-size:14px; color:#666;">Hesabınızın ilerleyişiyle ilgili özetler.</p>
    </div>

    <label style="position:relative; width:46px; height:24px; cursor:pointer; display:inline-block;">
      <input 
        type="checkbox"
        <?= !empty($prefs['feedback']) ? 'checked' : '' ?>
        style="opacity:0; width:0; height:0;"
        onchange="toggleSwitch(this)">
      <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0;
                   background-color:#ccc; border-radius:24px; transition:0.3s;"></span>
      <span style="position:absolute; height:18px; width:18px; left:3px; bottom:3px;
                   background-color:white; border-radius:50%; transition:0.3s;"></span>
    </label>
  </div>

  <button id="savePreferencesBtn"
    style="margin-top:30px; padding:12px 26px; border:none; border-radius:8px; background:#1b7d2f; color:#fff; font-weight:600; cursor:pointer;">
    Tercihleri Kaydet
  </button>
</section>
<!-- Bildirim Tercihleri -->

    

    <!-- Üyelik İptali -->
    <section style="border-top:1px solid #eee; padding-top:30px;">
      <h3 style="font-weight:700; color:#1c1c1c; font-size:22px; margin-bottom:20px;">
        <i class="fa-solid fa-user-slash" style="color:#d93025; margin-right:8px;"></i> Üyeliğimi İptal Etmek İstiyorum
      </h3>
      <p style="font-size:15px; color:#444; margin-bottom:15px;">
       Üyeliğinizi iptal etmeniz halinde hesabınıza tekrar erişemezsiniz. İncelemeleriniz ve profil bilgileriniz yayından kaldırılır. Yasal yükümlülükler kapsamında bazı veriler mevzuata uygun süre boyunca saklanabilir.
      </p>

      

      <button onclick="cancelMembership()"
              style="background:#d93025; color:#fff; padding:12px 26px; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
        Üyeliğimi İptal Et
      </button>
    </section>

  </div>

</main>


    <?php include('footer-main.php'); ?>

	</div>

	<script src="js/common_scripts.js"></script>
	<script src="js/functions.js"></script>
	<script src="assets/validate.js"></script>
	<script src="js/tabs.js"></script>
	<script>new CBPFWTabs(document.getElementById('tabs'));</script>


<!-- Şifre Degiştir -->
<script>
document.querySelector('form[action="/api/change-user-password.php"]').addEventListener('submit', function(e) {
  e.preventDefault();

  const form = e.target;
  const oldPass = form.old_pass.value;
  const newPass = form.new_pass.value;

  fetch("/api/change-user-password.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `old_pass=${encodeURIComponent(oldPass)}&new_pass=${encodeURIComponent(newPass)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      alert("Şifreniz başarıyla değiştirildi.");
      form.reset();
    } else {
      alert(data.message);
    }
  });
});
</script>

<!-- Şifre Gör/Gizle -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const toggleOld = document.getElementById("toggleOldPass");
  const toggleNew = document.getElementById("toggleNewPass");
  const oldInput = document.getElementById("old_pass");
  const newInput = document.getElementById("new_pass");

  const toggleVisibility = (icon, input) => {
    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
      icon.style.color = "#1b7d2f";
    } else {
      input.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
      icon.style.color = "#888";
    }
  };

  toggleOld?.addEventListener("click", () => toggleVisibility(toggleOld, oldInput));
  toggleNew?.addEventListener("click", () => toggleVisibility(toggleNew, newInput));
});
</script>
<!-- ifre Değiştir -->
  

<!-- Bildirim Tercihleri -->
<script>
function toggleSwitch(input) {
  const bg = input.nextElementSibling;       // arka plan span
  const dot = input.nextElementSibling.nextElementSibling; // top noktası

  if (input.checked) {
    bg.style.backgroundColor = "#1b7d2f";
    dot.style.transform = "translateX(22px)";
  } else {
    bg.style.backgroundColor = "#ccc";
    dot.style.transform = "translateX(0)";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const switches = document.querySelectorAll('#notification-preferences input[type="checkbox"]');
  const saveBtn = document.getElementById("savePreferencesBtn");

  if (!saveBtn) return;

  // Sayfa yklenince mevcut durumlara göre renklendir
  switches.forEach(sw => toggleSwitch(sw));

  // Her değişiklikte görünüm ve buton güncelle
  switches.forEach(sw => {
    sw.addEventListener("change", () => {
      toggleSwitch(sw);
      saveBtn.style.display = "inline-block";
    });
  });

  // Kaydet butonu işlemi
  saveBtn.addEventListener("click", () => {
    const checkboxes = document.querySelectorAll('#notification-preferences input[type="checkbox"]');
    const data = {
      marketing: checkboxes[0].checked ? 1 : 0,
      recommend: checkboxes[1].checked ? 1 : 0,
      updates: checkboxes[2].checked ? 1 : 0,
      features: checkboxes[3].checked ? 1 : 0,
      feedback: checkboxes[4].checked ? 1 : 0
    };

    fetch("/api/update-user-notifications.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include", 
      body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        alert("Tercihleriniz kaydedildi.");
        saveBtn.style.display = "none";
      } else {
        alert("Kaydetme başarısız.");
      }
    })
    .catch(() => alert("Bağlantı hatası, lütfen tekrar deneyin."));
  });
});
</script>
<!-- Bildirim Tercihleri -->


<!-- Üyelik İptal -->
<script>
function cancelMembership() {

  const confirmCancel = confirm(
    "Üyeliğiniz kalıcı olarak iptal edilecektir. Hesabınıza tekrar erişemezsiniz. Devam etmek istiyor musunuz?"
  );

  if (!confirmCancel) return;

  fetch("/api/cancel-user-membership-request.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" }
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      alert("Hesabınız iptal edilmiştir.");
      window.location.href = "/logout";
    } else {
      alert("İşlem başarısız.");
    }
  });
}
</script>
<!-- Üyelik İptal -->

<script>
document.addEventListener("DOMContentLoaded", function () {
  const header = document.getElementById("mobileMenuHeader");
  const content = document.getElementById("mobileMenuContent");
  const icon = header.querySelector("i.fa-chevron-down");

  header.addEventListener("click", () => {
    if (content.style.maxHeight && content.style.maxHeight !== "0px") {
      content.style.maxHeight = "0";
      icon.style.transform = "rotate(0deg)";
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
      icon.style.transform = "rotate(180deg)";
    }
  });
});
</script>



</body>
</html>