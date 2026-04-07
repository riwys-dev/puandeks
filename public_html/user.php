<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('/home/puandeks.com/backend/config.php');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: https://puandeks.com");
    exit();
}

// ---------------- USER DATA ----------------
$stmt = $pdo->prepare("
    SELECT name, surname, country, profile_image, phone, phone_prefix, phone_verified 
    FROM users 
    WHERE id = ? AND status = 'active'
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: https://puandeks.com/login");
    exit();
}

$name        = htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
$surname     = htmlspecialchars($user['surname'], ENT_QUOTES, 'UTF-8');
$countryCode = $user['country'] ?? '';
$profileImage = trim($user['profile_image'] ?? '');
$phone         = $user['phone'] ?? '';
$phonePrefix   = $user['phone_prefix'] ?? '90';
$phoneVerified = (int)($user['phone_verified'] ?? 0);

// ---------------- AVATAR MANTIĞI (YENİ) ----------------
// HTML tarafı bunu kullanacak
$hasImage    = false;
$imageUrl    = '';
$firstLetter = mb_strtoupper(mb_substr(trim($name), 0, 1));

if ($profileImage !== '') {
    if (strpos($profileImage, 'http') === 0) {
        $imageUrl = $profileImage;
        $hasImage = true;
    } elseif (strpos($profileImage, 'uploads/') === 0) {
        $imageUrl = "https://puandeks.com/" . $profileImage;
        $hasImage = true;
    } else {
        $imageUrl = "https://puandeks.com/uploads/users/" . $profileImage;
        $hasImage = true;
    }
}

// ---------------- YORUM SAYISI ----------------
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND status = 1");
$stmt->execute([$_SESSION['user_id']]);
$commentCount = (int)$stmt->fetchColumn();

// ---------------- ÜLKE ADI ----------------
$countryName = '';
if (!empty($countryCode)) {
    $stmt = $pdo->prepare("SELECT name FROM countries WHERE code = ?");
    $stmt->execute([$countryCode]);
    $countryName = $stmt->fetchColumn() ?: $countryCode;
}

// ---------------- BİLDİRİM SAYISI ----------------
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND status = 'unread'");
$stmt->execute([$_SESSION['user_id']]);
$unreadNotificationCount = (int)$stmt->fetchColumn();
?>


<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Puandeks - <?php echo htmlspecialchars($name . ' ' . $surname); ?></title>


  <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/vendors.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
    <link href="css/contact.css" rel="stylesheet">
    
    

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Country select mobile -->
<style>
@media (max-width: 768px) {
  #customDropdown {
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
  }

  #countryInput,
  #saveCountryBtn {
    width: 100% !important;
    display: block !important;
    box-sizing: border-box !important;
  }
}
</style>




</head>
  
  
<body>

<?php include 'header-main.php'; ?>
      
<!-- USER MENU -->
<div id="userMenuContainer" style="background-color:#f9f7f3; padding:20px 20px; text-align:center; margin-top:70px;">

<!-- Masaüstü Menü -->
<nav class="user-menu-desktop"
     style="display:flex; justify-content:center; gap:40px; flex-wrap:wrap;
            margin-bottom:5px; margin-top:10px;">

  <a href="/user" class="menu-item active"
     style="text-decoration:none; color:#1b7d2f; font-weight:600;">
    <i class="fa-solid fa-user me-1"></i> Profilim
  </a>

  <a href="/user-reviews" class="menu-item"
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


  <a href="/user-settings" class="menu-item"
     style="text-decoration:none; color:#333; font-weight:600;">
    <i class="fa-solid fa-gear me-1"></i> Ayarlar
  </a>

  <a href="logout.php" class="menu-item logout"
     style="text-decoration:none; color:#D5332E; font-weight:600;">
    <i class="fa-solid fa-right-from-bracket me-1"></i> Çıkış
  </a>
</nav>


<!-- Mobil Accordion Menü -->
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

  <!-- Menu içerikleri (açılır kapanır alan) -->
  <div id="mobileMenuContent"
       style="max-height:0; overflow:hidden; transition:max-height 0.4s ease;
              border-top:1px solid #ddd;">

    <a href="/user"
       style="display:block; padding:12px 18px; text-decoration:none; text-align:left; color:#1b7d2f; border-bottom:1px solid #eee;">
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
       style="display:block; padding:12px 18px; text-decoration:none; text-align:left; color:#333; border-bottom:1px solid #eee;">
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


