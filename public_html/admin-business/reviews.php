<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: /login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$stmt = $pdo->prepare("SELECT name, auto_reply_enabled FROM companies WHERE id = ?");
$stmt->execute([$_SESSION['company_id']]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

$_SESSION['company_name'] = $company['name'] ?? 'İşletme';

$BUSINESS_NAME = $_SESSION['company_name'] ?? 'İşletme';
$AUTO_REPLY_ENABLED = (int)($company['auto_reply_enabled'] ?? 0);
$PACKAGE_STATUS = 'free';

$api_url = "https://business.puandeks.com/api/get-company-subscription.php?company_id=" . $_SESSION['company_id'];
$response = file_get_contents($api_url . '&t=' . time());
$decoded = json_decode($response, true);

if ($decoded && $decoded['success'] && !empty($decoded['data'])) {
  $PACKAGE_STATUS = $decoded['data']['status'];
}

?>
<script>
  window.AUTO_REPLY_ENABLED = <?= $AUTO_REPLY_ENABLED ?>;
  window.PACKAGE_STATUS = "<?= $PACKAGE_STATUS ?>";
</script>


<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Puandeks - <?= htmlspecialchars($BUSINESS_NAME, ENT_QUOTES, 'UTF-8') ?> | İncelemeler </title>
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link href="css/business-admin.css" rel="stylesheet">
  <link rel="icon" href="img/favicon.png">

<style>
@media (max-width: 768px) {
  .review-footer {
    flex-direction: column !important;
    align-items: center !important;
  }

  .review-actions {
    width: 100% !important;
  }
}
</style>


</head>

<body id="page-top">
<div id="wrapper">
  
<!-- Sidebar -->
<?php include("inc/sidebar.php"); ?>
<!-- Sidebar -->
<div id="sidebar-overlay"></div>

<!-- Content -->
<div id="content-wrapper" class="d-flex flex-column" style="margin-left:280px; margin-top:100px; padding:24px; min-height:calc(100vh - 100px); background:#f9fafb;">

<div id="content">

<!-- Topbar -->
    <?php include("inc/topbar.php"); ?>
<!-- Topbar -->

<!-- Main Content -->

<div class="container-fluid">

  <!-- ORTAK WRAPPER -->
  <div style="
    max-width:900px;
    width:100%;
    margin-left:0;
  ">

    <h1 class="h3 mb-4 text-gray-800">İncelemeler</h1>

    <!-- İncelemeler -->
    <div id="reviewsContainer" style="
      display:flex;
      flex-direction:column;
      align-items:center;
      gap:16px;
    ">
      <!-- JS filled -->
    </div>

    <!-- Pagination -->
    <div id="reviews-pagination" style="
      display:flex;
      justify-content:flex-start;
      align-items:center;
      gap:10px;
      margin-top:24px;
      user-select:none;
      max-width:900px;
    ">

      <button id="prevPage" style="
        padding:8px 14px;
        border-radius:8px;
        border:1px solid #e5e7eb;
        background:#fff;
        font-size:14px;
        color:#374151;
        cursor:pointer;
      ">
        Önceki
      </button>

      <div id="pageNumbers" style="display:flex; gap:6px;"></div>

      <button id="nextPage" style="
        padding:8px 14px;
        border-radius:8px;
        border:1px solid #e5e7eb;
        background:#fff;
        font-size:14px;
        color:#374151;
        cursor:pointer;
      ">
        Sonraki
      </button>

    </div>
    <!-- /Pagination -->

  </div>
  <!-- /WRAPPER -->

</div>


<!-- / Main Content -->


<!-- Yanıtla Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="replyModalLabel">İncelemeyi Yanıtla</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Kapat">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" rows="4" placeholder="Yanıtınız..."></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Kapat</button>
        <button class="btn btn-primary" type="button">Yanıtla</button>
      </div>
    </div>
  </div>
</div>
<!-- Yanıtla Modal -->

<!-- Şikayet Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="reportModalLabel">İncelemeyi Admin'e Şikayet Et</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Kapat">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <textarea id="reportText" class="form-control" rows="4"
          placeholder="Şikayet nedeninizi yazın..."></textarea>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Kapat</button>
        <button id="sendReportBtn" class="btn btn-danger" type="button">
          Şikayet Gönder
        </button>
      </div>

    </div>
  </div>
</div>
<!-- Şikayet Modal -->

<!-- Media Modal -->
<div id="mediaModal" style="
  display:none;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.9);
  z-index:9999;
  align-items:center;
  justify-content:center;
