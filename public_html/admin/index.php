<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

// Today's User
try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total_users
        FROM users
        WHERE role = 'user'
        AND DATE(created_at) = CURDATE()
    ");
    $stmt->execute();
    $today_users = $stmt->fetchColumn();
} catch (Exception $e) {
    $today_users = 0;
}

// Today's Companies
try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total_businesses
        FROM users
        WHERE role = 'business'
        AND DATE(created_at) = CURDATE()
    ");
    $stmt->execute();
    $today_businesses = $stmt->fetchColumn();
} catch (Exception $e) {
    $today_businesses = 0;
}

// Today's Reviews
try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total_reviews
        FROM reviews
        WHERE DATE(created_at) = CURDATE()
    ");
    $stmt->execute();
    $today_reviews = $stmt->fetchColumn();
} catch (Exception $e) {
    $today_reviews = 0;
}

// Users last 30 days
try {
    // Total
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total_last_30
        FROM users
        WHERE role = 'user'
        AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $users_last_30_days = $stmt->fetchColumn();

    // En yogun gun (tarih + o gunku kayit sayisi)
    $stmt = $pdo->prepare("
        SELECT DATE(created_at) AS day, COUNT(*) AS count
        FROM users
        WHERE role = 'user'
        AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY day
        ORDER BY count DESC
        LIMIT 1
    ");
    $stmt->execute();
    $top_day = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($top_day) {
        $most_active_day = date("d M Y", strtotime($top_day['day']));
        $most_active_count = $top_day['count'];
    } else {
        $most_active_day = "-";
        $most_active_count = 0;
    }

} catch (Exception $e) {
    $users_last_30_days = 0;
    $most_active_day = "-";
    $most_active_count = 0;
}

// isletme Buyumesi
try {
    // Total
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM users 
        WHERE role = 'business' 
        AND YEAR(created_at) = YEAR(CURDATE()) 
        AND MONTH(created_at) = MONTH(CURDATE())
    ");
    $stmt->execute();
    $business_this_month = $stmt->fetchColumn();

    // Next month total
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM users 
        WHERE role = 'business' 
        AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
        AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
    ");
    $stmt->execute();
    $business_last_month = $stmt->fetchColumn();

    // Count
    if ($business_last_month > 0) {
        $business_growth_percent = round((($business_this_month - $business_last_month) / $business_last_month) * 100);
    } else {
        $business_growth_percent = 0;
    }

} catch (Exception $e) {
    $business_this_month = 0;
    $business_last_month = 0;
    $business_growth_percent = 0;
}

// Reviews last 7 days 
try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM reviews
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ");
    $stmt->execute();
    $reviews_last_7_days = $stmt->fetchColumn();
} catch (Exception $e) {
    $reviews_last_7_days = 0;
}



?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks - Admin V1.0</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png">

</head>
<body id="page-top">
<div id="wrapper">

<?php include('admin-sidebar.php'); ?>

<!-- Content -->
<div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

<!-- Topbar -->
<?php include('includes/topbar.php'); ?>
<!-- Topbar -->


<!-- =================================== -->

            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Admin V1.0</h1>

                <!-- statistik Kartları -->
                <div class="row">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                               <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                  <i class="fas fa-user-plus mr-1"></i> Bugün Kayıt Olan Tüketiciler
                              </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $today_users; ?></div>
                            </div>
                        </div>
                    </div>
                  
                  
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <i class="fas fa-store mr-1"></i> Bugün Eklenen İşletmeler
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $today_businesses; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                  
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                               <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                  <i class="fas fa-star mr-1"></i> Bugünkü İnceleme Sayısı
                              </div>
                              <div class="h5 mb-0 font-weight-bold text-gray-800">
                                  <?php echo $today_reviews; ?>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


<!-- Genel Analitik Veriler -->
<div class="container d-flex flex-column align-items-center mt-5">

    <h2 class="h4 mb-4 font-weight-bold text-gray-800 text-center">
      <span style="font-size: 22px;">
          <i class="fas fa-chart-bar mr-1"></i> Genel Analitik Veriler
      </span>
    </h2>
  
    <div class="row justify-content-center w-100">
  
      <!-- Tuketici Artisi -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow border-left-primary" style="border-radius: 10px;">
          <div class="card-body">
            <h6 class="font-weight-bold text-primary mb-2">
                <i class="fas fa-chart-line mr-1"></i> Tüketici Artış
            </h6>
            <p class="text-muted mb-1">
                Son 30 günde toplam <strong><?php echo $users_last_30_days; ?></strong> yeni tüketici kaydı yapıldı.
            </p>
            <p class="mb-0">
                En yoğun gün: <strong><?php echo $most_active_day; ?></strong> – <?php echo $most_active_count; ?> kayt
            </p>
          </div>
        </div>
      </div>
  
      <!-- isletme buyumesi -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow border-left-success" style="border-radius: 10px;">
          <div class="card-body">
            <h6 class="font-weight-bold text-success mb-2">
                <i class="fas fa-briefcase mr-1"></i> İşletme Büyümesi
            </h6>
            <p class="text-muted mb-1">
                Bu ay <strong><?php echo $business_this_month; ?></strong> yeni işletme kayıt oldu.
            </p>
            <p class="mb-0">
                Geçen aya göre 
                <span class="text-success font-weight-bold">
                    <?php echo ($business_growth_percent >= 0 ? '+' : '') . $business_growth_percent; ?>%
                </span> 
                <?php echo ($business_growth_percent >= 0 ? 'artış' : 'azalş'); ?>
            </p>
          </div>
        </div>
      </div>
  
      <!-- İnceleme Aktivitesi -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow border-left-warning" style="border-radius: 10px;">
          <div class="card-body">
            <h6 class="font-weight-bold text-warning mb-2">
                <i class="fas fa-comments mr-1"></i> İnceleme Aktivitesi
            </h6>
            <p class="text-muted mb-1">
                Son 7 gün içinde <strong><?php echo $reviews_last_7_days; ?></strong> yeni inceleme girildi.
            </p>
            <p class="mb-0 text-muted">
                %68 olumlu, %20 nötr, %12 olumsuz <span class="text-secondary">(Gerçek veri API sonrası)</span>
            </p>
          </div>
        </div>
      </div>
  
      <!-- Paket Tercihleri -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow border-left-dark" style="border-radius: 10px;">
          <div class="card-body">
           <h6 class="font-weight-bold text-dark mb-2">
                <i class="fas fa-box-open mr-1"></i> Paket Tercihleri
            </h6>
            <p class="text-muted mb-1">
                En çok tercih edilen: <strong>Premium</strong> (%57)
            </p>
          </div>
        </div>
      </div>
  
    </div>
  </div>
  

        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                     <span>© <?php echo date('Y'); ?> Puandeks</span>
                </div>
            </div>
        </footer>
    </div>
</div>




<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>


</body>
</html>
