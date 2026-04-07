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


<?php
require_once('/home/puandeks.com/backend/config.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
  // Görüntülenme sayısını artır
  $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$id]);
}

// Blog yazısnı çek
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? AND status = 1");
$stmt->execute([$id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
  header("Location: blog");
  exit;
}

// En çok okunan son 6 blog yazısı (views'e göre)
$popularStmt = $pdo->prepare("
  SELECT id, title, image, created_at 
  FROM blog_posts 
  WHERE status = 1 
  ORDER BY views DESC, created_at DESC 
  LIMIT 6
");
$popularStmt->execute();
$popularPosts = $popularStmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="tr">

<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Riwys">
    <title>Puandeks Blog</title>
  
  	<!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

    <!-- Favicons-->
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
    
    <!-- SPECIFIC CSS -->
    <link href="css/blog.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">

 <style>
  main {
      margin-top: 60px !important;
  }
  </style>


</head>

<body>
		
	<div id="page">
		
<!-- header -->
<?php include 'header-main.php'; ?>
<!-- /header -->
      
<main>

        
<div class="container margin_60_35">
<div class="row">

<!-- Post Section -->
<div class="col-lg-9">
  <div class="singlepost">
    <figure>
      <img alt="<?= htmlspecialchars($blog['title']) ?>" class="img-fluid" src="<?= htmlspecialchars($blog['image']) ?>">
    </figure>

    <h2 style="font-size: 26px; margin-top: 25px; margin-bottom: 15px; font-weight: 600; line-height: 1.4;">
      <?= htmlspecialchars($blog['title']) ?>
    </h2>

    <div class="postmeta">
      <ul>
        <li><i class="ti-calendar"></i> <?= date('d/m/Y', strtotime($blog['created_at'])) ?></li>
      </ul>
    </div>

    <div class="post-content" style="font-size: 16px; line-height: 1.7;">
      <?= $blog['content'] ?>
    </div>
  </div>

  <hr>
</div>
<!-- /Post Section -->

				
<!-- aside -->
			<aside class="col-lg-3">
					
					<!-- /widget -->
					<div class="widget">
						<div class="widget-title">
							<h4>En çok okunanlar</h4> <!-- backend: buraya en çok ziyaret edilen 6 blog yazısı sırasıyla gelecek. Sistem sürekli bunu tarayarak güncelleyecek-->
						</div>
						<ul class="comments-list">
                          <?php foreach ($popularPosts as $post): ?>
                            <li>
                              <div class="alignleft">
                                <a href="blog-post?id=<?= $post['id'] ?>">
                                  <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                                </a>
                              </div>
                              <a href="blog-post?id=<?= $post['id'] ?>">
                                <h3><?= htmlspecialchars($post['title']) ?></h3>
                              </a>
                              <a href="blog-post?id=<?= $post['id'] ?>">
                                <small><?= date('d/m/Y', strtotime($post['created_at'])) ?></small>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        </ul>
					</div>
					<!-- /widget -->

					
				</aside>
				<!-- /aside -->


			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</main>
	<!--/main-->

<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	

</div>
<!-- page -->
	
	
	

	
	<!-- COMMON SCRIPTS -->
    <script src="js/common_scripts.js"></script>
	<script src="js/functions.js"></script>
	<script src="assets/validate.js"></script>

</body>
</html>