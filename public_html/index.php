<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('/home/puandeks.com/backend/config.php');

try {
    $stmt = $conn->query("
        SELECT id, name, slug, icon_class
        FROM categories
        WHERE icon_class IS NOT NULL AND icon_class != ''
        ORDER BY RAND()
        LIMIT 8
    ");
    $homeCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $homeCategories = [];
}

// If cookie login
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

// User / company to session
if (isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
    // Check user
    $stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['profile_photo'] = '';
    } else {
        // Check company
        $stmt = $conn->prepare("SELECT name, email FROM companies WHERE id = ?");
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

// --- visitor_activity ---
$companiesData = [];
$visitorId = $_COOKIE['visitor_id'] ?? null;

if ($visitorId) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                c.id,
                c.name,
                c.slug,
                c.website,
                c.logo,
                COALESCE(AVG(r.rating), 0) AS avg_rating,
                COUNT(DISTINCT r.id) AS review_count
            FROM visitor_activity va
            JOIN companies c ON va.company_id = c.id
            LEFT JOIN reviews r 
                ON r.company_id = c.id 
                AND r.status = 1 
                AND r.parent_id IS NULL
            WHERE va.visitor_id = ?
            GROUP BY c.id
            ORDER BY va.viewed_at DESC
            LIMIT 4
        ");
        $stmt->execute([$visitorId]);
        $companiesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $companiesData = [];
    }
}

// --- SEO ---
try {
    $stmt = $conn->prepare("SELECT * FROM seo_meta WHERE page_type = 'homepage' AND page_id = 0 LIMIT 1");
    $stmt->execute();
    $seo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $seo = null;
}
?>


<!DOCTYPE html>
<html lang="tr">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
  <?php
  $canonical = !empty($seo['canonical_url']) ? $seo['canonical_url'] : 'https://puandeks.com';

  $og_title = !empty($seo['og_title']) ? $seo['og_title'] : ($seo['meta_title'] ?? 'Puandeks');
  $og_description = !empty($seo['og_description']) ? $seo['og_description'] : ($seo['meta_description'] ?? '');
  $og_image = !empty($seo['og_image']) ? $seo['og_image'] : 'https://puandeks.com/img/og-default.jpg';
  $og_url = !empty($seo['og_url']) ? $seo['og_url'] : $canonical;
  ?>

  <title><?= htmlspecialchars($seo['meta_title'] ?? 'Puandeks') ?></title>
  <meta name="description" content="<?= htmlspecialchars($seo['meta_description'] ?? '') ?>">
  <meta name="keywords" content="<?= htmlspecialchars($seo['meta_keywords'] ?? '') ?>">

  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

  <meta property="og:title" content="<?= htmlspecialchars($og_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($og_description) ?>">
  <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($og_url) ?>">
  <meta property="og:type" content="website">




	<!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

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
  
	<!-- CUSTOM CSS -->
	<link href="css/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="css/cookie.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


	<style>
		/* SVG starts */
		.rating .custom-star {
		  height: 22px !important;
		  width: auto !important;
		  margin-right: 2px !important;
		  display: inline-block !important;
		  vertical-align: middle !important;
		  padding: 0 !important;
		}
		
		.rating {
		  display: inline-flex !important;
		  align-items: center !important;
		  line-height: 1 !important;
		  gap: 0 !important;
		}
		
		
		.rating em {
		  margin-left: 6px !important;
		  font-size: 14px !important;
		  color: #2E9D3E !important;
		  font-weight: 600 !important;
		}
		</style>

      <!-- Last reviews -->
      <style>
/* Last reviews */
.review-card {
  flex: 0 0 calc(100% / 3 - 15px); 
  height: 240px;                  
}

@media (max-width: 991px) {
  .review-card { 
    flex: 0 0 calc(100% / 2 - 15px); 
    height: 240px;                  
  }
}

@media (max-width: 767px) {
  .slider-track {
    display: flex !important;       
    transform: translateX(0);       
  }
  .review-card { 
    flex: 0 0 100% !important;      
    max-width: 100%;
    height: 240px;                  
  }
  .nav-arrow {
    font-size:20px;                 
  }

  .last-reviews-header {
    flex-direction: column;   
    align-items: flex-start;  
    gap: 10px;                
  }
  .last-reviews-header h2,
  .last-reviews-header h4 {
    text-align: left;        
  }
}


.nav-arrow {
  font-size: 28px;
}

.best-companies-section {
  padding: 40px 15px; 
}

      </style>
      <!-- Last reviews -->

