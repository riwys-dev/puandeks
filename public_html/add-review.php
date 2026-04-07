<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

// C Control
if (isset($_SESSION['user_id']) && (($_SESSION['role'] ?? '') === 'business')) {
    echo "<script>
        alert('İşletmeler inceleme yazamaz.');
        if (document.referrer) {
            window.location.href = document.referrer;
        } else {
            window.location.href = 'https://puandeks.com/';
        }
    </script>";
    exit;
}

// CSRF token create
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// C ID
$company_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT name FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

$company_name = $company ? htmlspecialchars($company['name']) : "İşletme Bulunamadı";

// Phone verify
$phoneVerified = 0;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT phone_verified FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $phoneVerified = (int)$stmt->fetchColumn();
}
?>



<!DOCTYPE html>

<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Riwys">
    <title>
    <?php
      if (isset($_GET['new_company']) && !empty($_GET['new_company'])) {
          echo 'İnceleme gönder - "' . htmlspecialchars($_GET['new_company']) . '"';
      } elseif (isset($_GET['website']) && !empty($_GET['website'])) {
          echo 'İnceleme gönder - "' . htmlspecialchars($_GET['website']) . '"';
      } else {
          echo 'İnceleme gönder - "' . htmlspecialchars($company_name ?? 'İşletme Bulunamadı') . '"';
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
                              if (isset($_GET['new_company']) && !empty($_GET['new_company'])) {
                                  echo '"' . htmlspecialchars($_GET['new_company']) . '" için bir inceleme yaz';
                              } elseif (isset($_GET['website']) && !empty($_GET['website'])) {
                                  echo '"' . htmlspecialchars($_GET['website']) . '" için bir inceleme yaz';
                              } else {
                                  echo '"' . $company_name . '" hakkında bir inceleme yaz';
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

                      <div class="form-group" style="margin-top:20px;">
                        <label style="font-weight:600; font-size:16px;">
                          Fotoğraf veya Video Ekleyin (Opsiyonel)
                        </label>
                        <p style="font-size:13px; color:#888; margin-top:6px; margin-bottom:12px;">
                          En fazla 5 fotoğraf ve 1 video ekleyebilirsiniz • JPG, PNG, WEBP, MP4
                        </p>

                       <div id="mediaPreview" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">

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
                              margin-top:20px;
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
	  <h2>Teşekkür ederiz! Yorumunuz başarıyla alındı.</h2>
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

    <!-- ===================== -->
    <!-- STEP 1: TELEFON -->
    <!-- ===================== -->
    <div id="userPhoneStep">

      <p style="font-size:14px;color:#555;margin-bottom:15px;">
         <div style="background:#fff3cd;color:#856404;padding:12px 14px;border-radius:8px;margin-bottom:12px;font-size:14px;line-height:1.5;">
          ⚠ Puandeks’te işletmelere inceleme bırakabilmek için telefonunuzu doğrulamanız gerekmektedir.
        </div>
      </p>

      <input type="text"
             id="userPhoneInput"
             placeholder="5XXXXXXXXX"
             style="width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;margin-bottom:12px;">

      <button id="sendOtpBtn"
        style="width:100%;padding:12px;background:#1b7d2f;color:#fff;border:none;border-radius:6px;">
        Kod Gönder
      </button>

    </div>


    <!-- ===================== -->
    <!-- STEP 2: OTP -->
    <!-- ===================== -->
    <div id="userOtpStep" style="display:none;">

      <p style="font-size:14px;color:#555;margin-bottom:15px;">
        Telefonunuza gönderilen 6 haneli kodu giriniz.
      </p>

      <input type="text"
             id="userOtpInput"
             maxlength="6"
             placeholder="******"
             style="width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;margin-bottom:12px;text-align:center;font-size:18px;">

      <!-- HATA BOX -->
      <div id="userOtpError" style="
        display:none;
        background:#ffe5e5;
        color:#b30000;
        padding:10px;
        border-radius:6px;
        margin-bottom:10px;
        font-size:14px;
      "></div>

      <!-- KVKK -->
      <div style="text-align:left;margin-bottom:10px;">
        <label style="font-size:14px;color:#555;">
          <input type="checkbox" id="userKvkkCheck">
          <a href="https://puandeks.com/kvkk" target="_blank">
            KVKK metnini
          </a> okudum, kabul ediyorum
        </label>
      </div>

      <!-- BUTON -->
      <button id="userVerifyOtpBtn"
        style="width:100%;padding:12px;background:#ccc;color:#fff;border:none;border-radius:6px;cursor:not-allowed;"
        disabled>
        Doğrula ve Devam Et
      </button>

      <!-- COUNTDOWN -->
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
    window.location.href = document.referrer; // one step back
  } else {
    window.location.href = "https://puandeks.com"; // if referrer no
  }
});
</script>
<!-- Close button -->

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

    if (file.type.startsWith("image/")) {
        imageCount++;
    }

        if (file.type.startsWith("video/")) {
        videoCount++;
    }
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
        img.style.borderRadius = "8px";
        wrapper.appendChild(img);
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

   } else if (file.type.startsWith("video/")) {

        const video = document.createElement("video");
        video.src = e.target.result;
        video.style.width = "120px";
        video.style.height = "120px";
        video.style.objectFit = "cover";
        video.controls = true;
        wrapper.appendChild(video);
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

    }

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
  const draft = localStorage.getItem('review_draft');

