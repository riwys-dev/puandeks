<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
  session_start();
}

if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
  $_SESSION['user_id'] = $_COOKIE['user_id'];
}

if (isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
  require_once('/home/puandeks.com/backend/config.php');

  // 1. Tüketici mi kontrol et
  $stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['profile_photo'] = '';
  } else {
    // 2. İşletme mi kontrol et
    $stmt = $pdo->prepare("SELECT name, email FROM companies WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($company) {
      $_SESSION['role'] = 'business';
      $_SESSION['name'] = $company['name'];
      $_SESSION['email'] = $company['email'];
      $_SESSION['profile_photo'] = '';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<title>Hakkımızda - Puandeks</title>
  
    <!-- Favicons-->
    <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/apple-touch-icon-114x114-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/apple-touch-icon-144x144-precomposed.png">

	<!-- BASE CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/vendors.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Page CSS -->
<style>
  #about-hero {padding:140px 0 80px 0 !important;}
  #about-hero h1 {font-size:28px !important; font-weight:700 !important; margin-bottom:20px !important; color:#1C1C1C !important;}
 #about-hero h2 {font-size:22px !important; font-weight:500 !important; margin-bottom:20px !important; color:#1C1C1C !important;} 
  #about-hero p {font-size:16px !important; line-height:1.6 !important; margin-bottom:30px !important; color:#555 !important;}
  #about-hero img {max-width:100% !important; height:auto !important; border-radius:12px !important;}
  
  /* Buton renkleri */
  #about-hero .hero-btn {
    display:inline-block !important;
    background:#9FF6D3 !important;
    color:#1C1C1C !important;
    padding:12px 28px !important;
    border-radius:30px !important;
    font-weight:600 !important;
    text-decoration:none !important;
    transition:all .3s ease !important;
  }
  #about-hero .hero-btn:hover {
    background:#05462F !important;
    color:#FFFFFF !important;
  }

@media(max-width: 991px){
  #about-hero .row {flex-direction: column !important;} /* Satır yön dikey */
  #about-hero .col-lg-6 {flex:0 0 100% !important; max-width:100% !important; text-align:center !important;}
  #about-hero h1 {font-size:26px !important;}
  #about-hero p {font-size:16px !important;}
  #about-hero .hero-btn {margin-bottom:30px !important;} /* Buton ile görsel arası boşluk */
}

/* Vizyon & Misyon Section */
#about-vision {padding:100px 0 !important; background:#FCFBF3 !important;}
#about-vision h2 {font-size:28px !important; font-weight:700 !important; color:#1C1C1C !important; margin-bottom:30px !important;}
#about-vision h3 {font-size:22px !important; font-weight:600 !important; margin-bottom:15px !important; color:#05462F !important;}
#about-vision p {font-size:16px !important; line-height:1.6 !important; color:#555 !important; margin-bottom:30px !important;}
#about-vision img {max-width:100% !important; height:auto !important; border-radius:12px !important;}

@media(max-width: 991px){
  #about-vision .col-lg-6 {flex:0 0 100% !important; max-width:100% !important; text-align:center !important;}
  #about-vision h2 {font-size:24px !important;}
  #about-vision h3 {font-size:20px !important;}
  #about-vision p {font-size:15px !important;}
}

/* Neden Puandeks Section */
#about-why {padding:100px 0 !important; background:#FCFBF3 !important;}
#about-why h2 {font-size:28px !important; font-weight:700 !important; color:#1C1C1C !important; margin-bottom:50px !important;}
#about-why h3 {font-size:20px !important; font-weight:600 !important; margin-top:15px !important; margin-bottom:15px !important; color:#1C1C1C !important;}
#about-why p {font-size:16px !important; line-height:1.6 !important; color:#555 !important; margin-bottom:20px !important;}
#about-why i {font-size:36px !important; margin-bottom:10px !important; display:block !important;}

#about-why .icon-yorum {color:#04DA8D !important;}
#about-why .icon-google {color:#DB4437 !important;}
#about-why .icon-widget {color:#42A4FD !important;}
#about-why .icon-sikayet {color:#FC7919 !important;}
#about-why .icon-seo {color:#FAE108 !important;}

@media(max-width: 991px){
  #about-why h2 {font-size:24px !important; margin-bottom:30px !important;}
  #about-why h3 {font-size:18px !important;}
  #about-why p {font-size:15px !important;}
}