">

  <div style="
    position:relative;
    width:90%;
    max-width:900px;
    height:80%;
    display:flex;
    align-items:center;
    justify-content:center;
  ">

    <!-- TOP CONTROLS -->
    <div style="
      position:absolute;
      top:10px;
      left:15px;
      display:flex;
      gap:8px;
      z-index:10;
    ">

      <div id="mediaPrev" style="
        padding:6px 12px;
        border-radius:20px;
        background:rgba(0,0,0,0.6);
        color:#fff;
        cursor:pointer;
        font-size:16px;
        font-weight:bold;
      ">‹</div>

      <div id="mediaNext" style="
        padding:6px 12px;
        border-radius:20px;
        background:rgba(0,0,0,0.6);
        color:#fff;
        cursor:pointer;
        font-size:16px;
        font-weight:bold;
      ">›</div>

    </div>

    <!-- CLOSE -->
    <span id="mediaModalClose" style="
      position:absolute;
      top:10px;
      right:15px;
      font-size:22px;
      cursor:pointer;
      color:#fff;
      z-index:10;
    ">✕</span>

    <!-- CONTENT -->
    <div id="mediaModalContent" style="
      width:100%;
      height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
    "></div>

  </div>
</div>
<!-- Media Modal -->

<!-- Call Modal -->
<div id="callPopup" style="
  display:none;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.5);
  z-index:9999;
  align-items:center;
  justify-content:center;
">
  <div style="
    background:#fff;
    padding:24px;
    border-radius:12px;
    width:90%;
    max-width:400px;
    text-align:center;
  ">
    <h5 style="margin-bottom:10px;">Arama Başlatılıyor</h5>
    <p id="callPopupText" style="font-size:14px;color:#555;">
      Lütfen bekleyin...
    </p>

    <button id="closeCallPopup" style="
      margin-top:15px;
      padding:8px 16px;
      border:none;
      border-radius:6px;
      background:#05462F;
      color:#fff;
      cursor:pointer;
    ">
      Kapat
    </button>
  </div>
</div>
<!-- Call Modal -->


<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>


<script>
/* =========================================================
   PAGINATION STATE
========================================================= */
let currentPage = 1;
const limit = 10;
let totalPages = 1;

