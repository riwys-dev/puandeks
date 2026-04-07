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
    <meta name="author" content="Riwys">
    <title>Puandeks İşletme Kategorileri</title>

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
  
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
		
<div id="page">
		

<!-- header -->
<?php include 'header-main.php'; ?>
<!-- /header -->

<!-- hero categories -->
<section class="hero_single version_1">
    <div class="wrapper" style="background-color: #9FF6D3 !important;">
        <div class="container">
            <h3 class="fw-bolder fs-2" style="padding-bottom: 20px;">
                Kategorilere göre arama yapın
            </h3>

            <p class="fw-normal fs-6" style="padding-bottom: 20px;">
                Her kategoriden markaları inceleyin, güvenilir değerlendirmelerle tercihinizi güçlendirin.
            </p>

<!-- Search Bar -->
<div class="row justify-content-center position-relative">
    <div class="col-lg-9">
        <div class="search-bar">

            <div id="category-warning" style="color:red; font-size:13px; margin-bottom:8px; display:none;"></div>

            <div class="input-group">
                <input 
                    type="text" 
                    id="companySearchInput" 
                    name="q" 
                    class="form-control" 
                    placeholder="Kategori arayın" 
                    autocomplete="off"
                >
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>

            <!-- Dropdown -->
            <div id="search-results" class="search-dropdown"></div>
        </div>
    </div>
</div>
<!-- /Search Bar -->

        </div>
    </div>
</section>
<!-- hero categories -->



	<main>

		
		
		<!-- /container -->
		
		<div class="bg_color_1">
			<div class="container margin_60_35">
				<div class="main_title_3 text-center">
					<h2 style="font-weight: 700 !important;">Puandeks İşletme Kategorileri</h2>
					<p>İşletmeleri doğrulanmış yorumlar ile değerlendirin.</p>
				</div>
              
              
				<div class="row justify-content-center">
					<div class="col-xl-10 col-lg-12">
						<div class="all_categories clearfix add_bottom_30">
                          <ul>
                              <?php
                             $stmt = $pdo->query("
                                SELECT id, name, icon_class, slug
                                FROM categories
                                ORDER BY name ASC
                            ");

                            while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $id   = (int)$cat['id'];
                                $name = htmlspecialchars($cat['name'], ENT_QUOTES);
                                $icon = htmlspecialchars($cat['icon_class'], ENT_QUOTES);

                                echo '
                                <li>
                                    <a href="/company-search?category=' . $cat['slug'] . '" 
                                    data-id="' . $id . '" 
                                    data-name="' . $name . '"
                                    style="display:flex;align-items:center;gap:10px;">
                                    
                                        <i class="' . $icon . '" style="min-width:18px;"></i>
                                        <span>' . $name . '</span>

                                    </a>
                                </li>';
                            }

                              ?>
                          </ul>
                      </div>

					</div>
				</div>
			</div>
			<!-- /container -->
		</div>
		<!-- /bg_color_1 -->
		
	
		<!-- /call_section_2 -->
	</main>
	<!-- /main -->

<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	
  
</div>
<!-- page -->
	
	

	
	<!-- COMMON SCRIPTS -->
    <script src="js/common_scripts.js"></script>
	<script src="js/functions.js"></script>
	<script src="assets/validate.js"></script>
    <script src="js/search-categories.js"></script>


</body>
</html>