/* Degerlerimiz Section */
#about-values {padding:100px 0 !important; background:#FFFFFF !important;}
#about-values i {font-size:40px !important; color:#1C1C1C !important; margin-bottom:15px !important; display:block !important;}
#about-values h3 {font-size:20px !important; font-weight:600 !important; margin-bottom:15px !important; color:#1C1C1C !important;}
#about-values p {font-size:16px !important; line-height:1.6 !important; color:#555 !important; margin:0 auto 20px auto !important; max-width:400px !important;}

@media(max-width: 991px){
  #about-values h3 {font-size:18px !important;}
  #about-values p {font-size:15px !important;}
}

/* Kimler Kullanir Section */
#about-users {padding:100px 0 !important; background:#04DA8D !important;}
#about-users h2 {font-size:28px !important; font-weight:700 !important; color:#1C1C1C !important; margin-bottom:30px !important;}
#about-users h3 {font-size:20px !important; font-weight:600 !important; color:#1C1C1C !important; margin-bottom:10px !important;}
#about-users p {font-size:16px !important; line-height:1.6 !important; color:#1C1C1C !important; margin-bottom:20px !important;}
#about-users img {max-width:100% !important; height:auto !important; border-radius:12px !important;}

@media(max-width: 991px){
  #about-users h2 {font-size:24px !important;}
  #about-users h3 {font-size:18px !important;}
  #about-users p {font-size:15px !important;}
  #about-users .col-lg-6 {text-align:center !important;}
}

/* Teknoloji ve Guven Section */
#about-tech-trust {padding:100px 0 !important; background:#FFFFFF !important;}
#about-tech-trust h2 {font-size:26px !important; font-weight:700 !important; color:#1C1C1C !important; margin:20px 0 !important;}
#about-tech-trust p {font-size:16px !important; line-height:1.6 !important; color:#1C1C1C !important; margin-bottom:20px !important;}
#about-tech-trust ul {list-style-type: disc !important; padding-left:20px !important; font-size:16px !important; line-height:1.8 !important; color:#1C1C1C !important;}
#about-tech-trust ul li {margin-bottom:10px !important;}
#about-tech-trust i {font-size:50px !important; color:#1C1C1C !important; margin-bottom:15px !important; display:block !important;}
#about-tech-trust .row {gap:40px !important;}
#about-tech-trust .col-lg-5,
#about-tech-trust .col-lg-6 {text-align:left !important;}

@media(max-width: 991px){
  #about-tech-trust .col-lg-5,
  #about-tech-trust .col-lg-6 {flex:0 0 100% !important; max-width:100% !important; text-align:center !important; margin-bottom:40px !important;}
  #about-tech-trust ul {text-align:left !important; margin:0 auto !important; max-width:90% !important;}
}

/* Platformumuzun Korunması Section */
#about-protection {padding:100px 0 !important; background:#FCFBF3 !important;}
#about-protection h2 {font-size:28px !important; font-weight:700 !important; color:#1C1C1C !important; margin-bottom:20px !important; text-align:center !important;}
#about-protection p.section-subtitle {font-size:16px !important; line-height:1.6 !important; color:#555 !important; margin-bottom:50px !important; text-align:center !important;}
#about-protection h3 {font-size:20px !important; font-weight:600 !important; color:#1C1C1C !important; margin-bottom:15px !important;}
#about-protection p {font-size:16px !important; line-height:1.6 !important; color:#555 !important; margin-bottom:20px !important;}
#about-protection img {width:100px !important; height:100px !important; object-fit:cover !important; border-radius:12px !important; margin-bottom:20px !important;}

/* Kutuların hizalaması */
#about-protection .features-row {
  display:flex !important;
  margin: 20px 0 !important;
  justify-content:center !important;
  align-items:flex-start !important;
  gap:40px !important; /* kutular arası boşluk */
  flex-wrap:wrap !important; /* mobilde alta insin */
}
#about-protection .feature-box {
  max-width:280px !important;
  text-align:center !important;
}

@media(max-width: 991px){
  #about-protection img {width:80px !important; height:80px !important;}
  #about-protection h2 {font-size:24px !important;}
  #about-protection h3 {font-size:18px !important;}
  #about-protection p {font-size:15px !important;}
  #about-protection .features-row {gap:20px !important;}
}

