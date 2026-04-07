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

// Kullanıcı bilgilerini çek 
$stmt = $conn->prepare("SELECT name, surname FROM users WHERE id = ? AND status = 'active'");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: login');
    exit;
}

// Header’da göstermek için ad ve soyad değişkenleri
$name = htmlspecialchars($user['name'] ?? '');
$surname = htmlspecialchars($user['surname'] ?? '');

// Okunmamış bildirim sayısı
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND status = 'unread'");
$stmt->execute([$_SESSION['user_id']]);
$unreadNotificationCount = (int) $stmt->fetchColumn();

// Kullanıcının yorumlar
$stmt = $conn->prepare("
  SELECT 
    r.id, r.title, r.comment, r.rating, r.created_at, r.status,
    c.name AS company_name, c.id AS company_id
  FROM reviews r
  INNER JOIN companies c ON r.company_id = c.id
  WHERE r.user_id = :uid
  ORDER BY r.created_at DESC
");
$stmt->execute(['uid' => $_SESSION['user_id']]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
$reviewCount = count($reviews);
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

  <a href="/user" class="menu-item"
     style="text-decoration:none; color:#333; font-weight:600;">
    <i class="fa-solid fa-user me-1"></i> Profilim
  </a>

  <a href="#" class="menu-item active"
     style="text-decoration:none; color:#1b7d2f; font-weight:600;">
    <i class="fa-solid fa-star me-1"></i> İncelemelerim
  </a>

  <a href="/user-notifications" class="menu-item"
     style="text-decoration:none; color:#333; font-weight:600;">
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
    <span><i class="fa-solid fa-bars me-2"></i> Menü</span>
    <i class="fa-solid fa-chevron-down" style="transition:transform 0.3s;"></i>
  </div>

  <!-- Menü içerikleri (aılır kapanır alan) -->
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
       style="display:block; padding:12px 18px; text-decoration:none; text-align:left; color:#333; border-bottom:1px solid #eee;">
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


<!-- Filtre Grupları -->
<div class="d-flex flex-wrap justify-content-center" style="gap: 8px; margin-bottom: 30px;">

  <!-- Puan Dropdown -->
  <div style="position: relative; width: 150px; flex: 0 0 auto;">
    <div id="ratingDropdown" style="width: 100%; height: 44px; padding: 10px 12px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff; cursor: pointer; font-size: 17px; text-align: left;">
      <span id="ratingDropdownSelected">Puana Göre</span>
    </div>
    <ul id="ratingDropdownOptions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #ccc; border-radius: 4px; max-height: 220px; overflow-y: auto; margin-top: 2px; list-style: none; padding: 0; z-index: 100;">
      <li data-value="1" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #eee;"><img src="img/core/vote_1.svg" height="20"></li>
      <li data-value="2" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #eee;"><img src="img/core/vote_2.svg" height="20"></li>
      <li data-value="3" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #eee;"><img src="img/core/vote_3.svg" height="20"></li>
      <li data-value="4" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #eee;"><img src="img/core/vote_4.svg" height="20"></li>
      <li data-value="5" style="padding: 10px 12px; cursor: pointer;"><img src="img/core/vote_5.svg" height="20"></li>
    </ul>
    <input type="hidden" name="rating_filter" id="ratingFilterInput">
  </div>

  <!-- Tarih Dropdown -->
  <div style="position: relative; width: 150px; flex: 0 0 auto;">
    <div id="dateDropdown" style="width: 100%; height: 44px; padding: 10px 12px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff; cursor: pointer; font-size: 17px; text-align: left;">
      <span id="dateDropdownSelected">Tarihe Göre</span>
    </div>
    <ul id="dateDropdownOptions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #ccc; border-radius: 4px; max-height: 220px; overflow-y: auto; margin-top: 2px; list-style: none; padding: 0; z-index: 100;">
      <li data-value="desc" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #eee;">En yakın tarih</li>
      <li data-value="asc" style="padding: 10px 12px; cursor: pointer;">En uzak tarih</li>
    </ul>
    <input type="hidden" name="date_order" id="dateOrderInput">
  </div>

  <!-- Kategori Dropdown -->
<div style="position: relative; width: 180px; flex: 0 0 auto;">
  <div id="customDropdown" style="width: 100%; height: 44px; padding: 10px 12px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff; cursor: pointer; font-size: 17px; text-align: left;">
    <span id="dropdownSelected" style="display: inline-block; max-width: 100%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">Kategoriye Göre</span>
  </div>
  <ul id="dropdownOptions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #ccc; border-radius: 4px; max-height: 260px; overflow-y: auto; margin-top: 2px; list-style: none; padding: 0; z-index: 100;">
    <?php
    require_once('/home/puandeks.com/backend/config.php');
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo '<li data-value="' . $cat['id'] . '" style="padding: 10px 12px; cursor: pointer; text-align: left; border-bottom: 1px solid #eee;">' . htmlspecialchars($cat['name']) . '</li>';
    }
    ?>
  </ul>
  <input type="hidden" name="category" id="categoryInput" value="">
</div>


  <!-- Temizle Butonu -->
  <div style="width: 150px; flex: 0 0 auto;">
    <button onclick="clearFilters()" style="width: 100%; height: 44px; background-color: #007BFF; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">Filtreyi Temizle</button>
  </div>

</div>
<!-- /Filtre Grupları -->
  
  
<!-- Review box -->
   <div id="reviewContainer" class="container" style="margin-top: 40px;">
  <!-- JS Filled -->
  </div>
<!-- Review box -->


<!-- No review box -->
    <div
      id="noReviewsMessage"
      style="display: none; text-align: center; margin-top: 40px">
      <h2 style="font-size: 22px; margin-bottom: 10px">İlk incelemenizi yazın</h2>
      <p
        style="max-width: 500px; margin: 0 auto 20px auto; font-size: 15px; line-height: 1.6">
        Deneyiminizi paylaşın! Geri bildirimleriniz başkalarının güvenle alveriş
        yapmasnı sağlayacak ve işletmelerin gelişmesine yardımcı olacaktır.
      </p>
      <a
        href="categories"
        style="background-color: #2E76F6; color: white; padding: 12px 24px; border-radius: 25px; text-decoration: none; font-weight: bold"
        >Bir işletme bulun</a
      >
    </div>
<!-- No review box -->


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

  // --- Accordion aç/kapa davranşı ---
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

<!-- Review Box -->
<script>
  function fetchReviews(order = "rating", category = "", rating = "", date = "") {
    fetch(`api/get-user-reviews.php?order=${order}&category=${encodeURIComponent(category)}&rating=${encodeURIComponent(rating)}&date=${encodeURIComponent(date)}`)
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById("reviewContainer");
        const noMsg = document.getElementById("noReviewsMessage");

        if (!data || data.status !== "success" || !Array.isArray(data.reviews)) {
          container.innerHTML = "";
          noMsg.style.display = "block";
          return;
        }

        if (data.reviews.length > 0) {
          container.innerHTML = "";
          noMsg.style.display = "none";

          data.reviews.forEach(review => {
            const canEdit = review.can_edit;

            const editBtn = canEdit ? `
              <a href="#"
                 class="edit-review-btn"
                 data-id="${review.id}"
                 data-title="${encodeURIComponent(review.title)}"
                 data-content="${encodeURIComponent(review.content)}"
                 style="margin-right: 15px; color: #555; text-decoration: none;">
                <i class="fa-solid fa-pen-to-square"></i> Düzenle
              </a>` : '';

            const deleteBtn = canEdit ? `
              <a href="#"
                 class="delete-review-btn"
                 data-id="${review.id}"
                 style="color: #555; text-decoration: none;">
                <i class="fa-solid fa-trash"></i> Sil
              </a>` : '';

            const starImg = Array.from({ length: 5 }, (_, i) => {
              return `<img src="img/core/${i < review.rating ? `star_${review.rating}` : 'star_0'}.svg"` +
                     ` style="height: 20px; margin-right: 4px;">`;
            }).join('');

            const box = document.createElement("div");
            box.className = "review-box";
            box.setAttribute("data-rating", review.rating);

            box.innerHTML = `
              <div style="max-width: 600px; margin: 20px auto; background-color: white; border-radius: 6px; border: 1px solid #ddd; box-shadow: 0 1px 4px rgba(0,0,0,0.05); padding: 20px; font-family: Arial, sans-serif;">
                <p style="margin: 0 0 15px 0; font-size: 14px;">
                  <a href="company-profile.php?id=${review.company_id}" style="color:#0074cc; font-weight: 600; text-decoration: none;">${review.company_name}</a> incelemesi
                </p>


               

             <!-- USER TOP BLOCK -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">

    <!-- LEFT: Avatar + User Info -->
    <div style="display:flex; align-items:center; gap:12px;">

        <!-- Avatar + Badge -->
        <div style="position:relative; width:40px; height:40px; flex-shrink:0;">
            <img src="${review.user_image || 'img/placeholder/user.png'}"
                 alt="User"
                 style="width:40px; height:40px; border-radius:50%; object-fit:cover;">

            ${review.badge_label ? `
              <span style="
                position:absolute;
                top:-8px;
                right:-8px;
                background:${review.badge_color};
                color:#fff;
                padding:2px 6px;
                font-size:10px;
                font-weight:700;
                border-radius:10px;
                line-height:1;
              ">
                ${review.badge_label}
              </span>
            ` : ''}
        </div>

        <!-- User Info -->
        <div style="line-height:1.2;">
            <strong style="font-size:14px;">${review.display_name}</strong><br>
            <span style="font-size:12px; color:gray;">${review.total_review_count} İnceleme</span>
        </div>

    </div>

    <!-- RIGHT: Status Badge -->
    <span style="background-color: yellow; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
        ${review.status == 1 ? 'Onaylandı' : 'Onay bekliyor'}
    </span>

</div>



                <hr style="margin: 10px 0;">

                <div class="static-stars" style="margin-bottom: 10px;">${starImg}</div>

                <h4 style="margin-bottom: 5px;">${review.title}</h4>
                <p class="review-content" style="font-size: 14px; line-height: 1.5; color: #333;">
                      ${review.content}
                    </p>

                    ${review.media_count > 0 ? `
                      <div style="
                        margin-top:12px;
                        font-size:14px;
                        color:#374151;
                        display:flex;
                        align-items:center;
                        gap:6px;
                      ">
                        <i class="fa fa-camera"></i>
                        <strong>${review.media_count}</strong>
                      </div>

                      <hr style="margin:12px 0;">
                    ` : ""}

                    <p style="font-size: 13px; color: #555;">
                      <strong>Deneyim tarihi :</strong> ${review.experience_date}
                    </p>

                <hr style="margin: 15px 0;">
                <div>${editBtn}${deleteBtn}</div>
              </div>
            `;
            container.appendChild(box);
          });

          bindReviewEvents();
        } else {
          container.innerHTML = "";
          noMsg.style.display = "block";
        }
      })
      .catch(() => {
        const container = document.getElementById("reviewContainer");
        const noMsg = document.getElementById("noReviewsMessage");
        container.innerHTML = "";
        noMsg.style.display = "block";
      });
  }

  function filterByRating(rating) {
    const category = document.getElementById("categoryInput").value;
    const date = document.getElementById("dateOrderInput").value;
    fetchReviews("rating", category, rating, date);
  }

  function filterByCategory(category) {
    const rating = document.getElementById("ratingFilterInput").value;
    const date = document.getElementById("dateOrderInput").value;
    fetchReviews("rating", category, rating, date);
  }

  function filterByDate(date) {
    const rating = document.getElementById("ratingFilterInput").value;
    const category = document.getElementById("categoryInput").value;
    fetchReviews("rating", category, rating, date);
  }

  function clearFilters() {
    document.getElementById("ratingFilterInput").value = "";
    document.getElementById("categoryInput").value = "";
    document.getElementById("dateOrderInput").value = "";

    document.getElementById("ratingDropdownSelected").textContent = "Puana Gre";
    document.getElementById("dropdownSelected").textContent = "Kategoriye Göre";
    document.getElementById("dateDropdownSelected").textContent = "Tarihe Göre";

    fetchReviews();
  }

  function bindReviewEvents() {
    const editBtns = document.querySelectorAll(".edit-review-btn");
    const deleteBtns = document.querySelectorAll(".delete-review-btn");

    editBtns.forEach(btn => {
      btn.onclick = function (e) {
        e.preventDefault();
        const id = this.dataset.id;
        const title = decodeURIComponent(this.dataset.title);
        const contentRaw = decodeURIComponent(this.dataset.content);
        const content = contentRaw.replace(/<[^>]*>?/gm, '');
        editReview(id, title, content, true);
      };
    });

    deleteBtns.forEach(btn => {
      btn.onclick = function (e) {
        e.preventDefault();
        const id = this.dataset.id;
        deleteReview(id, true);
      };
    });
  }

  window.addEventListener("DOMContentLoaded", () => {
    fetchReviews();
    bindReviewEvents();
  });
