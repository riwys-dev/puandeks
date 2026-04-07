<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('/home/puandeks.com/backend/config.php');

$role = $_SESSION['role'] ?? null;

$user_display = '';
$badge_label  = '';
$badge_color  = '';

$hasImage   = false;
$imageUrl   = '';
$firstLetter = '';

if ($role === 'user' && isset($_SESSION['user_id'])) {

    // User DATA
    $stmt = $pdo->prepare("
        SELECT 
            name, 
            surname,
            profile_image,
            (
                SELECT COUNT(*) 
                FROM reviews r 
                WHERE r.user_id = users.id AND r.status = 1
            ) AS approved_review_count
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
        'name' => '',
        'surname' => '',
        'profile_image' => '',
        'approved_review_count' => 0
    ];

    // User Name 
    $user_display =
        htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . ' ' .
        mb_strtoupper(mb_substr($user['surname'], 0, 1, 'UTF-8'), 'UTF-8') . '.';

    // -------- AVATAR  --------
    $profileImage = trim($user['profile_image'] ?? '');
    $firstLetter = mb_strtoupper(mb_substr(trim($user['name'] ?? ''), 0, 1, 'UTF-8'), 'UTF-8');

    if ($profileImage !== '') {
        if (strpos($profileImage, 'http') === 0) {
            $imageUrl = $profileImage;
            $hasImage = true;
        } elseif (strpos($profileImage, 'uploads/') === 0) {
            $imageUrl = "https://puandeks.com/" . $profileImage;
            $hasImage = true;
        } else {
            $imageUrl = "https://puandeks.com/uploads/users/" . $profileImage;
            $hasImage = true;
        }
    }

    // -------- ROZET --------
    $count = intval($user['approved_review_count']);

    if ($count >= 500) {
        $badge_label = "Lider";
        $badge_color = "#D14B00";
    } elseif ($count >= 100) {
        $badge_label = "Elite";
        $badge_color = "#AA00FF";
    } elseif ($count >= 50) {
        $badge_label = "Uzman";
        $badge_color = "#0066FF";
    } elseif ($count >= 10) {
        $badge_label = "Yeni";
        $badge_color = "#1b7d2f";
    }
}
?>


<style>
  .mm-menu {
    background-color: #1C1C1C !important;
  }
</style>


<header class="header menu_fixed"
        style="background:#1c1c1c; padding:10px 0; width:100%; box-sizing:border-box;">

  <div class="container"
       style="max-width:1200px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;">

    <!-- Logo -->
    <div id="logo" style="flex-shrink:0;">
      <a href="https://puandeks.com" style="display:inline-flex; align-items:center;">
       <img src="/img/puandeks-logo.svg" width="140" height="35" alt="Puandeks" style="display:block; height:auto;">
      </a>
    </div>

    <!-- Menu -->
    <nav id="menu" class="main-menu" style="flex-grow:1;">
      <ul class="main-nav-list"
          style="list-style:none; margin:0; padding:0; display:flex; align-items:center; justify-content:flex-end; gap:15px; flex-wrap:wrap;">

        <li><a href="/write-review" style="font-size:15px; font-weight:700; color:#fff; text-decoration:none;"
               onmouseover="this.style.color='#9FF6D3'" onmouseout="this.style.color='#fff'">İnceleme Yaz</a></li>

        <li><a href="/categories" style="font-size:15px; font-weight:700; color:#fff; text-decoration:none;"
               onmouseover="this.style.color='#9FF6D3'" onmouseout="this.style.color='#fff'">Kategoriler</a></li>

        <li><a href="/blog" style="font-size:15px; font-weight:700; color:#fff; text-decoration:none;"
               onmouseover="this.style.color='#9FF6D3'" onmouseout="this.style.color='#fff'">Blog</a></li>

       <?php if ($role === 'user'): ?>
        <!-- Avatar + Name -->
        <li style="display:inline-flex; align-items:center;">
          <a href="/user"
             style="display:inline-flex; align-items:center; gap:8px; font-size:15px; font-weight:700; color:#3ADA8D; text-decoration:none;">

            <!-- Avatar Wrapper -->
            <span style="position:relative; display:inline-block; width:28px; height:28px;">

              <!-- Avatar -->
             <?php if ($hasImage): ?>
                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Profil"
                    style="width:28px; height:28px; border-radius:50%; object-fit:cover;">
            <?php else: ?>
                <div style="
                    width:28px;
                    height:28px;
                    border-radius:50%;
                    background:#05462F;
                    color:#ffffff;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-weight:bold;
                    font-size:14px;
                ">
                    <?= $firstLetter ?>
                </div>
            <?php endif; ?>


              <!-- Badge -->
              <?php if (!empty($badge_label)): ?>
              <span style="
                  position:absolute;
                  top:-8px;
                  right:-8px;
                  background:<?= $badge_color ?>;
                  color:#fff;
                  font-size:9px;
                  font-weight:700;
                  padding:1px 4px;
                  border-radius:10px;
                  line-height:1;
              ">
                  <?= $badge_label ?>
              </span>
              <?php endif; ?>

            </span>

            <span><?= $user_display ?></span>

          </a>
        </li>
    <?php else: ?>

        <li>
          <a href="/login" style="font-size:15px; font-weight:700; color:#fff; text-decoration:none;"
             onmouseover="this.style.color='#9FF6D3'" onmouseout="this.style.color='#fff'">Giriş yap</a>
        </li>
        <?php endif; ?>

        <li>
          <a href="https://business.puandeks.com"
             style="font-size:15px; font-weight:700; background:#05462F; color:#fff; text-decoration:none; padding:8px 20px; border-radius:20px;"
             onmouseover="this.style.background='#04DA8D'; this.style.color='#1C1C1C';"
             onmouseout="this.style.background='#05462F'; this.style.color='#fff';">
             İşletmeler için
          </a>
        </li>

      </ul>
    </nav>

    <!-- Mobil Menü Butonu -->
    <a href="#menu" class="btn_mobile" style="margin-left:10px; padding-top:10px;">
      <div class="hamburger hamburger--spin" id="hamburger">
        <div class="hamburger-box">
          <div class="hamburger-inner"></div>
        </div>
      </div>
    </a>

  </div>
</header>
