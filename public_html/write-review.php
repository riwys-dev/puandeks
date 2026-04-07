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

<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>İnceleme yaz</title>

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
	<link href="css/incelemeyaz.css" rel="stylesheet" >

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
<!-- APP SECTION CSS -->
<style>
.app-section {
  border: 1px solid #A8A8A8 !important;
  border-radius: 20px !important;
  background: #fff !important;
  overflow: hidden !important;
  padding: 30px !important;
}

.app-section > .container {
  background: transparent !important;
  border-radius: inherit !important;
  padding: 0 !important;
}

.app-section .app-buttons{
  display: flex;
}

/* MOBİL */
@media (max-width: 767px) {
  .app-section .app-buttons{
    flex-direction: column !important;
    align-items: center !important;
  }

  .app-section .app-buttons a + a{
    margin-top: 14px !important;
  }
}

/* DESKTOP */
@media (min-width: 768px) {
  .app-section .app-buttons{
    flex-direction: row !important;
  }

  .app-section .app-buttons a + a{
    margin-left: 16px !important;
  }
}
</style>
<!-- /APP SECTION CSS -->



  
  
</head>

<body>
		
	<div id="page">
		
<!-- header -->
<?php include 'header-main.php'; ?>
<!-- /header -->
      
<main>
        
<!-- hero_single -->
<section class="hero_single version_1">
  <div class="wrapper" style="background-color:#04DA8D !important;">
    <div class="container text-center">
      <h3 class="fw-bolder fs-2" style="padding-bottom:20px; color:#1C1C1C;">
        Deneyiminizi paylaşın
      </h3>

      <p class="fw-normal fs-6" style="padding-bottom:20px; color:#1C1C1C;">
        Başkalarının doğru seçimi yapmasına yardımcı olun.
      </p>

      <!-- Search Bar -->
      <div class="row justify-content-center position-relative">
        <div class="col-lg-9">
          <div class="search-bar">
            <div class="input-group">
              <input
                type="text"
                id="companySearchInput"
                name="q"
                class="form-control"
                placeholder="İnceleme için bir İşletme bulun"
                autocomplete="off"
              >
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
            </div>
            <div id="search-results" class="search-dropdown"></div>
          </div>
        </div>
      </div>
      <!-- /Search Bar -->

    </div>
  </div>
</section>
<!-- hero_single -->


        
            <div class="container margin_60_35">
                <div class="row">
				    <section class="app-section" style="border:3px solid #A8A8A8; border-radius:20px; background:#fff; overflow:hidden; padding:30px;">
                      <div class="app-content" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;">

                        <!-- Sol taraf: Metin -->
                        <div class="app-text" style="flex:1 1 300px; min-width:280px;">
                          <h2 style="font-size:24px; margin:0 0 10px 0; font-weight:600;">
                            Puandeks uygulamasıyla
                          </h2>
                          <p style="font-size:16px; line-height:1.5; margin:0 0 20px 0;">
                            İşletmeleri bulun, incelemeleri okuyun veya inceleme yazın, hem de hareket halindeyken.
                          </p>
                         <div class="app-buttons">
                            <a href="#">
                              <img src="img/core/app-store.svg"
                                   alt="Download from App Store"
                                   width="215"
                                   height="60">
                            </a>
                            <a href="#">
                              <img src="img/core/google-play.svg"
                                   alt="Download from Google Play"
                                   width="215"
                                   height="60">
                            </a>
                          </div>

                        </div>

                        <!-- Sağ taraf: Görsel -->
                        <div class="app-image" style="flex:0 0 auto; margin-left:20px; text-align:center;">
                          <img src="img/banners/mobile-app.svg" alt="Puandeks Mobile App" style="height:187px; width:auto; max-width:100%; display:block;">
                        </div>

                      </div>
                    </section>

			    </div>
			<!--/row-->
            </div>
            <!-- /container -->
  
 
    </main>
    <!-- /main -->

<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	
      
</div>

	
	<!-- COMMON SCRIPTS -->
    <script src="js/common_scripts.js"></script>
	<script src="js/functions.js"></script>
	<script src="assets/validate.js"></script>

   <!-- Search Scripts -->
    <script src="js/search-submit.js"></script>
    <script src="js/company-results.js"></script>
    <script src="js/search-live.js"></script>
    <!-- /Search Scripts -->


	
</body>
</html>