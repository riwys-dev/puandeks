<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

$plan = $_GET['plan'] ?? null;

// period güvenliği
$allowedPeriods = ['monthly', 'yearly'];
$period = htmlspecialchars($_GET['period'] ?? 'monthly');

if (!in_array($period, $allowedPeriods)) {
    $period = 'monthly';
}

// default değerler (plans sayfası patlamasın)
$package = null;
$price = null;
$term = null;

// SADECE plan varsa çalış
if ($plan) {

    $stmt = $conn->prepare("SELECT * FROM packages WHERE slug = ?");
    $stmt->execute([$plan]);
    $package = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$package) {
        die("Paket bulunamadı");
    }

    if ($period === 'yearly') {

        if ($package['price_yearly'] === null) {
            die("Yıllık plan mevcut değil");
        }

        $price = $package['price_yearly'];
        $term = 'yıl';

    } else {

        $price = $package['price_monthly'];
        $term = 'ay';
    }
}

$role = $_SESSION['role'] ?? null;
$company_display = '';
$company_logo = 'https://puandeks.com/img/placeholder/user.png';

$currentSubscription = null;
$currentPackageOrder = null;
$hasUsedTrial = false;

if ($role === 'business' && isset($_SESSION['company_id'])) {

    try {

        // Şirket bilgisi
        $stmt = $conn->prepare("SELECT name, logo FROM companies WHERE id = ?");
        $stmt->execute([$_SESSION['company_id']]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($company) {
            $company_display = htmlspecialchars($company['name']);
            if (!empty($company['logo'])) {
                $company_logo = htmlspecialchars($company['logo']);
            }
        }

        // Aktif veya trial abonelik
        $stmtSub = $conn->prepare("
            SELECT *
            FROM company_subscriptions
            WHERE company_id = ?
            AND status IN ('trial','active')
            AND end_date >= CURDATE()
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmtSub->execute([$_SESSION['company_id']]);
        $currentSubscription = $stmtSub->fetch(PDO::FETCH_ASSOC);

        // Mevcut paket sırası
        if ($currentSubscription) {
            $stmtPkg = $conn->prepare("SELECT sort_order FROM packages WHERE id = ?");
            $stmtPkg->execute([$currentSubscription['package_id']]);
            $currentPackageOrder = $stmtPkg->fetchColumn();
        }

        // Trial kontrol
        $stmtTrial = $conn->prepare("
            SELECT COUNT(*)
            FROM company_subscriptions
            WHERE company_id = ?
            AND trial_used = 1
        ");
        $stmtTrial->execute([$_SESSION['company_id']]);
        $hasUsedTrial = $stmtTrial->fetchColumn() > 0;

    } catch (PDOException $e) {
        echo "Veritabanı hatası";
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
	<title>Puandeks İşletme</title>
  
      <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

	<!-- Favicons-->
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" type="image/x-icon" href="https://puandeks.com/img/apple-touch-icon-57x57-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="https://puandeks.com/img/apple-touch-icon-72x72-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="114x114"
		href="img/apple-touch-icon-114x114-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="144x144"
		href="https://puandeks.com/img/apple-touch-icon-144x144-precomposed.png">

	<!-- GOOGLE WEB FONT -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

   <link rel="stylesheet" href="css/jquery.mmenu.all.css">

    <link href="css/bootstrap.min.css" rel="stylesheet">
	  <link href="css/style.css" rel="stylesheet">
	  <link href="css/vendors.css" rel="stylesheet">
	  <link href="css/custom.css" rel="stylesheet">
    <link href="css/plans.css" rel="stylesheet">

<!-- CHARTS -->
<style>
/* DESKTOP  */
.pricing-chart-container {
  max-width:1280px;
  margin:0 auto;
  padding:0 20px;
}

.pricing-chart-scroll {
  overflow-x:auto;
}

.pricing-table {
  min-width:1200px;
  border:1px solid #eee;
  border-radius:12px;
  background:#fff;
}

.pricing-header-grid,
.pricing-row {
  display:grid;
  grid-template-columns: 2.5fr repeat(5,1fr);
  align-items:center;
}

.pricing-header-grid {
  background:#f3f4f6;
  padding:20px 10px;
  font-weight:600;
  font-size:14px;
  border-bottom:1px solid #e5e7eb;
}

.pricing-row {
  padding:18px;
  border-top:1px solid #e5e7eb;
  font-size:14px;
}

/* -------------------- */
/* MOBILE / TABLET  */
/* -------------------- */

.mobile-chart{
  display:none;
}

@media (max-width:1024px){

  .desktop-table{
    display:none;
  }

  .mobile-chart{
    display:block;
  }

  .mobile-features{
    border:1px solid #eee;
    border-radius:12px;
    padding:10px;
    background:#fff;
  }

  .mobile-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 0;
    border-bottom:1px solid #eee;
  }

  .mobile-row:last-child{
    border-bottom:none;
  }

  .feature-name{
    max-width:70%;
    font-size:14px;
    line-height:1.4;
  }

  .feature-value{
    font-weight:600;
    font-size:14px;
  }

}

.mobile-row .feature-value{
  display:none;
}
</style>
<!-- CHARTS -->

<style>

#customDropdown {
  border:1px solid #e5e7eb;
  border-radius:10px;
  padding:12px;
  cursor:pointer;
  position:relative;
  background:#fff;
}

/* Seçili text */
#dropdownSelected {
  display:block;
  font-weight:600;
}

/* OK */
#customDropdown::after {
  content:"▼";
  position:absolute;
  right:12px;
  top:50%;
  transform:translateY(-50%);
  font-size:12px;
  transition:0.2s;
}

#customDropdown.open::after {
  content:"▲";
}

