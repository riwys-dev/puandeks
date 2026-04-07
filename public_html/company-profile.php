<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('/home/puandeks.com/backend/config.php');

// Cookie’den user_id varsa session’a yaz
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

// Kullanıcı veya işletme rolünü session’a al
if (isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
    // Kullanıcı kontrolü
    $stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['role']   = $user['role'];
        $_SESSION['name']   = $user['name'];
        $_SESSION['email']  = $user['email'];
        $_SESSION['profile_photo'] = '';
    } else {
        // İşletme kontrolü
        $stmt = $conn->prepare("SELECT name, email FROM companies WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($company) {
            $_SESSION['role']   = 'business';
            $_SESSION['name']   = $company['name'];
            $_SESSION['email']  = $company['email'];
            $_SESSION['profile_photo'] = '';
        }
    }
}

// ID kontrolü
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    die("Geçersiz işletme profili");
}


// İşletme bilgilerini çek
try {
    $stmt = $conn->prepare("
    SELECT 
    c.id, 
    c.name, 
    c.category_id, 
    COALESCE(c.verified, 0) AS verified,
    c.status,
    c.about, 
    c.country,
    c.address, 
    c.city_id,
    city.name AS city_name,      
    c.website, 
    c.email,
    c.email_verified,
    c.phone_prefix, 
    c.phone, 
    c.linkedin_url,
    c.facebook_url,
    c.instagram_url,
    c.x_url,
    c.youtube_url,
    c.logo,
    c.added_by_user_id,
    c.latitude,
    c.longitude,
    c.banner_url,
    c.banner_link,
    cat.name AS category_name
    FROM companies c
    LEFT JOIN categories cat ON c.category_id = cat.id
    LEFT JOIN cities city ON c.city_id = city.id
    WHERE c.slug = ?
");
    $stmt->execute([$slug]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        die("İşletme bulunamadı.");
    }

    $company_id = (int)$company['id'];


   $isUserAdded = !empty($company['added_by_user_id']); 

   $isClaimed = !empty($company['email']);


    // NULL > 0 döntrme
    $company['verified'] = isset($company['verified']) && $company['verified'] !== null ? (int)$company['verified'] : 0;

    $category_id = (int)($company['category_id'] ?? 0);

} catch (PDOException $e) {
    die("Veritabanı hatas: " . $e->getMessage());
}

// Benzer işletmeleri cek
try {

    $similar_companies = [];

    // 1️⃣ SOL: paketli + en yüksek puan (öncelik)
    $stmtA = $conn->prepare("
        SELECT 
            c.id, c.name, c.slug, c.domain, c.logo,
            ROUND(AVG(r.rating),1) AS avg_rating,
            COUNT(r.id) AS review_count,
            MAX(cs.package_id) AS package_priority
        FROM companies c
        LEFT JOIN company_subscriptions cs 
            ON cs.company_id = c.id AND cs.status = 'active'
        LEFT JOIN reviews r 
            ON r.company_id = c.id AND r.status = 1 AND r.parent_id IS NULL
        WHERE c.id != :current_id 
        AND c.category_id = :category_id
        GROUP BY c.id
        HAVING package_priority > 0
        ORDER BY 
            package_priority DESC,
            avg_rating DESC
        LIMIT 6
    ");

    $stmtA->execute([
        ':current_id' => $company_id,
        ':category_id' => $category_id
    ]);

    $premium = $stmtA->fetchAll(PDO::FETCH_ASSOC);

    $similar_companies = $premium;

    // 2️⃣ SAĞ: aynı kategori + en yüksek puan (paketsiz dahil)
    if (count($similar_companies) < 6) {

        $needed = 6 - count($similar_companies);

        $stmtB = $conn->prepare("
            SELECT 
                c.id, c.name, c.slug, c.domain, c.logo,
                ROUND(AVG(r.rating),1) AS avg_rating,
                COUNT(r.id) AS review_count
            FROM companies c
            LEFT JOIN reviews r 
                ON r.company_id = c.id AND r.status = 1 AND r.parent_id IS NULL
            WHERE c.id != :current_id 
            AND c.category_id = :category_id
            GROUP BY c.id
            ORDER BY avg_rating DESC
            LIMIT 10
        ");

        $stmtB->execute([
            ':current_id' => $company_id,
            ':category_id' => $category_id
        ]);

        $fallback = $stmtB->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fallback as $c) {
            if (!in_array($c['id'], array_column($similar_companies, 'id'))) {
                $similar_companies[] = $c;
            }
            if (count($similar_companies) == 6) break;
        }
    }

} catch (PDOException $e) {
    $similar_companies = [];
}
?>


<!DOCTYPE html>
<html lang="tr">
  
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<title><?= htmlspecialchars($company['name']) ?> | Puandeks</title>

      <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">


	<!-- Favicons-->
	<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" type="image/x-icon" href="/img/apple-touch-icon-57x57-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="/img/apple-touch-icon-72x72-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="114x114"
		href="/img/apple-touch-icon-114x114-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="144x144"
		href="/img/apple-touch-icon-144x144-precomposed.png">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<!-- GOOGLE WEB FONT -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

	<!-- BASE CSS -->
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/style.css" rel="stylesheet">
	<link href="/css/vendors.css" rel="stylesheet">

	<!-- CUSTOM CSS -->
	<link rel="stylesheet" href="/css/custom.css" >
	<link rel="stylesheet" href="/css/discover.css">
	<link rel="stylesheet" href="/css/vote.css">
	<link rel="stylesheet" href="/css/popup.css">

  <style>
    .slider-track-wrapper::-webkit-scrollbar { display: none; }
  </style>

<style>
.review_card[id^="review-"] {
  box-shadow: none !important;
  border: 2px solid #e5e5e5 !important;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 20px;
  background-color: #fff;
}
</style>

<style>
.best-companies-section {
  max-width: 1200px !important;
  margin: 0 auto !important;
  padding: 20px 0 40px 0 !important;
}

.best-companies-section .section-title {
  font-size: 20px !important;
  font-weight: 600 !important;
  margin-bottom: 20px !important;
  text-align: left !important;
}

.company-grid {
  display: grid !important;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)) !important;
  gap: 20px !important;
}

