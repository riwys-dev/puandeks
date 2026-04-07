<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
html,body{margin:0;padding:0;overflow:hidden;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;}

.pd-carousel{display:flex;gap:40px;align-items:flex-start;width:100%;max-width:900px;margin:0 auto;overflow:hidden;}
.pd-carousel *{box-sizing:border-box;}

.pd-summary{flex:0 0 260px;background:#fff;border:1px solid #e5e5e5;border-radius:18px;padding:24px;}
.pd-summary-stars{height:18px;display:block;margin-bottom:14px;}
.pd-summary-score{font-size:32px;font-weight:700;margin-bottom:6px;color:#1C1C1C;}
.pd-summary-count{font-size:14px;color:#666;margin-bottom:24px;}
.pd-summary-brand img{height:32px;display:block;}

.pd-carousel-slider{flex:1;min-width:0;overflow:hidden;}
.pd-carousel-viewport{overflow:hidden;width:100%;cursor:grab;user-select:none;}
.pd-carousel-viewport:active{cursor:grabbing;}
.pd-carousel-viewport::-webkit-scrollbar{display:none;}
.pd-carousel-viewport{scrollbar-width:none;}

.pd-carousel-track{display:flex;gap:20px;}
.pd-carousel-item{flex:0 0 280px;}
.pd-carousel-box{background:#fff;border:1px solid #e5e5e5;border-radius:18px;padding:20px;height:100%;}

.pd-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;background:#05462F;color:#fff;}

.pd-card-stars{height:16px;margin:10px 0 14px 0;display:block;}
.pd-card-verified{color:#1DB954;font-size:13px;font-weight:600;margin-bottom:8px;}
.pd-card-title{font-size:16px;font-weight:700;margin-bottom:6px;color:#1C1C1C;}
.pd-card-meta{font-size:13px;color:#777;}

@media(max-width:768px){
.pd-carousel{flex-direction:column;gap:20px;}
.pd-summary{width:100%;flex:none;padding:16px;}
.pd-carousel-slider{width:100%;}
}
</style>
</head>
<body>

<div class="pd-carousel">

<div class="pd-summary">
<img class="pd-summary-stars" src="https://puandeks.com/img/core/vote_5.svg">
<div class="pd-summary-score">4.7</div>
<div class="pd-summary-count">2.341 değerlendirme</div>
<div class="pd-summary-brand">
<img src="https://puandeks.com/img/puandeks-logo_2.svg">
</div>
</div>

<div class="pd-carousel-slider">
<div class="pd-carousel-viewport">
<div class="pd-carousel-track">

<div class="pd-carousel-item">
<div class="pd-carousel-box">
<div class="pd-avatar">A</div>
<img class="pd-card-stars" src="https://puandeks.com/img/core/vote_5.svg">
<div class="pd-card-verified">✓ Doğrulanmış</div>
<div class="pd-card-title">Harika hizmet</div>
<div class="pd-card-meta">Ali • 12.02.2026</div>
</div>
</div>

<div class="pd-carousel-item">
<div class="pd-carousel-box">
<div class="pd-avatar">A</div>
<img class="pd-card-stars" src="https://puandeks.com/img/core/vote_4.svg">
<div class="pd-card-verified">✓ Doğrulanmış</div>
<div class="pd-card-title">Çok memnun kaldım</div>
<div class="pd-card-meta">Ayşe • 10.02.2026</div>
</div>
</div>

<div class="pd-carousel-item">
<div class="pd-carousel-box">
<div class="pd-avatar">M</div>
<img class="pd-card-stars" src="https://puandeks.com/img/core/vote_5.svg">
<div class="pd-card-verified">✓ Doğrulanmış</div>
<div class="pd-card-title">Tavsiye ederim</div>
<div class="pd-card-meta">Mehmet • 08.02.2026</div>
</div>
</div>

<div class="pd-carousel-item">
<div class="pd-carousel-box">
<div class="pd-avatar">Z</div>
<img class="pd-card-stars" src="https://puandeks.com/img/core/vote_4.svg">
<div class="pd-card-verified">✓ Doğrulanmış</div>
<div class="pd-card-title">Profesyonel ekip</div>
<div class="pd-card-meta">Zeynep • 07.02.2026</div>
</div>
</div>

<div class="pd-carousel-item">
<div class="pd-carousel-box">
<div class="pd-avatar">K</div>
<img class="pd-card-stars" src="https://puandeks.com/img/core/vote_5.svg">
<div class="pd-card-verified">✓ Doğrulanmış</div>
<div class="pd-card-title">Güvenilir firma</div>
<div class="pd-card-meta">Kerem • 05.02.2026</div>
</div>
</div>

<div class="pd-carousel-item">
<div class="pd-carousel-box">
<div class="pd-avatar">F</div>
<img class="pd-card-stars" src="https://puandeks.com/img/core/vote_4.svg">
<div class="pd-card-verified">✓ Doğrulanmış</div>
<div class="pd-card-title">Başarılı hizmet</div>
<div class="pd-card-meta">Fatma • 03.02.2026</div>
</div>
</div>

</div>
</div>
</div>

</div>

<script>
document.querySelectorAll('.pd-carousel-slider').forEach(function(root){

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

window.addEventListener('mouseup', ()=>{
if(!isDown) return;
isDown = false;
prevTranslate = currentTranslate;
viewport.style.cursor = 'grab';
});

window.addEventListener('mousemove', e=>{
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

});
</script>

</body>
</html>