<?php
session_start();

require_once('/home/puandeks.com/backend/config.php');

$plan = $_GET['plan'] ?? null;

// period güvenliği
$allowedPeriods = ['monthly', 'yearly'];
$period = $_GET['period'] ?? 'monthly';

if (!in_array($period, $allowedPeriods)) {
    die("Geçersiz period");
}

// plan kontrol
if (!$plan) {
    die("Geçersiz istek");
}

// paket çek
$stmt = $pdo->prepare("SELECT * FROM packages WHERE slug = ?");
$stmt->execute([$plan]);
$package = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$package) {
    die("Paket bulunamadı");
}

// yearly kontrol
if ($period === 'yearly' && $package['price_yearly'] === null) {
    die("Yıllık plan mevcut değil");
}

// fiyat
if ($period === 'yearly') {
    $price = $package['price_yearly'];
    $term = 'yıl';
} else {
    $price = $package['price_monthly'];
    $term = 'ay';
}

// trial kontrol
$hasUsedTrial = false;

if (isset($_SESSION['company_id'])) {
    $stmtTrial = $pdo->prepare("
        SELECT COUNT(*)
        FROM company_subscriptions
        WHERE company_id = ?
        AND trial_used = 1
    ");
    $stmtTrial->execute([$_SESSION['company_id']]);
    $hasUsedTrial = $stmtTrial->fetchColumn() > 0;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ödeme | Puandeks</title>

<link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>
body {
  font-family: 'Roboto', sans-serif;
  background:#FCFBF3;
  margin:0;
}

/* container */
.payment-container{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
}

/* box */
.payment-box{
  width:100%;
  max-width:420px;
  background:#fff;
  padding:30px;
  border-radius:12px;
  border:2px solid #e5e7eb;
}

/* header */
.payment-box h2{
  font-size:20px;
  font-weight:600;
  margin-bottom:25px;
  text-align:center;
}

/* plan */
.plan-info{
  background:#FCFBF3;
  border:none;
  padding:20px;
  border-radius:10px;
  margin-bottom:25px;
  text-align:center;
}

.plan-name{
  font-weight:700;
  font-size:20px;
}

.plan-price{
  font-size:20px;
  margin-top:8px;
  font-weight:500;
}

/* button */
.pay-btn{
  width:100%;
  padding:14px;
  background:#05462F;
  color:#fff;
  border:none;
  border-radius:8px;
  font-weight:600;
  cursor:pointer;
  transition:0.2s;
}

.pay-btn:hover{
  background:#043a27;
}

/* alt text */
.payment-note{
  text-align:center;
  margin-top:12px;
  font-size:13px;
  color:#777;
}

/* alert */
.alert-box{
  padding:12px;
  border-radius:8px;
  margin-bottom:20px;
  font-size:14px;
  text-align:center;
}

.alert-success{
  background:#e8f8f2;
  color:#05462F;
}

.alert-warning{
  background:#fff4e5;
  color:#92400e;
}
</style>

</head>

<body>

<!-- X BUTTON -->
<button onclick="window.location.href='/plans'" 
style="position:fixed; top:20px; right:20px; background:none; border:none; cursor:pointer; z-index:9999;">
  <i class="fa-solid fa-xmark" style="font-size:26px; color:#333;"></i>
</button>

<div class="payment-container">

  <div class="payment-box">
    <h2>Seçilen Paket</h2>

  <div class="plan-info">

  <div class="plan-name" style="font-size:20px; font-weight:600;">
    <?= htmlspecialchars($package['name']) ?>

    <?php if (!$hasUsedTrial && isset($_GET['type']) && $_GET['type'] === 'trial'): ?>
      <span style="background:#04DA8D; color:#fff; font-size:12px; font-weight:600; padding:4px 8px; border-radius:6px; margin-left:8px;">
        7 gün ücretsiz
      </span>
    <?php endif; ?>
  </div>

  <div class="plan-price">
    ₺<?= number_format($price,0,',','.') ?> / <?= $term ?>
  </div>

</div>

<button class="pay-btn" id="payBtn">
  Ödeme Yap
</button>

<div style="text-align:center; margin-top:10px; font-size:14px; color:#888; display:flex; align-items:center; justify-content:center; gap:6px;">
  <img src="/img/secure.svg" style="height:26px;">
  ile güvenli ödeme
</div>


<div style="text-align:center; margin-top:6px; font-size:14px; color:#c1c1c1;">
  <small>Puandeks, ödeme işlemlerinde Lidio altyapısını kullanır.</small>
</div>

</div>

<script>
document.getElementById("payBtn").addEventListener("click", async function () {

  const urlParams = new URLSearchParams(window.location.search);

  const plan = urlParams.get("plan");
  const period = urlParams.get("period") || "monthly";

  if (!plan) {
    alert("Plan bulunamadı");
    return;
  }

  try {

    const res = await fetch(`/api/create-payment.php?plan=${plan}&period=${period}`);
    const data = await res.json();

    if (data.success && data.paymentUrl) {
      window.location.href = data.paymentUrl;
    } else {
      console.log(data);
      alert("Ödeme başlatılamadı");
    }

  } catch (err) {
    console.error(err);
    alert("Sunucu hatası");
  }

});
</script>

</body>
</html>