<!-- Avatar -->
<div style="display:flex;justify-content:center;align-items:center;flex-direction:column;gap:20px;min-height:80vh;background:#ffffff;padding:20px;">

  <!-- Profil Fotoğrafı + Ad Soyad (readonly) -->
  <div style="width:100%;max-width:600px;background:#fff;border:1px solid #dcdcdc;border-radius:16px;padding:24px;text-align:center;">
    <h4 style="font-size:18px;font-weight:600;margin-bottom:20px;">Profil Bilgileri</h4>

      <!-- Avatar -->
      <div style="position:relative;display:inline-block;">

        <?php if ($hasImage): ?>
            <img id="previewPhoto"
                src="<?= htmlspecialchars($imageUrl) ?>"
                alt="Profil Fotoğrafı"
                style="width:100px;height:100px;border-radius:50%;object-fit:cover;
                        border:2px solid #dcdcdc;cursor:pointer;">
        <?php else: ?>
            <div id="previewPhoto"
                style="
                    width:100px;
                    height:100px;
                    border-radius:50%;
                    background:#05462F;
                    color:#ffffff;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-weight:bold;
                    font-size:40px;
                    border:2px solid #dcdcdc;
                    cursor:pointer;
                ">
                <?= $firstLetter ?>
            </div>
        <?php endif; ?>


        <span id="addPhotoBtn"
              style="position:absolute;bottom:4px;right:4px;
                     background:#1b7d2f;color:#fff;font-size:16px;
                     width:22px;height:22px;border-radius:50%;
                     display:flex;align-items:center;justify-content:center;
                     cursor:pointer;line-height:1;">+</span>

        <input type="file" id="profilePhoto" accept="image/*" style="display:none;">
      </div>
      <!-- Avatar -->

      <!-- Ad Soyad ve İnceleme sayısı -->
      <div style="display:flex;flex-direction:column;align-items:center;gap:8px;margin-top:12px;">

        <!-- Ad Soyad -->
        <div style="width:100%;max-width:300px;
                    padding:12px 14px;
                    background:#fff;
                    color:#111;
                    text-align:center;
                    font-weight:700;
                    font-size:16px;">
          <?php echo htmlspecialchars($name . ' ' . $surname); ?>
        </div>

        <!-- İnceleme Sayısı -->
        <div style="font-size:14px;color:#666;margin-top:4px;">
          (<?php echo $commentCount; ?> İnceleme)
        </div>

      </div>
      <!-- /Ad Soyad ve İnceleme sayısı -->
