<style>
html,body{margin:0;padding:0;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;}

.pd-list-widget{width:100%;max-width:700px;height:420px;background:#fff;border:1px solid #e5e5e5;border-radius:18px;padding:20px;box-sizing:border-box;margin:0 auto;overflow:hidden;}

.pd-list-summary{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
.pd-list-summary-left{display:flex;align-items:center;gap:8px;}
.pd-list-summary-label{font-weight:700;color:#1C1C1C;}
.pd-list-summary-score{font-weight:700;font-size:16px;}
.pd-list-summary-count{font-size:13px;color:#666;}
.pd-list-summary-brand img{height:22px;}

.pd-list-viewport{display:flex;flex-direction:column;gap:16px;max-height:320px;overflow-y:auto;padding-right:6px;}
.pd-list-viewport::-webkit-scrollbar{width:0;}
.pd-list-viewport{scrollbar-width:none;}

.pd-list-item{display:flex;gap:14px;border-top:1px solid #eee;padding-top:14px;}
.pd-list-user{flex:0 0 44px;text-align:center;}

.pd-avatar{
width:44px;
height:44px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-weight:700;
font-size:16px;
background:#05462F;
color:#fff;
}

.pd-list-user-name{font-size:13px;margin-top:6px;}

.pd-list-content{flex:1;}

.pd-list-title{font-weight:600;font-size:15px;margin-bottom:6px;}

.pd-list-text{font-size:14px;line-height:1.45;color:#333;}

@media (max-width:480px){

.pd-list-widget{padding:16px;}

.pd-list-summary{
flex-direction:column;
align-items:flex-start;
gap:10px;
}

.pd-list-summary-left{flex-wrap:wrap;}

.pd-list-summary-count{white-space:normal;}

.pd-list-summary-brand img{height:20px;}

.pd-list-viewport{max-height:260px;}

.pd-list-item{flex-direction:column;}

.pd-list-user{text-align:left;}

.pd-avatar{
width:36px;
height:36px;
}

.pd-list-user-name{margin-top:4px;}

}
</style>

<div class="pd-list-widget">

<div class="pd-list-summary">
<div class="pd-list-summary-left">
<img src="https://puandeks.com/img/core/vote_5.svg">
<span class="pd-list-summary-score">4.7</span>
<span class="pd-list-summary-count">2.341 değerlendirme</span>
</div>

<div class="pd-list-summary-brand">
<img src="https://puandeks.com/img/puandeks-logo_2.svg">
</div>
</div>

<div class="pd-list-viewport">

<div class="pd-list-item">
<div class="pd-list-user">
<div class="pd-avatar">A</div>
<div class="pd-list-user-name">Ali</div>
</div>
<div class="pd-list-content">
<div class="pd-list-title">Harika hizmet</div>
<div class="pd-list-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
</div>
</div>

<div class="pd-list-item">
<div class="pd-list-user">
<div class="pd-avatar">A</div>
<div class="pd-list-user-name">Ayşe</div>
</div>
<div class="pd-list-content">
<div class="pd-list-title">Çok memnun kaldım</div>
<div class="pd-list-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
</div>
</div>

<div class="pd-list-item">
<div class="pd-list-user">
<div class="pd-avatar">M</div>
<div class="pd-list-user-name">Mehmet</div>
</div>
<div class="pd-list-content">
<div class="pd-list-title">Tavsiye ederim</div>
<div class="pd-list-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
</div>
</div>

<div class="pd-list-item">
<div class="pd-list-user">
<div class="pd-avatar">Z</div>
<div class="pd-list-user-name">Zeynep</div>
</div>
<div class="pd-list-content">
<div class="pd-list-title">Profesyonel ekip</div>
<div class="pd-list-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
</div>
</div>

<div class="pd-list-item">
<div class="pd-list-user">
<div class="pd-avatar">K</div>
<div class="pd-list-user-name">Kerem</div>
</div>
<div class="pd-list-content">
<div class="pd-list-title">Güvenilir firma</div>
<div class="pd-list-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
</div>
</div>

<div class="pd-list-item">
<div class="pd-list-user">
<div class="pd-avatar">F</div>
<div class="pd-list-user-name">Fatma</div>
</div>
<div class="pd-list-content">
<div class="pd-list-title">Başarılı hizmet</div>
<div class="pd-list-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
</div>
</div>

</div>

</div>