</script>





<!-- Delete Review -->
<script>
  function deleteReview(id, isUser) {
    if (!confirm("Bu yorumu silmek istediinizden emin misiniz?")) return;

    fetch("api/delete-review.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "id=" + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        alert("Yorum başaryla silindi.");
        fetchReviews();
      } else {
        alert("Silme işlemi başarsz oldu.");
      }
    })
    .catch(() => {
      alert("Sunucu hatas olutu.");
    });
  }
</script>

<!-- Edit Review -->
<script>
  function editReview(id, title, contentRaw, isUser) {
    const content = contentRaw.replace(/<[^>]*>?/gm, '');
    const box = document.querySelector(`.edit-review-btn[data-id="${id}"]`).closest(".review-box");
    if (!box) return;

    // Statik yıldız bloğunu sadece GİZLE
    const staticStarDiv = box.querySelector(".static-stars");
    if (staticStarDiv) staticStarDiv.style.display = "none";

    // Dinamik yıldız blou olutur
    const rating = parseInt(box.dataset.rating || 0);
    const dynamicStars = document.createElement("div");
    dynamicStars.className = "editable-stars";
    dynamicStars.style = "margin-bottom: 10px; display: flex; gap: 4px; align-items: center;";
    let currentRating = rating;

    for (let i = 1; i <= 5; i++) {
      const starImg = document.createElement("img");
      starImg.src = (i <= rating) ? `img/core/star_${rating}.svg` : `img/core/star_0.svg`;
      starImg.setAttribute("data-value", i);
      starImg.style.height = "20px";
      starImg.style.cursor = "pointer";

      starImg.addEventListener("click", function () {
        currentRating = i;
        for (let j = 1; j <= 5; j++) {
          const img = dynamicStars.querySelector(`img[data-value="${j}"]`);
          img.src = (j <= i) ? `img/core/star_${i}.svg` : `img/core/star_0.svg`;
        }
      });

      dynamicStars.appendChild(starImg);
    }

    // Dinamik yıldız bloğunu statik bloun ALTINA yerleştir
    staticStarDiv.after(dynamicStars);

    // Başlık <h4> → input
    const titleEl = box.querySelector("h4");
    const titleInput = document.createElement("input");
    titleInput.type = "text";
    titleInput.value = title;
    titleInput.style = "width: 100%; margin-bottom: 10px;";
    titleEl.replaceWith(titleInput);

    // İçeriği textarea'ya çevir
    const contentEl = box.querySelector("p.review-content");
    const contentTextarea = document.createElement("textarea");
    contentTextarea.value = content;
    contentTextarea.style = "width: 100%; height: 80px;";
    contentEl.replaceWith(contentTextarea);

    // Butonu güncelle
    const editBtn = box.querySelector(".edit-review-btn");
    editBtn.innerHTML = '<i class="fa-solid fa-check"></i> Bitir';
    editBtn.classList.remove("edit-review-btn");
    editBtn.classList.add("finish-edit-btn");

    editBtn.onclick = function (e) {
      e.preventDefault();
      finishEdit(id, titleInput.value, contentTextarea.value, isUser, currentRating);
    };
  }