<!-- Info Section -->
<style>
.info-text { 
  padding-left: 15px !important; 
}
.info-btn { 
  padding-right: 15px !important; 
}


.container.info-section {
  max-width: 1175px !important; 
  margin: 0 auto !important;
  padding-left: 50px !important;
  padding-right: 50px !important;
  box-sizing: border-box !important;
}

/* Ne Yapıyoruz? */
.info-btn a {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 160px !important;   
  height: 44px !important;   
  border-radius: 30px !important;
  font-weight: 600 !important;
  font-size: 15px !important;
  text-decoration: none !important;
  background: #064e3b !important;
  color: #fff !important;
  transition: background-color 0.3s;
  padding: 0 !important; 
}
.info-btn a:hover {
  background-color: #046c4e !important;
}

/* Mobile */
@media (max-width: 768px) {
  .info-text { 
    padding-left: 15px !important; 
  }
  .info-btn { 
    padding-right: 15px !important; 
  }
  .container.info-section {
    padding-left: 20px !important;
    padding-right: 20px !important;
  }
}
</style>
<!-- Info Section -->


  
 <!-- Before and Last review -->
  <style>
    .company-card {
      border: 1px solid #A8A8A8 !important;
      border-radius: 8px; 
      padding: 15px;
    }
    </style>


<style>
.best-companies-section,
.last-reviews {
  padding-left: 15px !important;
  padding-right: 15px !important;
  box-sizing: border-box;
}
</style>
 <!-- Before and Last review -->
 

<!-- App banner --> 
<style>
/* Ortak */
section[style*="04DA8D"] .app-buttons {
  display: flex;
}

/* Mobil (max 767px) */
@media (max-width: 767px) {
  section[style*="04DA8D"] > div {
    flex-direction: column !important; 
    align-items: center !important;
    text-align: center !important;
  }

  section[style*="04DA8D"] .app-image {
    margin-top: 32px !important;
    margin-left: 0 !important;
    text-align: center !important;
  }

  section[style*="04DA8D"] .app-buttons {
    flex-direction: column !important;
    align-items: center !important;
    margin-top: 24px !important;
    gap: 14px !important;
  }
}

/* Tablet + Desktop (768px+) */
@media (min-width: 768px) {
  section[style*="04DA8D"] > div {
    flex-direction: row !important; 
    align-items: center !important;
    justify-content: space-between !important;
    text-align: left !important;
  }

  section[style*="04DA8D"] .app-image {
    margin-left: 20px !important;
    text-align: right !important;
  }

  section[style*="04DA8D"] .app-buttons {
    flex-direction: row !important;
    gap: 16px !important;
    margin-top: 18px !important;
  }
}
</style>
<!-- /App banner -->


<!-- /Info Section 1 -->
<style>
/* Desktop */
.info-section {
  position: relative !important;
}
.info-section div[style*='position:absolute'] {
  opacity:1 !important;
  right:40px;
  bottom:10px;
}
.info-section div[style*='position:absolute'] img {
  height:100px !important;
}

/* Mobile */
@media (max-width:768px){
  .info-section div[style*='position:absolute']{
    left:50% !important;
    right:auto !important;
    bottom:10px !important;
    transform:translateX(-50%) !important;
  }
  .info-section div[style*='position:absolute'] img{
    height:70px !important;
  }
}
</style>
<!-- /Info Section 1 -->



</head>

<body>

	<div id="page">

      
<!-- header -->
<?php include 'header-main.php'; ?>
<!-- /header -->  



<main>

			<!-- hero_single -->
			<section class="hero_single version_1">
				<div class="wrapper" style="background-color: #fcfbf3 !important;">
					<div class="container">
						<h3 class="fw-bolder fs-2" style="padding-bottom: 20px;">Güvenebileceğiniz İşletmeleri Keşfedin
						</h3>
						<p class="fw-normal fs-6" style="padding-bottom: 20px;">İşletmeler hakkında gerçek yorumlarla
							güvenilir kararlar alın</p>
						
						<!-- Search Bar --> 
							<div class="row justify-content-center position-relative">
								<div class="col-lg-9">
									<div class="search-bar">
										<div class="input-group">
											<input type="text" id="companySearchInput" name="q" class="form-control" placeholder="İşletme ve Kategori arayın" autocomplete="off">
											<span class="input-group-text"><i class="fas fa-search"></i></span>
										</div>
										<!-- Arama sonuçları dropdown -->
										<div id="search-results" class="search-dropdown"></div>
									</div>
								</div>
							</div>
            <!-- Search Bar-->

						</div>
					</div>
			</section>
			<!-- hero_single -->

