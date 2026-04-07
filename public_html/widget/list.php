<?php
require_once("/home/puandeks.com/backend/config.php");

$companyId = isset($_GET['company']) ? (int)$_GET['company'] : 0;
if (!$companyId) exit;

/* SLUG */
$stmt = $pdo->prepare("SELECT slug FROM companies WHERE id = ?");
$stmt->execute([$companyId]);
$company_slug = $stmt->fetchColumn();

/* === YORUMLAR === */
$stmt = $pdo->prepare("
    SELECT r.title, r.comment, r.created_at, u.name, u.profile_image
    FROM reviews r
    INNER JOIN users u ON u.id = r.user_id
    WHERE r.company_id = ?
      AND r.status = 1
      AND r.parent_id IS NULL
    ORDER BY r.created_at DESC
");
$stmt->execute([$companyId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* === ORTALAMA === */
$stmtAvg = $pdo->prepare("
    SELECT AVG(rating) as avg_rating, COUNT(*) as total_count
    FROM reviews
    WHERE company_id = ?
      AND status = 1
      AND parent_id IS NULL
");
$stmtAvg->execute([$companyId]);
$ratingData = $stmtAvg->fetch(PDO::FETCH_ASSOC);

$avgRating = $ratingData['avg_rating'] ? round($ratingData['avg_rating'],1) : 0;
$totalCount = (int)$ratingData['total_count'];
?>

<style>
body{
  margin:0;
  padding:0;
  font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
}

.pd-list-widget{width:100%;background:#fff;border:1px solid #e5e5e5;border-radius:18px;padding:20px;box-sizing:border-box;margin:0 auto;}

.pd-list-summary{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
.pd-list-summary-left{display:flex;align-items:center;gap:8px;}
.pd-list-summary-label{font-weight:700;color:#1C1C1C;}
.pd-list-summary-score{font-weight:700;font-size:16px;}
.pd-list-summary-count{font-size:13px;color:#666;}
.pd-list-summary-brand img{height:22px;}

.pd-list-viewport{
  display:flex;
  flex-direction:column;
  gap:16px;
}

.pd-list-item{display:flex;gap:14px;border-top:1px solid #eee;padding-top:14px;}
.pd-list-user{flex:0 0 44px;text-align:center;}
.pd-list-user img{width:44px;height:44px;border-radius:50%;object-fit:cover;}
.pd-list-user-name{font-size:13px;margin-top:6px;}
.pd-list-content{flex:1;}
.pd-list-title{font-weight:600;font-size:15px;margin-bottom:6px;}
.pd-list-text{font-size:14px;line-height:1.45;color:#333;}

.pd-avatar{ width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:16px; background:#05462F; color:#fff; object-fit:cover; 
}

@media (max-width:480px){
.pd-list-widget{padding:16px;}
.pd-list-summary{flex-direction:column;align-items:flex-start;gap:10px;}
.pd-list-summary-left{flex-wrap:wrap;}
.pd-list-summary-count{white-space:normal;}
.pd-list-summary-brand img{height:20px;}
.pd-list-item{flex-direction:column;}
.pd-list-user{text-align:left;}
.pd-list-user img{width:36px;height:36px;}
.pd-list-user-name{margin-top:4px;}
}
</style>

<div class="pd-list-widget">

<div class="pd-list-summary">
<div class="pd-list-summary-left">
<img src="https://puandeks.com/img/core/vote_<?= round($avgRating) ?>.svg">
<span class="pd-list-summary-score"><?= $avgRating ?></span>
<span class="pd-list-summary-count"><?= number_format($totalCount,0,",",".") ?> değerlendirme</span>
</div>

<div class="pd-list-summary-brand">
  <a href="https://puandeks.com/company/<?= htmlspecialchars($company_slug) ?>" target="_blank">
    <img src="https://puandeks.com/img/puandeks-logo_2.svg">
  </a>
</div>

</div>

<div class="pd-list-viewport">

<?php foreach($reviews as $review): 
  $initial = mb_strtoupper(mb_substr($review['name'], 0, 1, "UTF-8"));
  $profileImage = $review['profile_image'];
?>
<div class="pd-list-item">
<div class="pd-list-user">
<?php if(!empty($profileImage)): ?>
    <img class="pd-avatar" src="https://puandeks.com/<?= htmlspecialchars($profileImage) ?>">
<?php else: ?>
    <div class="pd-avatar"><?= $initial ?></div>
<?php endif; ?>
<div class="pd-list-user-name"><?= htmlspecialchars($review['name']) ?></div>
</div>
<div class="pd-list-content">
<div class="pd-list-title"><?= htmlspecialchars($review['title']) ?></div>
<div class="pd-list-text"><?= nl2br(htmlspecialchars($review['comment'])) ?></div>
</div>
</div>
<?php endforeach; ?>

</div>

</div>