</script>



<!-- Finish Edit -->
<script>
  function finishEdit(id, newTitle, newContent, isUser, currentRating) {
    if (!newTitle.trim() || !newContent.trim()) {
      alert("Balk ve içerik bo olamaz.");
      return;
    }

    // Yeni yldız puanı da backend'e gönderiliyor
    fetch("api/update-review.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${encodeURIComponent(id)}&title=${encodeURIComponent(newTitle)}&content=${encodeURIComponent(newContent)}&rating=${encodeURIComponent(currentRating)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        alert("Yorum gncellendi.");
        fetchReviews(); // Sayfayı tazele, her ey sıfırdan yeniden yklenecek
        bindReviewEvents();
      } else {
        alert("Gncelleme başarısız: " + data.message);
      }
    })
    .catch(() => {
      alert("Sunucu hatası oluştu.");
    });
  }
</script>
<!-- Review Box -->
  
<!-- Kategori seçimi -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const dropdown = document.getElementById("customDropdown");
  const options = document.getElementById("dropdownOptions");
  const selected = document.getElementById("dropdownSelected");
  const hiddenInput = document.getElementById("categoryInput");

  // Aç/kapat
  dropdown.addEventListener("click", function (e) {
    e.stopPropagation();
    options.style.display = (options.style.display === "block") ? "none" : "block";
  });

  // Seçim yapınca
  options.querySelectorAll("li").forEach(function (item) {
    item.addEventListener("click", function () {
      const value = item.getAttribute("data-value");
      const text = item.textContent;
      selected.textContent = text;
      hiddenInput.value = value;
      options.style.display = "none";
      filterByCategory(value); // filtreleme tetiklemesi
    });
  });

  // Dışarı tıklanınca kapanma
  document.addEventListener("click", function (e) {
    if (!dropdown.contains(e.target)) {
      options.style.display = "none";
    }
  });
});
</script>
<!-- Kategori seçimi -->

