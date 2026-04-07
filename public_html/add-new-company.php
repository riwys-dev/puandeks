<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

$new_company = isset($_GET['new_company']) ? trim($_GET['new_company']) : '';
$website     = isset($_GET['website']) ? trim($_GET['website']) : '';

if ($new_company === '' && $website === '') {
    // Banned
    header("Location: /company-search");
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'business') {
    echo "<script>
        alert('İşletmeler işletme ekleyemez.');
        window.location.href = document.referrer ? document.referrer : 'https://puandeks.com/';
    </script>";
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /login?redirect=add-new-company");
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$phoneVerified = 0;

$stmt = $pdo->prepare("SELECT phone_verified FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$phoneVerified = (int)$stmt->fetchColumn();
?>


<!DOCTYPE html>

<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Riwys">
    <title>
    <?php
        if ($new_company) {
            echo htmlspecialchars($new_company) . " – Yeni işletme ekle ve inceleme yaz";
        } elseif ($website) {
            echo htmlspecialchars($website) . " – Yeni işletme ekle ve inceleme yaz";
        } else {
            echo "Yeni işletme ekle ve inceleme yaz";
        }
    ?>
   </title>

       <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

	<!-- Favicons-->
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="114x114"
		href="img/apple-touch-icon-114x114-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="144x144"
		href="img/apple-touch-icon-144x144-precomposed.png">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<!-- GOOGLE WEB FONT -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

	<!-- BASE CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/vendors.css" rel="stylesheet">

	<!-- YOUR CUSTOM CSS -->
	<link href="css/custom.css" rel="stylesheet">


	<!-- Popup -->
	<style>
	.popup-overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.5);
		z-index: 9999;
		display: flex;
		align-items: center;
		justify-content: center;
	  }
	  
	  .popup-box {
		background: white;
		padding: 30px;
		border-radius: 12px;
		max-width: 500px;
		text-align: center;
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
	  }
	</style>
	<!-- Popup -->

 
<!-- Close button -->
  <style>
  .close-popup {
    position: fixed;
    top: 40px;
    right: 60px;
    font-size: 24px;
    color: #666;
    cursor: pointer;
    z-index: 9999;
    transition: all 0.2s ease;
  }
  .close-popup:hover {
    color: #000;
    transform: scale(1.1);
  }
  </style>
 <!-- Close button -->

<style>
.box_general.write_review {
  box-shadow: none !important;
  margin: 60px auto;
  float: none;
}
</style>
  
<style>
/* Input + Textarea styling */
.form-control {
    font-size: 18px !important;
    font-weight: 500 !important;
    padding: 14px !important;
}

/* Label büyütme */
.form-group label {
    font-size: 18px !important;
    font-weight: 600 !important;
}

/* Submit button */
#sendReviewBtn {
    background-color: #05462F !important;
    color: #fff !important;
    transition: all 0.2s ease;
}

#sendReviewBtn:hover {
    background-color: #04DA8D !important;
    color: #1C1C1C !important;
}

/* Duygu analizi gizle */
.label-title,
#sentimentResult {
    display: none !important;
}
</style>


</head>

<body>
	
<div id="page">
      