if (draft) {
    const data = JSON.parse(draft);

    if (data.return_url === window.location.href) {
        titleInput.value = data.title || '';
        commentInput.value = data.comment || '';

        if (data.rating) {
            selectedRating = data.rating;
            stars.forEach((s, i) => {
                s.style.backgroundColor = i < selectedRating
                    ? (selectedRating === 1 ? 'red' :
                       selectedRating === 2 ? 'orange' :
                       selectedRating === 3 ? 'gold' :
                       selectedRating === 4 ? 'lightgreen' : 'green')
                    : '#ccc';
            });
        }

        checkFormStatus();
        localStorage.removeItem('review_draft');
    }
}


  submitBtn.classList.add("disabled");
  submitBtn.style.pointerEvents = "none";
  submitBtn.style.opacity = "0.5";

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

    const rating = selectedRating;
    const title = titleInput.value.trim();
    const comment = commentInput.value.trim();
    const companyId = new URLSearchParams(window.location.search).get('id');
    const csrfToken = document.getElementById('csrf_token')?.value;

    if (!rating || !title || !comment) {
      alert("Lütfen puan verin, başlık ve yorum girin.");
      return;
    }

    const phoneVerified = <?= $phoneVerified ?>;

    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

if (!isLoggedIn) {
    alert("Üye olmadan inceleme yazamazsınız.");
    window.location.href = "/login";
    return;
}

