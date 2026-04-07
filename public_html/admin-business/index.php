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
	<meta name="author" content="Riwys">
	<title>Puandeks İşletme</title>
  
      <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

	<!-- Favicons-->
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" type="image/x-icon" href="https://puandeks.com/img/apple-touch-icon-57x57-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="https://puandeks.com/img/apple-touch-icon-72x72-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="114x114"
		href="img/apple-touch-icon-114x114-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="144x144"
		href="https://puandeks.com/img/apple-touch-icon-144x144-precomposed.png">

	<!-- GOOGLE WEB FONT -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

   <link rel="stylesheet" href="css/jquery.mmenu.all.css">

    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/vendors.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
    


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


</head>

<body>
<div id="page">

<?php include 'inc/header.php'; ?>


		<main>

			<!-- hero -->
			<section class="section_puandeks">
				<div class="container">
					<div class="row align-items-center">

						<div class="col-lg-6 col-md-12">
							<h2 class="trust_heading">
								Güvenilir Yorumlarla İşinizi Büyütün ve Markanıza Güven Katın
							</h2>
							<p class="trust_subtext">
								Doğrulanmış müşteri değerlendirmeleriyle markanızın güvenilirliğini artırın, satışlarınızı güçlendirin.
							</p>

							<!-- CTA Butonu -->
                             <?php  
                              $cta_link = ($role === 'business')
                                  ? 'https://business.puandeks.com/home'
                                  : 'https://business.puandeks.com/register';
                              ?>

                              <a href="<?= $cta_link ?>" class="btn_1 trust_btn">
                                  Ücretsiz bir hesap oluşturun
                              </a>

							
							<p class="trust_note">
								<i>Nasıl başlayacağınızı öğrenmek için bizimle <a href="https://puandeks.com/contact" target="_blank">İletişime geçin</a></i>
							</p>
						</div>

						
						<div class="col-lg-6 col-md-12 text-center">
							<img src="https://puandeks.com/img/banners/business-puandeks-banner-tr.webp" alt="Doğrulanm Yorumlar" class="img-fluid trust_image">
						</div>

					</div>
				</div>
			</section>
			<!-- /hero -->
          
          
          <section style="padding:60px 0; text-align:center; background:#f9f9f9;">
            <h2 style="font-size:22px; font-weight:600; margin-bottom:25px; color:#333;">
              Müşterilerin işletmeniz hakkında neler söylediğine bakın:
            </h2>

           <div id="domainChecker"
               style="display:flex; justify-content:center; align-items:center; max-width:500px; margin:0 auto;">

              <input 
                  type="text"
                  id="domainInput"
                  placeholder="Domain adı girin(Ör: example.com)"
                  style="flex:1; padding:14px 18px; border:2px solid #ddd; border-right:0;
                         border-radius:50px 0 0 50px; font-size:16px; outline:none;">

              <button id="domainButton"
                      style="padding:14px 28px; border:none; background:#000; color:#fff;
                             font-size:16px; font-weight:600; border-radius:0 50px 50px 0; cursor:pointer;">
                  Kontrol et
              </button>


          </div>


          </section>




			<!-- /Ornek bölüm -->
			<section class="trust_stats_section">
				<div class="container text-center">
					<h2 class="trust_stats_title">Tüketiciler neden Puandeks'e güvenecek?</h2>
					<div class="row">
						<div class="col-md-4">
							<h3 class="stat_number">Yeni Nesil Değerlendirme</h3>
							<p class="stat_text">
								Gelişmiş doğrulama süreçlerimiz sayesinde, kullanıcı deneyimlerini sahte yorumlara karşı
								korunan şeffaf bir ortamda sunuyoruz.
							</p>
						</div>
						<div class="col-md-4">
							<h3 class="stat_number">Şeffaf & Güvenilir</h3>
							<p class="stat_text">
								Tüm yorumlar, gerçek kullanıcılar tarafından yapıldığını doğrulayan sistemlerle kontrol
								edilir. Böylece hem işletmeler hem de tüketiciler için güvenilir bir ekosistem
								oluşturuyoruz.
							</p>
						</div>
						<div class="col-md-4">
							<h3 class="stat_number">Hızla Büyüyen Topluluk</h3>
							<p class="stat_text">
								Her geçen gün daha fazla kullancı Puandeks'e katılıyor ve
								gerçek deneyimlerini paylaşıyor.
							</p>
						</div>
					</div>
				</div>
			</section>
          
          

          <!-- 6 Cards  -->
          <section class="info_cards_section">
            <div class="container" style="overflow:hidden !important;">
              <div class="row g-3 justify-content-center" style="width:100% !important; margin:0 !important;">



                <!-- Card 1 -->
                <div class="col-md-4">
                  <div class="info_card info_card_1" style="background-color:#a8d4ff !important; color:#000 !important; padding:20px; border-radius:8px; text-align:center; height:250px !important; display:flex !important; flex-direction:column !important; justify-content:center !important;">
                    <div style="font-size:42px; margin-bottom:10px;">
                      <i class="fas fa-users"></i>
                    </div>
                    <h5 style="margin-bottom:10px;">Gerçek Müşteri Deneyimi</h5>
                    <p>
                      Puandeks, güvenilir yorumlarla müşterilerinizin zihninde güven inşa etmenizi sağlar.
                    </p>
                  </div>
                </div>

                <!-- Card 2 -->
                <div class="col-md-4">
                  <div class="info_card info_card_2" style="background-color:#f7e5d2 !important; color:#000 !important; padding:20px; border-radius:8px; text-align:center; height:250px !important; display:flex !important; flex-direction:column !important; justify-content:center !important;">
                    <div style="font-size:42px; margin-bottom:10px;">
                      <i class="fas fa-trophy"></i>
                    </div>
                    <h5 style="margin-bottom:10px;">Güçlü İtibar Yönetimi</h5>
                    <p>
                      Tüm geri bildirimleri tek platformda toplayarak marka itibarnızı koruyun ve yükseltin.
                    </p>
                  </div>
                </div>

                <!-- Card 3 -->
                <div class="col-md-4">
                  <div class="info_card info_card_3" style="background-color:#9ff5d4 !important; color:#000 !important; padding:20px; border-radius:8px; text-align:center; height:250px !important; display:flex !important; flex-direction:column !important; justify-content:center !important;">
                    <div style="font-size:42px; margin-bottom:10px;">
                      <i class="fas fa-rocket"></i>
                    </div>
                    <h5 style="margin-bottom:10px;">Rekabet Avantajı</h5>
                    <p>
                      Sektörünüzde öne çıkın; müşteri memnuniyetinizi artrarak rakiplerinize fark atın.
                    </p>
                  </div>
                </div>

                <!-- Card 4 -->
                <div class="col-md-4">
                  <div class="info_card info_card_4" style="background-color:#ffd1b0 !important; color:#000 !important; padding:20px; border-radius:8px; text-align:center; height:250px !important; display:flex !important; flex-direction:column !important; justify-content:center !important;">
                    <div style="font-size:42px; margin-bottom:10px;">
                      <i class="fas fa-chart-bar"></i>
                    </div>
                    <h5 style="margin-bottom:10px;">Sürekli Gelişim</h5>
                    <p>
                      Analiz raporlarıyla yeni stratejiler belirleyin, müşteri ihtiyaçlarına hızlı cevap verin.
                    </p>
                  </div>
                </div>

                <!-- Card 5 -->
                <div class="col-md-4">
                  <div class="info_card info_card_5" style="background-color:#fff6a8 !important; color:#000 !important; padding:20px; border-radius:8px; text-align:center; height:250px !important; display:flex !important; flex-direction:column !important; justify-content:center !important;">
                    <div style="font-size:42px; margin-bottom:10px;">
                      <i class="fas fa-handshake"></i>
                    </div>
                    <h5 style="margin-bottom:10px;">Bağlı Müşteri Topluluğu</h5>
                    <p>
                      Etkileşim odaklı yaklaşımımızla sadık ve mutlu bir müşteri kitlesi oluşturun.
                    </p>
                  </div>
                </div>

                <!-- Card 6 -->
                <div class="col-md-4">
                  <div class="info_card info_card_6" style="background-color:#a9cce3 !important; color:#000 !important; padding:20px; border-radius:8px; text-align:center; height:250px !important; display:flex !important; flex-direction:column !important; justify-content:center !important;">
                    <div style="font-size:42px; margin-bottom:10px;">
                      <i class="fas fa-medal"></i>
                    </div>
                    <h5 style="margin-bottom:10px;">Müşteri Güveni</h5>
                    <p>
                      Onaylı işletme rozetinizle güvenilirliğinizi artırın, müşterilerinizin gözünde marka değeriniz yükselsin.
                    </p>
                  </div>
                </div>

              </div>
            </div>
          </section>
          <!-- 6 Cards  -->

          

			<!-- 2 Boxes area -->
			<section class="two_col_section">
				<div class="container">
					<div class="row gy-4">

						<!-- Banner 1 -->
						<div class="col-md-6">
							<div class="two_col_card">
								<h3>Tercihlerin Başladığı Güven Noktası</h3>
								<p>
									Tüm tüketiciler ve işletmeler arasnda güvene dayalı bir alan oluşturmayı ve
									güvenilirliğin bir kanıtı olmayı hedefliyoruz. Tüketicilerin ilk tercih ettiği karar merkezi olmak istiyoruz.
								</p>
                                <?php  
                                  $start_link = ($role === 'business')
                                      ? 'https://business.puandeks.com/home'
                                      : 'https://business.puandeks.com/register';
                                  ?>

                                  <a href="<?= $start_link ?>" class="btn_rounded">
                                      Hemen başlayın
                                  </a>

							</div>
						</div>

						<!-- Banner 2 -->
						<div class="col-md-6">
							<div class="two_col_card">
								<h3>Ticaretteki güven endeksi</h3>
								<p>
									Kullanıcıların gerçek deneyimlerini paylaşabildiği ve işletmelerin şeffaflıkla
									değerlendirildiği bu alan, güvenilir bağlantılar kurarak hem tüketicilerin hem de
									işletmelerin kazanacağı bir ekosistem yaratmayı öncelik alıyor.
								</p>
								<a href="plans" class="btn_rounded">Planlarımızı inceleyin</a>
							</div>
						</div>

					</div>
				</div>
			</section>
          <!-- 2 Boxes area -->
          
          
          
            <!-- Brand Carousel -->
            <section style="padding:40px 0; background:#fff; text-align:center;">
              <div style="max-width:1200px; margin:0 auto; position:relative; overflow:hidden;">

                <!-- Fade efektleri -->
                <div style="position:absolute; top:0; right:0; width:100px; height:100%; background:linear-gradient(to left, #fff, transparent); z-index:2;"></div>

                <!-- Wrapper -->
                <div id="brandCarouselWrapper" style="width:100% !important; overflow:hidden !important; position:relative;">
                  <div id="brandCarouselContainer" style="display:flex; gap:60px;">

                    <div id="brandCarousel" style="display:flex; gap:60px; flex-shrink:0;">
                      <img src="https://puandeks.com/img/brands/amazon_logo.png" alt="Amazon" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/apple_logo.png" alt="Apple" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/arcelik_logo.png" alt="Arelik" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/beko_logo.png" alt="Beko" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/cocacola_logo.png" alt="CocaCola" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/google_logo.png" alt="Google" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/hepsiburada_logo.png" alt="Hepsiburada" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/lcw_logo.png" alt="LC Waikiki" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/microsoft_logo.png" alt="Microsoft" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/migros_logo.png" alt="Migros" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/netflix_logo.png" alt="Netflix" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/nike_logo.png" alt="Nike" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/samsung_logo.png" alt="Samsung" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/thy_logo.png" alt="THY" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/trendyol_logo.png" alt="Trendyol" style="height:40px;">
                      <img src="https://puandeks.com/img/brands/vestel_logo.png" alt="Vestel" style="height:40px;">
                    </div><!-- /brandCarousel -->

                  </div><!-- /brandCarouselContainer -->
                </div><!-- /brandCarouselWrapper -->

              </div>
            </section>
            <!-- Brand Carousel -->



