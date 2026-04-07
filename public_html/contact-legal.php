<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
  session_start();
}

if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
  $_SESSION['user_id'] = $_COOKIE['user_id'];
}

if (isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
  require_once('/home/puandeks.com/backend/config.php');

  // 1. Tüketici mi kontrol et
  $stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['profile_photo'] = '';
  } else {
    // 2. İşletme mi kontrol et
    $stmt = $pdo->prepare("SELECT name, email FROM companies WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($company) {
      $_SESSION['role'] = 'business';
      $_SESSION['name'] = $company['name'];
      $_SESSION['email'] = $company['email'];
      $_SESSION['profile_photo'] = '';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<title>İncelemeler ve Yasal Destek - Puandeks</title>

  <!-- Favicons-->
  <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/apple-touch-icon-114x114-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/apple-touch-icon-144x144-precomposed.png">

	<!-- GOOGLE WEB FONT -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

	<!-- BASE CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/vendors.css" rel="stylesheet">

	<!-- YOUR CUSTOM CSS -->
	<link href="css/custom.css" rel="stylesheet">
	<link href="css/contact.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="bg_color_1">

	<div id="page">

    <!-- header -->
    <?php include 'header-main.php'; ?>
    <!-- /header -->
      
      <!-- Başlik ve alt baslik -->
      <div style="background-color:#f9f7f3; padding:40px 20px; text-align:center; margin-top:72px">
        <h1 style="font-size:32px; font-weight:600; margin:0; color:#000;">İncelemeler ve Yasal</h1>
        <p style="font-size:20px; color:#555; margin-top:8px;">İncelemeler, kötüye kullanım bildirimi ve yasal konularla ilgili destek alın.</p>
      </div>

    <main style="background-color:#f9f7f3; padding:40px 20px; font-family:Arial, sans-serif; margin-bottom:100px;">


    <div style="max-width:1000px; margin:0 auto;">
        
        <!-- Baslik ve alt baslik-->
        <h2 style="font-size:24px; font-weight:600; margin-bottom:24px; color:#000;">
            İncelemeler ve Yasal Destek
        </h2>

        <!-- Aciklama -->
        <p style="font-size:16px; line-height:1.6; color:#444; margin-bottom:32px;">
            Destek talebinizi buradan iletebilirsiniz.
        </p>

            <!-- ==== FORM ==== -->
            <form id="legalForm" style="background:#fff; padding:28px; border:1px solid #D0D0D0; border-radius:12px;">


                <?php $_SESSION['csrf'] = bin2hex(random_bytes(32)); ?>
                <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                <input type="hidden" name="form_type" value="legal">

                <label style="font-size:15px; font-weight:600; display:block; margin-bottom:8px;">
                    Konu
                </label>

                <div id="topicDropdown"
                    style="position:relative; width:100%; margin-bottom:20px;">

                    <div id="topicSelected"
                        style="padding:12px 16px; border:1px solid #ccc; border-radius:8px; cursor:pointer; background:#fff;">
                        Bir konu seçin
                    </div>

                    <ul id="topicOptions"
                        style="display:none; position:absolute; top:48px; left:0; width:100%; background:#fff; border:1px solid #ccc; border-radius:8px; max-height:200px; overflow-y:auto; z-index:1000;">
                        <li data-value="kotuye-kullanim" style="padding:12px; cursor:pointer;">Kötüye kullanım bildirimi</li>
                        <li data-value="inceleme-itiraz" style="padding:12px; cursor:pointer;">İnceleme hakkında itiraz</li>
                        <li data-value="gizlilik-talebi" style="padding:12px; cursor:pointer;">Gizlilik / veri talebi</li>
                        <li data-value="yasal-destek" style="padding:12px; cursor:pointer;">Yasal destek talebi</li>
                    </ul>

                    <input type="hidden" name="topic" id="topicInput">
                </div>

                <label style="font-size:15px; font-weight:600; display:block; margin-bottom:6px;">
                    Açıklama
                </label>

                <textarea name="message"
                    style="width:100%; height:160px; padding:12px; border:1px solid #ccc; border-radius:8px; margin-bottom:20px;"
                    placeholder="Durumu detaylı şekilde açıklayın"></textarea>

                <button type="submit"
                    style="background:#0F5F4A; color:#fff; font-weight:600; padding:12px 26px; border:none; border-radius:8px; cursor:pointer;">
                    Gönder
                </button>

            </form>
            <!-- ==== FORM ==== -->



    </div>

    </main>

    <!-- FOOTER -->	
    <?php include('footer-main.php'); ?>
    <!-- FOOTER -->	

	</div>


	<!-- COMMON SCRIPTS -->
	<script src="js/common_scripts.js"></script>
	<script src="js/functions.js"></script>
	<script src="assets/validate.js"></script>
	<script src="js/tabs.js"></script>
	<script>new CBPFWTabs(document.getElementById('tabs'));</script>


<!-- DROPDOWN JS -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const box = document.getElementById("topicDropdown");
    const selected = document.getElementById("topicSelected");
    const list = document.getElementById("topicOptions");
    const input = document.getElementById("topicInput");

    // Aç / kapa
    selected.addEventListener("click", () => {
        list.style.display = list.style.display === "block" ? "none" : "block";
    });

    // Seçim yapılınca
    list.querySelectorAll("li").forEach(li => {
        li.addEventListener("click", () => {
            selected.textContent = li.textContent;
            input.value = li.getAttribute("data-value");
            list.style.display = "none";
        });
    });

    // Dışar tıklayınca kapanması
    document.addEventListener("click", (e) => {
        if (!box.contains(e.target)) {
            list.style.display = "none";
        }
    });

});
</script>
<!-- DROPDOWN JS -->

<!-- ALERT -->
<script>
document.getElementById("legalForm").addEventListener("submit", function(e) {

    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch("/api/contact-forms.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Talebiniz başarıyla gönderildi.");
            form.reset();
            document.getElementById("topicSelected").textContent = "Bir konu seçin";
        } else {
            alert(data.message);
        }
    })
    .catch(() => {
        alert("Bir hata oluştu. Lütfen tekrar deneyin.");
    });

});
</script>
<!-- ALERT -->

</body>
</html>