<i class="fa-solid fa-xmark close-popup" id="closePopup"></i>

	
<main class="margin_main_container">
		<div class="container margin_60_35">
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
                  
					<div class="box_general write_review">

                         <h1>
                          <?php
                              if ($new_company) {
                                  echo '"' . htmlspecialchars($new_company) . '" işletmesini ekle ve inceleme yaz';
                              } elseif ($website) {
                                  echo '"' . htmlspecialchars($website) . '" işletmesini ekle ve inceleme yaz';
                              } else {
                                  echo "Yeni bir işletme ekleyin";
                              }
                          ?>
                          </h1>




                      <div class="rating_submit">
                        <div class="form-group">
                          <label class="d-block">Puan ver</label>
                          <div class="rating-stars" id="puanlama">
                            <span data-value="1" class="star">&#9733;</span>
                            <span data-value="2" class="star">&#9733;</span>
                            <span data-value="3" class="star">&#9733;</span>
                            <span data-value="4" class="star">&#9733;</span>
                            <span data-value="5" class="star">&#9733;</span>
                          </div>
                        </div>
                      </div>
                      <!-- rating_submit -->

                      <div class="form-group">
                       <label style="font-weight:600; font-size:16px;">İnceleme başlığı</label>
                        <input class="form-control" name="title" type="text" placeholder="Bir başlık yazın">
                      </div>

                      <div class="form-group">
                        <label style="font-weight:600; font-size:16px;">Deneyiminizi girin</label>
                        <textarea class="form-control" name="comment" style="height: 180px;" placeholder="Bu işletme hakkında başkalarının fikir edinebilmesi için, deneyiminizi samimi ve detaylı olarak yazın"></textarea>
                      </div>

                      <div class="form-group" style="margin-top:30px;">
                          <label style="font-weight:600; font-size:16px;">
                            Fotoğraf veya Video Ekleyin (Opsiyonel)
                          </label>

                          <p style="font-size:13px; color:#888; margin-top:6px; margin-bottom:15px;">
                            En fazla 5 fotoğraf ve 1 video ekleyebilirsiniz • JPG, PNG, WEBP, MP4
                          </p>

                          <div id="mediaPreview" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">

                            <div id="addMediaBtn" style="
                              width:120px;
                              height:120px;
                              border-radius:8px;
                              border:2px dashed #ccc;
                              display:flex;
                              align-items:center;
                              justify-content:center;
                              background:#f5f5f5;
                              cursor:pointer;
                              font-size:32px;
                              color:#888;
                            ">
                              +
                            </div>

                          </div>

                          <input type="file"
                            id="mediaInput"
                            name="media[]"
                            multiple
                            accept="image/jpeg,image/png,image/webp,video/mp4"
                            style="display:none;">
                        </div>

                      <!-- CSRF Token -->
                      <input type="hidden" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                      <div class="sentiment-label"> 
                        <span class="label-title">Duygu Analizi:</span>
                        <span id="sentimentResult" class="label-value">Henüz analiz edilmedi</span>
                        
                        <a href="javascript:void(0)" 
                           class="btn_1 disabled"
                           id="sendReviewBtn"
                           style="
                              pointer-events: none;
                              opacity: 0.5;
                              background-color: #1b7d2f !important;
                              color: #fff;
                              font-weight: 700;
                              font-size: 17px;
                              padding: 14px 28px;
                              border-radius: 28px;
                              display: inline-block;
                              text-align: center;
                           ">
                           İnceleme gönder
                        </a>


                      </div>
                    


				
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</main>
	<!--/main-->
	
	
	</div>
	<!-- page -->
	
<!-- Popup HTML --> 
<div id="reviewConfirmPopup" class="popup-overlay" style="display: none;">
	<div class="popup-box">
	  <div class="icon icon--order-success svg add_bottom_15">
		<svg xmlns="http://www.w3.org/2000/svg" width="72" height="72">
		  <g fill="none" stroke="#8EC343" stroke-width="2">
			<circle cx="36" cy="36" r="35"
			  style="stroke-dasharray:240px, 240px; stroke-dashoffset: 480px;"></circle>
			<path d="M17.417,37.778l9.93,9.909l25.444-25.393"
			  style="stroke-dasharray:50px, 50px; stroke-dashoffset: 0px;"></path>
		  </g>
		</svg>
	  </div>
	  <h2>Teşekkür ederiz! Yorumunuz ve işletme ekleme talebiniz başarıyla alındı.</h2>
	  <p><!-- JS filled --></p> 

	</div>
  </div>
  
<!-- Popup HTML -->