</div>


    <!-- Ulke Seçimi  -->
    <div style="width:100%;max-width:600px;background:#fff;border:1px solid #dcdcdc;border-radius:16px;padding:24px;margin-bottom:10px;">
      <h4 style="font-size:18px;font-weight:600;margin-bottom:10px;">Ülke Seçimi</h4>

      <div style="display:flex;flex-wrap:wrap;gap:10px;">
        <input type="text" id="countryInput" name="country" placeholder="Ülke adı yazın..."
               autocomplete="off"
               style="flex:1;min-width:200px;padding:12px 14px;border:1px solid #ccc;
                      border-radius:8px;font-size:15px;color:#333;box-sizing:border-box;">
        <button id="saveCountryBtn"
                disabled
                style="padding:12px 20px;border:none;border-radius:8px;
                       background:#ccc;color:#fff;font-weight:600;
                       cursor:not-allowed;white-space:nowrap;min-width:130px;">
          Kaydet
        </button>
     </div>
    </div>
    <!-- Ulke Seçimi -->

    <!-- Telefon -->
    <div style="width:100%;max-width:600px;background:#fff;border:1px solid #dcdcdc;border-radius:16px;padding:24px;margin-bottom:10px;">
      <h4 style="font-size:18px;font-weight:600;margin-bottom:14px;">Telefon *</h4>

      <?php if ($phoneVerified == 0): ?>
        <div style="background:#fff3cd;color:#856404;padding:12px 14px;border-radius:8px;margin-bottom:12px;font-size:14px;line-height:1.5;">
          ⚠ Puandeks’te işletmelere inceleme bırakabilmek için telefonunuzu doğrulamanız gerekmektedir.
        </div>
      <?php else: ?>
        <div style="background:#d4edda;color:#155724;padding:12px 14px;border-radius:8px;margin-bottom:12px;font-size:14px;line-height:1.5;">
          ✔ Telefon numaranız doğrulanmıştır.
        </div>
      <?php endif; ?>

      <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">

        <!-- Hidden prefix -->
        <input type="hidden" id="userPhonePrefix" value="90">

        <!-- prefix -->
        <input type="text"
              value="+90"
              readonly
              style="width:80px;padding:12px;border:1px solid #ccc;border-radius:8px;background:#f5f5f5;">

        <!-- Number -->
        <input type="text"
              id="userPhone"
              value="<?= $phoneVerified == 1 ? htmlspecialchars($phone) : '' ?>"
              placeholder="5XXXXXXXXX"
              style="flex:1;min-width:200px;padding:12px;border:1px solid #ccc;border-radius:8px;">

        <button id="saveUserPhoneBtn"
                style="padding:12px 20px;border:none;border-radius:8px;
                      background:#1b7d2f;color:#fff;font-weight:600;">
          Kaydet
        </button>

      </div>
    </div>
    <!-- Telefon -->



      <!-- Bağlantı Durumu -->
      <?php
      $stmt = $pdo->prepare("SELECT email, login_source FROM users WHERE id = ?");
      $stmt->execute([$_SESSION['user_id']]);
      $userData = $stmt->fetch(PDO::FETCH_ASSOC);

      $loginSource = $userData['login_source'] ?? 'email';
      $emailValue  = htmlspecialchars($userData['email'] ?? '');

      // login_sourcea göre eposta alt yazısı
      $sourceLabel = '';
      switch ($loginSource) {
        case 'google':
          $sourceLabel = '(Google hesabı)';
          break;
        case 'facebook':
          $sourceLabel = '(Facebook hesabı)';
          break;
        case 'apple':
          $sourceLabel = '(Apple hesabı)';
          break;
      }
      ?>

      <div style="width:100%;max-width:600px;background:#fff;border:1px solid #dcdcdc;
                  border-radius:16px;padding:24px;margin-bottom:10px;">
        <h4 style="font-size:18px;font-weight:600;margin-bottom:14px;">Bağlantı Durumu</h4>
        <hr style="border:0;border-top:1px solid #eee;margin:10px 0;">

        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
          <div style="flex:1;">
            <label style="font-weight:500;">E-posta:</label>
            <input type="email" readonly
                   value="<?php echo $emailValue . ' ' . $sourceLabel; ?>"
                   style="padding:8px 10px;border:1px solid #ccc;border-radius:6px;width:100%;
                          background:#f9f9f9;color:#111;">
          </div>
          <div style="min-width:70px;text-align:right;">
            <span style="color:#1b7d2f;font-weight:600;">Aktif</span>
          </div>
        </div>
      </div>
      <!-- /Bağlantı Durumu -->


  
      <!-- Rozetim -->
      <?php
      // Kullanıcının toplam onaylı yorum saysı
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND status = 1");
      $stmt->execute([$_SESSION['user_id']]);
      $commentCount = (int)$stmt->fetchColumn();

      // Rozet ve mesaj başlangıç
      $badge   = null;
      $message = "";

      // Rozet kuralları
      if ($commentCount < 10) {
          $badge = null;
          $remaining = 10 - $commentCount;
          $message = "$remaining yorum daha yaparak Yeni rozetini alabilirsin!";
      }
      elseif ($commentCount < 50) {
          $badge = "Yeni";
          $remaining = 50 - $commentCount;
          $message = "$remaining yorum daha yaparak Uzman rozetini alabilirsin!";
      }
      elseif ($commentCount < 100) {
          $badge = "Uzman";
          $remaining = 100 - $commentCount;
          $message = "$remaining yorum daha yaparak Elite rozetini alabilirsin!";
      }
      elseif ($commentCount < 500) {
          $badge = "Elite";
          $remaining = 500 - $commentCount;
          $message = "$remaining yorum daha yaparak Lider rozetini alabilirsin!";
      }
      else {
          $badge = "Lider";
          $message = "Tebrikler! Tüm rozetleri kazandınız.";
      }

      // Renk tablosu
      $badgeColors = [
          "Yeni"   => "#1b7d2f",  
          "Uzman"  => "#0066cc",  
          "Elite"  => "#8B008B",  
          "Lider"  => "#d48a00",  
          null     => "#ccc"     
      ];

      // Görsel cıktılar
      $badgeLabelText = $badge ? $badge : "Rozet yok";
      $badgeBgColor   = $badgeColors[$badge];
      $badgeTextColor = $badge ? "#fff" : "#666";
      ?>

   <div style="width:100%;max-width:600px;background:#fff;border:1px solid #dcdcdc;
              border-radius:16px;padding:24px;margin-bottom:10px;text-align:center;">
    <h4 style="font-size:18px;font-weight:600;margin-bottom:15px;">Rozetim</h4>

    <div id="badgeLabel"
         style="display:inline-block;padding:6px 16px;
                background:<?= $badgeBgColor ?>;
                color:<?= $badgeTextColor ?>;
                border-radius:30px;font-weight:600;">
        <?= htmlspecialchars($badgeLabelText, ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <div id="badgeInfoText"
         style="margin-top:15px;font-size:15px;color:#555;">
        <?= $message ?>
    </div>
  </div>
  <!-- /Rozetim -->
  
</div>


<!-- OTP POPUP -->
<div id="userOtpModal" style="
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.5);
  align-items:center;
  justify-content:center;
  z-index:9999;
