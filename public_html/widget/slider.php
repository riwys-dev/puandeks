<?php
require_once("/home/puandeks.com/backend/config.php");

$companyId = isset($_GET['company']) ? (int)$_GET['company'] : 0;
if (!$companyId) exit;
$stmt = $pdo->prepare("SELECT slug FROM companies WHERE id = ?");
$stmt->execute([$companyId]);
$company_slug = $stmt->fetchColumn();

/* === YORUMLAR === */
$stmt = $pdo->prepare("
    SELECT r.title, r.comment, r.created_at, u.name
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

.pd-slider-widget{overflow:visible;position:relative;width:100%;margin:0 auto;padding:20px;box-sizing:border-box;}
.pd-slider-widget > div:first-child{
  display:flex;
  gap:10px;
  margin-bottom:10px;
}

.pd-slider-viewport{margin-bottom:20px; overflow:visible;position:relative;height:220px;}

.pd-slider-track{position:relative;display:flex;gap:20px;transition:transform .6s ease;will-change:transform;}
.pd-slider-item{flex:0 0 280px;box-sizing:border-box;}

.pd-slider-box{position:relative;background:#fff;border:1px solid #e5e5e5;border-radius:18px;padding:20px;height:100%;display:flex;flex-direction:column;justify-content:space-between;}

.pd-slider-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;}
.pd-slider-user{font-weight:600;font-size:14px;color:#1C1C1C;}
.pd-slider-verified{color:#1DB954;font-size:13px;font-weight:600;}

.pd-slider-title{font-size:16px;font-weight:700;margin-bottom:6px;color:#1C1C1C;}
.pd-slider-text{font-size:14px;line-height:1.5;margin-bottom:8px;color:#333;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;text-overflow:ellipsis;}
.pd-slider-date{font-size:12px;color:#777;}

.pd-slider-footer{display:flex;justify-content:space-between;align-items:center;margin-top:24px;padding-top:16px;border-top:1px solid #eee;gap:20px;}
.pd-slider-rating{display:flex;align-items:center;gap:8px;flex-wrap:nowrap;margin-top:10px;}
.pd-slider-score{font-weight:700;font-size:16px;}
.pd-slider-count{font-size:13px;color:#666;white-space:nowrap;}
.pd-slider-brand img{height:32px;display:block;}

.pd-slider-arrow{
  position:static;
  top:auto;
  width:42px;
  height:42px;
  border-radius:12px;
  border:1px solid #e5e5e5;
  background:#fff;
  color:#1C1C1C;
  font-size:18px;
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

.pd-slider-arrow.left{ left:0; }
.pd-slider-arrow.right{ right:0; }

@media (max-width:768px){
  .pd-slider-track{display:flex;gap:30px;transition:transform .6s ease;will-change:transform;}
  .pd-slider-item{flex:0 0 100%;}
  .pd-slider-footer{flex-direction:column;align-items:flex-start;gap:10px;}
  .pd-slider-count{white-space:normal;}
}

</style>


<div class="pd-slider-widget">

  <div class="pd-slider-widget">
    <button class="pd-slider-arrow left">‹</button>
    <button class="pd-slider-arrow right">›</button>
  </div>

  <div class="pd-slider-viewport">

    <div class="pd-slider-track">

      <?php foreach($reviews as $review): ?>
      <div class="pd-slider-item">
        <div class="pd-slider-box">

          <div class="pd-slider-header">
            <span class="pd-slider-user"><?= htmlspecialchars($review['name']) ?></span>
            <span class="pd-slider-verified">✓ Doğrulanmış</span>
          </div>

          <div class="pd-slider-title"><?= htmlspecialchars($review['title']) ?></div>
          <div class="pd-slider-text"><?= nl2br(htmlspecialchars($review['comment'])) ?></div>
          <div class="pd-slider-date"><?= date("d.m.Y", strtotime($review['created_at'])) ?></div>

        </div>
      </div>
      <?php endforeach; ?>

    </div>

    

  </div>

  <div class="pd-slider-footer">
    <div class="pd-slider-rating">
      <img src="https://puandeks.com/img/core/vote_<?= round($avgRating) ?>.svg">
      <span class="pd-slider-score"><?= $avgRating ?></span>
      <span class="pd-slider-count"><?= number_format($totalCount,0,",",".") ?> değerlendirme</span>
    </div>

  <div class="pd-slider-brand">
  <a href="https://puandeks.com/company/<?= htmlspecialchars($company_slug) ?>" target="_blank" style="display:block;">
    <img src="https://puandeks.com/img/puandeks-logo_2.svg">
  </a>
</div>

  </div>

</div>

<script>
(function(){

const script = document.currentScript;
const root = script.closest('.pd-slider-widget');
if(!root) return;

const track = root.querySelector('.pd-slider-track');
const originalItems = Array.from(root.querySelectorAll('.pd-slider-item'));
const prev = root.querySelector('.pd-slider-arrow.left');
const next = root.querySelector('.pd-slider-arrow.right');

if(originalItems.length === 0) return;

let index;
let autoTimer;
const INTERVAL = 4000;

originalItems.forEach(item=>{
const cloneAfter = item.cloneNode(true);
const cloneBefore = item.cloneNode(true);
track.appendChild(cloneAfter);
track.prepend(cloneBefore);
});

index = originalItems.length;

function getSlideWidth(){
const style = window.getComputedStyle(track);
const gap = parseInt(style.columnGap || style.gap || 0);
const itemWidth = track.children[0].offsetWidth;
return itemWidth + gap;
}

function update(animate=true){
track.style.transition = animate ? "transform .6s ease" : "none";
track.style.transform = "translateX(-" + (index * getSlideWidth()) + "px)";
}

function checkLoop(){
if(index >= originalItems.length * 2){
index = originalItems.length;
update(false);
}
if(index <= originalItems.length - 1){
index = originalItems.length * 2 - 1;
update(false);
}
}

function nextSlide(){
index++;
update(true);
setTimeout(checkLoop,600);
}

function prevSlide(){
index--;
update(true);
setTimeout(checkLoop,600);
}

function startAuto(){
stopAuto();
autoTimer = setInterval(nextSlide, INTERVAL);
}

function stopAuto(){
if(autoTimer){
clearInterval(autoTimer);
autoTimer = null;
}
}

if(prev){
prev.addEventListener('click', function(){
stopAuto();
prevSlide();
startAuto();
});
}

if(next){
next.addEventListener('click', function(){
stopAuto();
nextSlide();
startAuto();
});
}

let startX = 0;

track.addEventListener('touchstart', function(e){
stopAuto();
startX = e.touches[0].clientX;
});

track.addEventListener('touchend', function(e){
const diff = e.changedTouches[0].clientX - startX;
if(diff > 60) prevSlide();
if(diff < -60) nextSlide();
startAuto();
});

const resizeObserver = new ResizeObserver(()=>{
update(false);
});

resizeObserver.observe(root);

update(false);
setTimeout(function(){
track.style.transition = "transform .6s ease";
},50);

startAuto();

})();
</script>

<script>
(function(){

function sendHeight(){
  const height = document.body.scrollHeight;

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