<!-- Entegrasyonlar -->
        <section style="padding:60px 0; background:#fafafa; text-align:center;">
          <h2 style="font-size:22px; font-weight:600; margin-bottom:50px; color:#111;">
            Mevcut araçlarınızla kolayca entegre oluyoruz
          </h2>

          <!-- Grid container -->
          <div id="integrationsGrid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:30px; max-width:900px; margin:0 auto;">

            <div style="border:1px solid #A8A8A8; border-radius:12px; padding:30px; display:flex; justify-content:center; align-items:center; background:#fff;">
              <img src="https://puandeks.com/img/brands/widget-brands/woocommerce-logo.svg" alt="WordPress" style="height:70px;">
            </div>

            <div style="border:1px solid #A8A8A8; border-radius:12px; padding:30px; display:flex; justify-content:center; align-items:center; background:#fff;">
              <img src="https://puandeks.com/img/brands/widget-brands/shopify-logo.svg" alt="Shopify" style="height:70px;">
            </div>

            <div style="border:1px solid #A8A8A8; border-radius:12px; padding:30px; display:flex; justify-content:center; align-items:center; background:#fff; overflow:hidden;">
            <img src="https://puandeks.com/img/brands/widget-brands/ikas-logo.svg" alt="WooCommerce" style="height:60px; max-width:100%; object-fit:contain;">
          </div>


            <div style="border:1px solid #A8A8A8; border-radius:12px; padding:30px; display:flex; justify-content:center; align-items:center; background:#fff;">
              <img src="https://puandeks.com/img/brands/widget-brands/ideasoft-logo.svg" alt="Ticimax" style="height:40px;">
            </div>

          </div>

          <!-- Buton -->
            <?php  
            $role = $_SESSION['role'] ?? null;
            $integrations_link = ($role === 'business')
                ? 'https://business.puandeks.com/integrations'
                : 'https://business.puandeks.com/register';
            ?>

            <div style="margin-top:50px;">
                <a href="<?= $integrations_link ?>" 
                   style="padding:14px 30px; border-radius:50px; border:1px solid #000; 
                          background:#fff; color:#000; font-size:15px; text-decoration:none; 
                          font-weight:600; display:inline-block;">
                    Tüm entegrasyonları görün
                </a>
            </div>
          <!-- Buton -->

        </section>