<!-- + Add Review -->
    <div style="text-align:center; margin:30px auto; padding:15px 20px; border:1px solid #ddd; border-radius:50px; max-width:600px; font-size:16px; background:#fff;">
      Son zamanlarda bir şey mi satın aldınız? 
      <a href="write-review" style="color:#04DA8D; font-weight:bold; text-decoration:none; margin-left:5px;">
        Bir değerlendirme yazın 
      </a>
    </div>
<!-- + Add Review -->

<!-- KATEGORİLER -->
<?php if (!empty($homeCategories)): ?>
<div style="max-width:1200px;margin:40px auto 50px auto;padding:0 16px;">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
    <h2 style="font-size:20px;font-weight:700;color:#1C1C1C;margin:0;">
      Kategoriler
    </h2>
    <a href="categories"
       style="font-size:13px;font-weight:600;color:#064e3b;text-decoration:none;">
      Tümünü Gör
    </a>
  </div>

  <div class="home-categories-grid">
    <?php foreach ($homeCategories as $cat): ?>
      <a href="/company-search?category=<?= htmlspecialchars($cat['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         class="home-category-item">

        <i class="<?= htmlspecialchars($cat['icon_class'], ENT_QUOTES, 'UTF-8') ?>"></i>

        <span>
          <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
        </span>
      </a>
    <?php endforeach; ?>
</div>


</div>

<style>
/* GRID */
.home-categories-grid{
  display:grid;
  grid-template-columns:repeat(8,1fr);
  gap:12px;
}

/* ITEM */
.home-category-item{
  text-decoration:none;
  color:#1C1C1C;
  border:1px solid #e5e7eb;
  border-radius:12px;
  padding:12px 8px;
  min-height:92px;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  text-align:center;
}

/* ICON */
.home-category-item i{
  font-size:22px;
  margin-bottom:6px;
  color:#064e3b;
}

/* TEXT */
.home-category-item span{
  font-size:12.5px;
  font-weight:600;
  line-height:1.2;
  word-break:normal;
  overflow-wrap:break-word;
}

/* TABLET */
@media (max-width: 992px){
  .home-categories-grid{
    grid-template-columns:repeat(4,1fr);
  }
}

/* MOBILE */
@media (max-width: 480px){
  .home-categories-grid{
    grid-template-columns:repeat(2,1fr);
  }
  .home-category-item{
    min-height:88px;
  }
}
</style>

<?php endif; ?>
<!-- KATEGORİLER -->


<!-- Info Section 1 -->
<div class="container info-section" style="position:relative; background-color:#42A4FD; border-radius:20px; padding-top:25px; padding-bottom:25px; overflow:hidden; margin-top:50px; margin-bottom:60px;">
  
  <!-- Starts -->
  <div style="position:absolute; right:40px; bottom:10px; display:flex; align-items:flex-end; gap:10px; opacity:0.35;">
    <img src="https://puandeks.com/img/core/star_blue.png" alt="star" style="height:56px; width:auto;">
  </div>

  <div class="row align-items-center">
    
    <!-- Title -->
    <div class="col-md-9 text-md-start text-center mb-3 mb-md-0 info-text" style="position:relative; z-index:2;">
      <h2 style="font-size:20px; font-weight:700; color:#1C1C1C; margin-bottom:10px;">Markanızı büyütmeye hazır mısınız?</h2>
      <p style="font-size:16px; line-height:1.6; margin:0; color:#1C1C1C;">
        İtibarınızı güçlendirin, işinizi bir adım öne taşıyın.
      </p>
    </div>

    <!-- Button -->
    <div class="col-md-3 d-flex align-items-center justify-content-md-end justify-content-center" style="position:relative; z-index:2;">
      <button 
        onclick="window.location.href='https://business.puandeks.com'" 
        style="background-color:#1C1C1C; color:#fff; font-weight:600; border:none; border-radius:30px; padding:10px 28px; cursor:pointer; transition:all 0.3s ease;"
        onmouseover="this.style.backgroundColor='#484848'; this.style.color='#fff';"
        onmouseout="this.style.backgroundColor='#1C1C1C'; this.style.color='#fff';">
        Başlayın
      </button>
    </div>
  </div>
</div>
<!-- /Info Section 1 -->






