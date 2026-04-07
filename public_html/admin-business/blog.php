<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
    session_start();
}

require_once('/home/puandeks.com/backend/config.php');

/* -----------------------------------------
   BUSINESS LOGIN DETECTION (same as help)
----------------------------------------- */

$role = $_SESSION['role'] ?? null;
$company_display = '';
$company_logo = 'https://puandeks.com/img/placeholder/user.png';

/* business logged in? */
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

/* -----------------------------------------
   BLOG LIST FETCH
----------------------------------------- */
try {
    $stmt = $pdo->prepare("
        SELECT id, title, content, image, created_at
        FROM business_blog_posts
        WHERE status = 1
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $blogs = [];
}
?>



<!DOCTYPE html>
<html lang="tr">

<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Puandeks İşletme Blog</title>

	<!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

    <!-- Favicons-->
    <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
  
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="https://puandeks.com/img/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="https://puandeks.com/img/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="https://puandeks.com/img/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="https://puandeks.com/img/apple-touch-icon-144x144-precomposed.png">

    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="https://puandeks.com/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://puandeks.com/css/style.css" rel="stylesheet">
	<link href="https://puandeks.com/css/vendors.css" rel="stylesheet">
    
    <!-- SPECIFIC CSS -->
    <link href="https://puandeks.com/css/blog.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="https://puandeks.com/css/custom.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
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
body {
  overflow-x: hidden;
}
</style>

<style>
html {
  overflow-y: scroll !important;
}
</style>

<style>
/* BLOG SEARCH DROPDOWN */

/* Dropdown Box */
.search-dropdown {
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    border-radius: 12px !important;
    padding: 0 !important;
    margin-top: 6px !important;
    list-style: none !important;
    max-height: 300px !important;
    overflow-y: auto !important;
}

/* Every result line */
.search-dropdown li {
    list-style: none !important;
    padding: 12px 18px !important;
    color: #1c1c1c !important;
    font-size: 16px !important;
    cursor: pointer !important;
    background: #fff !important;
}

/* Links */
.search-dropdown li a {
    color: #1c1c1c !important;
    text-decoration: none !important;
    font-weight: 400 !important;
    display: block !important;
    width: 100% !important;
}

/* Hover no */
.search-dropdown li:hover {
    background: #f7f7f7 !important;
}

/* Link hover black */
.search-dropdown li a:hover {
    color: #1c1c1c !important;
}

/* hr */
.search-dropdown li:not(:last-child) {
    border-bottom: 1px solid #e5e5e5 !important;
}
</style>


</head>

<body>
		
	<div id="page">
		
<?php include 'inc/header.php'; ?>
      
<main>
      
<!-- hero blog -->
<section class="hero_single version_1">
    <div class="wrapper" style="background-color: #05462F !important;">
        <div class="container">
            <h3 class="fw-bolder fs-2" style="color:#fff; padding-bottom: 20px;">
                Puandeks İşletme Blog
            </h3>

            <p class="fw-normal fs-6" style="color:#fff; padding-bottom: 20px;">
                Doğru tercih için rehberler, tüketici deneyimleri ve ipuçları Puandeks İşletme Blog'da!
            </p>

            <!-- Search Bar -->
            <div class="row justify-content-center position-relative">
                <div class="col-lg-9">
                    <div class="search-bar">
                        <div class="input-group">
                            <input type="text" id="blogSearch" name="q" class="form-control" placeholder="Blog yazılarında ara" autocomplete="off">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>

                        <!-- Dropdown -->
                        <div id="searchSuggestions" class="search-dropdown"></div>
                    </div>
                </div>
            </div>
            <!-- /Search Bar -->
        </div>
    </div>
</section>
<!-- hero blog -->   

        
<div class="container margin_60_35">
  <div class="container margin_60_35">
    <div class="row" id="blogCardsWrapper">
      <?php foreach ($blogs as $index => $blog): ?>
        <div class="col-md-6 blog-card" <?= $index >= 6 ? 'style="display:none;"' : '' ?>>
          <article class="blog">
            <figure>
              <a href="blog-post?id=<?= $blog['id'] ?>">
                  <img src="https://puandeks.com<?= htmlspecialchars($blog['image']) ?>" alt="puandeks blog">
              </a>
          </figure>


            <div class="post_info">
              <h2>
                <a href="blog-post?id=<?= $blog['id'] ?>">
                  <?= htmlspecialchars($blog['title']) ?>
                </a>
              </h2>

              <p>
                <?= mb_substr(strip_tags($blog['content']), 0, 180) ?>...
                &nbsp;&nbsp;
                <a href="blog-post?id=<?= $blog['id'] ?>">Devamını oku</a>
              </p>

              <small><?= date('d.m.Y', strtotime($blog['created_at'])) ?></small>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (count($blogs) > 6): ?>
      <div class="text-center mt-4">
        <button id="showMoreBtn" class="btn btn-primary">Daha fazla gr</button>
      </div>
    <?php endif; ?>
  </div>
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


<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ----------------------------------------------------
       1) Daha fazla göster
    ---------------------------------------------------- */
    const showMoreBtn = document.getElementById("showMoreBtn");
    const allCards = document.querySelectorAll(".blog-card");
    let visibleCount = 6;

    function updateVisibility() {
        allCards.forEach((card, index) => {
            card.style.display = index < visibleCount ? "block" : "none";
        });

        if (visibleCount >= allCards.length) {
            showMoreBtn.disabled = true;
            showMoreBtn.classList.add("disabled");
        }
    }

    if (showMoreBtn) {
        updateVisibility();
        showMoreBtn.addEventListener("click", () => {
            visibleCount += 6;
            updateVisibility();
        });
    }


    /* ----------------------------------------------------
       2) Blog veri hazırlığı (content temizlenmiş)
    ---------------------------------------------------- */
    let allBlogs = <?= json_encode(
        array_map(function($b){
            $b['clean_content'] = mb_strtolower(strip_tags($b['content']));
            return $b;
        }, $blogs),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ) ?>;


    /* ----------------------------------------------------
       3) Arama Fonksiyonu
    ---------------------------------------------------- */
    const searchInput = document.getElementById("blogSearch");
    const suggestionsBox = document.getElementById("searchSuggestions");

    function filterBlogs(keyword) {
        keyword = keyword.toLowerCase();

        return allBlogs.filter(blog =>
            blog.title.toLowerCase().includes(keyword) ||
            (blog.clean_content || "").includes(keyword)
        );
    }

    function renderSuggestions(results) {
        suggestionsBox.innerHTML = "";

        if (results.length === 0) {
            suggestionsBox.style.display = "none";
            return;
        }

        results.slice(0, 6).forEach(blog => {
            const li = document.createElement("li");
            const a = document.createElement("a");

            a.href = `blog-post?id=${blog.id}`;
            a.textContent = blog.title;

            li.appendChild(a);
            suggestionsBox.appendChild(li);
        });

        suggestionsBox.style.display = "block";
    }

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const value = this.value.trim();

            if (value === "") {
                suggestionsBox.style.display = "none";
                return;
            }

            const filtered = filterBlogs(value);
            renderSuggestions(filtered);
        });
    }

    /* ----------------------------------------------------
       4) Dışarı tıklanınca dropdown gizle
    ---------------------------------------------------- */
    document.addEventListener("click", function (e) {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.style.display = "none";
        }
    });

});
</script>



</body>
</html>