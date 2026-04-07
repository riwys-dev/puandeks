<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: /admin-business/");
    exit;
}

require_once('/home/puandeks.com/backend/config.php'); // her zaman yüklensin

if (empty($_SESSION['company_name'])) {
    $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
    $stmt->execute([$_SESSION['company_id']]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['company_name'] = $company['name'] ?? 'İşletme';
}

$BUSINESS_NAME = $_SESSION['company_name'] ?? 'İşletme';
$company_id = $_SESSION['company_id'];

// Toplam inceleme sayısı
$stmtTotal = $pdo->prepare("SELECT COUNT(*) AS total_reviews FROM reviews WHERE company_id = ? AND status = 1");
$stmtTotal->execute([$company_id]);
$totalReviews = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_reviews'] ?? 0;

// Yanıtlanan inceleme sayısı
$stmtReplied = $pdo->prepare("SELECT COUNT(*) AS replied_reviews FROM reviews WHERE company_id = ? AND reply IS NOT NULL AND reply != '' AND status = 1");
$stmtReplied->execute([$company_id]);
$repliedReviews = $stmtReplied->fetch(PDO::FETCH_ASSOC)['replied_reviews'] ?? 0;

// Puan trendi (son 6 ay için)
$stmtTrend = $pdo->prepare("
  SELECT 
    DATE_FORMAT(created_at, '%M') AS month_name,
    ROUND(AVG(rating), 1) AS avg_rating
  FROM reviews
  WHERE company_id = ? AND status = 1
  GROUP BY DATE_FORMAT(created_at, '%Y-%m')
  ORDER BY created_at ASC
  LIMIT 6
");
$stmtTrend->execute([$company_id]);
$trendData = $stmtTrend->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Puandeks - <?= htmlspecialchars($BUSINESS_NAME, ENT_QUOTES, 'UTF-8') ?></title>

     <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet"> 
    <link href="css/business-admin.css" rel="stylesheet">
    <!-- Favicon  -->
    <link rel="icon" href="img/favicon.png">

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
                <h1 class="h3 mb-4 text-gray-800">Hoş geldiniz, <strong><?= htmlspecialchars($_SESSION['company_name']) ?></strong></h1>

<!-- Kartlar -->
<div style="display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 32px;">
  
   <!-- Toplam İnceleme -->
    <div style="flex: 1 1 30%; background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
      <div>
        <div style="font-size: 20px; color: #888; margin-bottom: 6px;">Toplam İnceleme</div>
        <div style="font-size: 28px; font-weight: bold; color: #C1C1C1;">
          <?= number_format($totalReviews, 0, ',', '.') ?>
        </div>
      </div>
      <img src="img/icons/review-total.svg" alt="İnceleme İkonu" style="height:48px; opacity:0.65; margin-right:32px;">
    </div>

    <!-- Yanıtlanan İnceleme -->
    <div style="flex: 1 1 30%; background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
      <div>
        <div style="font-size: 20px; color: #888; margin-bottom: 6px;">Yanıtlanan İnceleme</div>
        <div style="font-size: 28px; font-weight: bold; color: #C1C1C1;">
          <?= number_format($repliedReviews, 0, ',', '.') ?>
        </div>
      </div>
      <img src="img/icons/review-back.svg" alt="Yanıtlanan İkonu" style="height:54px; opacity:0.65; margin-right:32px;">
    </div>


    <div style="flex: 1 1 30%; background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
  <div>
    <div style="font-size: 20px; color: #888; margin-bottom: 6px;">Ortalama Puan</div>
    <?php
      require_once('/home/puandeks.com/backend/config.php');
      $company_id = $_SESSION['company_id'];

      // Sadece onaylı (status = 1) yorumlardan ortalama hesapla
      $stmt = $pdo->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE company_id = ? AND status = 1");
      $stmt->execute([$company_id]);
      $avg = $stmt->fetch(PDO::FETCH_ASSOC);
      $average_rating = $avg && $avg['avg_rating'] ? round($avg['avg_rating'], 1) : 0;
    ?>
    <div style="font-size: 28px; font-weight: bold; color: #C1C1C1;">
      <?= number_format($average_rating, 1) ?> / 5
    </div>
  </div>
  <img src="https://puandeks.com/img/core/vote_<?= floor($average_rating) ?>.svg" alt="Yıldız İkonu" style="height: 25px; margin-right: 40px;">
</div>




</div>
              

<!-- Raporlar -->
<div style="display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 32px;">
  
  <!-- İnceleme Dağılımı -->
  <div style="flex: 1 1 300px; min-width: 280px; background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px;">
    <div style="font-size: 20px; color: #888; margin-bottom: 8px;">İnceleme Dağılımı</div>
    <div style="position: relative; width: 100%; max-width: 400px;">
      <canvas id="reviewDistribution"></canvas>
    </div>
  </div>

  <!-- Puan Trendleri -->
  <div style="flex: 1 1 300px; min-width: 280px; background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px;">
    <div style="font-size: 20px; color: #888; margin-bottom: 8px;">Puan Trendleri</div>
    <div style="position: relative; width: 100%; max-width: 600px;">
      <canvas id="scoreTrends"></canvas>
    </div>
  </div>

</div>
<!-- Raporlar -->
              

</div>
</div>

</div>
</div>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
<script src="js/dropdown.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
document.addEventListener("DOMContentLoaded", async function () {
  try {
    const res = await fetch("api/get-company-review-stats.php");
    const data = await res.json();

    if (data.status === "success") {
      document.querySelector(".total-review").textContent = data.total_reviews;
      document.querySelector(".replied-review").textContent = data.replied_reviews;
      document.querySelector(".average-score").textContent = data.average_score + " / 5";
    }
  } catch (err) {
    console.error("nceleme verileri alnamadı.");
  }
});
</script>

<!-- Notif count -->
<script>
function updateNotificationCount() {
  fetch('api/get-company-unread-count.php')
    .then(res => res.json())
    .then(data => {
      const count = parseInt(data.count || 0);
      const el = document.getElementById('notification-count');
      if (el) {
        el.innerText = count;
        el.style.display = count > 0 ? 'inline-block' : 'none';
      }
    });
}

updateNotificationCount();
setInterval(updateNotificationCount, 30000);
</script>
<!-- Notif count -->


<!-- Rapor Grafikleri -->
<script>
// Pie Chart - İnceleme Dağılımı
new Chart(document.getElementById("reviewDistribution"), {
  type: 'pie',
  data: {
    labels: ["Olumlu", "Olumsuz", "Nötr"],
    datasets: [{
      data: [60, 25, 15],
      backgroundColor: ["#3498DB", "#E74C3C", "#F1C40F"], // Mavi, Kırmızı, Sarı
      borderWidth: 1.5,
      borderColor: "#fff"
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 1.6,
    plugins: {
      legend: {
        position: 'bottom'
      }
    }
  }
});

// Line Chart - Puan Trendleri
const trendLabels = <?= json_encode(array_column($trendData, 'month_name')) ?>;
const trendValues = <?= json_encode(array_column($trendData, 'avg_rating')) ?>;

new Chart(document.getElementById("scoreTrends"), {
  type: 'line',
  data: {
    labels: trendLabels,
    datasets: [{
      label: "Ortalama Puan",
      data: trendValues,
      borderColor: "#3498DB",
      backgroundColor: "rgba(52, 152, 219, 0.2)",
      fill: true,
      tension: 0.3,
      pointBackgroundColor: "#E74C3C",
      pointRadius: 5
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 2.2,
    scales: {
      y: {
        min: 0,
        max: 5
      }
    },
    plugins: {
      legend: {
        display: false
      }
    }
  }
});
</script>
<!-- Rapor Grafikleri -->



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

</body>
</html>