<!-- Review -->
<section class="review-section" style="padding:40px 0;">
  <div class="review-container" 
     style="display:flex;align-items:center;justify-content:space-between;gap:30px;flex-wrap:wrap;
            background-color:#F1E5DA !important; border-radius:12px; padding:30px;">
    
    <div class="review-text" style="flex:1; min-width:280px; text-align:left;">
      <h2 style="margin-bottom:10px;">Milyonlarca tüketiciye rehberlik edin</h2>
      <p style="margin-bottom:20px;"> Deneyimlerinizi paylaşarak topluluğumuzun doğru seçimler yapmasına yardımcı olun.</p>
      
      <div class="review-actions" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">

        <button class="login-button" 
                onclick="window.location.href='login'"
                style="padding:10px 20px;flex-shrink:0;">
          Giriş Yapın / Kayıt olun
        </button>

        <div class="social-login" style="display:flex;gap:20px;">
          <a href="login"><img src="img/banners/google-logo.svg" alt="Google" style="height:32px;"></a>
          <a href="login"><img src="img/banners/facebook-logo.svg" alt="Facebook" style="height:32px;"></a>
          <a href="login"><img src="img/banners/apple-logo.svg" alt="Apple" style="height:32px;"></a>
        </div>
      </div>
    </div>

    <div class="review-images" 
         style="flex:1; min-width:280px; overflow:hidden;border-radius:12px;max-width:560px;margin:0 auto;">
      <img src="img/home/banner_1.webp" alt="Review" 
           style="width:100%;display:block;transform:translateY(100px);opacity:0;
                  animation:slideUp 0.5s ease-out forwards;">
    </div>
  </div>

  <style>
    @keyframes slideUp {
      to { transform: translateY(0); opacity: 1; }
    }
    /* Mobile  */
    @media (max-width: 767px) {
      .review-container { flex-direction:column !important; }
      .review-text { text-align:center !important; }
      .review-actions { flex-direction:column !important; gap:15px !important; }
      .social-login { justify-content:center !important; gap:20px !important; }
    }
  </style>
</section>
<!-- Review -->




<br>

<!-- Last reviews -->
<section class="best-companies-section" style="background:#ffffff; padding:40px 0; margin-bottom:40px;">
  
  <!-- Header + Navigasyon Arrows -->
<div class="last-reviews-header" 
     style="margin-bottom:20px;">

  <div style="display:flex; align-items:center; justify-content:space-between;">
    <h2 class="section-title" style="margin:0;">Son Değerlendirmeler</h2>
    <div>
      <button id="prevBtn" class="nav-arrow" style="background:none; border:none; cursor:pointer;">&#10094;</button>
      <button id="nextBtn" class="nav-arrow" style="background:none; border:none; cursor:pointer;">&#10095;</button>
    </div>
  </div>

  <h4 style="font-size:16px; font-weight:normal; color:#555; margin:10px 0 0 0;">
    Değerli deneyimlerinizi paylaşın, güveni büyüten bu ekosistemin bir parçası olun.
  </h4>
</div>