/* =========================================================
   REVIEWS LOAD (WITH PAGE)
========================================================= */
async function loadReviews(page = 1, filter = "all") {
  currentPage = page;

  try {
    const res = await fetch(
      `api/get-company-reviews.php?filter=${filter}&page=${page}`
    );
    const data = await res.json();

    const container = document.getElementById("reviewsContainer");

    container.style.display = "flex";
    container.style.flexDirection = "column";
    container.style.alignItems = "center";
    container.innerHTML = "";

    if (data.status === "success" && data.reviews.length > 0) {

      data.reviews.forEach(r => {

        /* AD + SOYAD*/
        let displayName = "Kullanıcı";
        if (r.user_name && r.user_name.includes(" ")) {
          const parts = r.user_name.trim().split(/\s+/);
          displayName = `${parts[0]} ${parts[1].charAt(0)}.`;
        } else if (r.user_name) {
          displayName = r.user_name;
        }

        const box = document.createElement("div");

        box.setAttribute("data-id", r.id);
        box.setAttribute("data-reply", r.company_reply ?? "");
        box.setAttribute("data-edited-at", r.edited_at ?? "");

        box.style.cssText = `
          background:#fff;
          border:1px solid #e5e7eb;
          border-radius:12px;
          padding:16px;
          display:flex;
          flex-direction:column;
          gap:12px;
          width:100%;
          max-width:900px;
          margin:0 auto;
        `;

        box.innerHTML = `
          <!-- ÜST -->
          <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
          ">
            <span style="font-size:16px;font-weight:600;">
              ${displayName}
            </span>
            <span style="font-size:13px;color:#6b7280;">
              ${r.created_at}
            </span>
          </div>

          <!-- YORUM -->
          <div>
            <strong style="font-size:15px;">${r.title}</strong>

            <div style="margin-top:6px;line-height:1.5;">
              ${r.message}
            </div>

            ${r.media_count > 0 ? `
              <div class="review-media-trigger"
                  data-review-id="${r.id}"
                  style="
                    margin-top:8px;
                    font-size:14px;
                    color:#374151;
                    cursor:pointer;
                    display:flex;
                    align-items:center;
                    gap:6px;
                  ">
                <i class="fa fa-camera" style="font-size:16px;"></i>
                <strong>${r.media_count}</strong>
              </div>
            ` : ""}
          </div>

          <!-- ALT -->
          <div class="review-footer" style="
            display:flex;
            flex-direction:row;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
          ">
            <div>
              <span style="font-size:18px;font-weight:600;">
                Puan: ${r.score}
              </span>
              <span style="
                margin-left:8px;
                font-size:12px;
                padding:4px 10px;
                border-radius:6px;
                color:#fff;
                background:${r.company_reply ? '#16a34a' : '#f59e0b'};
              ">
                ${r.company_reply ? 'Cevaplandı' : 'Cevap Bekliyor'}
              </span>
            </div>

         <!-- BUTONLAR -->
          <div class="review-actions" style="
            display:flex;
            flex-direction:column;
            gap:8px;
            width:160px;
          ">


              <!-- YANITLA -->
              <button class="btn btn-sm btn-primary w-100 reply-btn"
                ${
                  (window.AUTO_REPLY_ENABLED !== 1 && (window.PACKAGE_STATUS === 'active' || window.PACKAGE_STATUS === 'trial'))
                    ? 'data-toggle="modal" data-target="#replyModal"'
                    : ''
                }
              >
                ${
                  window.AUTO_REPLY_ENABLED === 1
                    ? 'Otomatik Yanıt Aktif'
                    : (r.company_reply ? 'Düzenle' : 'Yanıtla')
                }
              </button>
              

            <!-- ŞİKAYET -->
            <button class="btn btn-sm btn-danger w-100 report-to-admin-btn">
              Şikayet Et
            </button>

            <!-- ARA -->
            <button class="btn btn-sm btn-success w-100 call-user-btn">
              <i class="fa fa-phone"></i> Ara
            </button>

          </div>
          </div>
        `;

        container.appendChild(box);
      });

      totalPages = Math.ceil(data.total / limit);
      renderPagination();

      /* PAGINATION  */
      const pagination = document.getElementById("reviews-pagination");
      pagination.style.maxWidth = "900px";
      pagination.style.margin = "24px auto 0";

    } else {
      container.innerHTML = `
        <div style="
          max-width:900px;
          width:100%;
          text-align:center;
          padding:20px;
          color:#999;
          border:1px dashed #e5e7eb;
          border-radius:12px;
        ">
          Gösterilecek inceleme bulunamadı.
        </div>
      `;
      totalPages = 1;
      renderPagination();
    }

  } catch (err) {
    console.error("Yorumlar yüklenemedi:", err);
  }
}


/* =========================================================
   PAGINATION UI
========================================================= */
function renderPagination() {
  const pageNumbers = document.getElementById("pageNumbers");
  const prevBtn = document.getElementById("prevPage");
  const nextBtn = document.getElementById("nextPage");

  pageNumbers.innerHTML = "";

  prevBtn.disabled = currentPage === 1;
  nextBtn.disabled = currentPage === totalPages;

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.className = "btn btn-sm " + (i === currentPage ? "btn-primary" : "btn-light");
    btn.innerText = i;

    btn.addEventListener("click", () => {
      loadReviews(i);
    });

    pageNumbers.appendChild(btn);
  }
}

/* =========================================================
   PREV / NEXT EVENTS
========================================================= */
document.getElementById("prevPage").addEventListener("click", () => {
  if (currentPage > 1) {
    loadReviews(currentPage - 1);
  }
});

document.getElementById("nextPage").addEventListener("click", () => {
  if (currentPage < totalPages) {
    loadReviews(currentPage + 1);
  }
});

/* =========================================================
   INIT
========================================================= */
document.addEventListener("DOMContentLoaded", () => {
  loadReviews(1);
});
</script>



<script>
/* =========================================================
   REPLY MODAL LOGIC (ADD + EDIT + 24H RULE)
========================================================= */
let selectedReviewId = null;
let selectedEditedAt = null;