<!-- USER OTP POPUP -->
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
    position:relative;
  ">

    <!-- close -->
    <span id="closeUserOtpModal" style="
      position:absolute;
      top:10px;
      right:12px;
      cursor:pointer;
      font-size:18px;
    ">✕</span>

    <h4 style="margin-bottom:10px;">Telefon Doğrulama</h4>

    <!-- STEP 1 -->
    <div id="userPhoneStep">

      <div style="background:#fff3cd;color:#856404;padding:12px;border-radius:8px;margin-bottom:12px;font-size:14px;">
        ⚠ İnceleme yazabilmek için telefonunuzu doğrulamanız gerekiyor
      </div>

      <input type="text"
             id="userPhoneInput"
             placeholder="5XXXXXXXXX"
             style="width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;margin-bottom:12px;">

      <button id="sendOtpBtn"
        style="width:100%;padding:12px;background:#1b7d2f;color:#fff;border:none;border-radius:6px;">
        Kod Gönder
      </button>

    </div>

    <!-- STEP 2 -->
    <div id="userOtpStep" style="display:none;">

      <p style="font-size:14px;color:#555;margin-bottom:15px;">
        6 haneli kodu giriniz
      </p>

      <input type="text"
             id="userOtpInput"
             maxlength="6"
             placeholder="******"
             style="width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;margin-bottom:12px;text-align:center;font-size:18px;">

      <div id="userOtpError" style="
        display:none;
        background:#ffe5e5;
        color:#b30000;
        padding:10px;
        border-radius:6px;
        margin-bottom:10px;
        font-size:14px;
      "></div>

      <div style="text-align:left;margin-bottom:10px;">
        <label style="font-size:14px;">
          <input type="checkbox" id="userKvkkCheck">
          <a href="https://puandeks.com/kvkk" target="_blank">
            KVKK metnini
          </a> okudum, kabul ediyorum
        </label>
      </div>

      <button id="userVerifyOtpBtn"
        style="width:100%;padding:12px;background:#ccc;color:#fff;border:none;border-radius:6px;"
        disabled>
        Doğrula ve Devam Et
      </button>

      <div id="countdownText" style="font-size:14px;color:#999;margin-top:10px;">
        60 saniye kaldı
      </div>

    </div>

  </div>
</div>
<!-- USER OTP POPUP -->
	

	
<!-- COMMON SCRIPTS -->
<script src="js/common_scripts.js"></script>
<script src="js/functions.js"></script>


<!-- Close button -->
<script>
document.getElementById("closePopup").addEventListener("click", function () {
  if (document.referrer) {
    window.location.href = document.referrer; 
  } else {
    window.location.href = "index.php"; 
  }
});
</script>
<!-- Close button -->

