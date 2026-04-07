<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once('/home/puandeks.com/backend/config.php');

/* ======================
   SESSION / USER CONTEXT
   ====================== */
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

if (isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
    $stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['role']  = $user['role'];
        $_SESSION['name']  = $user['name'];
        $_SESSION['email'] = $user['email'];
    } else {
        $stmt = $pdo->prepare("SELECT name, email FROM companies WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($company) {
            $_SESSION['role']  = 'business';
            $_SESSION['name']  = $company['name'];
            $_SESSION['email'] = $company['email'];
        }
    }
}

/* ======================
   INPUTS
   ====================== */
$query           = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = 0;

if (!empty($_GET['category'])) {
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
    $stmt->execute([$_GET['category']]);
    $category = (int)$stmt->fetchColumn();
}

$country         = isset($_GET['country']) ? trim($_GET['country']) : '';
$city = isset($_GET['city']) ? trim($_GET['city']) : '';
$cityId = null;

if ($city !== '') {
    $stmt = $pdo->prepare("SELECT id FROM cities WHERE LOWER(name) = LOWER(?)");
    $stmt->execute([$city]);
    $cityId = $stmt->fetchColumn();

    if (!$cityId) {
        $city = '';
        $cityError = "Geçersiz şehir girdiniz.";
    }
}

$ratingInput     = $_GET['rating'] ?? [];
$reviewCountInput= $_GET['review_count'] ?? [];

if ($query === '' && isset($_GET['company_name'])) {
    $query = trim($_GET['company_name']);
}

/* ======================
   PAGINATION
   ====================== */
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$limit  = 15;
$offset = ($currentPage - 1) * $limit;

/* ======================
   FILTER SQL (WHERE)
   ====================== */
$filterSql = "";
$params    = [];

if ($query !== '') {
    $filterSql .= " AND c.name LIKE :query";
    $params['query'] = "%$query%";
}

if ($category > 0) {
    $filterSql .= " AND c.category_id = :category";
    $params['category'] = $category;
}

if ($country !== '') {
    $filterSql .= " AND c.country = :country";
    $params['country'] = $country;
}

if (!empty($cityId)) {
    $filterSql .= " AND c.city_id = :city_id";
    $params['city_id'] = $cityId;
}

/* ======================
   HAVING (AGGREGATE FILTERS)
   ====================== */
$havingParts  = [];
$havingParams = [];

/* ---- Review count ---- */
if (!empty($reviewCountInput)) {
    $ranges = [];
    foreach ($reviewCountInput as $rc) {
        if ($rc === '1-50')    $ranges[] = '(COUNT(r.id) BETWEEN 1 AND 50)';
        if ($rc === '51-100')  $ranges[] = '(COUNT(r.id) BETWEEN 51 AND 100)';
        if ($rc === '101-500') $ranges[] = '(COUNT(r.id) BETWEEN 101 AND 500)';
        if ($rc === '500+')    $ranges[] = '(COUNT(r.id) >= 500)';
    }
    if ($ranges) {
        $havingParts[] = '(' . implode(' OR ', $ranges) . ')';
    }
}

/* ---- Rating ---- */
if (!empty($ratingInput)) {
    $ratingConds = [];
    foreach ($ratingInput as $rate) {
        $rate = (int)$rate;

        if ($rate === 5) {
            $ratingConds[] = "(AVG(r.rating) >= 5)";
        } else {
            $min = $rate;
            $max = $rate + 1;
            $ratingConds[] = "(AVG(r.rating) >= $min AND AVG(r.rating) < $max)";
        }
    }

    if ($ratingConds) {
        $havingParts[] = '(' . implode(' OR ', $ratingConds) . ')';
    }
}


$havingSql = $havingParts ? ' HAVING ' . implode(' AND ', $havingParts) : '';

/* ======================
   COUNT
   ====================== */
$countSql = "
SELECT COUNT(*) FROM (
    SELECT c.id
    FROM companies c
    LEFT JOIN categories ct ON c.category_id = ct.id
    LEFT JOIN reviews r
        ON r.company_id = c.id
        AND r.status = 1
        AND r.parent_id IS NULL
    WHERE 1=1
    $filterSql
    GROUP BY c.id
    $havingSql
) t
";