<?php
try {
    $stmt = $conn->query("
    SELECT 
    reviews.rating,
    reviews.title,
    reviews.comment,
    reviews.created_at,

    users.id AS user_id,
    users.name AS user_name,
    users.surname AS user_surname,
    COALESCE(users.profile_image, '') AS profile_image,

    (
        SELECT COUNT(*) 
        FROM reviews r2 
        WHERE r2.user_id = users.id 
        AND r2.status = 1
        AND r2.parent_id IS NULL
    ) AS approved_review_count,

    companies.name AS company_name,
    companies.slug AS company_slug,
    reviews.company_id

FROM reviews
JOIN users ON reviews.user_id = users.id
JOIN companies ON reviews.company_id = companies.id

WHERE reviews.status = 1
AND reviews.parent_id IS NULL

ORDER BY reviews.created_at DESC
LIMIT 6;
");


    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $reviews = [];
}
?>


<!-- Slider -->
<div id="reviewSlider" style="overflow:hidden;">
  <div class="slider-track" style="display:flex; transition:transform 0.5s ease; gap:20px;">
    <?php foreach ($reviews as $review): ?>

    <?php
    $first = trim($review['user_name']);
    $surname = trim($review['user_surname'] ?? '');
    $surnameInitial = $surname !== '' ? mb_strtoupper(mb_substr($surname, 0, 1)) . '.' : '';
    $displayName = trim("$first $surnameInitial");


  $profileImage = trim($review['profile_image']);
    $hasImage = false;
    $imageUrl = '';

    if ($profileImage !== '') {
        if (strpos($profileImage, 'http') === 0) {
            $imageUrl = $profileImage;
            $hasImage = true;
        } elseif (strpos($profileImage, 'uploads/') === 0) {
            $imageUrl = "https://puandeks.com/" . $profileImage;
            $hasImage = true;
        } else {
            $imageUrl = "https://puandeks.com/uploads/users/" . $profileImage;
            $hasImage = true;
        }
    }

$firstLetter = mb_strtoupper(mb_substr(trim($review['user_name']), 0, 1));

// --- ROZET ---
$count = intval($review['approved_review_count']);
$badgeLabel = '';
$badgeColor = '';

if ($count >= 500) { 
    $badgeLabel = 'Lider'; 
    $badgeColor = '#D14B00'; 
}
elseif ($count >= 100) { 
    $badgeLabel = 'Elite'; 
    $badgeColor = '#AA00FF'; 
}
elseif ($count >= 50) { 
    $badgeLabel = 'Uzman'; 
    $badgeColor = '#0066FF'; 
}
elseif ($count >= 10) { 
    $badgeLabel = 'Yeni'; 
    $badgeColor = '#1B7D2F'; 
}
?>

<div class="review-card" 
     style="background:#fff; border:1px solid #A8A8A8; border-radius:12px;
            padding:15px; display:flex; flex-direction:column; justify-content:space-between;">

  <div style="display:flex; align-items:flex-start; gap:10px;">

    <div style="position:relative; width:50px; display:flex; flex-direction:column; align-items:center;">

      <a href="user-profile?id=<?= $review['user_id'] ?>"
         style="text-decoration:none;color:inherit;display:inline-block;position:relative;">

        <?php if ($hasImage && strpos($imageUrl, 'googleusercontent') === false): ?>
            <img src="<?= $imageUrl ?>" alt="user"
                 style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
        <?php else: ?>
            <div style="
                width:40px;
                height:40px;
                border-radius:50%;
                background:#05462F;
                color:#ffffff;
                display:flex;
                align-items:center;
                justify-content:center;
                font-weight:bold;
                font-size:18px;
            ">
                <?= $firstLetter ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($badgeLabel)): ?>
        <span style="
            position:absolute;
            top:-4px;
            right:-4px;
            background:<?= $badgeColor ?>;
            color:#fff;
            font-size:8px;
            font-weight:bold;
            padding:2px 5px;
            border-radius:10px;
        ">
            <?= $badgeLabel ?>
        </span>
        <?php endif; ?>

      </a>

          
            <a href="user-profile?id=<?= $review['user_id'] ?>"
               style="font-weight:bold;font-size:12px;margin-top:4px;display:block;
                      text-decoration:none;color:#333;">
                <?= htmlspecialchars($displayName) ?>
            </a>

        </div>


        <!-- Stars + puan + C -->
        <div style="flex:1;">
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
            <?php
              $rating = intval($review['rating']);
              echo '<img src="img/core/vote_' . $rating . '.svg" alt="rating" style="height:22px;">';
            ?>
            <span style="font-size:20px;font-weight:bold;color:#000;"><?= htmlspecialchars($review['rating']) ?></span>
          </div>
          <div style="font-size:12px;color:#999;"><?= htmlspecialchars($review['company_name']) ?></div>
        </div>
      </div>

      <!-- Yorum -->
      <div style="margin-top:8px; flex-grow:1;">
        <strong style="display:block; font-size:14px; margin-bottom:4px;">
          <?= htmlspecialchars($review['title']) ?>
        </strong>
        <p style="margin:0;font-size:13px;color:#555;
                  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;
                  overflow:hidden; text-overflow:ellipsis; line-height:1.4em; max-height:2.8em;">
          <?= htmlspecialchars($review['comment']) ?>
        </p>
      </div>

      <!-- Alt -->
      <div style="display:flex; align-items:center; justify-content:space-between; margin-top:10px;">
        <small style="color:#777; font-size:11px;">
          Tarih: <?= date('d.m.Y', strtotime($review['created_at'])) ?>
        </small>

        <a href="/company/<?= urlencode($review['company_slug']) ?>"
           style="background-color:#42A4FD;color:#fff; padding:6px 12px;border-radius:8px;
                  font-size:13px;font-weight:bold; text-decoration:none; display:inline-block;">
          İşletmeyi Gör
        </a>
      </div>

    </div>
    <?php endforeach; ?>
  </div>