if (phoneVerified !== 1) {

    // draft kaydet (kalsın)
    localStorage.setItem('review_draft', JSON.stringify({
        company_id: companyId,
        rating: rating,
        title: title,
        comment: comment,
        return_url: window.location.href
    }));

    // OTP popup aç
    document.getElementById("userOtpModal").style.display = "flex";

    // countdown başlat
    let timeLeft = 60;
    const countdown = document.getElementById("countdownText");

    countdown.textContent = timeLeft + " saniye kaldı";

    const timer = setInterval(() => {
        timeLeft--;
        countdown.textContent = timeLeft + " saniye kaldı";

        if (timeLeft <= 0) {
            clearInterval(timer);
            countdown.textContent = "Kod süresi doldu";
        }
    }, 1000);

    return;
}

    const formData = new FormData();

    formData.append("company_id", companyId);
    formData.append("rating", rating);
    formData.append("title", title);
    formData.append("comment", comment);
    formData.append("csrf_token", csrfToken);
    Array.from(mediaInput.files).forEach(file => {
    formData.append("media[]", file);
  });


    fetch("api/add-review-submit.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.redirect) {
    localStorage.setItem('review_draft', JSON.stringify({
        company_id: companyId,
        rating: rating,
        title: title,
        comment: comment,
        return_url: window.location.href
    }));

    alert(data.message);
    window.location.href = data.redirect;
    return;
}


      if (data.success) {
        popup.querySelector("p").innerHTML = `
      <strong>Değerlendirmeniz İncelemeye Alınmıştır</strong><br><br>

      Göndermiş olduğunuz değerlendirme, Puandeks güven ve kalite politikaları
      kapsamında kontrol sürecine alınmıştır.<br>
      İnceleme süreci en geç 24 saat içerisinde sonuçlandırılır.<br><br>

      İnceleme sırasında gerekli görülmesi halinde, yorumunuzla ilgili destekleyici bilgi
      veya belge talep edilebilir. Bu uygulama, platformumuzdaki yorumların
      doğruluğunu ve güvenilirliğini sağlamak amacıyla yapılmaktadır.<br><br>

      Onay sürecinin tamamlanmasının ardından değerlendirme yayına alınacaktır.
    `;
        popup.style.display = "flex";

        setTimeout(() => {
          popup.style.display = "none";
          window.location.href = "/company/" + data.slug;
        }, 3000);
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
// =======================
// ELEMENTLER
// =======================
const modal = document.getElementById("userOtpModal");

// STEP
const phoneStep = document.getElementById("userPhoneStep");
const otpStep = document.getElementById("userOtpStep");

// PHONE
const phoneInput = document.getElementById("userPhoneInput");
const sendOtpBtn = document.getElementById("sendOtpBtn");

// OTP
const otpInput = document.getElementById("userOtpInput");
const kvkkCheck = document.getElementById("userKvkkCheck");
const verifyBtn = document.getElementById("userVerifyOtpBtn");
const errorBox = document.getElementById("userOtpError");
const countdownText = document.getElementById("countdownText");

let timer;
let timeLeft = 60;


// =======================
// STEP 1 → TELEFON GÖNDER
// =======================
sendOtpBtn.addEventListener("click", function () {
  if (sendOtpBtn.disabled) return;
  sendOtpBtn.disabled = true;

  const phone = phoneInput.value.trim();

  if (!phone) {
  alert("Telefon numarası giriniz.");
  sendOtpBtn.disabled = false;
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
  sendOtpBtn.disabled = false;
    if (!data.success) {
      alert(data.message);
      return;
    }

    // STEP CHANGE
    phoneStep.style.display = "none";
    otpStep.style.display = "block";

    // reset
    otpInput.value = "";
    kvkkCheck.checked = false;
    toggleOtpButton();

    // countdown başlat
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
// BUTON AKTİF/PASİF
// =======================
function toggleOtpButton() {
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

otpInput.addEventListener("input", toggleOtpButton);
kvkkCheck.addEventListener("change", toggleOtpButton);


// =======================
// OTP DOĞRULA
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

      // popup kapat
      modal.style.display = "none";

      // yorumu tekrar gönder
      location.reload();

    } else {

      errorBox.style.display = "block";
      errorBox.innerText = data.message || "Kod hatalı, tekrar deneyin.";

      otpInput.value = "";
      toggleOtpButton();
    }

  })
  .catch(() => {
    errorBox.style.display = "block";
    errorBox.innerText = "Sunucu hatası";
  });

});


// =======================
// MODAL KAPATMA
// =======================

// ESC
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    modal.style.display = "none";
    clearInterval(timer);
  }
});

// dış tıklama
modal.addEventListener("click", function (e) {
  if (e.target === modal) {
    modal.style.display = "none";
    clearInterval(timer);
  }
});

// X butonu
document.getElementById("closeUserOtpModal").addEventListener("click", function () {
  modal.style.display = "none";
  clearInterval(timer);
});
</script>


</body>
</html>