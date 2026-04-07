<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once('/home/puandeks.com/backend/config.php');

/* -----------------------------------------
   SAFE SESSION VARIABLE EXTRACTION
----------------------------------------- */

$role = $_SESSION['role'] ?? null;

/* mevcut değişkenleri BOZMADAN koruyoruz */
$name = $_SESSION['name'] ?? "";
$company_logo = $_SESSION['profile_photo'] ?? "";

/* business girişliyse: DB'den gerçek veriyi çek */
if ($role === 'business' && !empty($_SESSION['company_id'])) {

    $stmt = $pdo->prepare("SELECT owner_name, logo FROM companies WHERE id = ?");
    $stmt->execute([$_SESSION['company_id']]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($company) {
        if (!empty($company['owner_name'])) {
            $name = $company['owner_name'];
        }

        if (!empty($company['logo'])) {
            $company_logo = $company['logo'];
        }
    }
}

/* logo hâlâ yoksa placeholder */
if (!$company_logo) {
    $company_logo = "https://puandeks.com/img/placeholder/user.png";
}

/* -----------------------------------------
   ACTIVE PAGE DETECTION
----------------------------------------- */

$activePath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$active = basename($activePath);
?>



<header class="header menu_fixed" 
        style="background:#1c1c1c; padding:10px 0; width:100%; box-sizing:border-box;">
  <div class="container"
       style="max-width:1200px; margin:0 auto; display:flex; align-items:center;
              justify-content:space-between; flex-wrap:wrap;">

    <!-- LOGO -->
    <div id="logo" style="flex-shrink:0;">
      <a href="https://business.puandeks.com"
         style="display:inline-flex; align-items:center;">
        <img src="https://puandeks.com/img/core/logo-p-business.svg"
             width="203" height="31" alt="Puandeks İşletme"
             style="display:block; height:auto;">
      </a>
    </div>

    <!-- MENU -->
    <nav id="menu" class="main-menu" style="flex-grow:1;">
      <ul class="main-nav-list"
          style="list-style:none; margin:0; padding:0; display:flex; 
                 align-items:center; justify-content:flex-end; 
                 gap:15px; flex-wrap:wrap;">

        <!-- Fiyatlandırma -->
        <li>
          <a href="https://business.puandeks.com/plans"
             style="font-size:15px; font-weight:700;
             color:<?= ($active === 'plans') ? '#9FF6D3' : '#fff' ?>;
             text-decoration:none;"
             onmouseover="this.style.color='#9FF6D3'"
             onmouseout="this.style.color='<?= ($active === 'plans') ? '#9FF6D3' : '#fff' ?>'">
             Fiyatlandırma
          </a>
        </li>

        <!-- Yardım -->
        <li>
          <a href="https://business.puandeks.com/help"
             style="font-size:15px; font-weight:700;
             color:<?= ($active === 'help') ? '#9FF6D3' : '#fff' ?>;
             text-decoration:none;"
             onmouseover="this.style.color='#9FF6D3'"
             onmouseout="this.style.color='<?= ($active === 'help') ? '#9FF6D3' : '#fff' ?>'">
             Yardım
          </a>
        </li>

        <!-- Blog -->
        <li>
          <a href="https://business.puandeks.com/blog"
             style="font-size:15px; font-weight:700;
             color:<?= ($active === 'blog') ? '#9FF6D3' : '#fff' ?>;
             text-decoration:none;"
             onmouseover="this.style.color='#9FF6D3'"
             onmouseout="this.style.color='<?= ($active === 'blog') ? '#9FF6D3' : '#fff' ?>'">
             Blog
          </a>
        </li>

       <?php if ($role === 'business'): ?>
        <li style="display:inline-flex; align-items:center;">
          <a href="https://business.puandeks.com/home"
            style="display:inline-flex; align-items:center; gap:8px;
                    font-size:15px; font-weight:700; color:#3ADA8D; text-decoration:none;">

            <?php if (!empty($name)): ?>
            <span style="max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:inline-block;">
              <?= htmlspecialchars($name) ?>
            </span>
            <?php endif; ?>

            <img src="<?= htmlspecialchars($company_logo) ?>"
                alt="İşletme Logo"
                style="width:28px; height:28px; border-radius:50%; object-fit:cover;">

          </a>
        </li>

        <?php else: ?>

<!-- Giriş Yap -->
<li>
  <a href="https://business.puandeks.com/login"
     style="font-size:15px; font-weight:700; color:#fff; text-decoration:none;"
     onmouseover="this.style.color='#9FF6D3'"
     onmouseout="this.style.color='#fff'">
     Giriş yap
  </a>
</li>

<!-- İşletme Kaydı -->
<li>
  <a href="https://business.puandeks.com/register"
     style="font-size:15px; font-weight:700; background:#05462F; color:#fff;
            text-decoration:none; padding:8px 20px; border-radius:20px;"
     onmouseover="this.style.background='#04DA8D'; this.style.color='#1C1C1C';"
     onmouseout="this.style.background='#05462F'; this.style.color='#fff';">
     İşletme Kaydı
  </a>
</li>

<?php endif; ?>


      </ul>
    </nav>

    <!-- MOBILE HAMBURGER -->
    <a href="#menu" class="btn_mobile" style="margin-left:10px; padding-top:10px;">
      <div class="hamburger hamburger--spin" id="hamburger">
        <div class="hamburger-box"><div class="hamburger-inner"></div></div>
      </div>
    </a>

  </div>
</header>