</div>

</section>
<!-- Last reviews -->



                 

<!-- Info Section 2 -->
<div class="container info-section" style="background-color:#9ffbd1; border-radius:20px; margin-bottom:40px; padding-top:25px; padding-bottom:25px;">
  <div class="row align-items-center flex-column-reverse flex-md-row">
    
    <!-- Sol: başlık + metin + buton -->
    <div class="col-md-8 text-md-start text-center mb-3 mb-md-0 info-text">
      <h2 style="font-weight:700; margin-bottom:10px;">Güven endeksi</h2>
      <p style="font-size:16px; line-height:1.6; margin-bottom:15px;">
        Puandeks, markaların kullanıcı deneyimlerinden ilham alarak geliştiği, şeffaflığa dayalı yeni nesil bir tüketici değerlendirme platformudur. 
        İşletmelerle tüketiciler arasında güvene dayalı kalıcı bağlar kuruyor, her iki tarafın da hizmet kalitesini ileriye taşımasına katkı sağlıyoruz.
      </p>
      <a href="https://puandeks.com/about" style="display:inline-block; background-color:#1C1C1C; color:#fff; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:600;">Ne Yapıyoruz?</a>
    </div>

    <!-- Sag: gorsel -->
    <div class="col-md-4 text-center mb-3 mb-md-0">
      <img src="img/home/happy-user-puandeks.webp" alt="Mutlu kullanıcı" style="max-width:100%; height:auto; border-radius:12px;">
    </div>

  </div>
</div>
<!-- /Info Section 2 -->




<!-- Visitor activity  -->
<?php if (!empty($companiesData)): ?>
<section class="best-companies-section">
    <h2 class="section-title">Kaldığınız yerden devam edin</h2> 

    <div class="company-grid" style="display:flex; flex-wrap:wrap; justify-content:flex-start; gap:20px;">
        
        <?php foreach ($companiesData as $company): ?>
            <div class="company-card">

                <?php
                    // LOGO (null-safe)
                    $logo = !empty($company['logo'])
                        ? htmlspecialchars($company['logo'])
                        : 'img/placeholder/company-profile.png';

                    // RATING (reviews tablosundan gelen)
                    $rating = isset($company['avg_rating'])
                        ? round((float)$company['avg_rating'], 1)
                        : 0.0;

                    $reviewCount = (int)($company['review_count'] ?? 0);

                    // yıldız seviyesi (işletme sayfası ile birebir)
                    $vote_level = $reviewCount === 0
                        ? 0
                        : min(max(floor($rating), 1), 5);
                ?>

                <a href="/company/<?= urlencode($company['slug']) ?>">
                    <img src="<?= $logo ?>" alt="<?= htmlspecialchars($company['name']) ?>">
                </a>

                <a href="/company/<?= urlencode($company['slug']) ?>">
                    <h3><?= htmlspecialchars($company['name']) ?></h3>
                </a>

                <!-- Yıldızlı puanlama -->
                <div class="rating">
                    <img 
                        src="img/core/vote_<?= $vote_level ?>.svg" 
                        alt="vote <?= $rating ?>" 
                        class="custom-star" 
                        height="20"
                    >
                </div>

                <span style="margin-bottom:10px; display:block;">
                    <?= number_format($rating, 1) ?> puan
                </span>

                <?php if (!empty($company['website'])): ?>
                    <?php
                        $website = trim($company['website']);

                        // https:// yoksa ekle
                        $websiteUrl = preg_match('~^https?://~i', $website)
                            ? $website
                            : 'https://' . $website;

                        // Görünen domain
                        $host = parse_url($websiteUrl, PHP_URL_HOST);
                        $host = $host ?: $website;
                    ?>
                    <a href="<?= htmlspecialchars($websiteUrl) ?>" target="_blank" rel="nofollow noopener">
                        <?= htmlspecialchars($host) ?>
                    </a>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>

    </div>
</section>
<?php endif; ?>
<!-- Visitor activity  -->