#customDropdown{
  border-top-left-radius:0;
  border-top-right-radius:0;
}

/* Liste */
#dropdownOptions {
  position:absolute;
  top:100%;
  left:0;
  width:100%;
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:10px;
  margin-top:5px;
  list-style:none;
  padding:0;
  z-index:100;
  display:none;
}

/* Free tekrar görünmesin */
#dropdownOptions li:first-child {
  display:none;
}

/* item */
#dropdownOptions li {
  padding:10px;
  border-bottom:1px solid #eee;
  cursor:pointer;
}

#dropdownOptions li:last-child {
  border-bottom:none;
}

#dropdownOptions li:hover {
  background:#f3f4f6;
}

#dropdownOptions li{
  display:flex;
  justify-content:space-between;
  align-items:center;
}


#customDropdown{
  border-bottom-left-radius:0;
  border-bottom-right-radius:0;
  margin-bottom:0;
}


.mobile-features{
  margin-top:0;
  border-top-left-radius:0;
  border-top-right-radius:0;
}

.filter_type{
  margin-bottom:0 !important;
}

.plan-name.free { color:#1c1c1c; }
.plan-name.plus { color:#1c1c1c; }
.plan-name.premium { color:#f59e0b; }
.plan-name.advanced { color:#3b82f6; }
.plan-name.enterprise { color:#1c1c1c; }

.plan-btn{
  font-size:12px;
  padding:6px 10px;
}

/* plan isimleri */
.plan-name{
  font-weight:600;
  font-size:14px;
}

/* buton genel */
.plan-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:110px;
  height:32px;
  padding:0 14px;
  border-radius:20px;
  font-size:12px;
  font-weight:500;
  text-align:center;
}
/* FREE */
.plan-btn.btn-green{
  background:#22c55e;
  color:#fff;
}

/* PLUS + PREMIUM */
.plan-btn.btn-outline{
  border:1px solid #22c55e;
  color:#22c55e;
  background:#fff;
}

/* ADVANCED */
.plan-btn.btn-blue{
  background:#3b82f6;
  color:#fff;
}

/* ENTERPRISE */
.plan-btn.btn-yellow{
  background:#facc15;
  color:#000;
}

.plan-col{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
}

.plan-col .plan-name{
  text-align:center;
  margin-bottom:6px;
}

.pricing-section-title{
  background:#f3f4f6;
  padding:18px 20px;
  margin-top:10px;
  margin-bottom:10px;
  font-weight:600;
  border-top:1px solid #e5e7eb;
  border-bottom:1px solid #e5e7eb;
}

.compare-title{
  font-weight:600;
  font-size:16px;
  text-align:left;
  padding-left:10px;
}

.mobile-compare-title{
  background:#f3f4f6;
  padding:14px 16px;
  font-weight:600;
  font-size:16px;
  border-radius:12px 12px 0 0;
}

</style>

<style>
  @media (max-width: 991px) {
    #mm-menu ul, 
    #mm-menu .mm-listview {
      display: flex !important;
      flex-direction: column !important;
      align-items: flex-start !important;
      justify-content: flex-start !important;
      gap: 8px !important;
    }

    #mm-menu li, 
    #mm-menu .mm-listview li {
      width: 100% !important;
    }

    #mm-menu a {
      display: block !important;
      width: 100% !important;
      padding: 10px 20px !important;
      color: #fff !important;
      text-align: left !important;
      text-decoration: none !important;
    }
  }
</style>

<style>
  .mm-menu {
    background-color: #1C1C1C !important;
  }
</style>

</head>

<body>
<div id="page">

<?php include 'inc/header.php'; ?>

<!-- main -->
<main class="pricing-page">

    <!-- Başlık -->
    <section class="pricing-header">
        <h1>İşletmeniz için mükemmel planı seçin</h1>
        <p>Tüketicilerin %89'u satın alma işlemi yapmadan önce çevrimiçi yorumları
            kontrol ediyor. Doğru planı sein ve güven oluşturun.</p>
    </section>

    <!-- Toggle -->
    <section class="pricing-toggle">
        <span id="label-monthly">Aylık</span>

        <label class="switch">
            <input id="billingSwitch" type="checkbox">
            <span class="switch-track"></span>
            <span class="switch-knob"></span>
        </label>

        <span id="label-yearly">Yıllk</span>
    </section>

    <!-- Pricing Grid -->
      <?php
      $packagesStmt = $conn->query("
        SELECT *
        FROM packages
        WHERE is_active = 1
        ORDER BY sort_order ASC
      ");
      $packages = $packagesStmt->fetchAll(PDO::FETCH_ASSOC);
      ?>

      <section class="pricing-grid">

      <?php foreach ($packages as $p): ?>

        <div class="pricing-card <?= htmlspecialchars($p['slug']) ?>">

          <!-- HEADER -->
          <div class="card-header">

          <!-- Badge -->
            <?php
              $showBadge = true;

              if (
                  $role === 'business' &&
                  isset($_SESSION['company_id']) &&
                  $hasUsedTrial
              ) {
                  $showBadge = false;
              }

              if ($showBadge && !empty($p['label_text'])):
              ?>
                <div class="badge"><?= htmlspecialchars($p['label_text']) ?></div>
              <?php endif; ?>
         <!-- Badge -->


            <h2><?= htmlspecialchars($p['name']) ?></h2>

            <div class="price">
              <?php if ($p['price_monthly'] !== null): ?>
                <span class="amount"
                      data-monthly="<?= (int)$p['price_monthly'] ?>"
                      data-yearly="<?= (int)$p['price_yearly'] ?>">
                  <?= number_format($p['price_monthly'], 0, ',', '.') ?>
                </span>
                <span class="term">/ ay</span>
              <?php else: ?>
                <span style="color:#1c1c1c;font-size:20px;margin-top:14px;">
                  Özel Fiyatlandırma
                </span>
              <?php endif; ?>
            </div>

          </div>

          <!-- CONTENT -->
          <div class="card-content">
            <ul>
              <?php
              $features = json_decode($p['features'], true) ?? [];
              foreach ($features as $f):
              ?>
                <li><?= htmlspecialchars($f) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- FOOTER -->
          <div class="card-footer">
          <?php

          $btnText = '';
          $btnLink = '#';
          $btnDisabled = false;

          $isLoggedBusiness = ($role === 'business' && isset($_SESSION['company_id']));
          $currentPackageId = $currentSubscription['package_id'] ?? null;

          // Enterprise
          if ($p['button_type'] === 'contact') {
              $btnText = 'Teklif Al';
              $btnLink = 'https://puandeks.com/contact-sales';
          }

          // Business login değil
          elseif (!$isLoggedBusiness) {
              $btnText = 'Ücretsiz Dene';
              $btnLink = 'https://business.puandeks.com/register?plan='.$p['slug'];
          }

          // Business login
          else {

              // Aynı paket
              if ($currentPackageId == $p['id']) {
                  $btnText = 'Mevcut Plan';
                  $btnDisabled = true;
              }

             // Aktif subscription yok
            elseif (!$currentSubscription) {

                $period = $_GET['period'] ?? 'monthly';

                if (!$hasUsedTrial) {
                    $btnText = 'Ücretsiz Dene';
                    $btnLink = 'https://business.puandeks.com/payment?plan='.$p['slug'].'&period='.$period.'&type=trial';
                } else {
                    $btnText = 'Satın Al';
                    $btnLink = 'https://business.puandeks.com/payment?plan='.$p['slug'].'&period='.$period;
                }
            }

              // Aktif subscription varsa
              else {

                  // Upgrade kontrolü sort_order ile
                  if ($p['sort_order'] > $currentPackageOrder) {

                      $btnText = 'Yükselt';
                      $btnLink = '#';

                  } else {

                      // Downgrade kapalı (MVP)
                      $btnText = 'Mevcut Paketten Düşük';
                      $btnDisabled = true;
                  }
              }
          }

          ?>
          <a href="<?= $btnLink ?>"
            class="btn <?= htmlspecialchars($p['slug']) ?>-btn <?= $btnDisabled ? 'disabled' : '' ?>"
            <?= $btnDisabled ? 'style="pointer-events:none;opacity:0.6;"' : '' ?>>
            <?= $btnText ?>
          </a>
          </div>

        </div>

      <?php endforeach; ?>

</section>
<!-- Pricing Grid -->

<br><br>

<!-- CHARTS Desktop -->
<section class="pricing-chart-section">
  <div class="pricing-chart-container">
    <div class="pricing-chart-scroll">
      
      <div class="pricing-table desktop-table">

        <!-- HEADER -->
        <div class="pricing-header-grid">

          <div class="compare-title">
          Paketleri karşılaştır
         </div>

          <div class="plan-col">
            <div class="plan-name free">Free</div>
          </div>

          <div class="plan-col">
            <div class="plan-name plus">Plus</div>
            <div class="mt-10">
              <span class="plan-btn btn-outline">7 Gün Ücretsiz Dene</span>
            </div>
          </div>

          <div class="plan-col">
            <div class="plan-name premium">Premium</div>
            <div class="mt-10">
              <span class="plan-btn btn-outline">7 Gün Ücretsiz Dene</span>
            </div>
          </div>

          <div class="plan-col">
            <div class="plan-name advanced">Advanced</div>
            <div class="mt-10">
              <span class="plan-btn btn-blue">7 Gün Ücretsiz Dene</span>
            </div>
          </div>

          <div class="plan-col">
            <div class="plan-name enterprise">Enterprise</div>
            <div class="mt-10">
             <a href="https://puandeks.com/contact-sales" class="plan-btn btn-yellow">Teklif Al</a>
            </div>
          </div>

        </div>

        <div class="pricing-section-title">
          İtibarınızı Yönetin
        </div>

        <!-- ROWS -->
        <div class="pricing-row">
          <div>Otomatik aylık yorum daveti</div>
          <div class="text-center danger">✕</div>
          <div class="text-center">200</div>
          <div class="text-center">400</div>
          <div class="text-center">800</div>
          <div class="text-center">Sınırsız</div>
        </div>

        <div class="pricing-row">
          <div>Web Sitesi Yorum Widgetları</div>
          <div class="text-center danger">✕</div>
          <div class="text-center success">✓</div>
          <div class="text-center success">✓</div>
          <div class="text-center success">✓</div>
          <div class="text-center success">✓</div>
        </div>

        <div class="pricing-row">
          <div>E-ticaret Entegrasyonları</div>
          <div class="text-center success">✓</div>
          <div class="text-center success">✓</div>
          <div class="text-center success">✓</div>
          <div class="text-center success">✓</div>
          <div class="text-center success">✓</div>
        </div>

        <div class="pricing-row">
          <div>Site Dışı Yorum Yanıtları</div>
          <div class="text-center danger">✕</div>
          <div class="text-center">Sınırlı</div>
          <div class="text-center">Sınırlı</div>
          <div class="text-center">Sınırlı</div>
          <div class="text-center">Sınırsız</div>
        </div>

        <div class="pricing-row">
          <div>AI İnceleme Özeti</div>
          <div class="text-center danger">✕</div>
          <div class="text-center danger">✕</div>
          <div class="text-center danger">✕</div>
          <div class="text-center success">✓</div>
          <div class="text-center success">✓</div>
        </div>

        <div class="pricing-row">
  <div>Güven Rozeti</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>Canlı Destek</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>İşletme Banner</div>
  <div class="text-center danger">✕</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>KVKK Uyumlu Arama</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>KVKK Uyumlu Mesajlaşma</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>İşletme Doğrulama</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>E-Posta Desteği</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>Performans Genel Bakışı</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>Şüpheli yorumları işaretle</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>Yapay Zeka Destekli İnceleme Yanıtları</div>
  <div class="text-center danger">✕</div>
  <div class="text-center danger">✕</div>
  <div class="text-center danger">✕</div>
  <div class="text-center danger">✕</div>
  <div class="text-center">Yakında</div>
</div>

<div class="pricing-row">
  <div>Sosyal Medya Paylaşım Şablonları</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>Özel Yazılım Entegrasyonu API</div>
  <div class="text-center danger">✕</div>
  <div class="text-center danger">✕</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row">
  <div>Müşteri Başarı Yöneticisi</div>
  <div class="text-center danger">✕</div>
  <div class="text-center danger">✕</div>
  <div class="text-center danger">✕</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
</div>

<div class="pricing-row last-row">
  <div>Google Merchant Center ve Ürün Değerlendirme Entegrasyonu</div>
  <div class="text-center danger">✕</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
  <div class="text-center success">✓</div>
</div>

</div>
</div>
</div>
</section>
<!-- CHARTS DESKTOP -->

<!-- MOBILE / TABLET DROPDOWN SYSTEM -->
<section class="pricing-chart-section mobile-chart">

  <div class="mobile-compare-title">
    Paketleri karşılaştır
  </div>

  <div class="filter_type" style="margin-bottom:20px;">
    <div id="customDropdown">

      <span id="dropdownSelected">Free</span>

      <ul id="dropdownOptions" style="display:none;">
        <li data-value="free">
          <span class="plan-name free">Free</span>
          <span class="plan-btn btn-green">Ücretsiz Başla</span>
        </li>

        <li data-value="plus">
          <span class="plan-name plus">Plus</span>
          <span class="plan-btn btn-outline">7 Gün Ücretsiz Dene</span>
        </li>

        <li data-value="premium">
          <span class="plan-name premium">Premium</span>
          <span class="plan-btn btn-outline">7 Gün Ücretsiz Dene</span>
        </li>

        <li data-value="advanced">
          <span class="plan-name advanced">Advanced</span>
          <span class="plan-btn btn-blue">7 Gün Ücretsiz Dene</span>
        </li>

        <li data-value="enterprise">
          <span class="plan-name enterprise">Enterprise</span>
          <a href="https://puandeks.com/contact-sales" class="plan-btn btn-yellow">Teklif Al</a>
        </li>
      </ul>

      <input type="hidden" id="categoryInput" value="free">

    </div>
  </div>

  <!-- FEATURES -->
  <div class="mobile-features">

    <div class="mobile-row">
      <span class="feature-name">Otomatik aylık yorum daveti</span>

      <span class="feature-value" data-plan="free">✕</span>
      <span class="feature-value" data-plan="plus">200</span>
      <span class="feature-value" data-plan="premium">400</span>
      <span class="feature-value" data-plan="advanced">800</span>
      <span class="feature-value" data-plan="enterprise">Sınırsız</span>
    </div>

    <div class="mobile-row">
      <span class="feature-name">Web Sitesi Yorum Widgetları</span>

      <span class="feature-value" data-plan="free">✕</span>
      <span class="feature-value" data-plan="plus">✓</span>
      <span class="feature-value" data-plan="premium">✓</span>
      <span class="feature-value" data-plan="advanced">✓</span>
      <span class="feature-value" data-plan="enterprise">✓</span>
    </div>

    <div class="mobile-row">
      <span class="feature-name">E-ticaret Entegrasyonları</span>

      <span class="feature-value" data-plan="free">✓</span>
      <span class="feature-value" data-plan="plus">✓</span>
      <span class="feature-value" data-plan="premium">✓</span>
      <span class="feature-value" data-plan="advanced">✓</span>
      <span class="feature-value" data-plan="enterprise">✓</span>
    </div>

    <div class="mobile-row">
      <span class="feature-name">Site Dışı Yorum Yanıtları</span>

      <span class="feature-value" data-plan="free">✕</span>
      <span class="feature-value" data-plan="plus">Sınırlı</span>
      <span class="feature-value" data-plan="premium">Sınırlı</span>
      <span class="feature-value" data-plan="advanced">Sınırlı</span>
      <span class="feature-value" data-plan="enterprise">Sınırsız</span>
    </div>

    <div class="mobile-row">
      <span class="feature-name">AI İnceleme Özeti</span>

      <span class="feature-value" data-plan="free">✕</span>
      <span class="feature-value" data-plan="plus">✕</span>
      <span class="feature-value" data-plan="premium">✕</span>
      <span class="feature-value" data-plan="advanced">✓</span>
      <span class="feature-value" data-plan="enterprise">✓</span>
    </div>

    <div class="mobile-row">
  <span class="feature-name">Güven Rozeti</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">Canlı Destek</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">İşletme Banner</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✕</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">KVKK Uyumlu Arama</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">KVKK Uyumlu Mesajlaşma</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">İşletme Doğrulama</span>
  <span class="feature-value" data-plan="free">✓</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">E-Posta Desteği</span>
  <span class="feature-value" data-plan="free">✓</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">Performans Genel Bakışı</span>
  <span class="feature-value" data-plan="free">✓</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">Şüpheli yorumları işaretle</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">Yapay Zeka Destekli İnceleme Yanıtları</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✕</span>
  <span class="feature-value" data-plan="premium">✕</span>
  <span class="feature-value" data-plan="advanced">✕</span>
  <span class="feature-value" data-plan="enterprise">Yakında</span>
</div>

<div class="mobile-row">
  <span class="feature-name">Sosyal Medya Paylaşım Şablonları</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">Özel Yazılım Entegrasyonu API</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✕</span>
  <span class="feature-value" data-plan="premium">✕</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">Müşteri Başarı Yöneticisi</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✕</span>
  <span class="feature-value" data-plan="premium">✕</span>
  <span class="feature-value" data-plan="advanced">✕</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

<div class="mobile-row">
  <span class="feature-name">Google Merchant Center ve Ürün Değerlendirme Entegrasyonu</span>
  <span class="feature-value" data-plan="free">✕</span>
  <span class="feature-value" data-plan="plus">✓</span>
  <span class="feature-value" data-plan="premium">✓</span>
  <span class="feature-value" data-plan="advanced">✓</span>
  <span class="feature-value" data-plan="enterprise">✓</span>
</div>

  </div>

</section>
  

<!-- SSS (FAQ) -->
<section class="faq-wrapper">
    <h2 class="faq-title">Sıkça Sorulan Sorular</h2>

    <!-- SORU 1 -->
    <div class="faq-item">
        <button class="faq-question">
            <div class="left">
                <i class="fa-solid fa-circle-question faq-icon"></i>
                Puandeks’i kullanmak için işimi kurmaya karar verdiğimde herhangi bir maliyet var mı?
            </div>
            <i class="fa-solid fa-chevron-right faq-arrow"></i>
        </button>
        <div class="faq-answer">
            Hiçbir şey! Puandeks yolculuğunuza ücretsiz başlayabilir, markanızı sahiplenebilir, müşteri güveni
            oluşturabilir ve temel özelliklerden hemen yararlanabilirsiniz. Finansal taahhüt olmadan
            deneyimleyebilirsiniz.
        </div>
    </div>

    <!-- SORU 2 -->
    <div class="faq-item">
        <button class="faq-question">
            <div class="left">
                <i class="fa-solid fa-book-open faq-icon"></i>
                Ücretsiz hesaba kaydolduğumda eğitim veya destek veriyor musunuz?
            </div>
            <i class="fa-solid fa-chevron-right faq-arrow"></i>
        </button>
        <div class="faq-answer">
            Ücretsiz planımız, Destek Merkezimize sınırsız erişim sağlar. Ayrıca, pratik yardım için destek
          ekibimizle <a href="https://puandeks.com/contact-technical" target="blank">BURADAN</a> iletişime geçebilir veya ihtiyaç duyduğunuzda Premium Destek Paketleri'ne yükseltme yapabilirsiniz. 
        </div>
    </div>

    <!-- SORU 3 -->
    <div class="faq-item">
        <button class="faq-question">
            <div class="left">
                <i class="fa-solid fa-money-check-dollar faq-icon"></i>
                Puandeks'e kaydolduğumda ödeme yapmam gerekiyor mu?
            </div>
            <i class="fa-solid fa-chevron-right faq-arrow"></i>
        </button>
        <div class="faq-answer">
            Hayır! hesap oluşturmak tamamen ücretsizdir. İşletme profilinizi hemen oluşturabilir, müşteri
            yorumları toplamaya başlayabilir ve temel entegrasyon araçlarını kullanabilirsiniz. Daha fazla özellik
            için tüm paket detaylarını inceleyebilirsiniz. 
        </div>
    </div>

    <!-- SORU 4 -->
    <div class="faq-item">
        <button class="faq-question">
            <div class="left">
                <i class="fa-solid fa-building faq-icon"></i>
                Hizmetinizi kullanmayı bırakırsam yorumlarım ne olur?
            </div>
            <i class="fa-solid fa-chevron-right faq-arrow"></i>
        </button>
        <div class="faq-answer">
            Yorumlarınız Puandeks profil sayfanızda kalır. Müşteri geri bildirim geçmişinize her zaman
            erişebilirsiniz. Verileriniz silinmez veya gizlenmez. Dilediğiniz zaman kaldığınız yerden devam
            edebilirsiniz.
        </div>
    </div>

    <!-- SORU 5 -->
    <div class="faq-item">
        <button class="faq-question">
            <div class="left">
                <i class="fa-solid fa-calendar faq-icon"></i>
                Bir plana ne kadar süre bağlı kalacağım?
            </div>
            <i class="fa-solid fa-chevron-right faq-arrow"></i>
        </button>
        <div class="faq-answer">
            Planlarımız aylık veya yıllık ödemelidir. Herhangi bir taahhüt yoktur; istediğiniz zaman iptal
            edebilirsiniz. 7 gün ücretsiz deneme sunuyoruz. Yıllık aboneliklerde ödeme bir yıl için geçerlidir, iptal
            edebilirsiniz ancak ücret iadesi yapılmaz ve abonelik yenilenmez. Aylık aboneliklerde ise
            faturalandırma dönemi gelmeden iptal etme özgürlüğünüz vardır. Puandeks’ın işletmenize değer
            katacağına inanıyor, esnek ihtiyaçlar için özelleştirilmiş paketler sunuyoruz.
        </div>
    </div>

    <!-- SORU 6 -->
    <div class="faq-item">
        <button class="faq-question">
            <div class="left">
                <i class="fa-solid fa-sitemap faq-icon"></i>
                Hangi entegrasyonları destekliyorsunuz?
            </div>
            <i class="fa-solid fa-chevron-right faq-arrow"></i>
        </button>
        <div class="faq-answer">
            Entegrasyon seçeneklerimiz sürekli genişlemektedir.
          Mevcut aktif entegrasyonlarımıza hemen erişebilirsiniz. 
          <br>
             <?php
                // İşletme giriş kontrolü
                if (isset($_SESSION['role']) && $_SESSION['role'] === 'business') {
                    $integrationLink = "https://business.puandeks.com/integrations";
                } else {
                    $integrationLink = "https://business.puandeks.com/login";
                }
                ?>

                <div style="margin:40px 0 20px 0; text-align:left; font-size:14px; font-weight:600;">
                    Tüm entegrasyonlarımızı incelemek için:
                    <a href="<?= $integrationLink ?>" 
                       style="color:#0e7b4f; text-decoration:none; font-weight:700;">
                       Puandeks Entegrasyonlar Sayfası
                    </a>
                </div>

        </div>
    </div>
  



</section>



</main>
<!-- main -->	

<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	
	
</div>
<!-- page -->


<!-- COMMON SCRIPTS -->
<script src="js/jquery.min.js"></script>
<script src="js/jquery.mmenu.all.js"></script>
<script src="js/common_scripts.js"></script>
<script src="js/functions.js"></script>


<!-- SWITCH -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  const sw = document.getElementById("billingSwitch");
  const track = document.querySelector(".switch-track");
  const knob = document.querySelector(".switch-knob");
  const priceEls = document.querySelectorAll("[data-monthly]");

  function getPeriod() {
    return new URL(window.location.href).searchParams.get("period") || "monthly";
  }

  function updatePrices() {
    priceEls.forEach(el => {
      const monthly = parseInt(el.dataset.monthly);
      const yearly = parseInt(el.dataset.yearly);

      if (sw.checked) {
        el.textContent = yearly;
        el.nextElementSibling.textContent = "/ yıl";
      } else {
        el.textContent = "₺" + monthly;
        el.nextElementSibling.textContent = "/ ay";
      }
    });
  }

  function updateUI() {
    if (sw.checked) {
      track.style.background = "#04DA8D";
      knob.style.transform = "translateX(24px)";
    } else {
      track.style.background = "#1C1C1C";
      knob.style.transform = "translateX(0)";
    }
  }

  function updateURL() {
    const period = sw.checked ? "yearly" : "monthly";
    const url = new URL(window.location.href);
    url.searchParams.set("period", period);
    window.history.replaceState({}, "", url);
  }

  function updateButtons() {
    const period = sw.checked ? "yearly" : "monthly";

    document.querySelectorAll(".pricing-card a.btn").forEach(btn => {
      if (!btn.href.includes("payment")) return;

      const url = new URL(btn.href);
      url.searchParams.set("period", period);
      btn.href = url.toString();
    });
  }

  // INIT
  const currentPeriod = getPeriod();
  sw.checked = (currentPeriod === "yearly");

  updatePrices();
  updateUI();
  updateButtons();

  // CHANGE
  sw.addEventListener("change", () => {
    updatePrices();
    updateUI();
    updateURL();
    updateButtons();
  });

});
</script>
<!-- SWITCH -->

