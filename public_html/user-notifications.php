<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

// Kullanıcı giriş yapmamışsa veya rol hatalıysa login sayfasına yönlendir
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'user'
) {
    session_destroy();
    header('Location: login');
    exit;
}

// Kullanıcı bilgilerini çek (aktif mi kontrolü dahil)
$stmt = $pdo->prepare("SELECT name, surname FROM users WHERE id = ? AND status = 'active'");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: login');
    exit;
}

$name = htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
$surname = htmlspecialchars($user['surname'], ENT_QUOTES, 'UTF-8');

// Bildirim sayısı
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND status = 'unread'");
$stmt->execute([$_SESSION['user_id']]);
$unreadNotificationCount = (int) $stmt->fetchColumn();
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

  <a href="/user-notifications" class="menu-item active" 
     style="text-decoration:none; color:#1b7d2f; font-weight:600;">
    <i class="fa-solid fa-bell me-1"></i> Bildirimlerim
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
    <span><i class="fa-solid fa-bars me-2"></i> Men</span>
    <i class="fa-solid fa-chevron-down" style="transition:transform 0.3s;"></i>
  </div>

  <!-- Mobil Menü ierikleri -->
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
       style="display:block; padding:12px 18px; text-decoration:none; text-align:left; color:#1b7d2f; border-bottom:1px solid #eee;">
      <i class="fa-solid fa-bell me-1"></i> Bildirimlerim
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


<main style="background-color:#f9f7f3; padding:60px 0; min-height:80vh;">


<div class="container" style="margin: 80px auto 40px auto; max-width: 700px;">
    <div id="noNotificationBox" style="display: none; background-color: #fff5cc; border: 1px solid #ffeb99; padding: 20px; border-radius: 8px; color: #8a6d3b; font-size: 15px;">
      Şu anda hiç bildiriminiz yok.
    </div>
  </div>


</main>


    <?php include('footer-main.php'); ?>

	</div>

	<script src="js/common_scripts.js"></script>
	<script src="js/functions.js"></script>
	<script src="assets/validate.js"></script>
	<script src="js/tabs.js"></script>
	<script>new CBPFWTabs(document.getElementById('tabs'));</script>

<!-- ===============================
     [1] MOBİL ACCORDION MENÜ
     =============================== -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const accordionMenu = document.querySelector(".user-menu-mobile");
  const desktopMenu = document.querySelector(".user-menu-desktop");
  const accordionHeader = document.getElementById("mobileMenuHeader");
  const accordionContent = document.getElementById("mobileMenuContent");
  const accordionIcon = accordionHeader ? accordionHeader.querySelector("i.fa-chevron-down") : null;

  // --- Görünürlük kontrolü ---
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

  // --- Accordion aç/kapa davranş ---
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

<script>
document.addEventListener("DOMContentLoaded", function () {
  fetch("api/get-user-notifications.php")
    .then(res => res.json())
    .then(data => {
      const container = document.querySelector("main .container");
      const noBox = document.getElementById("noNotificationBox");

      if (!data.success || !data.notifications || data.notifications.length === 0) {
        noBox.style.display = "block";
        return;
      }

      // Rozetleri kaldr
      document.querySelectorAll("a[href='user-notifications'] span").forEach(span => {
        span.style.display = "none";
      });

      data.notifications.forEach(notif => {
        const box = document.createElement("div");
        box.className = "card_box";
        box.style = `
          margin-top: 20px;
          padding: 15px;
          background-color: #f1f8ff;
          border: 1px solid #cce5ff;
          border-radius: 6px;
          position: relative;
        `;

        // Kapat butonu
        const closeBtn = document.createElement("button");
        closeBtn.innerHTML = "&times;";
        closeBtn.style = `
          position: absolute;
          top: -10px;
          right: -10px;
          background-color: red;
          color: white;
          font-size: 18px;
          font-weight: bold;
          border: none;
          border-radius: 50%;
          width: 28px;
          height: 28px;
          cursor: pointer;
        `;
        closeBtn.addEventListener("click", () => {
          fetch("api/delete-notification.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: notif.id })
          })
          .then(res => res.json())
          .then(response => {
            if (response.success) {
              box.remove();
              if (container.querySelectorAll(".card_box").length === 0) {
                noBox.style.display = "block";
              }
            } else {
              alert("Bildirim silinemedi: " + response.message);
            }
          })
          .catch(err => {
            alert("Sunucu hatas: " + err.message);
          });
        });

        box.innerHTML = `
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: bold; color: #004085;"> ${notif.title}</span>
            <span style="font-size: 12px; color: #666;">${new Date(notif.created_at).toLocaleDateString()}</span>
          </div>
          <p style="margin-top: 8px; color: #333; font-size: 14px;">${notif.content}</p>
        `;
        box.appendChild(closeBtn);
        container.appendChild(box);
      });
    })
    .catch(error => {
      console.error("Bildirimler alınamadı:", error);
      document.getElementById("noNotificationBox").style.display = "block";
    });
});
</script>




</body>
</html>