<!-- Entegrasyonlar -->


			  

			<!-- Last section -->

      <section class="cta_section" style="background:#ffffff !important;">
        <div class="container text-center">
          
          <!-- Head-->
          <h2 class="cta_title" style="color:#1C1C1C !important;">
            Yorumların tüm potansiyelini ortaya çıkarmaya hazır mısınız?
          </h2>
          
          <!-- Buton -->
          <a href="https://puandeks.com/contact" target="_blank" class="btn_rounded cta_button">
            Bizimle İletişime Geçin
          </a>
          
          <!-- Alt link (fiyatlar) -->
          <p class="cta_link">
            <a href="plans" style="color:#1C1C1C !important;">
              Fiyatlarımızı şimdi inceleyin
            </a>
          </p>

        </div>
      </section>
			

			
			

		</main>
		<!-- /main -->



<!-- Domain Not Found Popup -->
<div id="domainPopup" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.45); z-index:9999; justify-content:center; align-items:center;">

    <div style="background:#fff; width:90%; max-width:330px; padding:20px;
                border-radius:10px; text-align:center;">

        <p id="domainPopupText" style="font-size:16px; color:#333; margin-bottom:25px;">
            İşletmeniz Puandeks’te kayıtlı değil. Kayıt etmek ister misiniz?
        </p>

        <div style="display:flex; justify-content:center; gap:12px;">
            <button id="domainYes"
                    style="padding:8px 16px; background:#000; color:#fff; border:none; 
                           border-radius:6px; cursor:pointer; font-size:14px;">
                Evet
            </button>

            <button id="domainNo"
                    style="padding:8px 16px; background:#eee; color:#1c1c1c; border:1px solid #ccc; 
                           border-radius:6px; cursor:pointer; font-size:14px;">
                Hayır
            </button>
        </div>
    </div>