<!-- APP SECTION  -->
<!-- DESKTOP -->
<div class="app-desktop" style="display:block;">

  <!-- App -->
  <section style="border-radius:20px; background:#04DA8D; overflow:hidden; padding:30px; max-width:1200px; margin:60px auto;">
    <div style="display:flex; align-items:center; justify-content:space-between;">
      
      <!-- Sol taraf -->
      <div style="flex:1; max-width:600px; padding:20px;">
        <h2 style="margin:0 0 10px 0; font-size:24px; font-weight:600; color:#333;">
          Puandeks uygulamasıyla
        </h2>
        <p style="margin:0 0 20px 0; font-size:16px; line-height:1.5; color:#555;">
          İşletmeleri bulun, incelemeleri okuyun veya inceleme yazın, hem de hareket halindeyken.
        </p>

        <div style="display:flex; gap:12px;">
          <a href="#" style="display:inline-flex; align-items:center; justify-content:center; min-width:140px; height:42px; background:#000; color:#fff; border-radius:10px; text-decoration:none; padding:0 14px;">
            <i class="fa-brands fa-apple" style="margin-right:8px;"></i>
            <span style="font-size:13px;">App Store</span>
          </a>

          <a href="#" style="display:inline-flex; align-items:center; justify-content:center; min-width:140px; height:42px; background:#000; color:#fff; border-radius:10px; text-decoration:none; padding:0 14px;">
            <i class="fa-brands fa-google-play" style="margin-right:8px;"></i>
            <span style="font-size:13px;">Google Play</span>
          </a>
        </div>
      </div>

      <!-- Sağ taraf (Telefon) -->
      <div style="margin-left:20px;">
        <img src="img/banners/mobile-app.svg"
             alt="Puandeks Mobile App"
             style="max-width:140px;">
      </div>

    </div>
  </section>
  <!-- /App -->

</div>

<!-- MOBILE -->
<div class="app-mobile" style="display:none;">

  <!-- App -->
  <section style="border-radius:20px; background:#04DA8D; padding:30px; margin:60px auto; text-align:center;">
    
    <h2 style="margin:0 0 10px 0; font-size:22px; font-weight:600; color:#333;">
      Puandeks uygulamasıyla
    </h2>

    <p style="margin:0 0 20px 0; font-size:15px; color:#555;">
      İşletmeleri bulun, incelemeleri okuyun <br> veya inceleme yazın.
    </p>

    <!-- Butonlar -->
    <div style="display:flex; flex-direction:column; gap:10px; align-items:center;">
      
      <a href="#" style="width:160px; height:42px; display:flex; align-items:center; justify-content:center; background:#000; color:#fff; border-radius:10px; text-decoration:none;">
        <i class="fa-brands fa-apple" style="margin-right:8px;"></i>
        App Store
      </a>

      <a href="#" style="width:160px; height:42px; display:flex; align-items:center; justify-content:center; background:#000; color:#fff; border-radius:10px; text-decoration:none;">
        <i class="fa-brands fa-google-play" style="margin-right:8px;"></i>
        Google Play
      </a>

    </div>

    <!-- Telefon -->
    <img src="img/banners/mobile-app.svg"
         alt="Puandeks Mobile App"
         style="width:120px; margin-top:20px;">

  </section>
  <!-- /App -->

</div>



<!-- RESPONSIVE SWITCH -->
<style>
@media (max-width:768px){
  .app-desktop{ display:none !important; }
  .app-mobile{ display:block !important; }
}
</style>
<!-- APP SECTION -->




