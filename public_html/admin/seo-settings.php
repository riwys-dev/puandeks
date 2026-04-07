<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}


require_once('/home/puandeks.com/backend/config.php');

// --- SEO DATA (homepage örnek) ---
try {
    $stmt = $pdo->prepare("SELECT * FROM seo_meta WHERE page_type = 'homepage' AND page_id = 0 LIMIT 1");
    $stmt->execute();
    $seo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $seo = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_type = $_POST['page_type'];
    $page_id = $_POST['page_id'];

    $meta_title = $_POST['meta_title'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $meta_keywords = $_POST['meta_keywords'] ?? '';
    $canonical_url = $_POST['canonical_url'] ?? '';

    $og_title = $_POST['og_title'] ?? '';
    $og_description = $_POST['og_description'] ?? '';
    $og_image = $_POST['og_image'] ?? '';
    $og_url = $_POST['og_url'] ?? '';

    // var mı kontrol
    $stmt = $pdo->prepare("SELECT id FROM seo_meta WHERE page_type = ? AND page_id = ?");
    $stmt->execute([$page_type, $page_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // update
        $stmt = $pdo->prepare("
            UPDATE seo_meta SET 
                meta_title = ?, 
                meta_description = ?, 
                meta_keywords = ?, 
                canonical_url = ?, 
                og_title = ?, 
                og_description = ?, 
                og_image = ?, 
                og_url = ?
            WHERE page_type = ? AND page_id = ?
        ");
        $stmt->execute([
            $meta_title,
            $meta_description,
            $meta_keywords,
            $canonical_url,
            $og_title,
            $og_description,
            $og_image,
            $og_url,
            $page_type,
            $page_id
        ]);
    } else {
        // insert
        $stmt = $pdo->prepare("
            INSERT INTO seo_meta 
            (page_type, page_id, meta_title, meta_description, meta_keywords, canonical_url, og_title, og_description, og_image, og_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $page_type,
            $page_id,
            $meta_title,
            $meta_description,
            $meta_keywords,
            $canonical_url,
            $og_title,
            $og_description,
            $og_image,
            $og_url
        ]);
    }

    // refresh için tekrar çek
    $stmt = $pdo->prepare("SELECT * FROM seo_meta WHERE page_type = ? AND page_id = ?");
    $stmt->execute([$page_type, $page_id]);
    $seo = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - SEO Ayarları </title>


    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png">



</head>
<body id="page-top">
<div id="wrapper">

<!-- Sidebar -->
<?php include('admin-sidebar.php'); ?>
<!-- Sidebar -->

<!-- Content -->
<div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

<?php include('includes/topbar.php'); ?>


<!-- =================================== -->

<div class="container-fluid">
<h1 class="h3 mb-4 text-gray-800">SEO Ayarları</h1>

<div style="max-width: 700px; margin-bottom:30px;">

<form method="POST">

<div class="form-group">
  <label>Sayfa</label>
  <select class="form-control" disabled>
    <option selected>Ana Sayfa</option>
  </select>

  <input type="hidden" name="page_type" value="homepage">
  <input type="hidden" name="page_id" value="0">
</div>

<div class="form-group">
  <label>Meta Title</label>
  <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($seo['meta_title'] ?? '') ?>">
</div>

<div class="form-group">
  <label>Meta Description</label>
  <textarea name="meta_description" class="form-control"><?= htmlspecialchars($seo['meta_description'] ?? '') ?></textarea>
</div>

<div class="form-group">
  <label>Meta Keywords</label>
  <textarea class="form-control" disabled><?= htmlspecialchars($seo['meta_keywords'] ?? '') ?></textarea>
</div>

<div class="form-group">
  <label>Canonical URL</label>
  <input type="text" class="form-control" value="<?= htmlspecialchars($seo['canonical_url'] ?? '') ?>" disabled>
</div>

<div class="form-group">
  <label>OG Title</label>
  <input type="text" class="form-control" value="<?= htmlspecialchars($seo['og_title'] ?? '') ?>" disabled>
</div>

<div class="form-group">
  <label>OG Description</label>
  <textarea class="form-control" disabled><?= htmlspecialchars($seo['og_description'] ?? '') ?></textarea>
</div>

<div class="form-group">
  <label>OG Image</label>
  <input type="text" class="form-control" value="<?= htmlspecialchars($seo['og_image'] ?? '') ?>" disabled>
  <small class="form-text text-muted">
    1200x630 px önerilir. Sosyal medya paylaşım görselidir (logo değil). URL formatında girilir.
  </small>
</div>

<div class="form-group">
  <label>OG URL</label>
  <input type="text" class="form-control" value="<?= htmlspecialchars($seo['og_url'] ?? '') ?>" disabled>
</div>

<button type="submit" class="btn btn-primary">Kaydet</button>

</form>

</div>
      


  
<!-- =================================== -->

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
