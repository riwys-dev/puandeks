<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

// URL'den user ID al
$profileUserId = intval($_GET['id'] ?? 0);
if ($profileUserId <= 0) {
    die("Profil bulunamadı.");
}

// user bilgilerini çek
$stmt = $pdo->prepare("
    SELECT 
        name, 
        surname, 
        profile_image,
        (SELECT COUNT(*) FROM reviews WHERE user_id = users.id AND status = 1) AS approved_count
    FROM users
    WHERE id = ?
");
$stmt->execute([$profileUserId]);
$profileUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profileUser) {
    die("Kullanıcı bulunamadı.");
}

// NAME
$rawName    = trim($profileUser['name'] ?? '');
$rawSurname = trim($profileUser['surname'] ?? '');

$name    = htmlspecialchars($rawName, ENT_QUOTES, 'UTF-8');
$surname = htmlspecialchars($rawSurname, ENT_QUOTES, 'UTF-8');

$displayName = $name;

if ($rawSurname !== '') {
    $displayName .= ' ' . mb_strtoupper(mb_substr($rawSurname, 0, 1)) . '.';
}



// Badge
$approvedCount = intval($profileUser['approved_count']);

$profileUser['badge_label'] = '';
$profileUser['badge_color'] = '';

if ($approvedCount >= 500) {
    $profileUser['badge_label'] = 'Lider';
    $profileUser['badge_color'] = '#D14B00';
} elseif ($approvedCount >= 100) {
    $profileUser['badge_label'] = 'Elite';
    $profileUser['badge_color'] = '#AA00FF';
} elseif ($approvedCount >= 50) {
    $profileUser['badge_label'] = 'Uzman';
    $profileUser['badge_color'] = '#0066FF';
} elseif ($approvedCount >= 10) {
    $profileUser['badge_label'] = 'Yeni';
    $profileUser['badge_color'] = '#1b7d2f';
}

