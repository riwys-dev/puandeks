<style>
html,body{font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;}
.pd-flex-widget{position:relative;width:100%;height:auto;background:#ffffff;border:1px solid #e5e5e5;border-radius:16px;padding:16px;box-sizing:border-box;overflow:hidden;margin:0 auto;}
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

<div class="pd-flex-summary">
<img class="pd-stars" src="https://puandeks.com/img/core/vote_5.svg">
<div class="pd-score">
<span class="pd-rating">4.8</span>
<span class="pd-count">2.341 değerlendirme</span>
</div>
</div>

<div class="pd-slider">
<div class="pd-slider-track">

<div class="pd-slide">
<div class="pd-review-header">
<span class="pd-user">Ali</span>
<span class="pd-verified">✓ Doğrulanmış</span>
</div>
<div class="pd-review-title">Harika servis</div>
<div class="pd-review-text">
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
</div>
<div class="pd-review-date">12.02.2026</div>
</div>

<div class="pd-slide">
<div class="pd-review-header">
<span class="pd-user">Ayşe</span>
<span class="pd-verified">✓ Doğrulanmış</span>
</div>
<div class="pd-review-title">Olduça iyi</div>
<div class="pd-review-text">
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam quis nostrud exercitation ullamco.
</div>
<div class="pd-review-date">10.02.2026</div>
</div>

<div class="pd-slide">
<div class="pd-review-header">
<span class="pd-user">Ahmet</span>
<span class="pd-verified">✓ Doğrulanmış</span>
</div>
<div class="pd-review-title">Güzel hizmet</div>
<div class="pd-review-text">
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis aute irure dolor in reprehenderit in voluptate velit esse.
</div>
<div class="pd-review-date">08.02.2026</div>
</div>

</div>
</div>

<div class="pd-dots">
<div class="pd-dots-left">
<span class="pd-dot active"></span>
<span class="pd-dot"></span>
<span class="pd-dot"></span>
</div>

<div class="pd-brand">
<img src="https://puandeks.com/img/puandeks-logo_2.svg">
</div>
</div>

</div>

<script>
(function(){
const script = document.currentScript;
const widget = script.closest('.pd-flex-widget');

const track = widget.querySelector('.pd-slider-track');
const slides = widget.querySelectorAll('.pd-slide');
const dots = widget.querySelectorAll('.pd-dot');

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

setInterval(()=>{
index++;
if(index >= slides.length) index = 0;
updateSlider();
},4000);

})();
</script>