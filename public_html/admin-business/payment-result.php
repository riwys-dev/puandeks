<?php
session_start();

$status = $_GET['status'] ?? null;
$plan   = $_GET['plan'] ?? null;
$period = $_GET['period'] ?? null;

if (!$status) {
    header("Location: /plans");
    exit;
}

$isSuccess = ($status === 'success');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ödeme Sonucu | Puandeks</title>

<link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

<style>
body {
  font-family: 'Roboto', sans-serif;
  background:#FCFBF3;
  margin:0;
}

.container{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
}

.box{
  width:100%;
  max-width:420px;
  background:#fff;
  padding:30px;
  border-radius:12px;
  border:2px solid #e5e7eb;
  text-align:center;
}

.icon{
  font-size:48px;
  margin-bottom:15px;
}

.success{ color:#16a34a; }
.fail{ color:#dc2626; }

h2{
  margin-bottom:15px;
}

p{
  color:#555;
  font-size:14px;
  margin-bottom:25px;
}

.btn{
  width:100%;
  padding:14px;
  border:none;
  border-radius:8px;
  font-weight:600;
  cursor:pointer;
}

.btn-success{
  background:#05462F;
  color:#fff;
}

.btn-fail{
  background:#dc2626;
  color:#fff;
}
</style>

</head>

<body>

<div class="container">

  <div class="box">

    <?php if ($isSuccess): ?>

      <div class="icon success">✔</div>
      <h2>Ödeme Başarılı</h2>
      <p>Paketiniz başarıyla aktifleştirildi.</p>

      <button class="btn btn-success"
        onclick="window.location.href='https://business.puandeks.com/home'">
        İşletme Paneline Git
      </button>

    <?php else: ?>

      <div class="icon fail">✖</div>
      <h2>Ödeme Başarısız</h2>
      <p>Lütfen kart bilgilerinizi kontrol ederek tekrar deneyin.</p>

      <button class="btn btn-fail"
        onclick="window.location.href='/payment?plan=<?= htmlspecialchars($plan) ?>&period=<?= htmlspecialchars($period) ?>'">
        Tekrar Dene
      </button>

    <?php endif; ?>

  </div>

</div>

<!-- turn block -->
<script>
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}

window.addEventListener("pageshow", function (event) {
  if (event.persisted) {
    window.location.reload();
  }
});
</script>
<!-- turn block -->

</body>
</html>