">
  <div style="
    background:#fff;
    width:100%;
    max-width:400px;
    padding:24px;
    border-radius:12px;
    text-align:center;
  ">
    <h4 style="margin-bottom:10px;">Telefon Doğrulama</h4>
    <p style="font-size:14px;color:#555;margin-bottom:15px;">
      Telefonunuza gönderilen 6 haneli kodu giriniz.
    </p>

    <input type="text"
           id="userOtpInput"
           placeholder="******"
           style="width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;margin-bottom:12px;text-align:center;font-size:18px;">

           <div style="margin-top:12px; text-align:left;">
            <label style="font-size:14px;color:#555;">
              <input type="checkbox" id="userKvkkCheck">
              <a href="https://puandeks.com/kvkk" target="_blank">
                KVKK metnini
              </a> okudum, kabul ediyorum
            </label>
          </div>

        <button id="userVerifyOtpBtn"
          style="margin-top:12px;width:100%;padding:12px;background:#ccc;color:#fff;border:none;border-radius:6px;cursor:not-allowed;">
          Doğrula
        </button>

        <div id="userOtpError" style="
          display:none;
          margin-top:10px;
          background:#f8d7da;
          color:#842029;
          padding:10px;
          border-radius:6px;
          font-size:14px;
        ">
          Kod hatalı. Lütfen tekrar deneyin.
        </div>

    <div id="countdownText" style="font-size:14px;color:#999;margin-bottom:15px;">
      60 saniye kaldı
    </div>
  </div>
</div>
<!-- OTP POPUP -->

<?php include('footer-main.php'); ?>


	<script src="js/common_scripts.js"></script>
	<script src="js/functions.js"></script>
	<script src="assets/validate.js"></script>
	<script src="js/tabs.js"></script>
	<script>new CBPFWTabs(document.getElementById('tabs'));</script>


<!-- === Avatar === -->
  <script>
document.addEventListener("DOMContentLoaded", function () {
  const previewPhoto = document.getElementById("previewPhoto");
  const addPhotoBtn = document.getElementById("addPhotoBtn");
  const fileInput = document.getElementById("profilePhoto");

  // Fotoraf veya + ikonuna tıklaynca dosya seçici açlır
  [previewPhoto, addPhotoBtn].forEach(el => {
    el.addEventListener("click", () => fileInput.click());
  });

  // Yeni fotoğraf seçilince otomatik önizleme ve yükleme
  fileInput.addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
      reader.onload = ev => {
        const avatarWrapper = previewPhoto.parentElement;

        avatarWrapper.innerHTML = `
          <img id="previewPhoto"
              src="${ev.target.result}"
              alt="Profil Fotoğrafı"
              style="width:100px;height:100px;border-radius:50%;
                      object-fit:cover;border:2px solid #dcdcdc;cursor:pointer;">
        `;

        // Yeni oluşan img için tekrar referans al
        const newPreviewPhoto = document.getElementById("previewPhoto");

        // Tekrar tıklanınca dosya seçici açılsın
        newPreviewPhoto.addEventListener("click", () => fileInput.click());
      };
      reader.readAsDataURL(file);


    const formData = new FormData();
    formData.append("photo", file);
    fetch("api/update-user-profile-image.php", { method: "POST", body: formData })
      .then(res => res.json())
      .then(data => {
        if (!data.success) alert("Hata: " + data.message);
      })
      .catch(() => alert("Sunucuya ulaşlamadı."));
  });
});
</script>


  <!-- === Kullanıcı rozet sistemi === -->
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    fetch("/api/get-user-badge.php", { method: "GET", credentials: "same-origin" })
    .then(res => res.json())
    .then(data => {
      if (!data.success) return;
      const badgeSpan = document.getElementById("badgeLabel");
      const infoText = document.getElementById("badgeInfoText");
      if (data.badge) badgeSpan.textContent = data.badge;
      if (data.next && data.next.remaining > 0) {
        infoText.innerHTML = `${data.next.remaining} yorum daha yaparak <strong>${data.next.title}</strong> rozetini alabilirsin!`;
      } else {
        infoText.innerHTML = `Tebrikler! Tüm rozetleri kazandınız.`;
      }
    })
    .catch(() => console.warn("Rozet verisi alınamad."));
  });
  </script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const selected = document.getElementById("mobileMenuSelected");
  const options = document.getElementById("mobileMenuOptions");

  if (selected && options) {
    // aç/kapa
    selected.addEventListener("click", function (e) {
      e.stopPropagation();
      const isOpen = options.style.display === "block";
      options.style.display = isOpen ? "none" : "block";
      selected.classList.toggle("open", !isOpen);
    });

    // dış tiklamayla kapat
    document.addEventListener("click", function (e) {
      if (!selected.contains(e.target) && !options.contains(e.target)) {
        options.style.display = "none";
        selected.classList.remove("open");
      }
    });
  }
});
</script>


