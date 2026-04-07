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
    <meta name="author" content="Riwys">
    <title>Sık Sorulan Sorular - Puandeks </title>

    <!-- Favicons-->
   <link rel="icon" href="https://puandeks.com/img/favicons/favicon.png">

    <!-- Favicons-->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/apple-touch-icon-144x144-precomposed.png">

    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	<link href="css/vendors.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">
  
     <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
		
	<div id="page" class="theia-exception">
		
<!-- header -->
<?php include 'header-main.php'; ?>
<!-- /header -->
	
	<main>
        <section class="hero_single general">
            <div class="wrapper">
                <div class="container">
                    <i class="pe-7s-help1"></i>
                    <h1>Sıkça Sorulan Sorular</h1>
                    <p>Puandeks, doğrulanmış müşteri yorumlarını kullanarak işinizi büyütmenize yardımcı olur</p>
                </div>
            </div>
        </section>
        <!-- /hero_single -->
 
<!-- Aside -->       
         <div class="container margin_60_35">
			<div class="row">
				<aside class="col-lg-3" id="faq_cat">
						<div class="box_style_cat" id="faq_box">
							<ul id="cat_nav">								
								<li><a href="#aboutpuandeks"><i class="fa-solid fa-circle-info"></i> Puandeks nedir ve ne işe yarar?</a></li>
                                <li><a href="#becameauser"><i class="fa-solid fa-user-plus"></i> Puandeks’e nasıl üye olabilirim?</a></li>
                                <li><a href="#payments"><i class="fa-solid fa-credit-card"></i> Ödeme yöntemleri nelerdir?</a></li>
                                <li><a href="#subscription-start"><i class="fa-solid fa-calendar-check"></i> Abonelik sürecim ne zaman başlar?</a></li>
                                <li><a href="#subscription-cancel"><i class="fa-solid fa-ban"></i> Aboneliğimi nasıl iptal edebilirim?</a></li>
                                <li><a href="#card-security"><i class="fa-solid fa-lock"></i> Kart bilgilerim nasıl saklanır?</a></li>
                                <li><a href="#google-reviews"><i class="fa-brands fa-google"></i> Yorumlar Google’da nasıl görünür?</a></li>
                                <li><a href="#fake-review"><i class="fa-solid fa-triangle-exclamation"></i> Sahte bir yorum aldım, ne yapmalıyım?</a></li>
                                <li><a href="#complaints"><i class="fa-solid fa-comments"></i> Tüketici şikayetlerini nasıl yönetebilirim?</a></li>
                                <li><a href="#collect-faster"><i class="fa-solid fa-bolt"></i> Yorum toplamayı nasıl hızlandırabilirim?</a></li>
                                <li><a href="#languages"><i class="fa-solid fa-language"></i> Hangi diller ve para birimleri destekleniyor?</a></li>
                                <li><a href="#seo"><i class="fa-solid fa-magnifying-glass-chart"></i> Puandeks ile SEO’yu nasıl güçlendirebilirim?</a></li>
                                <li><a href="#trial"><i class="fa-solid fa-hourglass-half"></i> Deneme süresinde ödeme yapmam gerekir mi?</a></li>
                                <li><a href="#prove-review"><i class="fa-solid fa-shield"></i> Yorumun sahte olmadığını nasıl kanıtlarım?</a></li>
                                <li><a href="#integrations"><i class="fa-solid fa-plug"></i> Hangi entegrasyonlar mevcut?</a></li>
                                <li><a href="#mobile-view"><i class="fa-solid fa-mobile-screen-button"></i> Mobil cihazlarda yorumlar nasıl görünür?</a></li>
                                <li><a href="#support"><i class="fa-solid fa-headset"></i> Destek ekibine nasıl ulaşabilirim?</a></li>
                                <li><a href="#ai-reviews"><i class="fa-solid fa-robot"></i> Puandeks’in genel yorum yapay zeka sistemi nasıl çalışır?</a></li>
                                <li><a href="#ai-benefits"><i class="fa-solid fa-lightbulb"></i> Yapay zekanın işletmeme sağladığı avantajlar nelerdir?</a></li>

							</ul>
						</div>
               </aside>
<!-- Aside -->
              