</div>
<!-- Domain Not Found Popup -->


<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	
	
</div>
<!-- page -->


<!-- COMMON SCRIPTS -->
<script src="js/jquery.min.js"></script>
<script src="js/jquery.mmenu.all.js"></script>
<script src="js/common_scripts.js"></script>
<script src="js/functions.js"></script>


<!-- brand-carousel-js -->
<script>
  const carousel = document.getElementById('brandCarousel');
  const container = document.getElementById('brandCarouselContainer');

  const clone = carousel.cloneNode(true);
  container.appendChild(clone);

  let scrollAmount = 0;
  const speed = 0.5;
  let paused = false;

  function animate() {
    if (!paused) {
      scrollAmount -= speed;
      container.style.transform = `translateX(${scrollAmount}px)`;
      if (Math.abs(scrollAmount) >= carousel.scrollWidth) {
        scrollAmount = 0;
      }
    }
    requestAnimationFrame(animate);
  }

  animate();

  container.addEventListener('mouseover', () => paused = true);
  container.addEventListener('mouseout', () => paused = false);
  container.addEventListener('touchstart', () => paused = true);
  container.addEventListener('touchend', () => paused = false);
</script>
<!-- brand-carousel-js -->


<!-- Check Company -->
<script>
// GLOBAL LISTENERS
document.addEventListener("click", checkDomainHandler);
document.addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        let btn = document.getElementById("domainButton");
        if (btn) btn.click();
    }
});

// SIMPLE POPUP ELEMENTS
const dp = document.getElementById("domainPopup");
const dpText = document.getElementById("domainPopupText");
const dpYes = document.getElementById("domainYes");
const dpNo = document.getElementById("domainNo");

// DOMAIN CHECK HANDLER
function checkDomainHandler(e) {
    let btn = e.target.closest("#domainButton");
    if (!btn) return;

    let input = document.getElementById("domainInput");
    let raw = input.value.trim();
    if (!raw) {
        alert("Lütfen bir domain girin.");
        return;
    }

    raw = raw.replace("http://","")
             .replace("https://","")
             .replace("www.","");

    let domain = raw.split("/")[0];

    fetch("https://business.puandeks.com/api/check-company.php?domain=" + domain)
    .then(r => r.json())
    .then(d => {
        if (d.exists) {
            window.location.href = "https://puandeks.com/company/" + d.slug;
        } else {
            dp.style.display = "flex";

            dpYes.onclick = () => {
                window.location.href = "https://business.puandeks.com/register?domain=" + domain;
            };

            dpNo.onclick = () => {
                dp.style.display = "none";
            };
        }
    })
    .catch(err => alert("Bir hata oluştu: " + err));
}
</script>
<!-- Check Company -->


</body>
</html>