<!-- ===============================
     [1] MOBL ACCORDION MENÜ
     =============================== -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const accordionMenu = document.querySelector(".user-menu-mobile");
  const desktopMenu = document.querySelector(".user-menu-desktop");
  const accordionHeader = document.getElementById("mobileMenuHeader");
  const accordionContent = document.getElementById("mobileMenuContent");
  const accordionIcon = accordionHeader ? accordionHeader.querySelector("i.fa-chevron-down") : null;

  // --- Gorünürlük kontrolü ---
  function toggleMenuVisibility() {
    const isMobile = window.innerWidth <= 768;
    if (isMobile) {
      accordionMenu.style.display = "block";
      desktopMenu.style.display = "none";
    } else {
      accordionMenu.style.display = "none";
      desktopMenu.style.display = "flex";
    }

    // Her yeniden görünmede kapalı başlasın
    if (accordionContent) {
      accordionContent.style.maxHeight = "0";
      if (accordionIcon) accordionIcon.style.transform = "rotate(0deg)";
    }
  }

  toggleMenuVisibility();
  window.addEventListener("resize", toggleMenuVisibility);

  // --- Accordion ac/kapa davranışı ---
  if (accordionHeader && accordionContent) {
    accordionHeader.addEventListener("click", function () {
      const isOpen = accordionContent.style.maxHeight && accordionContent.style.maxHeight !== "0px";
      if (isOpen) {
        accordionContent.style.maxHeight = "0";
        accordionIcon.style.transform = "rotate(0deg)";
      } else {
        accordionContent.style.maxHeight = accordionContent.scrollHeight + "px";
        accordionIcon.style.transform = "rotate(180deg)";
      }
    });
  }
});
</script>

  
<!-- /Ülke Seçimi (Otomatik Doldurma) -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("countryInput");
  const saveBtn = document.getElementById("saveCountryBtn");
  const currentCountry = "<?php echo htmlspecialchars($countryName); ?>";

  if (currentCountry) input.value = currentCountry;

  let countries = [];

  // Tm ülke listesini çek
  fetch("/api/suggest-country.php?q=all")
    .then(res => res.json())
    .then(data => {
      if (data.success && data.results) {
        countries = data.results.map(c => c.name.toLowerCase());
      }
    });

  // Inputa tıklanınca mevcut ülke temizlensin
  input.addEventListener("focus", () => {
    if (input.value === currentCountry) input.value = "";
  });

  // Kullanıcı yazdkça kontrol et
  input.addEventListener("input", () => {
    const val = input.value.trim().toLowerCase();
    const valid = countries.includes(val);

    if (valid && val !== currentCountry.toLowerCase()) {
      saveBtn.disabled = false;
      saveBtn.style.background = "#1b7d2f";
      saveBtn.style.cursor = "pointer";
    } else {
      saveBtn.disabled = true;
      saveBtn.style.background = "#ccc";
      saveBtn.style.cursor = "not-allowed";
    }
  });

  // Kaydet işlemi
  saveBtn.addEventListener("click", () => {
    const newCountry = input.value.trim();
    if (!newCountry) return;

    const formData = new FormData();
    formData.append("country", newCountry);

    fetch("/api/update-user-country.php", {
      method: "POST",
      body: formData,
      credentials: "same-origin"
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("✅ " + data.message);
          location.reload();
        } else {
          alert("❌ " + data.message);
        }
      })
      .catch(() => alert("Sunucuya ulaşlamadı."));
  });
});
</script>
<!-- /Ülke Seçimi (Otomatik Doldurma) -->