$countStmt = $pdo->prepare($countSql);
$countStmt->execute(array_merge($params, $havingParams));
$resultCount = (int)$countStmt->fetchColumn();

/* ======================
   DATA
   ====================== */
$dataSql = "
SELECT
    c.*,
    ct.name AS category_name,
    city.name AS city_name,
    COALESCE(AVG(r.rating), 0) AS avg_rating,
    COUNT(r.id) AS review_count
FROM companies c
LEFT JOIN categories ct ON c.category_id = ct.id
LEFT JOIN cities city ON c.city_id = city.id
LEFT JOIN reviews r
    ON r.company_id = c.id
    AND r.status = 1
    AND r.parent_id IS NULL
WHERE 1=1
$filterSql
GROUP BY c.id
$havingSql
ORDER BY c.name ASC
LIMIT $limit OFFSET $offset
";

$dataStmt = $pdo->prepare($dataSql);
$dataStmt->execute(array_merge($params, $havingParams));
$results = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

/* ======================
   PAGE COUNT
   ====================== */
$totalPages = (int)ceil($resultCount / $limit);
?>



<!DOCTYPE html>
<html lang="tr">

<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Riwys">
   <title>İşletme Arama - Puandeks </title>

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

    <!-- CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">
	<link href="css/company-search.css" rel="stylesheet">
	<link href="css/incelemeyaz.css" rel="stylesheet" >
   <link href="css/company-search-pages.css" rel="stylesheet" >


</head>

<body>
	
	<div id="page" class="theia-exception">
      
<!-- header -->
<?php include 'header-main.php'; ?>
<!-- /header -->
	
<main>
		
 <!-- filters -->	
<?php
$selectedCategoryId = isset($_GET['category']) ? $_GET['category'] : '';
$companyInputValue = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
?>

		<div class="container margin_60_35" style="margin-top: 65px;">
			<div class="row">
				<aside class="col-lg-3" id="sidebar">
					<div id="filters_col">
						<a data-bs-toggle="collapse" href="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters" id="filters_col_bt">Filtreler </a>
						<div class="collapse show" id="collapseFilters">

<form action="company-search" method="get">							
<div class="filter_type">
  <h6>İnceleme Sayısı</h6>
  <ul>
    <li>
      <label class="container_check">1-50
        <input type="checkbox" name="review_count[]" value="1-50" <?php if (in_array('1-50', $reviewCountInput)) echo 'checked'; ?>>
        <span class="checkmark"></span>
      </label>
    </li>
    <li>
      <label class="container_check">51-100
        <input type="checkbox" name="review_count[]" value="51-100" <?php if (in_array('51-100', $reviewCountInput)) echo 'checked'; ?>>
        <span class="checkmark"></span>
      </label>
    </li>
    <li>
      <label class="container_check">101-500
        <input type="checkbox" name="review_count[]" value="101-500" <?php if (in_array('101-500', $reviewCountInput)) echo 'checked'; ?>>
        <span class="checkmark"></span>
      </label>
    </li>
    <li>
      <label class="container_check">500 +
        <input type="checkbox" name="review_count[]" value="500+" <?php if (in_array('500+', $reviewCountInput)) echo 'checked'; ?>>
        <span class="checkmark"></span>
      </label>
    </li>
  </ul>
</div>

<hr>

<div class="filter_type">
  <h6>Puan</h6>
  <ul>
    <?php
    $stars = [5, 4, 3, 2, 1];
    foreach ($stars as $star) {
      echo '<li>
        <label class="container_check">' . $star . ' Yıldız
          <img src="img/core/vote_' . $star . '.svg" height="14" style="margin-left: 5px;">
          <input type="checkbox" name="rating[]" value="' . $star . '" ' . (in_array((string)$star, $ratingInput) ? 'checked' : '') . '>
          <span class="checkmark"></span>
        </label>
      </li>';
    }
    ?>
  </ul>
</div>

<hr>

<!-- === İşletme adı filtre === -->
<div class="filter_type">
  <input 
    type="text" 
    class="form-control" 
    name="company_name" 
    id="companyInput" 
    placeholder="İşletme adı" 
    autocomplete="off" 
    value=""
  >
</div>