<!-- Right Area -->
				<div class="col-lg-9" id="faq">
                    
				<!-- Card -->
				<div class="col-lg-9" id="faq">
					<div role="tablist" id="aboutpuandeks">
                      <div class="card">
                        <div class="card-body">
                          <h4 class="nomargin_top">Puandeks nedir ve ne işe yarar?</h4>
									<p>
                                      Puandeks, işletmelerin müşteri yorumlarını toplamasına, yönetmesine ve analiz etmesine yardımcı olan
                                      yenilikçi bir değerlendirme ve herkese açık itibar yönetim platformudur. Google, Google Shopping,
                                      Google Haritalar gibi kanallarda yıldızlı görünüm sağlayarak markanızın güvenilirliğini artırır.
                                      Ayrıca sahte yorum tespiti, SEO uyumlu yorum widget’ları, yapay zeka destekli analiz ve gelişmiş
                                      raporlama gibi özellikler sunar.
                                    </p>
								</div>
							</div>
						</div>					
					<!-- Card -->	

                    <!-- Card -->
                    <div role="tablist" id="becameauser">
                      <div class="card">
                        <div class="card-body">
                          <h4 class="nomargin_top">Puandeks’e nasıl üye olabilirim?</h4>
                          <ul>
                            <li>Web sitemiz üzerinden <strong>“Kayıt Ol”</strong> butonuna tıklayın.</li>
                            <li>İşletme bilgilerinizi doldurun ve hesabınızı oluşturun.</li>
                            <li>Marka profilinizi talep ederek yorum toplamaya başlayın.</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                   <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="payments">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Ödeme yöntemleri nelerdir?</h4>
                        <p>Puandeks, dünya genelinde geçerli çok sayıda ödeme yöntemini destekler:</p>
                        <ul>
                          <li>Visa</li>
                          <li>Mastercard</li>
                          <li>Troy</li>
                          <li>American Express</li>
                        </ul>
                        <p>Ayrıca <strong>80 farklı para biriminde</strong> ödeme kabul ederiz.  
                        Tüm ödemeler güvenli altyapı üzerinden işlenir, kart bilgileriniz şifrelenmiş şekilde saklanır.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="subscription-start">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Abonelik sürecim ne zaman başlar?</h4>
                        <p><strong>7 günlük ücretsiz deneme süresi</strong> başlar. Bu süre dolduğunda, kayıt olduğunuz tarihe göre her ayın aynı gününde abonelik ücreti tahsil edilir.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="subscription-cancel">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Aboneliğimi nasıl iptal edebilirim?</h4>
                        <ul>
                          <li>Puandeks hesabınıza giriş yapın → <strong>Ayarlar → Abonelik</strong> bölümünden iptal talebi oluşturun.</li>
                          <li>İptal sonrası, mevcut faturalandırma dönemi bitene kadar hizmetlerden yararlanmaya devam edebilirsiniz.</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="card-security">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Kart bilgilerim nasıl saklanır?</h4>
                        <p>Kredi veya banka kartı bilgileriniz, <strong>PCI-DSS uyumlu</strong> altyapıda şifrelenmiş olarak saklanır.  
                        Puandeks kart bilgilerinizi doğrudan görmez veya kendi sunucularında tutmaz.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="google-reviews">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Yorumlar Google’da nasıl görünür?</h4>
                        <p>Puandeks, Google ile uyumlu <strong>schema markup</strong> teknolojisi kullanır.  
                        Böylece yorum puanlarınız Google Arama, Google Shopping ve Google Haritalar’da yıldızlı olarak görüntülenebilir.</p>
                        <p>Marka adınızla yapılan aramalarda “yorumları”, “değerlendirmeleri” ve “şikayetleri” içeren arama sonuçlarında üst sıralarda yer alırsınız.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="fake-review">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Sahte bir yorum aldım, ne yapmalıyım?</h4>
                        <ul>
                          <li>Panelde ilgili yorumu açın ve <strong>Bildir</strong> butonunu kullanın.</li>
                          <li>Ekibimiz inceleme yapar, gerekirse yayından kaldırır.</li>
                          <li>Tüketicilerden destekleyici ekran görüntüsü ve belgeler talep ederek süreci hızlandırırız.</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="complaints">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Tüketici şikayetlerini nasıl yönetebilirim?</h4>
                        <p>Panelden <strong>Plus</strong> özelliğini kullanarak yorumlara yanıt verebilirsiniz.  
                        Çözüm önerileri paylaşarak marka imajınızı güçlendirebilirsiniz.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="collect-faster">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Yorum toplamayı nasıl hızlandırabilirim?</h4>
                        <ul>
                          <li>E-posta veya SMS ile otomatik davet gönderin.</li>
                          <li>Satış sonrası teşekkür sayfasına yorum linki ekleyin.</li>
                          <li>Site dışı hizmet ve satışlarınızda müşterilerinizden Puandeks profiliniz için yorum talep edin.</li>
                          <li>Fiziksel mağazada <strong>QR kod</strong> ile yorum toplayın.</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="languages">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Hangi diller ve para birimleri destekleniyor?</h4>
                        <p>Puandeks çok dilli arayüz desteği sunar ve <strong>80’den fazla para biriminde</strong> ödeme kabul eder.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="seo">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Puandeks ile SEO’yu nasıl güçlendirebilirim?</h4>
                        <ul>
                          <li>Ürün sayfalarınıza yorum widget’ı ekleyin.</li>
                          <li>Düzenli yorum toplayarak Google taramalarını artırın.</li>
                          <li><strong>Schema markup</strong> ile yıldızlı snippet elde edin.</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="trial">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Deneme süresinde ödeme yapmam gerekir mi?</h4>
                        <p><strong>Hayır.</strong> Deneme süresinde ödeme alınmaz.  
                        Süre bitiminde kayıtlı karttan otomatik tahsilat yapılır.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="prove-review">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Yorumun sahte olmadığını nasıl kanıtlarım?</h4>
                        <p><strong>Sipariş numarası</strong>, fatura veya iletişim bilgilerini destek ekibimize iletebilirsiniz.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="integrations">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Hangi entegrasyonlar mevcut?</h4>
                        <p>WooCommerce, Shopify, Wix, PrestaShop, İkas, Ticimax, T-soft, İdeasoft, Shopier ve özel yazılım sitelerle çalışır.  
                        Ayrıca <strong>API desteği</strong> vardır.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="mobile-view">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Mobil cihazlarda yorumlar nasıl görünür?</h4>
                        <p>Tüm widget’lar <strong>mobil uyumludur</strong> ve hızlı yüklenir.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="support">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Destek ekibine nasıl ulaşabilirim?</h4>
                        <p>Panelinizden <strong>Canlı Destek</strong> bölümüne tıklayabilir veya destek e-posta adresimize yazabilirsiniz.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="ai-reviews">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Puandeks’in genel yorum yapay zeka sistemi nasıl çalışır?</h4>
                        <p>Puandeks yapay zekası, tüm işletme yorumlarınızı gerçek zamanlı olarak analiz eder, olumlu ve olumsuz eğilimleri belirler.  
                        Yorumlarınızdan elde edilen verileri harmanlayarak, sunduğunuz hizmetin özetini profil sayfanıza ekler.</p>
                        <p>Bu özellik yalnızca <strong>Enterprise</strong> plan kullanıcılarına özeldir.  
                        Böylece müşteri memnuniyetini artırmak için hızlı ve etkili adımlar atabilirsiniz.</p>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                  <!-- Card -->
                  <div role="tablist" id="ai-benefits">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="nomargin_top">Yapay zekanın işletmeme sağladığı avantajlar nelerdir?</h4>
                        <ul>
                          <li>Olumsuz yorumlara otomatik yanıt önerileri.</li>
                          <li>Müşteri memnuniyet trendlerinin raporlanması.</li>
                          <li>SEO için değerli anahtar kelimelerin otomatik çıkarılması.</li>
                          <li>Güçlü yönlerinizi öne çıkararak satış dönüşümünü artırma.</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <!-- Card -->

                      
   


<!-- Right Area -->						
						
						
					
					</div>
					<!-- /accordion Booking -->
				</div>
				<!-- /col -->
			</div>
			<!-- /row -->
		</div>
		<!--/container-->
    </main>
    <!-- /main -->

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