<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once('/home/puandeks.com/backend/config.php');

$role = $_SESSION['role'] ?? null;
$company_display = '';
$company_logo = 'https://puandeks.com/img/placeholder/user.png';

if ($role === 'business' && isset($_SESSION['company_id'])) {
  try {
    $stmt = $conn->prepare("SELECT name, logo FROM companies WHERE id = ?");
    $stmt->execute([$_SESSION['company_id']]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($company) {
      $company_display = htmlspecialchars($company['name']);
      if (!empty($company['logo'])) {
        $company_logo = htmlspecialchars($company['logo']);
      }
    }
  } catch (PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="description" content="">
   <title>İşletme Yardım Merkezi - Puandeks</title>

   <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
   <link rel="shortcut icon" href="https://puandeks.com/img/favicon.ico" type="image/x-icon">
   <link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
   <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
   <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/apple-touch-icon-114x114-precomposed.png">
   <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/apple-touch-icon-144x144-precomposed.png">

   <!-- BASE CSS -->
   <link href="https://puandeks.com/business-admin/css/jquery.mmenu.all.css" rel="stylesheet" >
   <link href="https://puandeks.com/css/bootstrap.min.css" rel="stylesheet">
   <link href="https://puandeks.com/css/style.css" rel="stylesheet">
   <link href="https://puandeks.com/css/vendors.css" rel="stylesheet">

   <!-- CUSTOM CSS -->
   <link href="https://puandeks.com/css/custom.css" rel="stylesheet">
  
   <!-- Font Awesome 6 -->
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">


   <!-- Accordion -->
   <style>
   .accordion-item { border:1px solid #ddd; border-radius:8px; margin-bottom:10px; background:#fff; overflow:hidden; }
   .accordion-header { width:100%; text-align:left; padding:15px 20px; font-size:1.1rem; font-weight:700; border:none; background:#f7f7f7; cursor:pointer; position:relative; display:flex; align-items:center; }
   .accordion-header i { color:#1C1C1C; font-size:1.3rem; margin-right:10px; }
   .accordion-header::after { content:""; position:absolute; right:20px; font-size:1.6rem; font-weight:bold; transition:transform 0.3s ease; }
   .accordion-header.active::after { transform:rotate(90deg); }
   .accordion-content { max-height:0; overflow:hidden; transition:max-height 0.4s ease; border-top:1px solid #ddd; padding:0 20px; }
   .accordion-content.open { max-height:2000px; padding:15px 20px; }
   .accordion-content p { color:#1C1C1C; font-size:1rem; line-height:1.6; margin:10px 0; }
   .accordion-content strong { display:block; margin-top:10px; margin-bottom:5px; font-size:1rem; color:#1C1C1C; }
   </style>
   <!-- /Accordion -->

<style>
  body, html {
    overflow-x: hidden;
  }
  .info_cards_section .row {
    margin-left: 0 !important;
    margin-right: 0 !important;
  }
</style>

<style>
  @media (max-width: 767px) {
    #integrationsGrid {
      padding-left: 15px !important;
      padding-right: 15px !important;
    }
  }
</style>

<style>
  @media (max-width: 991px) {
    #mm-menu ul, 
    #mm-menu .mm-listview {
      display: flex !important;
      flex-direction: column !important;
      align-items: flex-start !important;
      justify-content: flex-start !important;
      gap: 8px !important;
    }

    #mm-menu li, 
    #mm-menu .mm-listview li {
      width: 100% !important;
    }

    #mm-menu a {
      display: block !important;
      width: 100% !important;
      padding: 10px 20px !important;
      color: #fff !important;
      text-align: left !important;
      text-decoration: none !important;
    }
  }
</style>

<style>
  .mm-menu {
    background-color: #1C1C1C !important;
  }
</style>

<style>
  html {
    overflow-y: scroll !important;
  }
</style>



</head>
<body>
<div id="page">

<?php include 'inc/header.php'; ?>

<!-- main -->      
<main>

<!-- Hero -->
<section style="background-color:#04DA8D; padding:100px 20px 0 20px;">
  <div style="max-width:1100px; margin:0 auto; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:40px;">

    <!-- Sol taraf -->
    <div style="flex:1; min-width:280px;">
      <h1 style="font-size:2.4rem; font-weight:700; margin-bottom:15px; color:#222;">İşletme Yardım Merkezi</h1>
      <p style="font-size:1.3rem; margin-bottom:20px; color:#222;">Aradığınızı bulmanıza yardım edelim!</p>

      <!-- Search -->
      <div style="display:flex; max-width:400px; background:#fff; border-radius:30px; padding:3px;">
        <input id="accordionSearch" type="text" placeholder="Hızlı arama" 
               style="flex:1; padding:10px 10px 10px 20px; font-size:1rem; outline:none; border:none; border-radius:30px;">
        <button id="accordionSearchBtn" 
                style="padding:10px 20px; border:none; background:#42A4FD; color:#fff; font-weight:600; cursor:pointer; border-radius:30px;">
          Ara
        </button>
      </div>

      <p id="searchMessage" style="margin-top:10px; font-size:0.95rem; display:none;"></p>
      <!-- /Search -->
    </div>

    <!-- Sa taraf -->
    <div style="flex:1; min-width:280px; text-align:center;">
      <img src="https://puandeks.com/img/banners/help-center.png" alt="Help Center" style="max-width:100%; height:auto;">
    </div>

  </div>
</section>
<!-- /Hero -->

<!-- Accordion -->
<section style="padding:60px 20px; background:#fff;">
  <div style="max-width:1100px; margin:0 auto;">
    <h2 style="font-size:1.8rem; font-weight:700; margin-bottom:40px; color:#222;">İşletmeler için Yardm</h2>
    <div class="accordion">

      <!-- 1 -->
      <div class="accordion-item">
        <button class="accordion-header"><i class="fa-solid fa-envelope-circle-check"></i> İncelemeleri Otomatik Olarak Talep Edin</button>
        <div class="accordion-content">

          <p><strong>Otomatik İnceleme Davetleri: Puandeks'te Kolay ve Etkili Geri Bildirim Toplama</strong></p>
          <p>
            Puandeks, kullanıcı yorumlarını etkili şekilde toplamanız için çeşitli otomatik inceleme daveti yöntemleri sunar.
            Bu sistemler, işletmenizin müşterilerden düzenli ve güvenilir geri bildirim almasını kolaylaştırır.
          </p>

          <p>
            Tüm otomatik yöntemlerle gönderilen davetlere yazılan yorumlar <em>“Doğrulanmış”</em> etiketiyle yayınlanır.
            Böylece potansiyel müşteriler yorumların gerçek deneyimlere dayandığından emin olur.
          </p>

          <strong>1. Otomatik Geri Bildirim Servisi (OGS)</strong>
          <p>
            Satın alma veya hizmet deneyimlerinden sonra müşterilere otomatik olarak e-posta ile yorum daveti gönderilir.<br>
            • Teknik bilgi gerekmez, kolayca entegrasyon sağlanır.<br>
            • Süreç otomatik işler, insan hatası minimuma iner.<br>
            • Davetlerin zamanlamasını planlayabilirsiniz.<br>
          </p>

          <strong>2. API ile Gelişmiş Entegrasyon</strong>
          <p>
            Enterprise işletmeler için sunulur. Geliştiriciniz API ile sisteminize entegre eder.<br>
            • Yorum daveti akışnı özelleştirebilirsiniz.<br>
            • CRM, ERP veya özel yazılımlarla entegre çalışır.<br>
            • Gelişmiş veri takibi ve analiz sağlar.<br>
          </p>

          <strong>3. E-Ticaret Entegrasyonları</strong>
          <p>
            WooCommerce, Shopify, Wix, Prestashop, İkas, Ticimax, T-Soft, Ideasoft, Shopier gibi platformlarla çalışır.<br>
            • Sipariş sonrası müşteri otomatik davet alır.<br>
            • Gönderim zamanlaması sizin kontrolünüzdedir.<br>
            • Düşük eforla yüksek geri bildirim oranı sağlar.<br>
          </p>

          <p><em>Not:</em> Otomatik davet yöntemlerinin bazıları yalnızca kurumsal veya ücretli planlarda sunulmaktadır.</p>
          <p><strong>Müşterilerinizin sesine kulak verin, markanız Puandeks ile büyüsün. Yorum almak artık daha kolay, sistemli ve etkili!</strong></p>

        </div>
      </div>
      <!-- /1 -->

      <!-- 2 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-sliders" style="margin-right:10px;"></i> Davetlerinizi özelleştirin</button>
      <div class="accordion-content">

        <p>
          Puandeks, müşterilerinize gönderdiğiniz inceleme davetlerini kendi marka kimliğinize göre özelleştirmenizi sağlar.  
          Bylece hem profesyonel bir izlenim bırakırsınız hem de dönş oranlarını artırabilirsiniz.
        </p>

        <strong>Özelleştirme Seçenekleri</strong>
        <p>
          • <b>Logo ve Renkler:</b> Markanzın logosunu ekleyin, kurumsal renk paletinizi kullanın.<br>
          • <b>Mesaj İçeriği:</b> E-postalarda kullanılacak başlık, selamlama ve kapanış cümlelerini özelleştirin.<br>
          • <b>Dil Seçenekleri:</b> Müşterilerinize uygun dilde otomatik davetler gönderin.<br>
          • <b>Kişiselleştirme:</b> Müşteri adı gibi dinamik alanlarla davetlerinizi daha samimi hale getirin.<br>
        </p>

        <strong>Neden Önemli?</strong>
        <p>
          • Kurumsal kimliğinizi yansıtmak güven verir.<br>
          • Davetlerin açılma ve yanıtlanma oranı yükselir.<br>
          • Müteriyle marka arasında daha gçlü bir bağ oluşur.<br>
        </p>

        <p>
          Unutmayın, standart bir davet yerine markanıza özel hazırlanmış bir davet çok daha fazla geri dönüş sağlar.
        </p>

      </div>
    </div>
    <!-- /2 -->


     <!-- 3 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-envelope-open-text" style="margin-right:10px;"></i> Manuel İnceleme Davetleri Gönderin</button>
  <div class="accordion-content">

    <p>
      Otomatik sistemlere ek olarak, müşterilerinize tek tek ya da toplu olarak manuel davet gönderebilirsiniz.  
      Bu yöntem özellikle düşük hacimli işletmeler veya belirli mşteri gruplarına ulaşmak isteyen firmalar için idealdir.
    </p>

    <strong>Nasıl Çalşır?</strong>
    <p>
       İşletme panelinizdeki “Davet Gönder” bölümüne gidin.<br>
      • Müşterinizin adını ve e-posta adresini girin.<br>
      • İsteğe bağlı olarak kısa bir kişisel not ekleyin.<br>
      • “Gönder” butonuna tıklayın.<br>
    </p>

    <strong>Toplu Davet Gönderimi</strong>
    <p>
      • CSV dosyası yükleyerek birden fazla müşteriye aynı anda davet gönderebilirsiniz.<br>
      • Sistem her bir müşteriye kişiselleştirilmiş e-posta yollar.<br>
    </p>

    <strong>Avantajları</strong>
    <p>
      • Küçük işletmeler için pratik ve hızlıdır.<br>
      • Belirli kampanyalar veya özel müşteri grupları için kullanılabilir.<br>
      • Teknik bilgi gerekmez, herkes kolayca uygulayabilir.<br>
    </p>

    <p>
      Manuel davetler, müşterilerle doğrudan iletişimi güçlendirir ve samimiyet katar.
    </p>

    <!-- Banner -->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınızdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
          style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
          onmouseover="this.style.backgroundColor='#04DA8D'" 
          onmouseout="this.style.backgroundColor='#fff'">
          Bugünden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /3 -->

      <!-- 4 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-qrcode" style="margin-right:10px;"></i> QR Kod ile İnceleme Toplayın</button>
  <div class="accordion-content">

    <p>
      Fiziksel mekânlarda müşterilerinizden hızlı ve kolay şekilde yorum toplamak için QR kodları kullanabilirsiniz.  
      QR kodlar, müterilerin telefon kameralarıyla tarayarak doğrudan Puandeks işletme sayfanıza ulaşmalarını sağlar.
    </p>

    <strong>Nasıl Kullanılır?</strong>
    <p>
      • İşletme panelinizde “QR Kod Oluştur” seçeneğine gidin.<br>
      • İşletmenize özel QR kodu indirin.<br>
      • Kodunuzu masaüstü, kapı, kasa veya menü gibi görünür alanlara yerleştirin.<br>
      • Müşterileriniz kodu tarayarak saniyeler iinde yorum bırakabilir.<br>
    </p>

    <strong>Avantajları</strong>
    <p>
      • Yorum toplama sürecini hızlandırr.<br>
      • Müşteri deneyimi çok daha kolay hale gelir.<br>
      • Özellikle restoran, kafe ve mağaza gibi fiziksel işletmeler için idealdir.<br>
    </p>

    <p>
      QR kodlar sayesinde müşterilerinizden yerinde ve taze yorumlar alabilirsiniz.
    </p>

    <!-- Banner-->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınızdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
          style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
          onmouseover="this.style.backgroundColor='#04DA8D'" 
          onmouseout="this.style.backgroundColor='#fff'">
          Bugünden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /4 -->


<!-- 5 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-sms" style="margin-right:10px;"></i> Müşterilerinizi SMS ile Davet Edin</button>
  <div class="accordion-content">

    <p>
      SMS davetleri, müşterilerinize doğrudan ve hızl şekilde ulaşmanızı sağlar.  
      Özellikle e-postalarnı sık kontrol etmeyen müşterilere ulaşmak için idealdir.
    </p>

    <strong>Nasıl Çalışır?</strong>
    <p>
      • İşletme panelinizdeki SMS Daveti Gönder” alanına girin.<br>
      • Müşterinizin telefon numarasını ekleyin.<br>
      • Kısa bir davet mesajı oluşturun.<br>
       Gönder butonuna basın; müşteriniz doğrudan yorum bırakma sayfasına yönlendirilir.<br>
    </p>

    <strong>Avantajları</strong>
    <p>
      • Yüksek erişim oranı sağlar, SMS’ler genellikle dakikalar içinde açılır.<br>
      • Özellikle hızlı geri dönüş isteyen işletmeler için uygundur.<br>
      • Teknik bilgi gerektirmez, kolayca uygulanabilir.<br>
    </p>

    <p>
      SMS yoluyla davet, müşteri deneyimini basitleştirir ve yorum toplama oranını artırır.
    </p>

    <!-- Banner -->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınzdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
          style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
          onmouseover="this.style.backgroundColor='#04DA8D'" 
          onmouseout="this.style.backgroundColor='#fff'">
          Bugünden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /5 -->



<!-- 6 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-link" style="margin-right:10px;"></i> Link ile Yorum Toplayın</button>
  <div class="accordion-content">

    <p>
      Müşterilerinizle paylaşabileceğiniz özel bir Puandeks yorum linki sayesinde hızlı ve kolay şekilde geri bildirim toplayabilirsiniz.  
      Bu yöntem özellikle sosyal medya, WhatsApp veya web sitenizde paylaşım için idealdir.
    </p>

    <strong>Nasıl Çalışır?</strong>
    <p>
      • İşletme panelinizdeki “Yorum Linki” alanına gidin.<br>
      • Size özel oluşturulmuş yorum linkini kopyalayın.<br>
      • Bu linki müterilerinize SMS, e-posta veya sosyal medya yoluyla gönderin.<br>
      • Müşteri linke tklayarak doğrudan yorum bırakma ekranına ulaşır.<br>
    </p>

    <strong>Avantajları</strong>
    <p>
      • Her ortamda kullanılabilir (e-posta, SMS, sosyal medya, web sitesi).<br>
      • Ekstra teknik işlem gerektirmez.<br>
      • Küçük işletmeler için en pratik yöntemlerden biridir.<br>
    </p>

    <p>
      Link paylaımı, müşterilerinize yorum bırakmaları için kolay ve erişilebilir bir yol sunar.
    </p>

    <!-- Banner -->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınızdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
           style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
           onmouseover="this.style.backgroundColor='#04DA8D'" 
           onmouseout="this.style.backgroundColor='#fff'">
          Bugünden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /6 -->


<!-- 7 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-window-maximize" style="margin-right:10px;"></i> İnceleme Widgetlarını Kullanın</button>
  <div class="accordion-content">

    <p>
      Puandeks inceleme widget’ları, işletme web sitenizde veya diğer dijital platformlarda müşteri yorumlarınızı sergilemenizi sağlar.  
      Böylece güven artırır, dönüşüm oranlarını yükseltirsiniz.
    </p>

    <strong>Nasıl Çalışır?</strong>
    <p>
      • İşletme panelinizden “Widgetlar” bölümüne girin.<br>
      • Dilediğiniz tasarımı seçin (liste, carousel, kart vb.).<br>
      • Renk, boyut ve tema ayarlarını markanıza uygun şekilde özelleştirin.<br>
      • Size verilen embed kodunu web sitenize ekleyin.<br>
    </p>

    <strong>Avantajları</strong>
    <p>
      • Müşterilerinizin güvenini artırır.<br>
      • Daha fazla dönüşüm ve satış getirir.<br>
      • Kurulum kolaydır, teknik bilgi gerekmez.<br>
    </p>

    <p>
      Widgetlar sayesinde işletmenizin güvenilirliğini artırabilir ve müşteri deneyimlerini görünür kılabilirsiniz.
    </p>

    <!-- Banner -->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınızdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
           style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
           onmouseover="this.style.backgroundColor='#04DA8D'" 
           onmouseout="this.style.backgroundColor='#fff'">
          Bugünden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /7 -->


<!-- 8 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-brands fa-square-facebook" style="margin-right:10px;"></i> İncelemelerinizi Sosyal Medyada Paylaşın</button>
  <div class="accordion-content">

    <p>
      İşletmenizin aldğı olumlu yorumları sosyal medyada paylaşarak marka güveninizi artırabilir ve daha geniş kitlelere ulaşabilirsiniz.  
      Puandeks, bu süreci kolaylaştıran araçlar sunar.
    </p>

    <strong>Nasıl Çalışr?</strong>
    <p>
      • şletme panelinizdeki “Yorumlar” bölümünden paylaşmak istediğiniz incelemeyi seçin.<br>
      • “Paylaş” butonuna tıklayın.<br>
      • Facebook, Instagram, LinkedIn veya Twitter için otomatik paylaşm seçeneklerini kullanın.<br>
      • Dilerseniz yorumun grsel çıktısını indirip manuel olarak paylaşabilirsiniz.<br>
    </p>

    <strong>Avantajları</strong>
    <p>
      • Müşteri güvenini artırır, işletmenize sosyal kanıt sağlar.<br>
      • Marka bilinirliğini yükseltir.<br>
      • Potansiyel müşterilerin ilgisini çeker.<br>
    </p>

    <p>
      Sosyal medya paylaşımları sayesinde olumlu yorumlarınızı çok daha fazla kişiye ulaştırabilirsiniz.
    </p>

    <!-- Banner -->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınızdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
           style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
           onmouseover="this.style.backgroundColor='#04DA8D'" 
           onmouseout="this.style.backgroundColor='#fff'">
          Bugünden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /8 -->



<!-- 9 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-reply" style="margin-right:10px;"></i> Yorumlara Yanıt Verin</button>
  <div class="accordion-content">

    <p>
      Müşterilerinizin yorumlarına yanıt vermek, geri bildirimlerini önemsediğinizi gösterir.  
      Bu sayede müşteri ilişkilerini güçlendirir ve marka imajınızı olumlu yönde etkilersiniz.
    </p>

    <strong>Neden Önemli?</strong>
    <p>
      • Olumlu yorumlara teşekkür ederek müteri memnuniyetini artırabilirsiniz.<br>
      • Olumsuz yorumlara yapıcı yanıt vererek özüm odaklı yaklaşım sergilersiniz.<br>
      • Yanıtlar, potansiyel müşterilere işletmenizin müşteri odaklı olduğunu gösterir.<br>
    </p>

    <strong>Nasıl Çalışır?</strong>
    <p>
      • İşletme panelinizde “Yorumları Ynet” bölümüne girin.<br>
      • Yanıt vermek istediğiniz yorumu seçin.<br>
      • Yanıtınızı yazın ve yayınlayın.<br>
      • Dilerseniz önceden hazırladığınız yanıt şablonlarını kullanabilirsiniz.<br>
    </p>

    <strong>İpuçları</strong>
    <p>
      • Yanıtlarınızda profesyonel ve saygılı bir dil kullanın.<br>
      • Sorunu çözmeye yönelik somut adımlar belirtin.<br>
      • Gerektiğinde müşteriyi özel iletişime davet edin.<br>
    </p>

    <p>
      Yorumlara düzenli ve zenli yanıt vermek, marka sadakati oluşturmanın en güçlü yollarından biridir.
    </p>

    <!-- Banner -->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınızdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
           style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
           onmouseover="this.style.backgroundColor='#04DA8D'" 
           onmouseout="this.style.backgroundColor='#fff'">
          Bugünden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /9 -->

<!-- 10 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-file-signature" style="margin-right:10px;"></i> Yanıt Şablonlarını Kullanın</button>
  <div class="accordion-content">

    <p>
      Müşterilerinizin yorumlarına daha hızlı ve tutarlı yant verebilmek için önceden hazırlanmış yanıt şablonlarını kullanabilirsiniz.  
      Bu özellik, özellikle yoğun yorum akışı olan işletmeler için büyük kolaylık sağlar.
    </p>

    <strong>Nasıl Çalşır?</strong>
    <p>
       İşletme panelinizde “Yant Şablonları” bölümüne gidin.<br>
      • Pozitif, negatif ve nötr yorumlar için hazır şablonları görüntüleyin.<br>
      • Dilerseniz kendi şablonlarınızı oluşturun.<br>
      • Yorum yanıtlanırken uygun şablonu seçip yayınlayın.<br>
    </p>

    <strong>Avantajları</strong>
    <p>
      • Zamandan tasarruf sağlar.<br>
      • Yanıtların tutarlılığını korur.<br>
      • Profesyonel bir iletişim tarzı sürdürmenizi kolaylatırır.<br>
    </p>

    <strong>İpuçları</strong>
    <p>
      • Şablonları kendi marka dilinize uyarlayın.<br>
      • Gerektiğinde şablonu kişiselleştirerek müşteriye özel bir dokunuş katın.<br>
      • Her zaman yapıcı ve çzüm odaklı ifadeler kullanın.<br>
    </p>

    <p>
      Yanıt şablonları, müşteri iletişim sürecinizi sistematik ve verimli hale getirir.
    </p>

    <!-- Banner -->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınızdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
           style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
           onmouseover="this.style.backgroundColor='#04DA8D'" 
           onmouseout="this.style.backgroundColor='#fff'">
          Bugünden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /10 -->


<!-- 11 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-face-frown" style="margin-right:10px;"></i> Olumsuz Yorumlara Profesyonel Yaklaşın</button>
  <div class="accordion-content">

    <p>
      Olumsuz yorumlar işletmeniz için bir tehdit değil, gelişim fırsatıdır.  
      Profesyonel bir şekilde yanıt verdiğinizde müşteri memnuniyetini artırabilir ve marka imajınızı güçlendirebilirsiniz.
    </p>

    <strong>Neden Önemlidir?</strong>
    <p>
      • Müşteriye değer verdiğinizi gösterir.<br>
      • Kriz durumlarını fırsata çevirebilirsiniz.<br>
      • Potansiyel müşterilere işletmenizin çözüm odaklı olduğunu kantlarsınız.<br>
    </p>

    <strong>Nasıl Yaklaşmalısınz?</strong>
    <p>
      • Yorumları dikkatle ve sakin şekilde okuyun.<br>
      • Müşteriye empatiyle yaklaşın, sorununu anladığınızı belirtin.<br>
      • Çözüm önerileri sunun, gerekiyorsa müşteriyi özel iletişime yönlendirin.<br>
      • Her zaman saygılı ve profesyonel bir dil kullanın.<br>
    </p>

    <strong>İpuçları</strong>
    <p>
      • Olumsuz yorumu asla görmezden gelmeyin.<br>
      • Suçu müşteriye atmak yerine çzüm odaklı olun.<br>
       Yanıtınızın diğer müşteriler tarafından da görüleceini unutmayın.<br>
    </p>

    <p>
      Unutmayın: Doğru yönetilen olumsuz yorumlar, işletmenize olan güveni artırır.
    </p>

  </div>
</div>
<!-- /11 -->


<!-- 12 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-flag" style="margin-right:10px;"></i> Yorumları İşaretleyin ve İtiraz Edin</button>
  <div class="accordion-content">

    <p>
      İşletmenize yapılan yorumların adil ve kurallara uygun olmasını sağlamak için yorumları işaretleyebilir veya itiraz edebilirsiniz.  
      Bu özellik, haksız ya da yanıltıcı yorumlara karşı işletmenizi korumanızı sağlar.
    </p>

    <strong>Ne Zaman İşaretlenmeli?</strong>
    <p>
      • Yorum işletme ile ilgisizse.<br>
      • Kişisel hakaret veya küfür içeriyorsa.<br>
      • Yanıltcı ya da yanlış bilgi içeriyorsa.<br>
      • Rakipler tarafından kasıtlı bırakılmışsa.<br>
    </p>

    <strong>Nasıl İşler?</strong>
    <p>
      • Yorumun altında bulunan “İşaretle” seçeneğine tıklayın.<br>
      • İtiraz gerekçenizi seçin ve açıklamanızı ekleyin.<br>
       Talebiniz yönetici ekibimiz tarafından incelenir.<br>
      • Gerekirse yorum geçici olarak gizlenir veya kaldırılır.<br>
    </p>

    <strong>İpuları</strong>
    <p>
       İşaretleme hakkınızı yalnızca gerekli durumlarda kullann.<br>
      • Şeffaflık iin yorumlar inceleme süreci sonucuna göre güncellenir.<br>
      • Süreci hızlandırmak için ekran görüntüsü veya belge gibi kanıtlar ekleyin.<br>
    </p>

    <p>
      Unutmayın: İtiraz mekanizması, işletmenizi haksız yorumlara karş korumak için vardır; ancak effaflık ilkesi her zaman ön plandadır.
    </p>

  </div>
</div>
<!-- /12 -->


<!-- 13 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-circle-check" style="margin-right:10px;"></i> Doğrulanmış Yorumların Gücünden Yararlanın</button>
  <div class="accordion-content">

    <p>
      Doğrulanmış yorumlar, işletmenizin güvenilirliğini artıran en önemli unsurlardan biridir.  
      Müşterilerin gerçekten sizden hizmet aldığını gösteren bu yorumlar, potansiyel müşteriler için güçlü bir referans sağlar.
    </p>

    <strong>Doğrulanmış Yorum Nedir?</strong>
    <p>
      • Otomatik davet, SMS, QR kod veya link ile toplanan yorumlar “Doğrulanmış etiketi alır.<br>
      • Bu etiket, yorumun gerçek bir deneyime dayandığını gösterir.<br>
    </p>

    <strong>Neden Önemlidir?</strong>
    <p>
      • Müşteri güvenini artırır.<br>
      • İşletmenizin şeffaf ve güvenilir olduunu kanıtlar.<br>
      • Potansiyel müşterilerin satın alma kararlarını hızlandırır.<br>
    </p>

    <strong>Nasl Daha Fazla Doğrulanmış Yorum Alabilirsiniz?</strong>
    <p>
      • Otomatik davet sistemlerini etkinleştirin.<br>
      • SMS ve QR kod yöntemlerini kullanın.<br>
      • İnceleme linkinizi müşterilerinizle paylaşın.<br>
    </p>

    <p>
      Doğrulanmış yorumlar, işletmenizin dijital itibarını güçlendirir ve sizi rakiplerinizin önüne taşır.
    </p>

    <!-- Banner -->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınızdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
           style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
           onmouseover="this.style.backgroundColor='#04DA8D'" 
           onmouseout="this.style.backgroundColor='#fff'">
          Bugnden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /13 -->

<!-- 14 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-chart-line" style="margin-right:10px;"></i> Yorum Analizlerini Kullanın</button>
  <div class="accordion-content">

    <p>
      Puandeks, müşterilerinizin yorumlarını detaylı analiz ederek işletmenizin güçlü ve zayıf yönlerini gösterir.  
      Bu analizler sayesinde daha bilinçli kararlar alabilir ve mşteri memnuniyetini artırabilirsiniz.
    </p>

    <strong>Hangi Verileri Sağlar?</strong>
    <p>
      • Ortalama puan dağılımı.<br>
      • Pozitif, negatif ve nötr yorum oranları.<br>
      • Kategori bazlı değerlendirmeler.<br>
      • Zaman içinde gelişim grafikleri.<br>
    </p>

    <strong>Neden Önemlidir?</strong>
    <p>
      • Müşteri beklentilerini daha iyi anlarsınız.<br>
      • Hangi alanlarda gelişmeniz gerektiğini netleştirirsiniz.<br>
      • Stratejik kararlarınızı gerçek verilere dayandırırsınız.<br>
    </p>

    <p>
      Yorum analizleri, işletmenizin gelişimi için en güvenilir yol haritalarından biridir.
    </p>

  </div>
</div>
<!-- /14 -->


<!-- 15 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-chart-column" style="margin-right:10px;"></i> Raporlama Özelliklerini Kullanın</button>
  <div class="accordion-content">

    <p>
      Puandeks’in raporlama araçları, işletmenizin performansını düzenli olarak takip etmenizi sağlar.  
      Yorumlardan elde edilen verileri grafikler ve tablolar halinde görüntleyebilir, stratejik kararlarınızı bu raporlara göre şekillendirebilirsiniz.
    </p>

    <strong>Sunulan Raporlar</strong>
    <p>
      • Günlük, haftalık ve aylık yorum sayıları.<br>
      • Ortalama puan trendleri.<br>
      • Olumlu/olumsuz yorumların zaman içindeki dağılımı.<br>
      • Kategori bazlı analizler.<br>
    </p>

    <strong>Avantajlar</strong>
    <p>
      • İşletme performansınızı net şekilde görürsünüz.<br>
      • Zayıf noktalarınızı hızlıca tespit edebilirsiniz.<br>
      • Yatırım ve pazarlama kararlarınızı verilere dayandırabilirsiniz.<br>
    </p>

    <p>
      Düzenli raporlamalar, işletmenizin gelişim sürecini şeffaf ve izlenebilir hale getirir.
    </p>

  </div>
</div>
<!-- /15 -->


<!-- 16 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-bell" style="margin-right:10px;"></i> Bildirimleri Kullanın</button>
  <div class="accordion-content">

    <p>
      Puandeks bildirim sistemi sayesinde işletmenizle ilgili gelişmelerden anında haberdar olabilirsiniz.  
      Yeni yorumlar, itiraz sonuçları ve sistem güncellemeleri size bildirim olarak iletilir.
    </p>

    <strong>Hangi Bildirimleri Alırsınız?</strong>
    <p>
      • Yeni bir yorum eklendiinde.<br>
      • Yorumunuza yanıt geldiğinde.<br>
       İtiraz ettiğiniz yorumla ilgili karar alındığında.<br>
      • Paket kullanımınızla ilgili önemli hatırlatmalarda.<br>
    </p>

    <strong>Nasl Çalışır?</strong>
    <p>
      • İşletme panelinizde bildirim simgesi üzerinden tüm bildirimlerinizi görebilirsiniz.<br>
      • Dilerseniz e-posta bildirimlerini de açabilirsiniz.<br>
      • Bildirim tercihlerinizi “Ayarlar” bölmünden yönetebilirsiniz.<br>
    </p>

    <p>
      Bildirimler sayesinde işletmenize dair hiçbir gelişmeyi kaçırmazsınız.
    </p>

  </div>
</div>
<!-- /16 -->


<!-- 17 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-file-invoice-dollar" style="margin-right:10px;"></i> Paket ve Faturalandırma Bilgilerinizi Yönetin</button>
  <div class="accordion-content">

    <p>
      İşletmenizin kullandığı paketleri ve faturalandırma bilgilerini kolayca ynetebilirsiniz.  
      Bu sayede ihtiyaçlarınıza uygun planı seçebilir ve ödemelerinizi güvenle takip edebilirsiniz.
    </p>

    <strong>Neleri Yapabilirsiniz?</strong>
    <p>
      • Mevcut paket detaylarını görüntüleyin.<br>
      • Paket yükseltme veya düşürme işlemlerini yapın.<br>
      • Fatura geçmişinizi inceleyin.<br>
      • Ödeme yöntemlerinizi güncelleyin.<br>
    </p>

    <strong>Avantajlar</strong>
    <p>
      • Kullanım ihtiyacınıza göre en uygun paketi seçebilirsiniz.<br>
      • Gereksiz maliyetlerden kaçınabilirsiniz.<br>
      • Ödemelerinizi düzenli takip ederek kesintisiz hizmet alırsınız.<br>
    </p>

    <p>
      Paket ve faturalandırma yönetimi, işletmenizin Puandeks kullanımını daha verimli hale getirir.
    </p>

  </div>
</div>
<!-- /17 -->

<!-- 18 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-user-gear" style="margin-right:10px;"></i> Hesap ve Profil Bilgilerinizi Güncelleyin</button>
  <div class="accordion-content">

    <p>
      İşletmenizin profil bilgilerini güncel tutarak müşterilerinize her zaman doğru ve güvenilir bilgiler sunabilirsiniz.  
      Ayrıca hesap ayarları üzerinden giriş bilgilerinizi ve güvenlik seçeneklerinizi de yönetebilirsiniz.
    </p>

    <strong>Neleri Güncelleyebilirsiniz?</strong>
    <p>
      • İletme adı, açıklama ve kategori bilgileri.<br>
      • Adres, telefon ve e-posta gibi iletişim bilgileri.<br>
      • Sosyal medya linkleri.<br>
      • Logo ve görsel yüklemeleri.<br>
    </p>

    <strong>Hesap Ayarları</strong>
    <p>
      • Şifre değişikliği yapabilirsiniz.<br>
      • Bildirim tercihlerinizi düzenleyebilirsiniz.<br>
      • İki faktörlü doğrulama gibi güvenlik ayarlarını aktif edebilirsiniz.<br>
    </p>

    <p>
      Güncel ve doğru bilgiler, işletmenizin hem aramalarda daha kolay bulunmasını sağlar hem de müşteri güvenini artırır.
    </p>

  </div>
</div>
<!-- /18 -->


<!-- 19 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-headset" style="margin-right:10px;"></i> Destek ve İletişim Kanallarını Kullanın</button>
  <div class="accordion-content">

    <p>
      Puandeks, işletmelerin ihtiyaç duyduğu her an destek alabilmesi için çeşitli iletişim kanallar sunar.  
      Sorun yaşadıınızda veya bilgi almak istediğinizde kolayca ulaşabilirsiniz.
    </p>

    <strong>Hangi Kanallardan Destek Alabilirsiniz?</strong>
    <p>
      • Canlı destek hattı üzerinden annda iletişim kurabilirsiniz.<br>
      • E-posta ile detayl sorularınızı iletebilirsiniz.<br>
      • SSS ve yardım merkezi bölümlerinden hızlıca yanıt bulabilirsiniz.<br>
    </p>

    <strong>Avantajları</strong>
    <p>
      • Sorunlarınız kısa sürede çözülebilir.<br>
      • İşletmenizin ihtiyaçlarına özel çözümler alabilirsiniz.<br>
      • Profesyonel destek ile süreçlerinizi kesintisiz sürdürebilirsiniz.<br>
    </p>

    <p>
      Destek kanallarını kullanarak her zaman güvenle ilerleyebilir, işletmenizi Puandeks ile daha verimli yönetebilirsiniz.
    </p>

  </div>
</div>
<!-- /19 -->





<!-- 20 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-users" style="margin-right:10px;"></i> Puandeks Topluluğunun Bir Parçası Olun</button>
  <div class="accordion-content">

    <p>
      Puandeks sadece bir inceleme platformu değil, aynı zamanda şeffaflık ve güven üzerine kurulmu bir topluluktur.  
      İşletmeler olarak siz de bu topluluğun bir parçası olabilir, müşterilerinizle daha güçlü bağlar kurabilirsiniz.
    </p>

    <strong>Neden Katılmalısınız?</strong>
    <p>
      • Müşteri güvenini artırırsnız.<br>
      • İşletmenizin görünürlüğünü yükseltirsiniz.<br>
      • Yorumlardan gelen geri bildirimlerle sürekli gelişim sağlarsınız.<br>
      • Şeffaf ve adil bir ekosistemin destekçisi olursunuz.<br>
    </p>

    <strong>Nasıl Katılabilirsiniz?</strong>
    <p>
      • Ücretsiz işletme hesabı oluşturun.<br>
      • Yorum toplamaya başlayn.<br>
      • Müşterilerinizle aktif olarak etkileşimde bulunun.<br>
    </p>

    <p>
      Unutmayın: Puandeks topluluğu, işletmeler ve tüketiciler için güveni birlikte inşa eder.  
      Siz de bugünden itibaren bu güven zincirinin bir halkası olun.
    </p>

    <!-- Banner -->
    <div style="max-width:700px; margin:30px 0 0 0; border-radius:16px; overflow:hidden;">
      <div style="background:url('https://puandeks.com/img/banners/business-help-2.png') no-repeat center/cover; width:100%; height:230px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; padding:30px; color:#1C1C1C;">
        <h3 style="font-size:1.4rem; font-weight:700; margin-bottom:15px; max-width:500px;">
          Puandeks yorumlarınızdan daha yüksek getiri elde etmek ister misiniz?
        </h3>
        <a href="https://business.puandeks.com/register.php" 
           style="padding:10px 20px; background:#fff; color:#1C1C1C; font-weight:600; border-radius:30px; text-decoration:none; display:inline-block;"
           onmouseover="this.style.backgroundColor='#04DA8D'" 
           onmouseout="this.style.backgroundColor='#fff'">
          Bugünden itibaren toplamaya başlayın
        </a>
      </div>
    </div>
    <!-- /Banner -->

  </div>
</div>
<!-- /20 -->





    </div>
  </div>
</section>
<!-- /Accordion -->

</main>
<!-- /main -->

<!-- footer -->
<?php include('footer-main.php'); ?>
<!-- /footer -->

</div>

<!-- COMMON SCRIPTS -->
<script src="js/jquery.mmenu.all.js"></script>
<script src="js/common_scripts.js"></script>
<script src="js/functions.js"></script>
<script src="assets/validate.js"></script>

<!-- Accordion -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const headers = document.querySelectorAll(".accordion-header");
  headers.forEach(header => {
    header.addEventListener("click", function() {
      this.classList.toggle("active");
      const content = this.nextElementSibling;
      content.classList.toggle("open");
    });
  });
});
</script>
<!-- /Accordion -->

<!-- Search box -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("accordionSearch");
  const searchBtn = document.getElementById("accordionSearchBtn");
  const searchMessage = document.getElementById("searchMessage");

  function clearHighlights() {
    document.querySelectorAll("mark").forEach(m => {
      const parent = m.parentNode;
      parent.replaceChild(document.createTextNode(m.textContent), m);
      parent.normalize();
    });
  }

  function resetAccordion() {
    clearHighlights();
    document.querySelectorAll(".accordion-item").forEach(item => {
      item.querySelector(".accordion-header").classList.remove("active");
      item.querySelector(".accordion-content").classList.remove("open");
    });
    searchMessage.style.display = "none";
  }

  function highlightText(el, query) {
    const regex = new RegExp(query, "gi");
    el.innerHTML = el.innerHTML.replace(regex, "<mark style='background:yellow;'>$&</mark>");
  }

  function searchAccordion() {
    const query = searchInput.value.trim();
    if (!query) {
      resetAccordion();
      searchMessage.innerText = "Ltfen bir arama terimi girin.";
      searchMessage.style.display = "block";
      searchMessage.style.color = "#d00";
      return;
    }

    clearHighlights();
    searchMessage.style.display = "none";

    let found = false;
    let foundCount = 0;
    let firstMatchElement = null;
    const regex = new RegExp(query, "gi");

    document.querySelectorAll(".accordion-item").forEach(item => {
      item.querySelector(".accordion-header").classList.remove("active");
      item.querySelector(".accordion-content").classList.remove("open");
    });

    document.querySelectorAll(".accordion-item").forEach(item => {
      const header = item.querySelector(".accordion-header");
      if (regex.test(header.innerText)) {
        header.classList.add("active");
        item.querySelector(".accordion-content").classList.add("open");
        highlightText(header, query);
        foundCount++;
        if (!found) firstMatchElement = header;
        found = true;
      }
    });

    document.querySelectorAll(".accordion-item").forEach(item => {
      const header = item.querySelector(".accordion-header");
      const content = item.querySelector(".accordion-content");
      content.querySelectorAll("strong").forEach(strong => {
        if (regex.test(strong.innerText)) {
          header.classList.add("active");
          content.classList.add("open");
          highlightText(strong, query);
          foundCount++;
          if (!found) firstMatchElement = strong;
          found = true;
        }
      });
    });

    document.querySelectorAll(".accordion-item").forEach(item => {
      const header = item.querySelector(".accordion-header");
      const content = item.querySelector(".accordion-content");
      content.querySelectorAll("p").forEach(p => {
        if (regex.test(p.innerText)) {
          header.classList.add("active");
          content.classList.add("open");
          highlightText(p, query);
          foundCount++;
          if (!found) firstMatchElement = p;
          found = true;
        }
      });
    });

    if (firstMatchElement) {
      firstMatchElement.scrollIntoView({behavior: "smooth", block: "start"});
    }

    if (found) {
      searchMessage.innerText = `Arama kriterlerinize uygun ${foundCount} eşleşme bulundu.`;
      searchMessage.style.display = "block";
      searchMessage.style.color = "#05462F";
    } else {
      searchMessage.innerText = "Aradıınız kriterlerde bir ierik bulunamad.";
      searchMessage.style.display = "block";
      searchMessage.style.color = "#d00";
    }
  }

  searchBtn.addEventListener("click", searchAccordion);
  searchInput.addEventListener("keypress", function(e) {
    if (e.key === "Enter") searchAccordion();
  });
  searchInput.addEventListener("input", function() {
    if (!this.value.trim()) resetAccordion();
  });
});
</script>
<!-- /Search box -->

</body>
</html>