/* Help Section */
#about-helpcontact {padding:80px 0 !important; background:#FFFFFF !important;}
#about-helpcontact .help-box {border:1px solid #A8A8A8 !important; border-radius:12px !important; padding:30px !important; height:100% !important; display:flex !important; flex-direction:column !important; justify-content:center !important; text-align:center !important;}
#about-helpcontact h3 {font-size:22px !important; font-weight:700 !important; margin-bottom:15px !important; color:#1C1C1C !important;}
#about-helpcontact p {font-size:16px !important; line-height:1.6 !important; color:#555 !important; margin-bottom:20px !important;}
#about-helpcontact .help-btn {display:inline-block !important; background:#9FF6D3 !important; color:#1C1C1C !important; padding:12px 28px !important; border-radius:30px !important; font-weight:600 !important; text-decoration:none !important; transition:all .3s ease !important; border:none !important;}
#about-helpcontact .help-btn:hover {background:#05462F !important; color:#FFFFFF !important;}





</style>
<!-- Page CSS -->

</head>

<body>
<div id="page">

<!-- header -->
<?php include 'header-main.php'; ?>
<!-- /header -->

<main>

  <!-- Hero Section -->
  <section id="about-hero">
    <div class="container">
      <div class="row d-flex align-items-center">
        
      <!-- Left Content -->
      <div class="col-lg-6 col-md-12">
        <h1>Gerçek deneyimler ile doğru kararlar alın!</h1>
        <h2>Türkiye'nin ilk ve tek şeffaf ve bağımsız değerlendirme platformu.</h2>

        <p style="margin-top:30px !important; font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Türkiye'nin önde gelen bağımsız değerlendirme ve müşteri geri bildirim platformu Puandeks, 2024 yılında 
          <strong>Puandeks Bilişim ve Değerlendirme Hizmetleri Limited Şirketi</strong> çatısı altında kurulmuştur. 
          Amacımız, tüketiciler ile işletmeler arasında güvene dayalı, şeffaf ve sürdürülebilir bir ilişki kurmaktır. 
          Gerçek kullanıc deneyimlerini merkeze alarak, hem tüketicilerin daha bilinçli kararlar almasını hem de işletmelerin 
          müşteri memnuniyetini artırmasın sağlıyoruz.
        </p>
        
        <a href="https://www.puandeks.com" class="hero-btn">
          Platformumuzu Keşfedin
        </a>
        
      </div>

        <!-- Right Image -->
        <div class="col-lg-6 col-md-12 text-center">
          <img src="img/banners/about-hero.webp" alt="Puandeks Hakkımızda">
        </div>

      </div>
    </div>
  </section>
  <!-- Hero Section -->

  
  
<!-- Vizyon & Misyon Section -->
<section id="about-vision" style="background:#FCFBF3 !important; padding:100px 0 !important;">
  <div class="container">
    <div class="row d-flex align-items-center">
      
      <!-- Left Image -->
      <div class="col-lg-6 col-md-12 text-center">
        <img src="img/banners/about-vision.webp" alt="Puandeks Vizyon & Misyon" 
             style="max-width:100% !important; height:auto !important; border-radius:12px !important;">
      </div>

      <!-- Right Content -->
      <div class="col-lg-6 col-md-12" style="padding:20px !important;">
        <h2 style="font-size:28px !important; font-weight:700 !important; color:#1C1C1C !important; margin-bottom:30px !important;">
          Geleceğe Bakışımız
        </h2>

        <h3 style="font-size:22px !important; font-weight:600 !important; margin-bottom:15px !important; color:#05462F !important;">
          Vizyonumuz
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important; margin-bottom:30px !important;">
          Türkiye’de güven odaklı dijital ticaretin merkezi olmak ve herkes için daha şeffaf, adil ve güvenilir bir alıveriş ekosistemi oluşturmaktr.
        </p>

        <h3 style="font-size:22px !important; font-weight:600 !important; margin-bottom:15px !important; color:#05462F !important;">
          Misyonumuz
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Tüketicilere, satın alma kararlarında rehberlik edecek gerçek ve doğrulanmş yorumlar sunmak; işletmelere ise müşteri
          geri bildirimlerini etkin bir şekilde yönetebilecekleri, itibarlarını güçlendirebilecekleri ve dijital varlklarını
          büyütebilecekleri araçlar sağlamak.
        </p>
      </div>

    </div>
  </div>