const replyModal = document.getElementById("replyModal");
const replyTextarea = replyModal.querySelector("textarea");
const replyButton = replyModal.querySelector(".btn-primary");

document.addEventListener("click", function (e) {
  const btn = e.target.closest(".review-actions .btn-primary");
  if (!btn) return;

  const box = btn.closest("[data-id]");
  if (!box) return;

  selectedReviewId = box.dataset.id;
  const existingReply = box.dataset.reply || "";
  selectedEditedAt = box.dataset.editedAt || null;

  replyTextarea.value = existingReply;

  // 24 saat kuralı
  if (selectedEditedAt) {
    const editedTime = new Date(selectedEditedAt).getTime();
    const now = Date.now();
    const diffHours = (now - editedTime) / (1000 * 60 * 60);

    if (diffHours > 24) {
      replyTextarea.disabled = true;
      replyButton.disabled = true;
      replyButton.innerText = "Düzenleme Süresi Doldu";
      return;
    }
  }

  replyTextarea.disabled = false;
  replyButton.disabled = false;
  replyButton.innerText = existingReply ? "Düzenle" : "Yanıtla";
});



/* =========================================================
   SUBMIT REPLY
========================================================= */
replyButton.addEventListener("click", async function () {
  const replyText = replyTextarea.value.trim();

  if (!replyText) {
    alert("Lütfen bir yanıt yazın.");
    return;
  }

  try {
    const res = await fetch("api/update-company-reply.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        review_id: selectedReviewId,
        reply: replyText
      })
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Yanıt kaydedildi.");
      $('#replyModal').modal('hide');
      loadReviews(currentPage);
    } else {
      alert(data.message);
    }
  } catch (err) {
    alert("Sunucu hatası oluştu.");
  }
});
</script>

<script>
/* =========================================================
   REPORT MODAL + SUBMIT 
========================================================= */
let selectedReportReviewId = null;

/* Modal Açma */
document.addEventListener("click", function (e) {

  const btn = e.target.closest(".report-to-admin-btn");
  if (!btn) return;

  const isLocked = (window.PACKAGE_STATUS !== 'active' && window.PACKAGE_STATUS !== 'trial');

  if (isLocked) {
    e.preventDefault();
    e.stopImmediatePropagation();
    alert("İncelemeyi şikayet edebilmeniz için paketinizi yükseltin");
    return;
  }

  const box = btn.closest("[data-id]");
  if (!box) return;

  selectedReportReviewId = box.dataset.id;

  document.getElementById("reportText").value = "";
  $('#reportModal').modal('show');

});


/* Şikayet Gönder */
document.getElementById("sendReportBtn").addEventListener("click", async function () {

  const text = document.getElementById("reportText").value.trim();

  if (!text) {
    alert("Lütfen şikayet nedeninizi yazın.");
    return;
  }

  try {
    const res = await fetch("api/report-review.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        review_id: selectedReportReviewId,
        reason: text
      })
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Şikayet admin'e gönderildi.");
      $('#reportModal').modal('hide');
    } else {
      alert(data.message || "Bir hata oluştu.");
    }

  } catch (err) {
    alert("Sunucu hatası oluştu.");
  }

});
</script>



<!--  Notif count -->
<script>
fetch('api/get-company-unread-count.php')
  .then(res => {
    if (!res.ok) throw new Error();
    return res.json();
  })
  .then(data => {
    const count = parseInt(data.count || 0);
    const el = document.getElementById('notification-count');
    if (el) {
      el.innerText = count;
      el.style.display = count > 0 ? 'inline-block' : 'none';
    }
  })
  .catch(() => {});

updateNotificationCount();
setInterval(updateNotificationCount, 30000);
</script>
<!--  Notif count -->

<!-- Sidebar Aç/Kapat -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.getElementById("menuToggle");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebar-overlay");

  if (menuToggle && sidebar && overlay) {
    menuToggle.addEventListener("click", function () {
      sidebar.classList.toggle("open");
      overlay.classList.toggle("active");
    });

    overlay.addEventListener("click", function () {
      sidebar.classList.remove("open");
      overlay.classList.remove("active");
    });
  }
});
</script>
<!-- Sidebar Aç/Kapat -->