<!-- rozetim -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  fetch("/api/get-user-badge.php", { method: "GET", credentials: "same-origin" })
    .then(res => res.json())
    .then(data => {

      if (!data.success) return;

      const badgeLabel = document.getElementById("badgeLabel");
      const badgeInfo  = document.getElementById("badgeInfoText");

      // --- Rozeti ASLA sıfırlama ---
      // API gerçekten bir rozet gnderirse güncelle
      if (data.badge && data.badge.trim() !== "") {
        badgeLabel.textContent = data.badge;
      }

      // Açıklama (varsa güncelle)
      if (data.message && data.message.trim() !== "") {
        badgeInfo.textContent = data.message;
      }

    })
    .catch(() => console.warn("Rozet verisi alınamadı."));
});
</script>
<!-- /Rozetim -->

<!-- OTP -->
<script>
const saveBtn = document.getElementById("saveUserPhoneBtn");

const modal = document.getElementById("userOtpModal");
const otpInput = document.getElementById("userOtpInput");
const kvkkCheck = document.getElementById("userKvkkCheck");
const verifyBtn = document.getElementById("userVerifyOtpBtn");
const countdownText = document.getElementById("countdownText");
const errorBox = document.getElementById("userOtpError");

let timer;
let timeLeft = 60;


// =======================
// PHONE SAVE + OTP SEND
// =======================
saveBtn.addEventListener("click", function () {

  const phone = document.getElementById("userPhone").value.trim();

  if (!phone) {
    alert("Telefon numarası giriniz.");
    return;
  }

  const formData = new FormData();
  formData.append("phone", phone);

  fetch("/api/update-user-phone.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin"
  })
  .then(res => res.json())
  .then(data => {
    if (!data.success) {
      alert(data.message);
      return;
    }

    // OTP popup aç
    modal.style.display = "flex";

    // reset
    otpInput.value = "";
    kvkkCheck.checked = false;
    errorBox.style.display = "none";

    verifyBtn.disabled = true;
    verifyBtn.style.background = "#ccc";
    verifyBtn.style.cursor = "not-allowed";

    // countdown
    timeLeft = 60;
    countdownText.textContent = timeLeft + " saniye kaldı";

    clearInterval(timer);
    timer = setInterval(() => {
      timeLeft--;
      countdownText.textContent = timeLeft + " saniye kaldı";

      if (timeLeft <= 0) {
        clearInterval(timer);
        countdownText.textContent = "Kod süresi doldu";
      }
    }, 1000);
  });
});


// =======================
// BUTTON AKTİF/PASİF
// =======================
function toggleUserOtpButton() {
  if (otpInput.value.length === 6 && kvkkCheck.checked) {
    verifyBtn.disabled = false;
    verifyBtn.style.background = "#1b7d2f";
    verifyBtn.style.cursor = "pointer";
  } else {
    verifyBtn.disabled = true;
    verifyBtn.style.background = "#ccc";
    verifyBtn.style.cursor = "not-allowed";
  }
}

otpInput.addEventListener("input", () => {
  errorBox.style.display = "none";
  toggleUserOtpButton();
});

kvkkCheck.addEventListener("change", toggleUserOtpButton);


// =======================
// OTP VERIFY (BUTON)
// =======================
verifyBtn.addEventListener("click", function () {

  const otp = otpInput.value.trim();

  const formData = new FormData();
  formData.append("otp", otp);

  fetch("/api/verify-user-otp.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin"
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      clearInterval(timer);
      errorBox.style.display = "none";
      location.reload();
    } else {
      errorBox.style.display = "block";
      otpInput.value = "";
      toggleUserOtpButton();
    }
  })
  .catch(() => {
      errorBox.style.display = "block";
  });

});


// =======================
// MODAL KAPAT
// =======================
function closeUserOtpModal() {
  modal.style.display = "none";
  clearInterval(timer);
}

// dış tık
modal.addEventListener("click", function(e) {
  if (e.target === modal) closeUserOtpModal();
});

// ESC
document.addEventListener("keydown", function(e) {
  if (e.key === "Escape") closeUserOtpModal();
});
</script>
<!-- OTP -->

</body>
</html>