</section>
<!-- Vizyon & Misyon Section -->

  
  
<!-- Neden Puandeks Section -->
<section id="about-why" style="background:#FCFBF3 !important; padding:100px 0 !important;">
  <div class="container">
    
    <!-- Section Title -->
    <div class="row">
      <div class="col-12 text-center">
        <h2 style="font-size:28px !important; font-weight:700 !important; color:#1C1C1C !important; margin-bottom:50px !important;">
          Neden Puandeks?
        </h2>
      </div>
    </div>

    <!-- Feature Boxes -->
    <div class="row">
      
      <!-- Box 1 -->
      <div class="col-lg-6 col-md-12 mb-4 text-center">
        <i class="fa-solid fa-circle-check" style="font-size:36px; color:#04DA8D;"></i>
        <h3 style="font-size:20px !important; font-weight:600 !important; margin-top:15px !important; color:#1C1C1C !important;">
          Gerçek ve Doğrulanmış Yorumlar
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Tm yorumlar gerçek satın alma deneyimlerine dayanır, sahte ierikler filtrelenir.
        </p>
      </div>

      <!-- Box 2 -->
      <div class="col-lg-6 col-md-12 mb-4 text-center">
        <i class="fa-brands fa-google" style="font-size:36px; color:#DB4437;"></i>
        <h3 style="font-size:20px !important; font-weight:600 !important; margin-top:15px !important; color:#1C1C1C !important;">
          Google İş Birliği
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Markanız arama sonuçlarında üst sıralarda görnür, yıldızlı snippet desteğiyle öne çıkar.
        </p>
      </div>

      <!-- Box 3 -->
      <div class="col-lg-6 col-md-12 mb-4 text-center">
        <i class="fa-solid fa-plug" style="font-size:36px; color:#42A4FD;"></i>
        <h3 style="font-size:20px !important; font-weight:600 !important; margin-top:15px !important; color:#1C1C1C !important;">
          Widget & API Entegrasyonları
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          WordPress, Shopify ve diğer e-ticaret altyapılarıyla kolayca entegre edin.
        </p>
      </div>

      <!-- Box 4 -->
      <div class="col-lg-6 col-md-12 mb-4 text-center">
        <i class="fa-solid fa-comments" style="font-size:36px; color:#FC7919;"></i>
        <h3 style="font-size:20px !important; font-weight:600 !important; margin-top:15px !important; color:#1C1C1C !important;">
          Şikayet ve Memnuniyet Yönetimi
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Müşteri şikayetlerini ve olumlu geri bildirimleri tek panelden yönetin.
        </p>
      </div>

      <!-- Box 5 -->
      <div class="col-lg-12 col-md-12 mb-4 text-center">
        <i class="fa-solid fa-chart-line" style="font-size:36px; color:#FAE108;"></i>
        <h3 style="font-size:20px !important; font-weight:600 !important; margin-top:15px !important; color:#1C1C1C !important;">
          SEO & Trafik Artırıcı Çözümler
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Organik arama trafiğinizi artırın, markanız “güvenilir mi?” aramalarnda öne çıkarın.
        </p>
      </div>

    </div>
  </div>
</section>
<!-- Neden Puandeks Section -->