<!-- Popular  -->	
<?php
try {
    $stmtA = $conn->prepare("
        SELECT 
          c.id,
          c.slug,
          c.name,
          c.website,
          c.logo,
          ROUND(AVG(r.rating), 1) AS avg_rating,   
          COUNT(r.id) AS review_count
      FROM companies c
      LEFT JOIN company_subscriptions cs 
          ON cs.company_id = c.id 
          AND cs.status = 'active'
      JOIN reviews r 
          ON r.company_id = c.id
          AND r.status = 1
          AND r.parent_id IS NULL
      GROUP BY c.id
      HAVING avg_rating >= 4.9
      AND MAX(cs.package_id) = 4
      ORDER BY RAND()
      LIMIT 4
    ");
    $stmtA->execute();
    $topCompanies = $stmtA->fetchAll(PDO::FETCH_ASSOC);


    $featuredCompanies = $topCompanies;

    if (count($featuredCompanies) < 4) {

        $stmtB = $conn->prepare("
            SELECT 
                c.id,
                c.slug,
                c.name,
                c.website,
                c.logo,
                ROUND(AVG(r.rating), 1) AS avg_rating,   
                COUNT(r.id) AS review_count,
                CASE 
                    WHEN cs.package_id = 4 THEN 4
                    WHEN cs.package_id = 3 THEN 3
                    WHEN cs.package_id = 2 THEN 2
                    WHEN cs.package_id = 1 THEN 1
                    ELSE 0
                END AS package_priority
            FROM companies c
            LEFT JOIN company_subscriptions cs 
                ON cs.company_id = c.id 
                AND cs.status = 'active'
            JOIN reviews r 
                ON r.company_id = c.id
                AND r.status = 1
                AND r.parent_id IS NULL
            GROUP BY c.id
            HAVING avg_rating >= 4.0
            ORDER BY 
                package_priority DESC,
                avg_rating DESC,
                RAND()
            LIMIT 10
        ");
        $stmtB->execute();
        $fallbackCompanies = $stmtB->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fallbackCompanies as $c) {
            if (!in_array($c['id'], array_column($featuredCompanies, 'id'))) {
                $featuredCompanies[] = $c;
            }
            if (count($featuredCompanies) == 4) break;
        }
    }

} catch (PDOException $e) {
    $featuredCompanies = [];
}
?>

<section class="best-companies-section">
    <h2 class="section-title">Popüler İşletmeler</h2> 
    <div class="company-grid">

    <?php foreach ($featuredCompanies as $company): ?>
        <?php
            $logo = !empty($company['logo'])
                ? htmlspecialchars($company['logo'])
                : 'img/placeholder/company-profile.png';

            $rating = (float)$company['avg_rating'];
            $reviewCount = (int)$company['review_count'];

            $vote_level = min(max(floor($rating), 1), 5);

            $website = trim($company['website'] ?? '');
            $websiteUrl = $website
                ? (preg_match('~^https?://~i', $website) ? $website : 'https://' . $website)
                : '';

            $host = $websiteUrl ? parse_url($websiteUrl, PHP_URL_HOST) : '';
            $host = $host ?: $website;
        ?>

        <div class="company-card">

            <a href="/company/<?= urlencode($company['slug']) ?>">
                <img src="<?= $logo ?>" alt="<?= htmlspecialchars($company['name']) ?>">
            </a>

            <a href="/company/<?= urlencode($company['slug']) ?>">
                <h3><?= htmlspecialchars($company['name']) ?></h3>
            </a>

            <div class="rating">
                <img 
                    src="img/core/vote_<?= $vote_level ?>.svg" 
                    alt="vote <?= $rating ?>" 
                    class="custom-star" 
                    height="20"
                >
            </div>

            <span style="margin-bottom:10px; display:block;">
                <?= number_format($rating, 1) ?> puan
            </span>

            <?php if ($websiteUrl): ?>
                <a href="<?= htmlspecialchars($websiteUrl) ?>" target="_blank" rel="nofollow noopener">
                    <?= htmlspecialchars($host) ?>
                </a>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>

    </div>
</section>
<!-- Popular  -->



		

</main>
<!-- main -->


<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	



</div>
<!-- page -->

<!-- Cookie banner -->	
<?php include 'inc/cookie.php'; ?>
<!-- Cookie banner -->	



	<!-- COMMON SCRIPTS -->
	<script src="js/common_scripts.js"></script>
	<script src="js/functions.js"></script>
	<script src="assets/validate.js"></script>
    <script src="js/search-submit.js"></script>
    <script src="js/company-results.js"></script>
    <script src="js/search-live.js"></script>
    <script src="js/cookie.js"></script>

<!-- Last reviews -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const track = document.querySelector('.slider-track');
  const cards = document.querySelectorAll('.review-card');
  const prev = document.getElementById('prevBtn');
  const next = document.getElementById('nextBtn');
  let index = 0;

  function getPerPage() {
    if (window.innerWidth <= 767) return 1;
    if (window.innerWidth <= 991) return 2;
    return 3;
  }

  function updateSlider() {
    if (cards.length === 0) return;

    const cardWidth = cards[0].getBoundingClientRect().width + 20; // width + gap
    const perPage = getPerPage();
    const maxIndex = Math.max(0, cards.length - perPage);

    if (index > maxIndex) index = maxIndex;
    if (index < 0) index = 0;

    const offset = -index * cardWidth;
    track.style.transform = 'translateX(' + offset + 'px)';
  }


  next.addEventListener('click', () => {
    const perPage = getPerPage();
    if (index < cards.length - perPage) {
      index++;
      updateSlider();
    }
  });

  prev.addEventListener('click', () => {
    if (index > 0) {
      index--;
      updateSlider();
    }
  });

  window.addEventListener('resize', updateSlider);

  // ilk yükleme
  updateSlider();
});
</script>
<!-- Last reviews -->
      

</body>
</html>