<!-- === Kategori dropdown  === -->
<div class="filter_type" style="margin-top: 12px;">
  <div id="customDropdown" 
       style="position: relative; width: 100%; border: 1px solid #ccc; border-radius: 6px; background-color: #fff; padding: 10px 14px; font-size: 15px; cursor: pointer;">

    <span id="dropdownSelected">
      <?php
        if (!empty($_GET['category'])) {
        $catId = (int) $_GET['category'];

        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$catId]);
        $catName = $stmt->fetchColumn();

        echo $catName ? htmlspecialchars($catName) : 'Kategori';
    } else {
        echo 'Kategori';
    }
      ?>
    </span>

    <ul id="dropdownOptions" 
        style="list-style: none; padding: 0; margin: 0; border: 1px solid #ccc; border-radius: 6px; background-color: white; position: absolute; top: 110%; left: 0; width: 100%; z-index: 1000; display: none; max-height: 200px; overflow-y: auto;">
      
      <li data-value="" style="padding: 10px 14px; font-weight: bold; border-bottom: 1px solid #ccc;">
        Tüm Kategoriler
      </li>

      <?php
        $stmt = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name ASC");
        while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<li data-value="' . htmlspecialchars($cat['slug']) . '" style="padding: 10px 14px; border-bottom: 1px solid #eee;">' . htmlspecialchars($cat['name']) . '</li>';
        }
      ?>
    </ul>

    <input type="hidden" name="category" id="categoryInput" value="<?php echo htmlspecialchars($selectedCategoryId); ?>">
  </div>
</div>


<div class="filter_type">
  <h6>Konum </h6>
  <div class="form-group" style="position: relative; margin-bottom: 10px;">
    <input class="form-control" type="text" placeholder="Türkiye" disabled style="padding-right: 35px;">
    <i class="icon_globe-2" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #ccc;"></i>
  </div>
  <div class="form-group" style="position: relative;">
    <input class="form-control" type="text" name="city" placeholder="Şehir" value="<?php echo htmlspecialchars($city); ?>">
    <?php if (!empty($cityError)): ?>
      <small style="color:#c0392b; font-size:13px;">
        <?php echo $cityError; ?>
      </small>
    <?php endif; ?>
    
    <i class="icon_pin_alt" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #ccc;"></i>
  </div>
</div>

<button id="filterButton" type="submit"
 style="
  width:100% !important;
  background-color:#05462F !important;
  color:#ffffff !important;
  font-weight:600 !important;
  font-size:16px !important;
  padding:12px !important;
  border:none !important;
  border-radius:6px !important;
  margin-top:15px !important;
  cursor:pointer !important;
  transition:0.25s !important;
 "
 onmouseover="this.style.backgroundColor='#04DA8D'; this.style.color='#1c1c1c';"
 onmouseout="this.style.backgroundColor='#05462F'; this.style.color='#ffffff';"
>
  Filtrele
</button>
</form>



						  

</div>
<!--collapse -->
</div>
<!--filters -->
<br>

</aside>
<!-- aside -->

				
              
<div class="col-lg-9">
<h5><?php echo $resultCount; ?> Sonuç bulundu</h5>
<div class="isotope-wrapper">
						
						
<div class="row">

<!-- Companies -->
<?php if (empty($results)): ?>
  <div class="company-box">
    <div class="company-info">
      <div class="company-details">
        <h4>Sonuç bulunamadı</h4>
        <p>Aramanıza uyan bir işletme bulunamadı. Lütfen tekrar deneyin.</p>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php foreach ($results as $company): ?>
  <?php
    $rating = round((float)($company['avg_rating'] ?? 0), 1);
    $reviewCount = (int)($company['review_count'] ?? 0);

    $voteLevel = $reviewCount === 0
        ? 0
        : min(max(floor($rating), 1), 5);

    $starSvg = "vote_" . $voteLevel . ".svg";
    $displayRating = number_format($rating, 1);

    $categoryName = $company['category_name'] ?? '';
    ?>

  <!-- C BOX  -->
  <div class="company-box">
    <div class="company-info">
      <img 
        src="<?= htmlspecialchars($company['logo'] ?? 'img/placeholder/company-profile.png'); ?>" 
        alt="logo" 
        class="company-logo">

      <div class="company-details">
        <h4><?= htmlspecialchars($company['name']); ?></h4>

        <?php if (!empty($company['website'])): ?>
          <p><?= htmlspecialchars($company['website']); ?></p>
        <?php endif; ?>

        <img src="/img/core/<?= $starSvg ?>" alt="vote" height="30">

        <div class="rating-meta">
          <span><?= $displayRating ?></span> |
          <span><?= $reviewCount ?> inceleme</span><br>
          <span><?= htmlspecialchars($company['country'] ?? ''); ?></span> |
          <span><?= htmlspecialchars($company['city_name'] ?? ''); ?></span>
        </div>
      </div>
    </div>
    <!-- C BOX  -->

  <hr>

  <div class="company-footer">
    <span class="footer-left"><?= htmlspecialchars($categoryName); ?></span>
    <a href="/company/<?= htmlspecialchars($company['slug']); ?>" class="footer-right">
      İşletmeyi İncele
    </a>
  </div>