<!-- Değerlerimiz Section -->
<section id="about-values" style="background:#FFFFFF !important; padding:100px 0 !important;">
  <div class="container">

    <!-- Section Title -->
    <div class="row">
      <div class="col-12 text-center">
        <h2 style="font-size:28px !important; font-weight:700 !important; color:#1C1C1C !important; margin-bottom:50px !important;">
          Değerlerimiz
        </h2>
      </div>
    </div>

    <!-- Values Grid -->
    <div class="row text-center">
      
      <!-- Box 1 -->
      <div class="col-lg-6 col-md-6 mb-5">
        <i class="fa-solid fa-shield-halved" style="font-size:40px; color:#1C1C1C;"></i>
        <h3 style="font-size:20px !important; font-weight:600 !important; margin-top:15px !important; margin-bottom:15px !important; color:#1C1C1C !important;">
          Güven ve Şeffaflk
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Tüketici yorumlarını olduğu gibi yansıtıyor, manipülasyona izin vermiyoruz.
        </p>
      </div>

      <!-- Box 2 -->
      <div class="col-lg-6 col-md-6 mb-5">
        <i class="fa-solid fa-microchip" style="font-size:40px; color:#1C1C1C;"></i>
        <h3 style="font-size:20px !important; font-weight:600 !important; margin-top:15px !important; margin-bottom:15px !important; color:#1C1C1C !important;">
          İnovasyon ve Teknoloji
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Yapay zeka destekli moderasyon, anlık bildirimler ve kullanıcı dostu arayüzlerle sürekli iyileştirme yapıyoruz.
        </p>
      </div>

      <!-- Box 3 -->
      <div class="col-lg-6 col-md-6 mb-5">
        <i class="fa-solid fa-people-group" style="font-size:40px; color:#1C1C1C;"></i>
        <h3 style="font-size:20px !important; font-weight:600 !important; margin-top:15px !important; margin-bottom:15px !important; color:#1C1C1C !important;">
          Topluluk ve İş Birliği
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Kullanıcılarımızın geri bildirimleriyle büyüyor, iletmelerle el ele vererek sağlıklı bir ticaret ortamı yaratıyoruz.
        </p>
      </div>

      <!-- Box 4 -->
      <div class="col-lg-6 col-md-6 mb-5">
        <i class="fa-solid fa-globe" style="font-size:40px; color:#1C1C1C;"></i>
        <h3 style="font-size:20px !important; font-weight:600 !important; margin-top:15px !important; margin-bottom:15px !important; color:#1C1C1C !important;">
          Yerellik ve Küresellik
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important;">
          Türkiye pazarının ihtiyaçlarına odaklanırken, global standartlarda hizmet sunuyoruz.
        </p>
      </div>

    </div>
  </div>
</section>
<!-- Değerlerimiz Section -->  

<!-- Kimler Kullanır Section -->
<section id="about-users" style="background:#04DA8D !important; padding:100px 0 !important;">
  <div class="container">
    <div class="row d-flex align-items-center">

      <!-- Left Image -->
      <div class="col-lg-6 col-md-12 text-center mb-4 mb-lg-0">
        <img src="img/banners/about-users.webp" alt="Kimler Kullanır Görseli" 
             style="max-width:100% !important; height:auto !important; border-radius:12px !important;">
      </div>

      <!-- Right Content -->
      <div class="col-lg-6 col-md-12" style="color:#FFFFFF !important;">
        <h2 style="font-size:28px !important; font-weight:700 !important; margin-bottom:30px !important;">
          Kimler Kullanır?
        </h2>

        <h3 style="font-size:20px !important; font-weight:600 !important; margin-bottom:10px !important;">
          Tüketiciler
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; margin-bottom:20px !important;">
          Ürün veya hizmet satın almadan önce gerek kullanıcı deneyimlerini okuyarak karar verir.
        </p>

        <h3 style="font-size:20px !important; font-weight:600 !important; margin-bottom:10px !important;">
          E-Ticaret Siteleri ve Fiziksel Mağazalar
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important; margin-bottom:20px !important;">
          Müşteri geri bildirimlerini yönetir, itibarını güçlendirir ve satışlarını artırır.
        </p>

        <h3 style="font-size:20px !important; font-weight:600 !important; margin-bottom:10px !important;">
          KOBİ’ler ve Büyük Ölekli Şirketler
        </h3>
        <p style="font-size:16px !important; line-height:1.6 !important;">
          Dijital pazarlama stratejilerini güçlendirmek ve müşteri sadakati oluşturmak için Puandeks’i tercih eder.
        </p>
      </div>

    </div>
  </div>
</section>
<!-- Kimler Kullanır Section -->

