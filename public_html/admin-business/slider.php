<style>
.pd-slider-widget{position:relative;}
.pd-slider-widget{width:100%;}
.pd-slider-widget{max-width:1000px;}
.pd-slider-widget{margin:0 auto;}
.pd-slider-widget{padding:20px;}
.pd-slider-widget{box-sizing:border-box;}

.pd-slider-viewport{overflow:hidden;}
.pd-slider-viewport{position:relative;}

.pd-slider-track{display:flex;}
.pd-slider-track{gap:20px;}
.pd-slider-track{transition:transform .6s ease;}
.pd-slider-track{will-change:transform;}

.pd-slider-item{flex:0 0 280px;}
.pd-slider-item{box-sizing:border-box;}

.pd-slider-widget > div:first-child{
  display:flex;
  gap:10px;
  margin-bottom:10px;
}

.pd-slider-box{background:#ffffff;}
.pd-slider-box{border:1px solid #e5e5e5;}
.pd-slider-box{border-radius:18px;}
.pd-slider-box{padding:20px;}
.pd-slider-box{height:100%;}
.pd-slider-box{display:flex;}
.pd-slider-box{flex-direction:column;}
.pd-slider-box{justify-content:space-between;}

.pd-slider-header{display:flex;}
.pd-slider-header{justify-content:space-between;}
.pd-slider-header{align-items:center;}
.pd-slider-header{margin-bottom:8px;}

.pd-slider-user{font-weight:600;}
.pd-slider-user{font-size:14px;}
.pd-slider-user{color:#1C1C1C;}

.pd-slider-verified{color:#1DB954;}
.pd-slider-verified{font-size:13px;}
.pd-slider-verified{font-weight:600;}

.pd-slider-title{font-size:16px;}
.pd-slider-title{font-weight:700;}
.pd-slider-title{margin-bottom:6px;}
.pd-slider-title{color:#1C1C1C;}

.pd-slider-text{font-size:14px;}
.pd-slider-text{line-height:1.5;}
.pd-slider-text{margin-bottom:8px;}
.pd-slider-text{color:#333;}

.pd-slider-date{font-size:12px;}
.pd-slider-date{color:#777;}

.pd-slider-footer{display:flex;}
.pd-slider-footer{justify-content:space-between;}
.pd-slider-footer{align-items:center;}
.pd-slider-footer{margin-top:24px;}
.pd-slider-footer{padding-top:16px;}
.pd-slider-footer{border-top:1px solid #eee;}
.pd-slider-footer{gap:20px;}

.pd-slider-rating{display:flex;}
.pd-slider-rating{align-items:center;}
.pd-slider-rating{gap:8px;}
.pd-slider-rating{flex-wrap:nowrap;}

.pd-slider-score{font-weight:700;}
.pd-slider-score{font-size:16px;}

.pd-slider-count{font-size:13px;}
.pd-slider-count{color:#666;}
.pd-slider-count{white-space:nowrap;}

.pd-slider-brand img{height:32px;}
.pd-slider-brand img{display:block;}

.pd-slider-arrow{
  position:static;
  top:auto;
  transform:none;
  width:48px;
  height:48px;
  border-radius:14px;
  border:1px solid #e5e5e5;
  background:#fff;
  color:#1C1C1C;
  font-size:20px;
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 6px 16px rgba(0,0,0,0.08);
  transition:.2s ease;
}

.pd-slider-arrow:hover{
  transform:scale(1.05);
}

.pd-slider-arrow:disabled{
  opacity:.3;
  cursor:default;
}
@media (max-width:1024px){

  .pd-slider-item{
    flex:0 0 240px;
  }

}

@media (max-width:768px){

  .pd-slider-track{
    gap:0;
  }

  .pd-slider-item{
    flex:0 0 100%;
  }

  .pd-slider-arrow{
    width:34px;
    height:34px;
    font-size:18px;
  }

  .pd-slider-arrow.left{
    left:6px;
  }

  .pd-slider-arrow.right{
    right:6px;
  }

  .pd-slider-footer{
    flex-direction:column;
    align-items:flex-start;
    gap:10px;
  }

  .pd-slider-rating{
    flex-wrap:wrap;
  }

  .pd-slider-count{
    white-space:normal;
  }

}

</style>


<div class="pd-slider-widget">
<div style="display:flex; gap:10px; margin-bottom:10px;">
  <button class="pd-slider-arrow left">‹</button>
  <button class="pd-slider-arrow right">›</button>
</div>

<div class="pd-slider-viewport">
    <div class="pd-slider-track">

      <!-- SLIDE -->
      <div class="pd-slider-item">
        <div class="pd-slider-box">

          <div class="pd-slider-header">
            <span class="pd-slider-user">Ahmet</span>
            <span class="pd-slider-verified">✓ Doğrulanmış</span>
          </div>

          <div class="pd-slider-title">
            Harika deneyim!
          </div>

          <div class="pd-slider-text">
            Kesinlikle tavsiye ederim. Çok iyi çalışıyorlar ve destek ekibi çok ilgiliydi.
          </div>

          <div class="pd-slider-date">
            12.02.2026
          </div>

        </div>
      </div>

      <!-- 2 -->
      <div class="pd-slider-item">
        <div class="pd-slider-box">
          <div class="pd-slider-header">
            <span class="pd-slider-user">Ayşe</span>
            <span class="pd-slider-verified">✓ Doğrulanmış</span>
          </div>
          <div class="pd-slider-title">Çok memnun kaldım</div>
          <div class="pd-slider-text">
            Hizmet kalitesi beklentimin üzerindeydi.
          </div>
          <div class="pd-slider-date">10.02.2026</div>
        </div>
      </div>

      <!-- 3 -->
      <div class="pd-slider-item">
        <div class="pd-slider-box">
          <div class="pd-slider-header">
            <span class="pd-slider-user">Mehmet</span>
            <span class="pd-slider-verified">✓ Doğrulanmış</span>
          </div>
          <div class="pd-slider-title">Tavsiye ederim</div>
          <div class="pd-slider-text">
            Baştan sona sorunsuz bir süreçti.
          </div>
          <div class="pd-slider-date">08.02.2026</div>
        </div>
      </div>

      <!-- 4 -->
      <div class="pd-slider-item">
        <div class="pd-slider-box">
          <div class="pd-slider-header">
            <span class="pd-slider-user">Mehmet</span>
            <span class="pd-slider-verified">✓ Doğrulanmış</span>
          </div>
          <div class="pd-slider-title">Tavsiye ederim</div>
          <div class="pd-slider-text">
            Baştan sona sorunsuz bir süreçti.
          </div>
          <div class="pd-slider-date">08.02.2026</div>
        </div>
      </div>

      <!-- 5 -->
      <div class="pd-slider-item">
        <div class="pd-slider-box">
          <div class="pd-slider-header">
            <span class="pd-slider-user">Mehmet</span>
            <span class="pd-slider-verified">✓ Doğrulanmış</span>
          </div>
          <div class="pd-slider-title">Tavsiye ederim</div>
          <div class="pd-slider-text">
            Baştan sona sorunsuz bir süreçti.
          </div>
          <div class="pd-slider-date">08.02.2026</div>
        </div>
      </div>

       <!-- 5 -->
      <div class="pd-slider-item">
        <div class="pd-slider-box">
          <div class="pd-slider-header">
            <span class="pd-slider-user">Mehmet</span>
            <span class="pd-slider-verified">✓ Doğrulanmış</span>
          </div>
          <div class="pd-slider-title">Tavsiye ederim</div>
          <div class="pd-slider-text">
            Baştan sona sorunsuz bir süreçti.
          </div>
          <div class="pd-slider-date">08.02.2026</div>
        </div>
      </div>

    </div>
  </div>

  <!-- ALT BİLGİ -->
  <div class="pd-slider-footer">
    <div class="pd-slider-rating">
      <img src="https://puandeks.com/img/core/vote_5.svg">
      <span class="pd-slider-score">4.8</span>
      <span class="pd-slider-count">2341 değerlendirme</span>
    </div>

    <div class="pd-slider-brand">
      <img src="https://puandeks.com/img/puandeks-logo_2.svg">
    </div>
  </div>

</div>


<script>
document.querySelectorAll('.pd-slider-widget').forEach(function(root){

  const track = root.querySelector('.pd-slider-track');
  const originalItems = Array.from(root.querySelectorAll('.pd-slider-item'));
  const prev = root.querySelector('.pd-slider-arrow.left');
  const next = root.querySelector('.pd-slider-arrow.right');

  if(originalItems.length === 0) return;

  let index;
  let autoTimer;
  const INTERVAL = 4000;

  /* === CLONE EKLE === */
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

  /* SWIPE */
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

  window.addEventListener('resize', function(){
    update(false);
  });

  /* BAŞLANGIÇ */
  update(false);
  setTimeout(function(){
    track.style.transition = "transform .6s ease";
  },50);

  startAuto();

});
</script>