.company-card {
  background: #fff !important;
  border: 1px solid #e5e5e5 !important;
  border-radius: 10px !important;
  text-align: center !important;
  padding: 16px !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease !important;
}

.company-card:hover {
  transform: translateY(-3px) !important;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05) !important;
}

.company-card h3 {
  font-size: 16px !important;
  font-weight: 600 !important;
  color: #333 !important;
  margin-bottom: 8px !important;
}

.company-card .rating {
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  gap: 3px !important;
  margin-bottom: 6px !important;
}

.company-card .custom-star {
  width: 18px !important;
  height: 18px !important;
}

.company-card span {
  color: #444 !important;
  font-size: 14px !important;
}

.company-card a {
  display: block !important;
  font-size: 13px !important;
  color: #3578fa !important;
  text-decoration: none !important;
}

.company-card a:hover {
  text-decoration: underline !important;
}

@media (max-width: 768px) {
  .company-grid {
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)) !important;
    gap: 16px !important;
  }
  .company-card img {
    width: 64px !important;
    height: 64px !important;
  }
}
</style>

<style>
    /* Desktop hizalama */
    .claim-wrapper {
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        margin-left:auto;
    }

    /* Mobile hizalama */
    @media (max-width: 768px) {
        .claim-wrapper {
            align-items:center;
            margin-top:12px;
        }
    }

    /* Üst gri metin */
    .claim-small-text {
        font-size:13px;
        color:#dcdcdc;
        margin-bottom:6px;
        text-align:center;
    }

    /* Sahiplen butonu */
    .claim-btn {
        background-color:#9FF6D3;
        color:#1C1C1C;
        font-weight:600;
        border:none;
        border-radius:20px;
        padding:8px 20px;
        display:inline-flex;
        align-items:center;
        gap:6px;
        text-decoration:none;
        transition:all .2s ease;
    }

    /* Hover */
    .claim-btn:hover {
        background-color:#b9ffe1; /* biraz daha açık ton */
        color:#1C1C1C;
    }

</style>

<style> 
/* MOBILE LOGO*/
@media (max-width: 600px) {
    .company-logo {
        margin: 0 auto 20px auto !important;
        display: block !important;
        float: none !important;
    }
}

/* DESKTOP LOGO */
@media (min-width: 601px) {
    .company-logo {
        float: left !important;
        margin-right: 20px !important;
        margin-bottom: 0 !important;
    }
}
</style>
	

</head>

<body data-company-id="<?= intval($company['id']) ?>">

  <div id="page">

<!-- header -->
<?php include 'header-main.php'; ?>
<!-- /header -->