<!-- The final unified review submission script -->
<script>
document.addEventListener("DOMContentLoaded", function () {

const mediaInput = document.getElementById("mediaInput");
const addBtn = document.getElementById("addMediaBtn");
const mediaPreview = document.getElementById("mediaPreview");

addBtn.addEventListener("click", () => mediaInput.click());

mediaInput.addEventListener("change", function () {

  const files = Array.from(this.files);
  const existingCount = mediaPreview.querySelectorAll("div").length - 1;

  let imageCount = 0;
  let videoCount = 0;

  for (let file of files) {
    if (file.type.startsWith("image/")) imageCount++;
    if (file.type.startsWith("video/")) videoCount++;
  }

  if ((imageCount + existingCount) > 5 || videoCount > 1) {
    alert("En fazla 5 fotoğraf ve 1 video yükleyebilirsiniz.");
    this.value = "";
    return;
  }

  files.forEach(file => {

    const reader = new FileReader();

    reader.onload = function (e) {

      const wrapper = document.createElement("div");
      wrapper.style.width = "120px";
      wrapper.style.height = "120px";
      wrapper.style.overflow = "hidden";
      wrapper.style.borderRadius = "8px";
      wrapper.style.display = "flex";
      wrapper.style.alignItems = "center";
      wrapper.style.justifyContent = "center";
      wrapper.style.background = "#f5f5f5";
      wrapper.style.position = "relative";

      if (file.type.startsWith("image/")) {

        const img = document.createElement("img");
        img.src = e.target.result;
        img.style.width = "120px";
        img.style.height = "120px";
        img.style.objectFit = "cover";
        wrapper.appendChild(img);

      } else if (file.type.startsWith("video/")) {

        const video = document.createElement("video");
        video.src = e.target.result;
        video.style.width = "120px";
        video.style.height = "120px";
        video.style.objectFit = "cover";
        video.controls = true;
        wrapper.appendChild(video);
      }

      const removeBtn = document.createElement("span");
      removeBtn.innerHTML = "×";
      removeBtn.style.position = "absolute";
      removeBtn.style.top = "5px";
      removeBtn.style.right = "8px";
      removeBtn.style.cursor = "pointer";
      removeBtn.style.background = "rgba(0,0,0,0.6)";
      removeBtn.style.color = "#fff";
      removeBtn.style.borderRadius = "50%";
      removeBtn.style.width = "20px";
      removeBtn.style.height = "20px";
      removeBtn.style.display = "flex";
      removeBtn.style.alignItems = "center";
      removeBtn.style.justifyContent = "center";
      removeBtn.style.fontSize = "14px";

      removeBtn.onclick = () => {
        wrapper.remove();
      };

      wrapper.appendChild(removeBtn);

      const addBtn = document.getElementById("addMediaBtn");
      mediaPreview.insertBefore(wrapper, addBtn);
    };

    reader.readAsDataURL(file);
  });

});

  const stars = document.querySelectorAll('#puanlama .star');
  const popup = document.getElementById("reviewConfirmPopup");
  const submitBtn = document.getElementById("sendReviewBtn");
  const titleInput = document.querySelector('input.form-control[name="title"]');
  const commentInput = document.querySelector('textarea.form-control[name="comment"]');
  let selectedRating = 0;

  submitBtn.classList.add("disabled");
  submitBtn.style.pointerEvents = "none";
  submitBtn.style.opacity = "0.5";

  const urlParams = new URLSearchParams(window.location.search);
  const companyId = urlParams.get('id');
  const newCompany = urlParams.get('new_company');
  const website = urlParams.get('website');
  const csrfToken = document.getElementById('csrf_token')?.value;

  // Decide which API to use
  const API_URL = companyId 
    ? "api/add-review.php" 
    : "api/add-new-company-review.php";

  stars.forEach(star => {
    star.addEventListener('click', function () {
      const puan = parseInt(this.dataset.value);
      selectedRating = puan;
      stars.forEach((s, i) => {
        s.style.backgroundColor = i < puan
          ? (puan === 1 ? 'red' :
             puan === 2 ? 'orange' :
             puan === 3 ? 'gold' :
             puan === 4 ? 'lightgreen' : 'green')
          : '#ccc';
      });
      checkFormStatus();
    });
  });

  function checkFormStatus() {
    const title = titleInput.value.trim();
    const comment = commentInput.value.trim();
    if (title && comment && selectedRating > 0) {
      submitBtn.classList.remove("disabled");
      submitBtn.style.pointerEvents = "auto";
      submitBtn.style.opacity = "1";
    } else {
      submitBtn.classList.add("disabled");
      submitBtn.style.pointerEvents = "none";
      submitBtn.style.opacity = "0.5";
    }
  }

  titleInput.addEventListener('input', checkFormStatus);
  commentInput.addEventListener('input', checkFormStatus);

  submitBtn.addEventListener("click", function (e) {
  e.preventDefault();

  const phoneVerified = <?= $phoneVerified ?>;

  if (phoneVerified !== 1) {
      document.getElementById("userOtpModal").style.display = "flex";
      return;
  }

    const rating = selectedRating;
    const title = titleInput.value.trim();
    const comment = commentInput.value.trim();

    if (!rating || !title || !comment) {
      alert("Lütfen puan verin, başlık ve yorum girin.");
      return;
    }

    const formData = new FormData();

    // Media files ekle
if (mediaInput && mediaInput.files.length > 0) {
  Array.from(mediaInput.files).forEach((file, index) => {
    formData.append("media[]", file);
  });
}

    // Existing company
    if (companyId) {
      formData.append("company_id", companyId);
    }

    // New company (from popup)
    if (newCompany) formData.append("new_company", newCompany);
    if (website) formData.append("website", website);

    formData.append("rating", rating);
    formData.append("title", title);
    formData.append("comment", comment);
    formData.append("csrf_token", csrfToken);

    fetch(API_URL, {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {

      if (data.redirect) {

    popup.querySelector("p").innerHTML = `
        <strong>İşletme eklendi ve incelemeniz alındı</strong><br><br>
        Yorumunuz kontrol sürecine alınmıştır.<br>
        Onay sonrası yayınlanacaktır.
    `;

    popup.style.display = "flex";

    setTimeout(() => {
        popup.style.display = "none";
        window.location.href = data.redirect;
    }, 2500);

    return;
}

      if (data.success) {
        popup.querySelector("p").textContent =
          `İşleminiz başarıyla tamamlandı.`;
        popup.style.display = "flex";

        setTimeout(() => {
          popup.style.display = "none";
          window.location.href = data.redirect ?? "/";
        }, 2500);

      } else {
        alert("Hata: " + data.message);
      }
    })
    .catch(() => alert("Bağlantı hatası."));
  });

  checkFormStatus();
});
</script>

<script>
const modal = document.getElementById("userOtpModal");

const phoneStep = document.getElementById("userPhoneStep");
const otpStep = document.getElementById("userOtpStep");

const phoneInput = document.getElementById("userPhoneInput");
const sendOtpBtn = document.getElementById("sendOtpBtn");

const otpInput = document.getElementById("userOtpInput");
const kvkkCheck = document.getElementById("userKvkkCheck");
const verifyBtn = document.getElementById("userVerifyOtpBtn");
const errorBox = document.getElementById("userOtpError");
const countdownText = document.getElementById("countdownText");

let timer;
let timeLeft = 60;


// TELEFON → OTP
sendOtpBtn.addEventListener("click", function () {

  const phone = phoneInput.value.trim();

  if (!phone) {
    alert("Telefon gir");
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

    phoneStep.style.display = "none";
    otpStep.style.display = "block";

    otpInput.value = "";
    kvkkCheck.checked = false;
    toggleBtn();

    timeLeft = 60;
    countdownText.textContent = timeLeft + " saniye";

    clearInterval(timer);
    timer = setInterval(() => {
      timeLeft--;
      countdownText.textContent = timeLeft + " saniye";

      if (timeLeft <= 0) {
        clearInterval(timer);
        countdownText.textContent = "Süre doldu";
      }
    }, 1000);

  });

});


// BUTTON
function toggleBtn() {
  if (otpInput.value.length === 6 && kvkkCheck.checked) {
    verifyBtn.disabled = false;
    verifyBtn.style.background = "#1b7d2f";
  } else {
    verifyBtn.disabled = true;
    verifyBtn.style.background = "#ccc";
  }
}

otpInput.addEventListener("input", toggleBtn);
kvkkCheck.addEventListener("change", toggleBtn);


// OTP VERIFY
verifyBtn.addEventListener("click", function () {

  const formData = new FormData();
  formData.append("otp", otpInput.value.trim());

  fetch("/api/verify-user-otp.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin"
  })
  .then(res => res.json())
  .then(data => {

    if (data.success) {

      clearInterval(timer);
      modal.style.display = "none";

      modal.style.display = "none";
      submitBtn.click();

    } else {
      errorBox.style.display = "block";
      errorBox.innerText = data.message;
    }

  });

});
</script>


</body>
</html>