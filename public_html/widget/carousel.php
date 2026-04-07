<?php
require_once("/home/puandeks.com/backend/config.php");

/* COMPANY ID */
$companyId = isset($_GET['company']) ? (int)$_GET['company'] : 0;
if (!$companyId) exit;

/* SLUG */
$stmt = $pdo->prepare("SELECT slug FROM companies WHERE id = ?");
$stmt->execute([$companyId]);
$company_slug = $stmt->fetchColumn();

/* === YORUMLAR === */
$stmt = $pdo->prepare("
    SELECT r.title, r.comment, r.rating, r.created_at, u.name, u.profile_image 
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
    SELECT AVG(r.rating) as avg_rating, COUNT(*) as total_count
    FROM reviews r
    WHERE r.company_id = ?
      AND r.status = 1
      AND r.parent_id IS NULL
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

.pd-carousel{
  display:flex;
  gap:40px;
  align-items:flex-start;
  width:100%;
  overflow:visible;
  position:relative;
}
.pd-carousel *{box-sizing:border-box;}

.pd-summary{flex:0 0 260px;background:#fff;border:1px solid #e5e5e5;border-radius:18px;padding:24px;}
.pd-summary-stars{height:18px;display:block;margin-bottom:14px;}
.pd-summary-score{font-size:32px;font-weight:700;margin-bottom:6px;color:#1C1C1C;}
.pd-summary-count{font-size:14px;color:#666;margin-bottom:24px;}
.pd-summary-brand img{height:32px;display:block;}

.pd-carousel-slider{flex:1;min-width:0;overflow:hidden;}
.pd-carousel-track{will-change: transform;}
.pd-carousel-viewport{
  overflow:visible;
  width:100%;
  cursor:grab;
  user-select:none;
}
.pd-carousel-viewport:active{cursor:grabbing;}
.pd-carousel-viewport::-webkit-scrollbar{display:none;}
.pd-carousel-viewport{scrollbar-width:none;}

.pd-carousel-track{display:flex;gap:20px;}
.pd-carousel-item{flex:0 0 260px;max-width:260px;}
.pd-carousel-box{background:#fff;border:1px solid #e5e5e5;border-radius:18px;padding:20px;height:100%;}

.pd-avatar{ width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; object-fit:cover; background:#05462F; color:#fff; }

.pd-card-stars{height:16px;margin:10px 0 14px 0;display:block;}
.pd-card-verified{color:#1DB954;font-size:13px;font-weight:600;margin-bottom:8px;}
.pd-card-title{font-size:16px;font-weight:700;margin-bottom:6px;color:#1C1C1C;}
.pd-card-meta{font-size:13px;color:#777;}

@media(max-width:768px){
.pd-carousel{flex-direction:column;gap:20px;}
.pd-summary{width:100%;flex:none;padding:16px;}
.pd-carousel-slider{width:100%;}
.pd-summary{width:100%;flex:none;max-width:100%;padding:16px;}
}

</style>

<div class="pd-carousel">

<div class="pd-summary">
<img class="pd-summary-stars" src="https://puandeks.com/img/core/vote_<?= round($avgRating) ?>.svg">
<div class="pd-summary-score"><?= $avgRating ?></div>
<div class="pd-summary-count"><?= number_format($totalCount,0,",",".") ?> değerlendirme</div>

<div class="pd-summary-brand">
  <a href="https://puandeks.com/company/<?= htmlspecialchars($company_slug) ?>" target="_blank">
    <img src="https://puandeks.com/img/puandeks-logo_2.svg">
  </a>
</div>

</div>

<div class="pd-carousel-slider">
<div class="pd-carousel-viewport">
<div class="pd-carousel-track">

<?php foreach($reviews as $review): 
$initial = mb_strtoupper(mb_substr($review['name'],0,1,"UTF-8"));
$profileImage = $review['profile_image'];
?>
<div class="pd-carousel-item">
<div class="pd-carousel-box">

<?php if(!empty($profileImage)): ?>
    <img class="pd-avatar" src="https://puandeks.com/<?= htmlspecialchars($profileImage) ?>">
<?php else: ?>
    <div class="pd-avatar"><?= $initial ?></div>
<?php endif; ?>

<img class="pd-card-stars" src="https://puandeks.com/img/core/vote_<?= (int)$review['rating'] ?>.svg">

<div class="pd-card-verified">✓ Doğrulanmış</div>

<div class="pd-card-title"><?= htmlspecialchars($review['title']) ?></div>

<div class="pd-card-meta">
<?= htmlspecialchars($review['name']) ?> • <?= date("d.m.Y", strtotime($review['created_at'])) ?>
</div>

</div>
</div>
<?php endforeach; ?>

</div>
</div>
</div>

</div>

<script>
(function(){

const script = document.currentScript;
const root = script.closest('.pd-carousel');
if(!root) return;

const viewport = root.querySelector('.pd-carousel-viewport');
const track = root.querySelector('.pd-carousel-track');
if(!viewport || !track) return;

const items = Array.from(track.children);
if(items.length === 0) return;

items.forEach(item=>{
track.appendChild(item.cloneNode(true));
});

let isDown = false;
let startX = 0;
let currentTranslate = 0;
let prevTranslate = 0;
let itemWidth = items[0].offsetWidth + 20;
let totalWidth = itemWidth * items.length;

function setPosition(){
track.style.transform = `translate3d(${currentTranslate}px,0,0)`;
}

function moveTo(x){
currentTranslate = x;

if(currentTranslate <= -totalWidth){
currentTranslate += totalWidth;
prevTranslate = currentTranslate;
}
if(currentTranslate >= 0){
currentTranslate -= totalWidth;
prevTranslate = currentTranslate;
}
setPosition();
}

viewport.addEventListener('mousedown', e=>{
isDown = true;
startX = e.clientX;
viewport.style.cursor = 'grabbing';
});

viewport.addEventListener('mouseup', ()=>{
if(!isDown) return;
isDown = false;
prevTranslate = currentTranslate;
viewport.style.cursor = 'grab';
});

viewport.addEventListener('mouseleave', ()=>{
if(!isDown) return;
isDown = false;
prevTranslate = currentTranslate;
viewport.style.cursor = 'grab';
});

viewport.addEventListener('mousemove', e=>{
if(!isDown) return;
moveTo(prevTranslate + (e.clientX - startX));
});

viewport.addEventListener('touchstart', e=>{
isDown = true;
startX = e.touches[0].clientX;
});

viewport.addEventListener('touchend', ()=>{
isDown = false;
prevTranslate = currentTranslate;
});

viewport.addEventListener('touchmove', e=>{
if(!isDown) return;
moveTo(prevTranslate + (e.touches[0].clientX - startX));
});

})();
</script>

<script>
(function(){

function sendHeight(){
  const height = document.documentElement.scrollHeight;

  window.parent.postMessage({
    type: "puandeks-widget-height",
    height: height,
    src: window.location.href
  }, "*");
}

window.addEventListener("load", sendHeight);
window.addEventListener("resize", sendHeight);
setTimeout(sendHeight, 300);
setTimeout(sendHeight, 800);

})();
</script>