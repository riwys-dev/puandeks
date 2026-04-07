<?php
require_once('/home/puandeks.com/backend/config.php');

$company_id = isset($_GET['company']) ? (int)$_GET['company'] : 0;
if ($company_id <= 0) {
    exit;
}

/* SLUG EKLENDİ */
$stmt = $conn->prepare("SELECT slug FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$company_slug = $stmt->fetchColumn();

/* Ortalama + toplam */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total_reviews,
           AVG(rating) AS avg_rating
    FROM reviews
    WHERE company_id = ?
      AND status = 1
      AND parent_id IS NULL
");
$stmt->execute([$company_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$total_reviews = (int)($data['total_reviews'] ?? 0);
$avg_rating_raw = $data['avg_rating'] !== null ? (float)$data['avg_rating'] : 0.0;
$avg_rating = $total_reviews > 0 ? round($avg_rating_raw, 1) : 0.0;

/* vote svg */
if ($avg_rating <= 0) $vote_level = 0;
elseif ($avg_rating < 2) $vote_level = 1;
elseif ($avg_rating < 3) $vote_level = 2;
elseif ($avg_rating < 4) $vote_level = 3;
elseif ($avg_rating < 5) $vote_level = 4;
else $vote_level = 5;

$vote_svg = "https://puandeks.com/img/core/vote_{$vote_level}.svg";

/* Yorumlar – en yeni 4 */
$stmt = $conn->prepare("
    SELECT r.title, r.comment, r.created_at, u.name
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.company_id = ?
      AND r.status = 1
      AND r.parent_id IS NULL
    ORDER BY r.created_at DESC
    LIMIT 4
");
$stmt->execute([$company_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
$review_count = count($reviews);
?>


<style>
body{
  margin:0;
  padding:0;
  font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
}

.pd-flex-widget{position:relative;width:100%;max-width:420px;background:#ffffff;border:1px solid #e5e5e5;border-radius:16px;padding:16px;box-sizing:border-box;margin:0 auto;}
.pd-flex-summary{display:flex;align-items:center;gap:12px;margin-bottom:16px;}
.pd-stars{height:18px;display:block;}
.pd-score{display:flex;flex-direction:column;}
.pd-rating{font-size:22px;font-weight:700;line-height:1;color:#1C1C1C;}
.pd-count{font-size:13px;color:#666;}
.pd-review-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;}
.pd-user{font-size:14px;font-weight:600;color:#1C1C1C;max-width:60%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.pd-verified{font-size:12px;font-weight:600;color:#1DB954;}
.pd-review-title{font-size:16px;font-weight:700;margin-bottom:6px;color:#1C1C1C;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.pd-review-text{font-size:14px;line-height:1.5;color:#333;margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;text-overflow:ellipsis;}
.pd-review-date{font-size:12px;color:#777;}
.pd-brand{display:flex;align-items:center;}
.pd-brand img{height:20px;display:block;}
.pd-slider{overflow:hidden;}
.pd-slider-track{display:flex;transition:transform .35s ease;}
.pd-slide{min-width:100%;box-sizing:border-box;}
.pd-dots{display:flex;align-items:center;justify-content:space-between;margin-top:12px;}
.pd-dot{width:8px;height:8px;background:#ccc;border-radius:50%;cursor:pointer;}
.pd-dot.active{background:#1DB954;}
.pd-dots-left{display:flex;gap:6px;align-items:center;}
</style>


<div class="pd-flex-widget">

  <!-- Ortalama -->
  <div class="pd-flex-summary">
    <img class="pd-stars" src="<?= $vote_svg ?>" alt="puan">
    <div class="pd-score">
      <span class="pd-rating"><?= number_format($avg_rating,1) ?></span>
      <span class="pd-count"><?= number_format($total_reviews,0,',','.') ?> değerlendirme</span>
    </div>
  </div>

  <!-- Slider -->
  <div class="pd-slider">
    <div class="pd-slider-track">

      <?php foreach($reviews as $r): ?>
      <div class="pd-slide">
        <div class="pd-review-header">
          <span class="pd-user"><?= htmlspecialchars($r['name'] ?? 'Kullanıcı') ?></span>
          <span class="pd-verified">✓ Doğrulanmış</span>
        </div>
        <div class="pd-review-title"><?= htmlspecialchars($r['title']) ?></div>
        <div class="pd-review-text"><?= htmlspecialchars($r['comment']) ?></div>
        <div class="pd-review-date"><?= date('d.m.Y', strtotime($r['created_at'])) ?></div>
      </div>
      <?php endforeach; ?>

    </div>
  </div>

  <!-- Dots -->
  <div class="pd-dots">

  <div class="pd-dots-left">
    <?php foreach($reviews as $index=>$r): ?>
      <span class="pd-dot <?= $index===0?'active':'' ?>"></span>
    <?php endforeach; ?>
  </div>

  <div class="pd-brand">
    <a href="https://puandeks.com/company/<?= htmlspecialchars($company_slug) ?>" target="_blank">
      <img src="https://puandeks.com/img/puandeks-logo_2.svg">
    </a>
  </div>

</div>

</div>

<script>
(function(){
  const track = document.querySelector('.pd-slider-track');
  const slides = document.querySelectorAll('.pd-slide');
  const dots = document.querySelectorAll('.pd-dot');

  if(slides.length <= 1){
  document.querySelector('.pd-dots-left').style.display='none';
  return;
}

  let index = 0;

  function updateSlider(){
    track.style.transform = `translateX(-${index * 100}%)`;
    dots.forEach(d => d.classList.remove('active'));
    dots[index].classList.add('active');
  }

  dots.forEach((dot,i)=>{
    dot.addEventListener('click',()=>{
      index = i;
      updateSlider();
    });
  });

  /* Infinite auto rotate */
  setInterval(()=>{
    index++;
    if(index >= slides.length) index = 0;
    updateSlider();
  }, 4000);

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

// load sonrası
window.addEventListener("load", sendHeight);

// resize
window.addEventListener("resize", sendHeight);

// içerik geç yüklenirse
setTimeout(sendHeight, 300);
setTimeout(sendHeight, 800);

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