<!-- Teknoloji ve Güven Section -->
<section id="about-tech-trust" style="background:#FFFFFF !important; padding:100px 0 !important;">
  <div class="container">
    <div class="row d-flex align-items-start justify-content-between">
      
      <!-- Left Column -->
      <div class="col-lg-5 col-md-12 mb-4">
        <i class="fa-solid fa-plug"></i>
        <h2>Teknolojik Altyapı ve Entegrasyonlar</h2>
        <p>
          Puandeks; WooCommerce, Shopify, Magento, OpenCart  gibi popüler e-ticaret sistemleriyle tam uyumludur. 
          Ayrıca REST API desteği sayesinde işletmeler kendi yazılım ve CRM sistemleriyle entegre çalışabilir. 
          Gelişmiş dashboard özellikleriyle, yorum istatistiklerini, müşteri memnuniyet oranlarnı ve şikayet çözüm sürelerini anlık takip edebilirsiniz.
        </p>
      </div>

      <!-- Right Column -->
      <div class="col-lg-6 col-md-12">
        <i class="fa-solid fa-shield-halved"></i>
        <h2>Güven İlkelerimiz</h2>
        <p>Puandeks olarak, operasyonlarmızda bize rehberlik eden beş temel ilkemiz vardır:</p>
        <ul>
          <li><strong>Doğallık:</strong> Bağımsız ve tarafsız bir platformuz.</li>
          <li><strong>Açıklık:</strong> Tüketiciler özgürce deneyimlerini paylaşabilir, sisteme kayıtlı işletmeler inceleme daveti gönderebilir.</li>
          <li><strong>Adalet:</strong> Kurallarımız herkes için eşit ve şeffaf şekilde uygulanır.</li>
          <li><strong>Şeffaflık:</strong> Ne yaptığımız ve neden yaptığımız konusunda netiz.</li>
          <li><strong>Alakalı Olmak:</strong> Kullanıcılarımız için sürekli değer yaratırız.</li>
        </ul>
      </div>

    </div>
  </div>
</section>
<!-- Teknoloji ve Güven Section -->

<!-- Platformumuzun Korunması Section -->
<section id="about-protection">
  <div class="container">
    
    <!-- Section Title -->
    <div class="row">
      <div class="col-12 text-center">
        <h2>Platformumuzun Korunması</h2>
        <p class="section-subtitle">
          Puandeks, platform bütünlüğünü korumak için teknoloji, topluluk ve insan odaklı bir yaklaşım benimser
        </p>
      </div>
    </div>

    <!-- Three Columns -->
    <div class="features-row">
      
      <!-- Teknoloji -->
      <div class="feature-box">
        <img src="img/banners/tech.webp" alt="Teknoloji">
        <h3>Teknoloji</h3>
        <p>Otomatik sahte tespit sistemleri ve yapay zeka destekli moderasyon.</p>
      </div>

      <!-- Topluluk -->
      <div class="feature-box">
        <img src="img/banners/community.webp" alt="Topluluk">
        <h3>Topluluk</h3>
        <p>Kullanıcılar ve işletmeler şüpheli içeriği işaretleyebilir.</p>
      </div>

      <!-- İnsanlar -->
      <div class="feature-box">
        <img src="img/banners/people.webp" alt="İnsanlar">
        <h3>İnsanlar</h3>
        <p>İçerik bütünlüğü ekiplerimiz hassasiyetle yorumları inceler ve özüm sunar.</p>
      </div>

    </div>

  </div>
</section>
<!-- Platformumuzun Korunması Section -->

<!-- Help & Contact Section -->
<section id="about-helpcontact" style="background:#FFFFFF !important; padding:80px 0 !important;">
  <div class="container">
    <div class="row justify-content-center">

      <!-- Box 1 -->
      <div class="col-lg-5 col-md-6 mb-4">
        <div class="help-box text-center">
          <h3 style="font-size:22px !important; font-weight:700 !important; color:#1C1C1C !important; margin-bottom:15px !important;">
            Yardıma mı ihtiyacınız var?
          </h3>
          <p style="font-size:16px !important; line-height:1.6 !important; color:#555 !important; margin-bottom:20px !important;">
            Platformumuzun nasıl çalıştığını ve yorumların nasıl toplandığnı öğrenmek için:
          </p>
          <button class="help-btn" onclick="window.location.href='https://business.puandeks.com/help'">Bizi Keşfedin</button>
        </div>
      </div>

      <!-- Box 2 -->
      <div class="col-lg-5 col-md-6 mb-4">
        <div class="help-box text-center">
          <h3 style="font-size:22px !important; font-weight:700 !important; color:#1C1C1C !important; margin-bottom:15px !important;">
            Ekibimize sormak istediğiniz bir soru mu var?
          </h3>
          <button class="help-btn" onclick="window.location.href='contact'">Bize Ulaşın</button>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- Help & Contact Section -->






  
</main>
<!--/main-->

<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	

</div>
<!-- page -->

<!-- COMMON SCRIPTS -->
<script src="js/common_scripts.js"></script>
<script src="js/functions.js"></script>
<script src="assets/validate.js"></script>

</body>
</html>