</div>
<!-- C BOX  -->

<?php endforeach; ?>
<?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php
      // take uery string
      $queryString = $_GET;
      unset($queryString['page']);

      function buildPageUrl($page, $queryString) {
        $queryString['page'] = $page;
        return '?' . http_build_query($queryString);
      }
    ?>

    <ul class="pagination-list">
      <?php if ($currentPage > 1): ?>
        <li><a href="<?php echo buildPageUrl(1, $queryString); ?>">Başa dön</a></li>
      <?php endif; ?>

      <?php
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $currentPage + 2);
        for ($i = $start; $i <= $end; $i++): ?>
          <li>
            <a href="<?php echo buildPageUrl($i, $queryString); ?>"
               <?php if ($i == $currentPage) echo 'class="active"'; ?>>
              <?php echo $i; ?>
            </a>
          </li>
      <?php endfor; ?>

      <?php if ($currentPage < $totalPages): ?>
        <li><a href="<?php echo buildPageUrl($totalPages, $queryString); ?>">Sona git</a></li>
      <?php endif; ?>
    </ul>
  </div>
<?php endif; ?>
<!-- Companies -->


<!-- Add Company -->
<div class="g_color_1">
  <div class="container margin_60_35">
    <section class="add-company" style="text-align:center;">
      <h2>Aradığım İşletmeyi bulamıyorum!</h2>
      <p>Henüz Puandeks'te listelenmemiş olabilir. Ekleyin ve ilk yorumu yazan siz olun.</p>
     <button 
       type="button"
        id="openModal" 
        class="add-company-btn"
        style="
            background-color:#05462F !important;
            color:#ffffff !important;
            padding:10px 18px !important;
            border:none !important;
            border-radius:30px !important;
            font-weight:600 !important;
            cursor:pointer !important;
            transition:0.25s !important;
        "
        onmouseover="this.style.backgroundColor='#04DA8D'; this.style.color='#1c1c1c';"
        onmouseout="this.style.backgroundColor='#05462F'; this.style.color='#ffffff';">
    İşletme ekle
</button>

    </section>
  </div>
</div>
<!-- /Add Company -->
				
 


                      </div>
                      <!-- row -->
                  </div>
                  <!-- wrapper -->

						
                </div>
				<!-- col -->
			</div>		
		</div>
		<!-- container -->
		
</main>
<!--main-->
	


<!-- Add company Pop-up -->
<div id="companyModal" class="modal" 
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
            justify-content:center;align-items:center;
            background:rgba(0,0,0,0.5);z-index:9999;">

  <div class="modal-content" style="max-width:400px;margin:auto;padding:24px;border-radius:8px;position:relative;background:#fff;">
    <span class="close" style="position:absolute;top:10px;right:14px;font-size:22px;cursor:pointer;">&times;</span>

    <!-- BAŞLIK (Kalın) -->
    <h2 style="margin-bottom:16px;font-weight:700;">İşletme ekle</h2>
    
    <input type="text" id="companyName" class="modal-input" placeholder="İşletme adı" 
      style="width:100%;margin-bottom:12px;padding:10px;border:1px solid #ccc;border-radius:6px;">
    
    veya
    
    <input type="text" id="companyWebsite" class="modal-input" placeholder="Web sitesi ile (örnek: example.com)" 
      style="width:100%;margin-bottom:16px;padding:10px;border:1px solid #ccc;border-radius:6px;">
    
    <!-- BUTON -->
    <a href="javascript:void(0)" 
       id="submitCompany" 
       class="modal-submit"
       style="
          display:inline-block;
          background-color:#05462F;
          color:#ffffff;
          padding:12px 20px;
          border-radius:6px;
          text-decoration:none;
          font-weight:600;
          transition:0.25s;
       "
       onmouseover="this.style.backgroundColor='#04DA8D'; this.style.color='#1c1c1c';"
       onmouseout="this.style.backgroundColor='#05462F'; this.style.color='#ffffff';"
    >
      İşletme ekle
    </a>

  </div>