<!-- Puana göre filtreleme -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const ratingDropdown = document.getElementById("ratingDropdown");
  const ratingOptions = document.getElementById("ratingDropdownOptions");
  const ratingSelected = document.getElementById("ratingDropdownSelected");
  const ratingInput = document.getElementById("ratingFilterInput");

  // Aç/kapat
  ratingDropdown.addEventListener("click", function () {
    ratingOptions.style.display = (ratingOptions.style.display === "block") ? "none" : "block";
  });

  // Seim yapınca
  ratingOptions.querySelectorAll("li").forEach(function (item) {
    item.addEventListener("click", function () {
      const value = item.getAttribute("data-value");
      ratingSelected.innerHTML = `<img src="img/core/vote_${value}.svg" height="20">`;
      ratingInput.value = value;
      ratingOptions.style.display = "none";
      filterByRating(value); // filtreleme fonksiyonu tetiklenir
    });
  });

  // Dışarı tıklanınca kapanma
  document.addEventListener("click", function (e) {
    if (!ratingDropdown.contains(e.target)) {
      ratingOptions.style.display = "none";
    }
  });
});
</script>
<!-- /Puana gre filtreleme -->

<!-- Tarihe göre filtreleme -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const dateDropdown = document.getElementById("dateDropdown");
  const dateOptions = document.getElementById("dateDropdownOptions");
  const dateSelected = document.getElementById("dateDropdownSelected");
  const dateInput = document.getElementById("dateOrderInput");

  dateDropdown.addEventListener("click", function (e) {
    e.stopPropagation();
    dateOptions.style.display = (dateOptions.style.display === "block") ? "none" : "block";
  });

  dateOptions.querySelectorAll("li").forEach(function (item) {
    item.addEventListener("click", function () {
      const value = item.getAttribute("data-value");
      const label = item.textContent;

      dateInput.value = value;
      dateSelected.textContent = label;
      dateOptions.style.display = "none";

      // Güncel filtreleri toplayıp gönder
      const date = document.getElementById("dateOrderInput").value;
      const rating = document.getElementById("ratingFilterInput").value;
      const category = document.getElementById("categoryInput").value;
      fetchReviews("rating", category, rating, date);
    });
  });

  document.addEventListener("click", function (e) {
    if (!dateDropdown.contains(e.target)) {
      dateOptions.style.display = "none";
    }
  });
});
</script>
<!-- /Tarihe göre filtreleme -->

</body>
</html>