<!-- Media Modal -->
<script>
document.addEventListener("DOMContentLoaded", function() {

  let currentMedia = [];
  let currentIndex = 0;

  const modal = document.getElementById("mediaModal");
  const content = document.getElementById("mediaModalContent");

  function renderMedia() {
    const item = currentMedia[currentIndex];

    content.style.opacity = "0";

    if (item.type === "image") {
      content.innerHTML = `
        <img src="${item.url}" style="
          max-width:100%;
          max-height:100%;
          border-radius:14px;
        ">
      `;
    } else {
      content.innerHTML = `
        <video src="${item.url}" controls autoplay style="
          max-width:100%;
          max-height:100%;
          border-radius:14px;
        "></video>
      `;
    }

    setTimeout(() => {
      content.style.opacity = "1";
    }, 50);
  }

  // CLICK
  document.addEventListener("click", function(e) {

    const btn = e.target.closest(".review-media-trigger");
    if (!btn) return;

    const reviewId = btn.getAttribute("data-review-id");

    fetch("/api/get-review-media.php?review_id=" + reviewId)
      .then(res => res.json())
      .then(data => {

        if (!data || data.length === 0) {
          alert("Medya bulunamadı");
          return;
        }

        currentMedia = data;
        currentIndex = 0;

        renderMedia();

        modal.style.display = "flex";
      });

  });

  // CLOSE
  document.getElementById("mediaModalClose").onclick = () => {
    modal.style.display = "none";
  };

  // OUTSIDE CLICK
  modal.onclick = function(e) {
    if (e.target === this) {
      modal.style.display = "none";
    }
  };

  // NEXT
  document.getElementById("mediaNext").onclick = () => {
    currentIndex = (currentIndex + 1) % currentMedia.length;
    renderMedia();
  };

  // PREV
  document.getElementById("mediaPrev").onclick = () => {
    currentIndex = (currentIndex - 1 + currentMedia.length) % currentMedia.length;
    renderMedia();
  };

});
</script>
<!-- Media Modal -->

<!-- Call -->
<script>
document.addEventListener("click", async function(e){
  const btn = e.target.closest(".call-user-btn");
  if (!btn) return;

  const box = btn.closest("[data-id]");
  const reviewId = box?.dataset.id;

  const popup = document.getElementById("callPopup");
  const text = document.getElementById("callPopupText");

  popup.style.display = "flex";
  text.innerText = "Arama başlatılıyor...";

  try {
    const res = await fetch("/api/start-call.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        review_id: reviewId
      })
    });

    const data = await res.json();

    if (data.status === "success") {
      text.innerText = "Arama başlatıldı, telefonunuz çalacak.";
    } else {
      text.innerText = data.message || "Hata oluştu.";
    }

  } catch (err) {
    text.innerText = "Sunucu hatası.";
  }
});


document.addEventListener("DOMContentLoaded", function () {
  const closeBtn = document.getElementById("closeCallPopup");
  if (closeBtn) {
    closeBtn.onclick = function(){
      document.getElementById("callPopup").style.display = "none";
    };
  }
});
</script>
<!-- Call -->

<script>
  document.addEventListener("click", function(e){

  const isLocked = (window.PACKAGE_STATUS !== 'active' && window.PACKAGE_STATUS !== 'trial');

  // YANITLA
  const replyBtn = e.target.closest(".reply-btn");
  if (replyBtn && isLocked) {
    e.preventDefault();
    e.stopImmediatePropagation();
    alert("İncelemeyi yanıtlayabilmeniz için lütfen paketinizi yükseltin");
    return;
  }

  // ŞİKAYET
  const reportBtn = e.target.closest(".report-to-admin-btn");
  if (reportBtn && isLocked) {
    e.preventDefault();
    e.stopImmediatePropagation();
    $('#reportModal').modal('hide');
    alert("İncelemeyi şikayet edebilmeniz için lütfen paketinizi yükseltin");
    return;
  }

  // ARA
  const callBtn = e.target.closest(".call-user-btn");
  if (callBtn && isLocked) {
    e.preventDefault();
    e.stopImmediatePropagation();
    document.getElementById("callPopup").style.display = "none";
    alert("Kullanıcıyı arayabilmeniz için lütfen paketinizi yükseltin");
    return;
  }

});
</script>  

</body>
</html>
