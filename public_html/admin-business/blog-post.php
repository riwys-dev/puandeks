<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
    session_start();
}

require_once('/home/puandeks.com/backend/config.php');

/* ------------------------------
   BUSINESS LOGIN DETECTION
------------------------------ */

$role = $_SESSION['role'] ?? null;
$company_display = '';
$company_logo = 'https://puandeks.com/img/placeholder/user.png';

if ($role === 'business' && isset($_SESSION['company_id'])) {

    try {
        $stmt = $conn->prepare("SELECT name, logo FROM companies WHERE id = ?");
        $stmt->execute([$_SESSION['company_id']]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($company) {
            $company_display = htmlspecialchars($company['name']);
            if (!empty($company['logo'])) {
                $company_logo = htmlspecialchars($company['logo']);
            }
        }

    } catch (PDOException $e) {
        echo "DB Error: " . $e->getMessage();
    }
}

/* ------------------------------
   GET SINGLE BLOG POST
------------------------------ */

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header("Location: blog");
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, title, content, image, meta_description, created_at 
    FROM business_blog_posts
    WHERE id = ? AND status = 1
");
$stmt->execute([$id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    header("Location: blog");
    exit;
}

/* ------------------------------
   POPULAR POSTS
------------------------------ */

$pop = $pdo->prepare("
    SELECT id, title, image, created_at 
    FROM business_blog_posts
    WHERE status = 1 
    ORDER BY views DESC, created_at DESC
    LIMIT 6
");
$pop->execute();
$popularPosts = $pop->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
body {
  overflow-x: hidden;
}
</style>



<!DOCTYPE html>
<html lang="tr">

<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($blog['meta_description']) ?>">
    <meta name="author" content="Riwys">
    <title><?= htmlspecialchars($blog['title']) ?> - Puandeks şletme Blog</title>

	<link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

    <!-- FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

        <!-- BASE CSS -->
    <link href="https://puandeks.com/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://puandeks.com/css/style.css" rel="stylesheet">
	<link href="https://puandeks.com/css/vendors.css" rel="stylesheet">
    
    <!-- SPECIFIC CSS -->
    <link href="https://puandeks.com/css/blog.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="https://puandeks.com/css/custom.css" rel="stylesheet">

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

    <style>
      main {
          margin-top: 60px !important;
      }
      </style>

    </head>

<body>
<div id="page">

<?php include 'inc/header.php'; ?>


<main>


<div class="container margin_60_35">
    <div class="row">

<!-- ANA BLOG -->
<div class="col-lg-9">
  <div class="singlepost">

    <figure>
      <img 
        alt="<?= htmlspecialchars($blog['title']) ?>" 
        class="img-fluid"
        src="https://puandeks.com<?= htmlspecialchars($blog['image']) ?>"
      >
    </figure>

    <h2 style="font-size:26px; margin-top:25px; margin-bottom:15px; font-weight:600;">
      <?= htmlspecialchars($blog['title']) ?>
    </h2>

    <div class="postmeta">
      <ul>
        <li>
          <i class="ti-calendar"></i> 
          <?= date('d/m/Y', strtotime($blog['created_at'])) ?>
        </li>
      </ul>
    </div>

    <div class="post-content" style="font-size:16px; line-height:1.7;">
      <?= $blog['content'] ?>
    </div>

  </div>

  <hr>
</div>


<!-- YAN MENÜ -->
<aside class="col-lg-3">

  <div class="widget">
    <div class="widget-title">
      <h4>En çok okunanlar</h4>
    </div>

    <ul class="comments-list">

      <?php foreach ($popularPosts as $post): ?>
      <li>

        <div class="alignleft">
          <a href="blog-post?id=<?= $post['id'] ?>">
            <img 
              src="https://puandeks.com<?= htmlspecialchars($post['image']) ?>" 
              alt="<?= htmlspecialchars($post['title']) ?>"
            >
          </a>
        </div>

        <a href="blog-post?id=<?= $post['id'] ?>">
          <h3><?= htmlspecialchars($post['title']) ?></h3>
        </a>

        <small><?= date('d/m/Y', strtotime($post['created_at'])) ?></small>

      </li>
      <?php endforeach; ?>

    </ul>
  </div>

</aside>



</div>
</div>
</main>

<?php include('footer-main.php'); ?>

</div>

<script src="js/common_scripts.js"></script>
<script src="js/functions.js"></script>
<script src="assets/validate.js"></script>

</body>
</html>