// Yorumlar
$stmt = $pdo->prepare("
    SELECT 
        r.id,
        r.title,
        r.comment,
        r.rating,
        r.created_at,
        r.status,
        c.name AS company_name,
        c.id AS company_id,
        c.slug AS company_slug,
        (
            SELECT COUNT(*) 
            FROM review_media rm 
            WHERE rm.review_id = r.id
        ) AS media_count

    FROM reviews r
    INNER JOIN companies c ON r.company_id = c.id
    WHERE r.user_id = :uid
    ORDER BY r.created_at DESC
");
$stmt->execute(['uid' => $profileUserId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
$reviewCount = count($reviews);
?>




<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Puandeks - <?php echo $displayName; ?></title>


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
      
<script>
  const PROFILE_USER_ID = <?php echo $profileUserId; ?>;
</script>

<main style="background-color:#f9f7f3; padding:60px 0; min-height:80vh; margin-top:60px;">

<!-- Profile Card -->
<div style="width:100%;max-width:600px;margin:0 auto 40px auto;
            background:#fff;border:1px solid #dcdcdc;border-radius:16px;
            padding:24px;text-align:center;">

  <h4 style="font-size:18px;font-weight:600;margin-bottom:20px;">
    Profil Bilgileri
  </h4>

<!-- Avatar (readonly) -->
<div style="position:relative;display:inline-block;">

<?php if (!empty($profile_image) && strpos($profile_image, 'googleusercontent') === false): ?>

    <img src="<?= htmlspecialchars($profile_image) ?>"
         style="
            width:100px;
            height:100px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid #dcdcdc;
         ">

<?php else: ?>

    <div id="profileAvatar"
     style="
        position:relative;
        display:flex;
        align-items:center;
        justify-content:center;
        width:100px;
        height:100px;
        border-radius:50%;
        background:#05462F;
        color:#fff;
        font-weight:bold;
        font-size:40px;
        border:2px solid #dcdcdc;
     ">
    <?= mb_strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'), 'UTF-8') ?>
    </div>

<?php endif; ?>

</div>



<?php if (!empty($profileUser['badge_label'])): ?>
  <span style="
    position:absolute;
    top:-8px;
    right:-8px;
    background:<?php echo $profileUser['badge_color']; ?>;
    color:#fff;
    padding:4px 10px;
    font-size:12px;
    font-weight:700;
    border-radius:12px;
  ">
    <?php echo $profileUser['badge_label']; ?>
  </span>
<?php endif; ?>


</div>


  <!-- Ad + soyad bas harf -->
  <div style="display:flex;flex-direction:column;align-items:center;gap:6px;margin-top:12px;">
    <div style="width:100%;max-width:300px;padding:12px 14px;background:#fff;
                color:#111;text-align:center;font-weight:700;font-size:16px;">
      <?php echo $displayName; ?>
    </div>

    <!-- Ülke + inceleme sayısı -->
    <div style="font-size:14px;color:#666;">
      Türkiye (<?php echo $reviewCount; ?> İnceleme)
    </div>
  </div>

</div>


<!-- Filtre Gruplar -->
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
  <!-- JS  -->
  </div>
<!-- Review box -->


<!-- Hiç Yorum Yoksa Gosterilecek Alan -->
    <div
      id="noReviewsMessage"
      style="display: none; text-align: center; margin-top: 40px">
      <h3 style="font-size: 18px; margin-bottom: 10px">Kullanıcının Puandeks içinde ya da seçtiğiniz tercihlerde henüz incelemesi bulunmamaktadr.</h3>
    </div>
<!-- Hiç Yorum Yoksa Gosterilecek Alan -->  


</main>


    <?php include('footer-main.php'); ?>

	</div>

	<script src="js/common_scripts.js"></script>
	<script src="js/functions.js"></script>
	<script src="assets/validate.js"></script>
	<script src="js/tabs.js"></script>
	<script>new CBPFWTabs(document.getElementById('tabs'));</script>



<!-- Review Box -->
<script>
  function fetchReviews(order = "rating", category = "", rating = "", date = "") {
    fetch(`api/get-user-reviews.php?user_id=${PROFILE_USER_ID}&order=${order}&category=${encodeURIComponent(category)}&rating=${encodeURIComponent(rating)}&date=${encodeURIComponent(date)}`)
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
                  <a href="/company/${review.company_slug}" style="color:#0074cc; font-weight: 600; text-decoration: none;">${review.company_name}</a> incelemesi
                </p>

            <!-- USER TOP BLOCK -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">

    <!-- LEFT: Avatar + User Info -->
    <div style="display:flex; align-items:center; gap:12px;">

        <!-- Avatar + Badge -->
        <div style="position:relative; width:40px; height:40px; flex-shrink:0;">
            ${(review.user_image && !review.user_image.includes('googleusercontent'))
              ? `<img src="${review.user_image}"
                  style="width:40px;height:40px;border-radius:50%;object-fit:cover;">`
              : `<div style="
                    width:40px;
                    height:40px;
                    border-radius:50%;
                    background:#05462F;
                    color:#fff;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-weight:bold;
                    font-size:18px;
                  ">
                    ${review.display_name.trim().substring(0,1).toUpperCase()}
                </div>`
            }


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
            <span style="font-size:12px; color:gray;">${review.approved_count} İnceleme</span>

        </div>

    </div>


</div>


                <hr style="margin: 10px 0;">

                <div class="static-stars" style="margin-bottom: 10px;">${starImg}</div>

                <h4 style="margin-bottom: 5px;">${review.title}</h4>
                <p class="review-content" style="font-size: 14px; line-height: 1.5; color: #333;">${review.content}</p>

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

                <p style="font-size: 13px; color: #555;"><strong>Deneyim tarihi :</strong> ${review.experience_date}</p>

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

    document.getElementById("ratingDropdownSelected").textContent = "Puana Göre";
    document.getElementById("dropdownSelected").textContent = "Kategoriye Göre";
    document.getElementById("dateDropdownSelected").textContent = "Tarihe Gre";

    fetchReviews(); // Tm yorumları tekrar getir
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
  loadProfileAvatar();
  fetchReviews();
  bindReviewEvents();
});
</script>


  
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

  // Seçim yapınca
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
<!-- /Puana göre filtreleme -->

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


<script>
function loadProfileAvatar() {
  fetch(`api/get-user-reviews.php?user_id=${PROFILE_USER_ID}`)
    .then(r => r.json())
    .then(d => {
      if (!d.reviews || !d.reviews.length) return;

      const u = d.reviews[0];
      const box = document.getElementById('profileAvatar');
      if (!box) return;

      if (u.user_image && !u.user_image.includes('googleusercontent')) {
        box.innerHTML = `
          <img src="${u.user_image}"
               style="width:100px;height:100px;border-radius:50%;
                      object-fit:cover;border:2px solid #dcdcdc;">
        `;
      } else {
        box.innerHTML = `
          <div style="
            width:100px;
            height:100px;
            border-radius:50%;
            background:#05462F;
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:bold;
            font-size:40px;
            border:2px solid #dcdcdc;
          ">
            ${u.user_initial}
          </div>
        `;
      }
    });
}
</script>




</body>
</html>