</div>
<!-- Add company Pop-up -->


<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	


</div>
<!-- page -->
	

<!-- COMMON SCRIPTS -->
<script src="js/common_scripts.js"></script>
<script src="js/functions.js"></script>
<script src="assets/validate.js"></script>
<script src="js/company-search-filter.js"></script>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const modal     = document.getElementById("companyModal");
    const openBtn   = document.getElementById("openModal");
    const closeBtn  = document.querySelector(".close");

    const submitBtn = document.getElementById("submitCompany");
    const nameInput = document.getElementById("companyName");
    const siteInput = document.getElementById("companyWebsite");

    const userRole  = "<?= isset($_SESSION['role']) ? trim($_SESSION['role']) : '' ?>";

    // Modal açma
    openBtn.addEventListener("click", function () {
        if (userRole !== "user") {
        alert("İşletme eklemek için kullanıcı girişi gerekli.");
        window.location.href = "/login";
        return;
    }
        modal.style.display = "flex";
    });

    // Modal kapama
    closeBtn.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", (e) => { if (e.target === modal) modal.style.display = "none"; });

    // -----------------------------------------
    // DOMAIN CLEANER
    // -----------------------------------------
    function cleanDomain(val) {
        return val
            .replace("https://", "")
            .replace("http://", "")
            .replace("www.", "")
            .trim();
    }

    // -----------------------------------------
    // DOMAIN VALIDATION
    // -----------------------------------------
    function isValidDomain(val) {
        val = cleanDomain(val);
        return val.includes(".") && val.length > 4;
    }

    // -----------------------------------------
    // INPUT LINK BEH
    // -----------------------------------------
    function updateStates() {
        const nameVal = nameInput.value.trim();
        const siteVal = siteInput.value.trim();

        // Birine yazınca diğeri disable
        if (nameVal.length > 0) {
            siteInput.disabled = true;
            siteInput.style.background = "#f2f2f2";
        } else {
            siteInput.disabled = false;
            siteInput.style.background = "#fff";
        }

        if (siteVal.length > 0) {
            nameInput.disabled = true;
            nameInput.style.background = "#f2f2f2";
        } else {
            nameInput.disabled = false;
            nameInput.style.background = "#fff";
        }

        // Buton kontrol
        const nameOk = nameVal.length > 1;
        const siteOk = isValidDomain(siteVal);

        if (nameOk || siteOk) {
            submitBtn.style.opacity = "1";
            submitBtn.style.pointerEvents = "auto";
        } else {
            submitBtn.style.opacity = "0.5";
            submitBtn.style.pointerEvents = "none";
        }
    }

    // Input listenerlar
    nameInput.addEventListener("input", updateStates);
    siteInput.addEventListener("input", function() {
        this.value = cleanDomain(this.value);
        updateStates();
    });

    updateStates(); 

    // -----------------------------------------
    // SUBMIT & YÖNLENDİRME
    // -----------------------------------------
    submitBtn.addEventListener("click", function(e) {
        e.preventDefault();

        const nameVal = nameInput.value.trim();
        const rawDomain = siteInput.value.trim();
        const domain = cleanDomain(rawDomain);

        let url = "add-new-company?";

        if (nameVal.length > 1) {
            url += "new_company=" + encodeURIComponent(nameVal);
        } else if (isValidDomain(domain)) {
            url += "website=" + encodeURIComponent(domain);
        } else {
            alert("Lütfen geçerli bir alan doldurun.");
            return;
        }

        window.location.href = url;
    });

});
</script>





  
</body>
</html>