<!-- FAQ -->
<script>
document.querySelectorAll(".faq-question").forEach(btn => {
    btn.addEventListener("click", () => {
        const item = btn.parentElement;

        // Toggle
        item.classList.toggle("open");
    });
});
</script>
<!-- FAQ -->

<!-- Package button ALERT -->
<script>
document.querySelectorAll(".btn").forEach(btn => {
    if (btn.textContent.trim() === "Yükselt") {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            alert("Mevcut paketinizi yükseltmeniz için paket kullanım sürenizin bitmesi gerekli.");
        });
    }
});
</script>
<!-- Package button ALERT -->

<script>
document.addEventListener("DOMContentLoaded", function () {

  const dropdown = document.getElementById("customDropdown");
  const options = document.getElementById("dropdownOptions");
  const selected = document.getElementById("dropdownSelected");
  const hiddenInput = document.getElementById("categoryInput");

  // PLAN GÜNCELLE
  function updateView(plan){
    document.querySelectorAll(".mobile-row").forEach(row => {

      row.querySelectorAll(".feature-value").forEach(el=>{
        el.style.display = "none";
      });

      const active = row.querySelector(`.feature-value[data-plan="${plan}"]`);
      if(active){
        active.style.display = "block";
      }

    });
  }

  // default
  updateView("free");

  // dropdown aç/kapat
  dropdown.addEventListener("click", function (e) {
    e.stopPropagation();
    options.style.display = (options.style.display === "block") ? "none" : "block";
    dropdown.classList.toggle("open");
  });

  // seçim
  options.querySelectorAll("li").forEach(function (item) {
    item.addEventListener("click", function (e) {

      e.stopPropagation();

      const plan = this.getAttribute("data-value");

      selected.innerHTML = this.innerHTML;
      hiddenInput.value = plan;

      updateView(plan);

      options.style.display = "none";
      dropdown.classList.remove("open");

    });
  });

  // dışarı tıklayınca kapat
  document.addEventListener("click", function () {
    options.style.display = "none";
    dropdown.classList.remove("open");
  });

  // renkler (desktop + mobile)
  document.querySelectorAll(".feature-value, .pricing-row div").forEach(el => {

    if(el.textContent.trim() === "✓"){
      el.style.color = "#22c55e";
    }

    if(el.textContent.trim() === "✕"){
      el.style.color = "#ef4444";
    }

  });

});
</script>



</body>
</html>