<main>
<div class="reviews_summary" style="overflow:hidden !important;">
  <div class="wrapper">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          
        <div class="company-logo-wrap" style="text-align:center;">
          <figure class="company-logo"
            style="
                width:120px !important;
                height:120px !important;
                padding:0 !important;
                overflow:hidden !important;
                border-radius:6px !important;
                background:#fff !important;
                display:block !important;">

              <img 
                  src="<?= !empty($company['logo']) ? htmlspecialchars($company['logo']) : '/img/placeholder/company-profile.png' ?>"
                  alt="<?= htmlspecialchars($company['name']) ?> logo"
                  style="width:100% !important; height:100% !important; object-fit:cover !important; display:block !important;">
          </figure>
      </div>


          <!-- Category -->
            <small>
              <?= htmlspecialchars($company['category_name'] ?? 'Kategori yok') ?>
            </small>


              <span style="display: inline-block; margin-left: 10px; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-family: Arial, sans-serif; font-weight: 600;">
                <?php if (isset($company['email_verified']) && $company['email_verified'] == 1): ?>
                  <span style="background-color: #28a745; color: #fff; padding: 4px 10px; border-radius: 12px; display: inline-block;">
                    <i class="fa fa-check" style="margin-right: 4px;"></i> Onaylı Profil
                  </span>
                <?php else: ?>
                  <span style="background-color: #6c757d; color: #fff; padding: 4px 10px; border-radius: 12px; display: inline-block;">
                    <i class="fa fa-times" style="margin-right: 4px;"></i> Onaysız Profil
                  </span>
                <?php endif; ?>
              </span>


         <!-- C Name-->
          <h1><?= htmlspecialchars($company['name']) ?></h1>

         
         <!-- Star Rating -->
          <?php
          // Onaylı yorum sayısı
          $stmt = $conn->prepare("
              SELECT COUNT(*) 
              FROM reviews 
              WHERE company_id = ? 
                AND status = 1 
                AND parent_id IS NULL
          ");
          $stmt->execute([$company['id']]);
          $total_reviews = (int)$stmt->fetchColumn();

          $stmt = $conn->prepare("
              SELECT AVG(rating) 
              FROM reviews 
              WHERE company_id = ? 
                AND status = 1 
                AND parent_id IS NULL
          ");
          $stmt->execute([$company['id']]);
          $avg_rating = $stmt->fetchColumn();

          $avg_rating = $avg_rating ? round(floatval($avg_rating), 1) : 0.0;

          $vote_level = $total_reviews === 0 ? 0 : min(max(floor($avg_rating), 1), 5);
          $vote_svg = "/img/core/vote_" . $vote_level . ".svg";

          $rating_display = $total_reviews === 0 ? "0.0" : number_format($avg_rating, 1);
          ?>
          <img src="<?= $vote_svg ?>" alt="vote <?= $rating_display ?>" height="30">
          <span style="color:white;margin-left:10px;">
              <?= $rating_display ?> | <?= $total_reviews ?> inceleme
          </span>
          <!-- Star Rating -->


          <br><br><br>
          
          <!-- INCELEME YAZ BUTON -->
          <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'business'): ?>
            <a href="javascript:void(0)" 
               onclick="alert('İşletmeler inceleme yazamaz.');" 
               class="btn_primary" 
               style="background-color:#3578fa; color:#fff; font-weight:normal; border:none; border-radius:20px; padding:8px 16px;">
              <i class="bi bi-pencil" style="margin-right:5px;"></i> İnceleme yaz
            </a>
          <?php else: ?>
            <a href="/add-review?id=<?= $company_id ?>" 
               class="btn_primary" 
               style="background-color:#3578fa; color:#fff; font-weight:normal; border:none; border-radius:20px; padding:8px 16px;">
              <i class="bi bi-pencil" style="margin-right:5px;"></i> İnceleme yaz
            </a>
          <?php endif; ?>
         <!-- INCELEME YAZ BUTON -->
         
         
         <!-- SAHIPLEN BUTONU -->
        <?php if ($isUserAdded && !$isClaimed && $company['status'] !== 'approved'): ?>
            <a href="https://business.puandeks.com/register-claim?company_id=<?= $company_id ?>"
               class="claim-btn"
               style="background-color:#9FF6D3; color:#1C1C1C; font-weight:600; border:none; border-radius:20px; padding:8px 16px; display:inline-flex; align-items:center; gap:6px; margin-left:12px; text-decoration:none;">
                <i class="fa-solid fa-user-check"></i>
                Sahiplen
            </a>

            <!-- INFO ICON -->
            <i id="claimInfoIcon"
               class="fa-solid fa-circle-info"
               style="font-size:18px; color:white; cursor:pointer; margin-left:10px; position:relative;">
            </i>

            <!-- INFO BOX -->
            <div id="claimInfoBox"
                 style="display:none; position:absolute; background:#1C1C1C; color:white; padding:10px 14px; border-radius:8px; font-size:13px; width:240px; text-align:center; z-index:999;">
                Bu işletme size mi ait? Sahiplenerek yönetebilirsiniz.
            </div>

        <?php endif; ?>
      <!-- SAHIPLEN BUTONU -->


        </div> <!-- col-lg-8 -->
      </div> <!-- row -->
    </div> <!-- container -->
  </div> <!-- wrapper -->
</div> <!-- reviews -->

		

  
<div class="container margin_60_35">
			<div class="row">
				<div class="col-lg-8">
                  
                  
					
        <!-- Review Box -->
        <?php if ($total_reviews > 0): ?>
        <div class="review_card">
            <h2>Son incelemeler</h2>
            <br>

        <?php


        try {
            $stmt = $conn->prepare("
                SELECT 
                  r.id,
                  r.user_id,
                  r.rating,
                  r.title,
                  r.comment,
                  r.reply,
                  r.updated_at AS reply_date,
                  r.created_at,
                  u.profile_image,
                  u.name AS user_name,
                  (
                    SELECT COUNT(*) 
                    FROM reviews rr 
                    WHERE rr.user_id = r.user_id 
                    AND rr.status = 1 
                    AND rr.parent_id IS NULL
                  ) AS total_reviews,
                  (
                    SELECT COUNT(*) 
                    FROM review_media rm 
                    WHERE rm.review_id = r.id
                  ) AS media_count
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.company_id = :company_id 
                  AND r.parent_id IS NULL
                  AND r.status = 1
                ORDER BY r.created_at DESC
                LIMIT 5
            ");
            $stmt->bindParam(':company_id', $company_id, PDO::PARAM_INT);
            $stmt->execute();
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $reviews = [];
        }

        /*  BADGE */
        foreach ($reviews as $i => $r) {

            $approved = intval($r['total_reviews']); 

            $reviews[$i]['badge_label'] = '';
            $reviews[$i]['badge_color'] = '';

            if ($approved >= 500) {
                $reviews[$i]['badge_label'] = 'Lider';
                $reviews[$i]['badge_color'] = '#D14B00';
            } elseif ($approved >= 100) {
                $reviews[$i]['badge_label'] = 'Elite';
                $reviews[$i]['badge_color'] = '#AA00FF';
            } elseif ($approved >= 50) {
                $reviews[$i]['badge_label'] = 'Uzman';
                $reviews[$i]['badge_color'] = '#0066FF';
            } elseif ($approved >= 10) {
                $reviews[$i]['badge_label'] = 'Yeni';
                $reviews[$i]['badge_color'] = '#1b7d2f';
            }
        }
        ?>

        <?php foreach ($reviews as $review): ?>
        <div class="review_card" id="review-<?= $review['id'] ?>">
            <div class="row">

        <!-- Avatar + Badge + Username -->
        <div style="width:70px; position:relative; margin-right:15px; text-align:center;">

         <!-- Avatar -->
        <a href="/user-profile?id=<?= $review['user_id'] ?>">

       <?php if (!empty($review['profile_image']) && strpos($review['profile_image'], 'googleusercontent') === false): ?>

            <?php
            $isExternal = strpos($review['profile_image'], 'http') === 0;
            $imgSrc = $isExternal ? $review['profile_image'] : '/' . $review['profile_image'];
            ?>

            <img src="<?= htmlspecialchars($imgSrc) ?>"
                style="width:55px; height:55px; border-radius:50%; object-fit:cover; display:block; margin:0 auto;">

                  <?php else: ?>

              <div style="
                  width:55px;
                  height:55px;
                  border-radius:50%;
                  background:#05462F;
                  color:#fff;
                  display:flex;
                  align-items:center;
                  justify-content:center;
                  font-weight:700;
                  font-size:20px;
                  margin:0 auto;
              ">
                  <?= strtoupper(substr($review['user_name'], 0, 1)) ?>
              </div>

          <?php endif; ?>

          </a>


            <?php
                $badge_label = "";
                $badge_color = "";

                if ($review['total_reviews'] >= 500) {
                    $badge_label = "Lider";
                    $badge_color = "#D14B00";
                } elseif ($review['total_reviews'] >= 100) {
                    $badge_label = "Elite";
                    $badge_color = "#AA00FF";
                } elseif ($review['total_reviews'] >= 50) {
                    $badge_label = "Uzman";
                    $badge_color = "#0066FF";
                } elseif ($review['total_reviews'] >= 10) {
                    $badge_label = "Yeni";
                    $badge_color = "#1B7D2F";
                }
            ?>

            <!-- Badge -->
            <?php if (!empty($badge_label)): ?>
                <span style="
                    position:absolute;
                    top:-8px;
                    right:-8px;
                    background:<?= $badge_color ?>;
                    color:#fff;
                    padding:3px 7px;
                    font-size:10px;
                    font-weight:700;
                    border-radius:12px;
                    line-height:1;
                    white-space:nowrap;
                ">
                    <?= $badge_label ?>
                </span>
            <?php endif; ?>

            <!-- Username link -->
            <a href="/user-profile.php?id=<?= $review['user_id'] ?>"
               style="display:block; margin-top:6px; font-size:13px; font-weight:600; color:#333; text-decoration:none;">
                <?= htmlspecialchars($review['user_name']) ?>
            </a>

            <div style="font-size:12px; color:#666;">
                (<?= $review['total_reviews'] ?> İnceleme)
            </div>

            <div style="font-size:12px; color:#666;">
                Türkiye
            </div>

        </div>




                <div class="col-md-10 review_content">
                    <div class="clearfix add_bottom_15">
                        <img src="/img/core/vote_<?= intval($review['rating']) ?>.svg"
                             alt="vote <?= intval($review['rating']) ?>" height="20">
                        <em><?= date('d.m.Y', strtotime($review['created_at'])) ?></em>
                    </div>

                    <h4>"<?= htmlspecialchars($review['title']) ?>"</h4>

                    <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                    <?php if (!empty($review['media_count']) && $review['media_count'] > 0): ?>
                    <div style="margin-top:8px; margin-bottom:16px; font-size:13px; color:#555;">
                      <a href="javascript:void(0)" 
                        class="review-media-trigger"
                        data-review-id="<?= $review['id'] ?>"
                        style="margin-top:6px; font-size:13px; color:#555; display:inline-block;">
                        <i class="fa fa-camera" style="font-size:18px; margin-right:4px;"></i>
                        <strong><?= $review['media_count'] ?></strong>
                      </a>
                    </div>
                  <?php endif; ?>

                  <hr style="margin:10px 0; border-top:1px solid #ddd;">

                    <ul>
                        <li>
                            <a href="javascript:void(0)" class="open-popup" data-review-id="<?= $review['id'] ?>">
                                <i class="icon-attention"></i><span>Bildir</span>
                            </a>
                        </li>
                        <li>
                            <span>Paylaş</span>

                            <a href="https://www.facebook.com/sharer/sharer.php?u=https://puandeks.com/company/<?= urlencode($slug) ?>#review-<?= $review['id'] ?>"
                            target="_blank" style="margin-right:8px;">
                              <i class="ti-facebook"></i>
                          </a>


                            <a href="https://twitter.com/intent/tweet?url=https://puandeks.com/company/<?= urlencode($slug) ?>#review-<?= $review['id'] ?>"
                              target="_blank" style="margin-right:8px;">
                                <i class="bi-twitter-x"></i>
                            </a>


                           <a href="https://api.whatsapp.com/send?text=<?= urlencode('Bu yorumu Puandekste gör: https://puandeks.com/company/' . $slug . '#review-' . $review['id']) ?>"
                            target="_blank">
                              <i class="bi-whatsapp"></i>
                          </a>


                        </li>
                    </ul>

                    <hr style="margin:10px 0; border-top:1px solid #ddd;">
                </div>
            </div>

            <?php if (!empty($review['reply'])): ?>
                <div class="row reply" style="margin-top:10px;">
                    <div class="col-md-12">
                        <div class="review_content">
                            <strong>İşletme <?= htmlspecialchars($company['name']) ?> Yanıt</strong>
                            <em><?= !empty($review['reply_date']) ? date('d.m.Y', strtotime($review['reply_date'])) : date('d.m.Y') ?></em>
                            <p><?= nl2br(htmlspecialchars($review['reply'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>

        </div>
        <?php endif; ?>
        <!-- Review Box -->



        <!-- Banner -->
        <?php if (!empty($company['banner_url'])): ?>
          <div style="margin:30px 0; text-align:center;">
            
            <?php if (!empty($company['banner_link'])): ?>
              <a href="<?= htmlspecialchars($company['banner_link']) ?>" target="_blank" rel="nofollow noopener">
            <?php endif; ?>

                <img src="<?= htmlspecialchars($company['banner_url']) ?>" style="width:100%; max-width:100%; border-radius:12px; border:1px solid #e5e7eb;">

            <?php if (!empty($company['banner_link'])): ?>
              </a>
            <?php endif; ?>

          </div>
        <?php endif; ?>
         <!-- Banner -->


<!-- Company Details -->
			</div>
				<!-- /col -->
				<div class="col-lg-4">
					<div class="box_general company_info">

                      <h3>İşletme Hakkında</h3>
                     <p><?= !empty($company['about']) ? nl2br(htmlspecialchars($company['about'])) : 'Henüz eklenmedi' ?></p>

                      <p>
                      <strong>Adres</strong><br>
                      <?php
                        if (!empty($company['country'])) {
                            echo htmlspecialchars($company['country']) . "<br>";
                        }

                        if (!empty($company['city_name'])) {
                            echo htmlspecialchars($company['city_name']) . "<br>";
                        }

                        if (!empty($company['address'])) {
                            echo htmlspecialchars($company['address']);
                        }

                        if (empty($company['country']) && empty($company['city_name']) && empty($company['address'])) {
                            echo "Henüz eklenmedi";
                        }
                        ?>
                      </p>


                    <p><strong>Website</strong><br>
                    <?php if (!empty($company['website'])): ?>
                        <?php
                            $website = trim($company['website']);

                            $websiteUrl = preg_match('~^https?://~i', $website)
                                ? $website
                                : 'https://' . $website;

                            $host = parse_url($websiteUrl, PHP_URL_HOST);
                            $host = $host ?: $website;
                        ?>
                        <a href="<?= htmlspecialchars($websiteUrl) ?>" target="_blank" rel="nofollow noopener">
                            <?= htmlspecialchars($host) ?>
                        </a>
                    <?php else: ?>
                        Henüz eklenmedi
                    <?php endif; ?>
                    </p>


                  <p><strong>Email</strong><br>
                      <?php if (!empty($company['email'])): ?>
                          <a href="mailto:<?= htmlspecialchars($company['email']) ?>">
                              <?= htmlspecialchars($company['email']) ?>
                          </a>
                      <?php else: ?>
                          Henüz eklenmedi
                      <?php endif; ?>
                  </p>

                  <p><strong>Telefon</strong><br>
                      <?php
                        if (!empty($company['phone_prefix']) && !empty($company['phone'])) {
                            echo '+' . htmlspecialchars($company['phone_prefix']) . ' ' . htmlspecialchars($company['phone']);
                        } else {
                            echo 'Henüz eklenmedi';
                        }
                        ?>
                  </p>

                    <p class="follow_company">
                    <strong>Takip et</strong><br>

                    <?php
                    // social url helper
                    function social_url($base, $value) {
                      if (empty($value)) return null;

                      // if already full url
                      if (preg_match('#^https?://#i', $value)) {
                        return $value;
                      }

                      return $base . ltrim($value, '/');
                    }

                    $has_social =
                      !empty($company['facebook_url']) ||
                      !empty($company['x_url']) ||
                      !empty($company['linkedin_url']) ||
                      !empty($company['instagram_url']) ||
                      !empty($company['youtube_url']);

                    if ($has_social):
                    ?>

                      <?php if (!empty($company['facebook_url'])): ?>
                        <a href="<?= htmlspecialchars(social_url('https://facebook.com/', $company['facebook_url'])) ?>" target="_blank">
                          <i class="ti-facebook"></i>
                        </a>
                      <?php endif; ?>

                      <?php if (!empty($company['x_url'])): ?>
                        <a href="<?= htmlspecialchars(social_url('https://x.com/', $company['x_url'])) ?>" target="_blank">
                          <i class="bi-twitter-x"></i>
                        </a>
                      <?php endif; ?>

                      <?php if (!empty($company['linkedin_url'])): ?>
                        <a href="<?= htmlspecialchars(social_url('https://linkedin.com/in/', $company['linkedin_url'])) ?>" target="_blank">
                          <i class="ti-linkedin"></i>
                        </a>
                      <?php endif; ?>

                      <?php if (!empty($company['instagram_url'])): ?>
                        <a href="<?= htmlspecialchars(social_url('https://instagram.com/', $company['instagram_url'])) ?>" target="_blank">
                          <i class="ti-instagram"></i>
                        </a>
                      <?php endif; ?>

                      <?php if (!empty($company['youtube_url'])): ?>
                        <a href="<?= htmlspecialchars(social_url('https://youtube.com/', $company['youtube_url'])) ?>" target="_blank">
                          <i class="ti-youtube"></i>
                        </a>
                      <?php endif; ?>

                    <?php else: ?>

                      Henüz eklenmedi

                    <?php endif; ?>
                  </p>



    <hr>
                  
    <!-- Map -->
    <h4>Konum</h4>

      <?php
      $lat = !empty($company['latitude']) ? $company['latitude'] : '41.0082';
      $lng = !empty($company['longitude']) ? $company['longitude'] : '28.9784';
      ?>
        <div id="companyMap" style="width:100%;height:250px;border-radius:8px;"></div>
      <!-- Map -->

</div>

				</div>
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
		
</main>
<!--/main-->
    
<!-- Benzer Işletmeler -->
<div class="slider-section" style="padding: 24px 0;">
  <div class="slider-header" style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
  <h3 class="slider-title" style="font-size: 18px; font-weight: 600; margin-left: 8px;">Benzer İşletmelere Gözat</h3>
  <div style="margin-right: 8px;">
    <button onclick="scrollSlider(-1)" style="border: none; background: none; font-size: 20px; cursor: pointer;">&#10094;</button>
    <button onclick="scrollSlider(1)" style="border: none; background: none; font-size: 20px; cursor: pointer;">&#10095;</button>
  </div>
</div>


  <div class="slider-container" style="overflow: hidden;">
    <div id="sliderWrapper" class="slider-track-wrapper" style="overflow-x: auto; scroll-behavior: smooth; cursor: grab; -ms-overflow-style: none; scrollbar-width: none;">
      <div class="slider-track" style="display: flex; gap: 16px; padding-bottom: 8px; width: max-content;">
        <?php foreach ($similar_companies as $company): ?>
          <?php
            $rating = isset($company['avg_rating']) ? round($company['avg_rating']) : 0;
            $rating_display = isset($company['avg_rating']) ? number_format((float)$company['avg_rating'], 1) : '0.0';
            $host = !empty($company['domain']) ? parse_url($company['domain'], PHP_URL_HOST) : null;
          ?>
          <a href="/company/<?= urlencode($company['slug']) ?>" style="text-decoration: none; color: inherit;">
            <div class="slider-card" style="flex: 0 0 240px; border: 1px solid #ddd; border-radius: 8px; padding: 16px; background: #fff; text-align: center; cursor: pointer;">
              <img src="<?= !empty($company['logo']) ? htmlspecialchars($company['logo']) : '/img/placeholder/company-photo-slider.png' ?>" 
                   alt="<?= htmlspecialchars($company['name']) ?>" 
                   style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">

              <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 8px;"><?= htmlspecialchars($company['name']) ?></h4>

              <img src="/img/core/vote_<?= $rating ?>.svg" alt="vote <?= $rating ?>" height="20" style="margin-bottom: 6px;">
              <div style="color: #333; font-size: 14px; margin-bottom: 8px;"><?= $rating_display ?> puan</div>

              <?php if ($host): ?>
                <div style="color: #0066cc; font-size: 13px;"><?= $host ?></div>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<!-- /Benzer isletmeler -->
	
<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	

</div>
<!-- page -->
	

<!-- Popup Alert -->
<div id="popupOverlay" class="popup-overlay"></div>

<!-- Popup kutusu -->
<div id="popupBox" class="popup-box">
  <span class="close-btn" style="font-size: 26px; font-weight: 700;">&times;</span>

  <div class="box_general write_review" style="padding:25px;">

    <h1 style="font-size:22px; font-weight:700; color:#1c1c1c; margin-bottom:15px;">
      Şikayet veya Spam Bildirimi
    </h1>

    <div class="form-group">
      <label style="font-size:16px; font-weight:700; color:#1c1c1c;">
        Lütfen neden bu bildirimi yaptığınızı açıklayın
      </label>

      <textarea 
        id="reportReason" 
        class="form-control"
        style="height:180px; font-size:16px; font-weight:600; margin-top:10px;"
        placeholder="Mesajınızı yazın...">
      </textarea>

    </div>

    <a 
      href="#" 
      id="popupSubmitBtn"
      class="btn_1"
      style="
        background-color:#FAE108;
        color:#1c1c1c;
        font-weight:700;
        padding:12px 25px;
        display:inline-block;
        margin-top:15px;
      "
      onmouseover="this.style.backgroundColor='#ffeb4d'"
      onmouseout="this.style.backgroundColor='#FAE108'"
    >
      Gönder
    </a>

  </div>
</div>


<!-- Bildirim kutusu -->
<div id="popupAlert" class="popup-alert">Bildiriminiz gönderildi</div>
<!-- Popup Alert -->

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


	
	<!-- COMMON SCRIPTS -->
  <script src="/js/common_scripts.js"></script>
	<script src="/js/functions.js"></script>
	<script src="/assets/validate.js"></script>
	<script src="/assets/vote.js"></script>
  <script src="/js/visitor.js"></script>



<!-- Slider Scroll JS -->
<script>
  function scrollSlider(direction) {
    const wrapper = document.getElementById("sliderWrapper");
    wrapper.scrollBy({
      left: direction * 260,
      behavior: 'smooth'
    });
  }

  const sliderWrapper = document.getElementById('sliderWrapper');
  let isDown = false;
  let startX, scrollLeft;

  sliderWrapper.addEventListener('mousedown', (e) => {
    isDown = true;
    startX = e.pageX - sliderWrapper.offsetLeft;
    scrollLeft = sliderWrapper.scrollLeft;
  });

  sliderWrapper.addEventListener('mouseleave', () => isDown = false);
  sliderWrapper.addEventListener('mouseup', () => isDown = false);
  sliderWrapper.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - sliderWrapper.offsetLeft;
    const walk = (x - startX) * 1.5;
    sliderWrapper.scrollLeft = scrollLeft - walk;
  });
</script>
<!-- /Slider Scroll JS -->



<!-- User sikayet -->
<script>
window.addEventListener("load", function () {
  const popupBox = document.getElementById("popupBox");
  const popupOverlay = document.getElementById("popupOverlay");
  const closeBtn = document.querySelector(".close-btn");
  const submitBtn = document.getElementById("popupSubmitBtn");
  const alertBox = document.getElementById("popupAlert");


document.querySelectorAll(".open-popup").forEach(btn => {
  btn.addEventListener("click", function (e) {
    e.preventDefault();

    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

    if (!isLoggedIn) {
      alert("İnceleme hakkında bildirim yapabilmeniz için üye girişi yapmanız gerekmektedir.");
      return;
    }

    const reviewId = this.getAttribute('data-review-id');
    popupBox.setAttribute('data-review-id', reviewId);
    document.getElementById('reportReason').value = '';
    popupOverlay.style.display = "block";
    popupBox.style.display = "block";
  });
});

  // Kapatma
  closeBtn?.addEventListener("click", closePopup);
  popupOverlay?.addEventListener("click", closePopup);

  function closePopup() {
    popupOverlay.style.display = "none";
    popupBox.style.display = "none";
  }

  // Gönderim
  submitBtn?.addEventListener("click", function (e) {
    e.preventDefault();
    const reviewId = popupBox.getAttribute('data-review-id');
    const reason = document.getElementById('reportReason').value;
    const companyId = <?= $company_id ?>;
    const formData = new URLSearchParams();
    formData.append('review_id', reviewId);
    formData.append('company_id', companyId);
    formData.append('reason', reason);

    fetch('/api/report-review.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
      closePopup();
      alertBox.style.display = "block";
      setTimeout(() => {
        alertBox.style.display = "none";
      }, 3000);
    });
  });
});
</script>
<!-- User sikayet -->

<script>
(function() {
  // Cookie al
  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
  }

  // Cookie yaz
  function setCookie(name, value, days) {
    let expires = "";
    if (days) {
      const date = new Date();
      date.setTime(date.getTime() + (days*24*60*60*1000));
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
  }

  // visitor_id oluştur
  let visitorId = getCookie("visitor_id");
  if (!visitorId) {
    visitorId = Math.random().toString(36).substring(2, 12);
    setCookie("visitor_id", visitorId, 365);
  }

  // companyId body attribute'undan
  const companyId = document.body.getAttribute("data-company-id");

  if (companyId) {
    fetch("/api/save-visitor.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ visitor_id: visitorId, company_id: companyId })
    })
    .then(res => res.json())
    .then(data => console.log("Visitor activity saved:", data))
    .catch(err => console.error("Visitor save error:", err));
  }
})();
</script>

<!-- added company Badge control -->
    <script>
        const infoIcon = document.getElementById("claimInfoIcon");
        const infoBox  = document.getElementById("claimInfoBox");

        infoIcon.addEventListener("click", function (event) {
            event.stopPropagation(); // ikon tıklannca kapanmay engelle
            infoBox.style.display = (infoBox.style.display === "block") ? "none" : "block";
        });

        document.addEventListener("click", function (event) {
            if (!infoBox.contains(event.target) && event.target !== infoIcon) {
                infoBox.style.display = "none";
            }
        });
    </script>
<!-- added company Badge control -->

<!-- Map -->
<script>
let companyMap;
let companyMarker;

function initCompanyMap() {

  const lat = <?= $lat ?>;
  const lng = <?= $lng ?>;

  const location = { lat: parseFloat(lat), lng: parseFloat(lng) };

  companyMap = new google.maps.Map(document.getElementById("companyMap"), {
    center: location,
    zoom: 15
  });

  companyMarker = new google.maps.Marker({
    position: location,
    map: companyMap,
    icon: {
      url: "/img/core/puandeks-pin.png",
      scaledSize: new google.maps.Size(40, 40)
    }
  });
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA2ttyxRIB7WeGPdukqe3TVM6pd8rhkVEM&callback=initCompanyMap" async defer></script>
<!-- Map -->

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

</body>
</html>