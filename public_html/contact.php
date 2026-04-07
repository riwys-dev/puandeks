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
	<title>Bize ulaşın - Puandeks </title>

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
      
      <!-- Başlık ve alt başlık -->
      <div style="background-color:#f9f7f3; padding:40px 20px; text-align:center; margin-top:72px">
        <h1 style="font-size:32px; font-weight:600; margin:0; color:#000;">Bize Ulaşın</h1>
        <p style="font-size:20px; color:#555; margin-top:8px;">İletişime geçmek ister misiniz? Bize nasıl ulaşabileceğinizi öğrenin</p>
      </div>

    <main style="background-color:#f9f7f3; padding:40px 20px; font-family:Arial, sans-serif; margin-bottom:100px;">



      <!-- Alt Başlık -->
      <div style="max-width:1000px; margin:0 auto;">
        <h2 style="font-size:24px; font-weight:600; margin-bottom:24px; color:#000;">Nasıl yardımcı olabiliriz?</h2>
         
        <!-- Boxes -->
          <div style="display:flex; gap:20px; flex-wrap:wrap;">

            <!-- Incelemeler ve Yasal -->
            <div style="flex:1; min-width:280px; border:1px solid #D0D0D0; border-radius:12px; background:#fff; padding:28px;">
                <h3 style="font-size:20px; font-weight:600; margin:0 0 12px 0; color:#000;">
                  İncelemeler ve Yasal
                </h3>
                <p style="font-size:16px; line-height:1.5; color:#444; margin:0 0 20px 0;">
                  Bir inceleme hakkında sorunuz mu var, kötüye kullanım bildirmek mi istiyorsunuz ya da
                  yasal veya gizlilik ilgili bir konuda yardıma mı ihtiyacınız var?
                </p>
                <a href="contact-legal"
                 style="display:inline-block; font-size:15px; font-weight:700; color:#1a73e8;
                 border:1px solid #1a73e8; background:#fff; padding:8px 16px; border-radius:20px;
                 text-decoration:none; transition:all 0.2s ease;">
                 Devam et 
               </a>
            </div>

            <!-- Teknik Destek -->
            <div style="flex:1; min-width:280px; border:1px solid #D0D0D0; border-radius:12px; background:#fff; padding:28px;">
                <h3 style="font-size:20px; font-weight:600; margin:0 0 12px 0; color:#000;">
                  Teknik Destek
                </h3>
                <p style="font-size:16px; line-height:1.5; color:#444; margin:0 0 20px 0;">
                  Kurulum veya oturum açma sorunları gibi teknik yardımlar için bu yolu kullanın.
                </p>
                <a href="contact-technical"
                 style="display:inline-block; font-size:15px; font-weight:700; color:#1a73e8;
                 border:1px solid #1a73e8; background:#fff; padding:8px 16px; border-radius:20px;
                 text-decoration:none; transition:all 0.2s ease;">
                 Devam et ↗
               </a>
            </div>

            <!-- Satıs ve Fiyatlandirma -->
            <div style="flex:1; min-width:280px; border:1px solid #D0D0D0; border-radius:12px; background:#fff; padding:28px;">
                <h3 style="font-size:20px; font-weight:600; margin:0 0 12px 0; color:#000;">
                  Satış ve Fiyatlandırma
                </h3>
                <p style="font-size:16px; line-height:1.5; color:#444; margin:0 0 20px 0;">
                  Planlarımız, yükseltmelerimiz, hizmetlerimiz ve daha fazlası hakkında bilgi alın.
                </p>
                <a href="contact-sales"
                 style="display:inline-block; font-size:15px; font-weight:700; color:#1a73e8;
                 border:1px solid #1a73e8; background:#fff; padding:8px 16px; border-radius:20px;
                 text-decoration:none; transition:all 0.2s ease;">
                 Satış Ekibiyle İletişime Geçin ↗
               </a>
            </div>

          </div>


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

</body>
</html>
