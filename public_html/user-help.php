<!DOCTYPE html>

<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Tüketici Yardım Merkezi - Puandeks</title>

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

    <!-- YOUR CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">
  
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Accordion -->
<style>
.accordion-item { border:1px solid #ddd !important; border-radius:8px !important; margin-bottom:10px !important; background:#fff !important; overflow:hidden !important; }
.accordion-header { width:100% !important; text-align:left !important; padding:15px 20px !important; font-size:1.1rem !important; font-weight:700 !important; border:none !important; background:#f7f7f7 !important; cursor:pointer !important; position:relative !important; display:flex !important; align-items:center !important; }
.accordion-header i { color:#1C1C1C !important; font-size:1.3rem !important; margin-right:10px !important; }
.accordion-header.active::after { transform:rotate(90deg) !important; }
.accordion-content { max-height:0 !important; overflow:hidden !important; transition:max-height 0.4s ease !important; border-top:1px solid #ddd !important; padding:0 20px !important; }
.accordion-content.open { max-height:2000px !important; padding:15px 20px !important; }
.accordion-content p { color:#1C1C1C !important; font-size:1rem !important; line-height:1.6 !important; margin:10px 0 !important; }
.accordion-content strong { display:block !important; margin-top:10px !important; margin-bottom:5px !important; font-size:1rem !important; color:#1C1C1C !important; }
</style>
<!-- Accordion -->

</head>

<body>
		
<div id="page">
		
<!-- header -->
<?php include 'header-main.php'; ?>
<!-- /header -->

      
<!-- main -->      
<main>
<main>

<!-- Hero -->
<section style="background-color:#FF6F0E; padding:100px 20px 0 20px;">
  <div style="max-width:1100px; margin:0 auto; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:40px;">

    <!-- Sol taraf -->
    <div style="flex:1; min-width:280px;">
      <h1 style="font-size:2.4rem; font-weight:700; margin-bottom:15px; color:#222;">Tüketici yardım merkezi</h1>
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

    <!-- Sağ taraf -->
    <div style="flex:1; min-width:280px; text-align:center;">
      <img src="img/banners/user-help-center.png" alt="Help Center" style="max-width:100%; height:auto;">
    </div>

  </div>
</section>
<!-- /Hero -->

<!-- Accordion -->
<section style="padding:60px 20px; background:#fff;">
  <div style="max-width:1100px; margin:0 auto;">
    <h2 style="font-size:1.8rem; font-weight:700; margin-bottom:40px; color:#222;">Tüketiciler için Yardım</h2>
    <div class="accordion">

      <!-- 1 -->
      <div class="accordion-item">
        <button class="accordion-header"><i class="fa-solid fa-user-check" style="margin-right:10px;"></i> Kimler Yorum Yazabilir?</button>
        <div class="accordion-content">

          <p>
            Puandeks olarak, değerlendirmelerin gerçek kişisel deneyimlere dayanmasını önemsiyoruz. 
            Bu nedenle platformumuza yapılacak yorumların belirli koşulları sağlaması gerekir. 
            Gerçek kullanıcı deneyimlerine dayalı yorumlar, hem tüketicilere hem de işletmelere güvenilir bir referans sunar.
          </p>

          <strong>Ne Zaman Yorum Yazabilirsiniz?</strong>
          <p>
            Eğer 18 yaşın üzerindeyseniz ve bir işletmeyle yakın geçmişte gerçek bir deneyim yaşadıysanız, yorum bırakabilirsiniz. Bu deneyim şunlardan biri olabilir:<br><br>

            1. Ürün ya da Hizmet Satın Aldıysanız – Memnuniyet ya da sorunları paylaşabilirsiniz.<br>
            2. Satın Alma Niyetiniz Vardıysa – İşlemi tamamlamadan yaşadığınız deneyimi paylaşabilirsiniz.<br>
            3. İletişim Kurduysanız – Telefon, e-posta veya canlı destek üzerinden yaşadığınız deneyimi paylaşabilirsiniz.<br>
            4. Fiziksel veya Online Ziyaret Gerçekleştirdiyseniz – Mağaza ziyareti veya online inceleme deneyiminizi paylaşabilirsiniz.<br>
            5. Dolaylı Hizmet veya Teslimat Deneyimi Yaşadıysanız – Hediye ürün, kurye hizmeti gibi deneyimleri paylaşabilirsiniz.<br>
            6. Ürün Satın Aldıysanız – Ürün kalitesi, teslimat süreci ve genel deneyim hakkında görüş bildirebilirsiniz.<br>
          </p>

          <p><em>İpucu:</em> Yorumunuzu destekleyecek belge veya ekran görüntülerini saklamanızı öneririz.</p>

          <strong>Yorum Yazarken Dikkat Edilmesi Gereken Diğer Kriterler</strong>
          <p>
            • Sadece kendi deneyiminizi paylaşın. Başkaları adına yorum yazılamaz.<br>
            • Deneyiminiz son 12 ay içinde olmalıdır.<br>
          </p>

          <strong>Ne Zaman Yorum Yazamazsınız?</strong>
          <p>
            • Teşvik aldıysanız (indirim, hediye, menfaat)<br>
            • İşletmeyle yakın ilişkiniz varsa (sahibi, çalışanı, hissedarı, rakibiyseniz)<br>
            • Hukuki veya profesyonel tarafı olmadığınız deneyimler<br>
            • Gündemle alakalı yorumlar<br>
          </p> 

        </div>
      </div>
      <!-- 1 -->


      <!-- 2 -->
      <div class="accordion-item">
        <button class="accordion-header"><i class="fa-solid fa-pen-to-square" style="margin-right:10px;"></i> Değerlendirme Nasıl Yazılır?</button>
        <div class="accordion-content">

          <p>
            Puandeks’e üye olduktan sonra işletme sayfasına giderek hızlıca değerlendirme bırakabilirsiniz. 
            Yorumlarınızı daha sonra düzenleyebilir veya güncelleyebilirsiniz.
          </p>

          <strong>Harika Yorumlar İçin Öneriler</strong>
          <p>
            • Açık ve dürüst olun.<br>
            • Övgü veya eleştirilerinizi net bir şekilde ifade edin.<br>
            • Ürün/hizmetin hangi yönünü değerlendirdiğinizi belirtin.<br>
            • Geliştirici geri bildirimler sunmaya özen gösterin.<br>
          </p>

          <p>
            Puandeks’te yazacağınız her yorum, sadece diğer kullanıcılar için değil, aynı zamanda markaların gelişimi için de büyük bir değer taşır. 
            Gerçek deneyimlerinizi paylaşarak daha şeffaf, güvenilir ve yüksek kaliteli bir hizmet ekosisteminin parçası olun.
          </p>

        </div>
      </div>
      <!-- 2 -->




    <!-- 3 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-id-badge" style="margin-right:10px;"></i> Bir Profil Oluşturun</button>
  <div class="accordion-content">

    <p>
      Puandeks’te yorum yazmaya başlamak ister misiniz? Kaydolmak ve profilinizi oluşturmak çok kolay!  
      Yorum yapabilmek için öncelikle bir kullanıcı profili oluşturmanız gerekir.
    </p>

    <strong>Kayıt Yöntemleri:</strong>
    <p>
      • E-posta ile kayıt<br>
      • Google hesabıyla kayıt<br>
      • Facebook hesabıyla kayıt<br>
      • Apple hesabıyla kayıt<br>
    </p>

    <strong>1. E-Posta ile Kayıt Olma</strong>
    <p>
      • Puandeks.com adresine gidin ve sağ üstteki <em>Giriş Yap</em> butonuna tıklayın.<br>
      • Açılan ekranda <em>E-posta ile Devam Et</em> seçeneğini seçin.<br>
      • E-posta adresinizi girin ve devam edin. Ardından adınızı girin (görünen kullanıcı adınız olacaktır).<br>
      • Kullanıcı Sözleşmesini ve Gizlilik Politikasını onaylayarak kayıt işlemini tamamlayın.<br>
      • Gelen kutunuza gönderilen 4 haneli doğrulama kodunu girin.<br>
      <em>Not:</em> Adınız yorumlarınızın yanında herkese açık olarak görüntülenir. Daha sonra değiştirebilirsiniz.
    </p>

    <strong>2. Facebook ile Kayıt</strong>
    <p>
      • Giriş ekranında <em>Facebook ile Devam Et</em> seçeneğini seçin.<br>
      • Facebook hesabınıza giriş yapın ve izinleri onaylayarak devam edin.<br>
      • Kullanıcı sözleşmesini onayladıktan sonra hesabınız otomatik olarak oluşturulur.<br>
      <em>Facebook profil adınız ve fotoğrafınız Puandeks hesabınıza yansıtılabilir, dilerseniz düzenleyebilirsiniz.</em>
    </p>

    <strong>3. Google ile Kayıt</strong>
    <p>
      • Giriş sayfasında <em>Google ile Devam Et</em> seçeneğini seçin.<br>
      • Açılan pencerede bir Google hesabı seçin.<br>
      • Onay adımlarını takip ederek kolayca giriş yapabilirsiniz.
    </p>

    <strong>4. Apple ile Kayıt</strong>
    <p>
      • <em>Apple ile Giriş Yap</em> seçeneğini seçin.<br>
      • Apple ID bilgilerinizle oturum açın ve 6 haneli doğrulama kodunu girin.<br>
      • Onayları tamamladıktan sonra Puandeks hesabınız aktif hale gelir.
    </p>

    <p><strong>Artık Hazırsınız!</strong><br>
      Kayıt işleminin ardından, deneyimlediğiniz markaları arayarak kolayca değerlendirme bırakabilirsiniz. 
      Unutmayın: Gerçek yorumlar sadece başkalarına değil, işletmelere de değer katar!
    </p>

  </div>
</div>
<!-- 3 -->


<!-- 4 -->
<div class="accordion-item">
  <button class="accordion-header"><i class="fa-solid fa-book-open" style="margin-right:10px;"></i> Hızlı Başlangıç Kılavuzu</button>
  <div class="accordion-content">

    <p>
      Puandeks’te yeni misiniz?  
      Ya da sistemin nasıl çalıştığını daha yakından mı tanımak istiyorsunuz?  
      Bu rehber ile işletmeler hakkında yorum yazmaya ve değerlendirmeleri keşfetmeye hemen başlayabilirsiniz.
    </p>

    <strong>Puandeks Nedir?</strong>
    <p>
      Puandeks; deneyimlediğiniz ürün ve hizmetler hakkında gerçek, doğrulanmış yorumları okuyabileceğiniz ve 
      aynı zamanda kendi değerlendirmelerinizi paylaşabileceğiniz yeni nesil bir kullanıcı değerlendirme platformudur.  
      Amacımız, sadece şikayetlerin değil; olumlu deneyimlerin, önerilerin ve geliştirmeye açık alanların da adil şekilde paylaşılmasını sağlamaktır.  
      Burada sesiniz değerli; çünkü işletmeler, kullanıcı yorumlarından beslenerek daha iyi hizmet sunar.
    </p>

    <strong>Başlamak İçin Ne Yapmalısınız?</strong>
    <p>
      1. Profil Oluşturun – Yorum yazabilmek için önce bir kullanıcı profili oluşturmanız gerekir.<br>
      2. Neden Profil Gerekli? – Gerçek kullanıcıları ve deneyimleri ön planda tutmak için her yorum, bir kullanıcı profiline bağlıdır.<br>
      3. Profil Bilgilerinizi Nasıl Güncelleyebilirsiniz? – Kullanıcı adınızı, konumunuzu, dil tercihinizi değiştirebilir; bildirim tercihlerini düzenleyebilirsiniz.<br>
    </p>

    <strong>Bir İşletmeyi Nasıl Bulabilirsiniz?</strong>
    <p>
      • Puandeks.com ana sayfasındaki arama çubuğuna işletmenin adını veya web sitesini yazın.<br>
      • Doğru işletmeyi seçtiğinizden emin olun (web adresini kontrol edin).<br>
      • İşletmenin profil sayfasında yıldız ortalamasını, doğrulanmış kullanıcı yorumlarını ve daha fazlasını görüntüleyebilirsiniz.<br>
      • Yorumları yıldız puanına, tarihe veya dile göre filtreleyebilirsiniz.<br>
    </p>

    <strong>Nasıl Yorum Yazılır?</strong>
    <p>
      • Yorumlamak istediğiniz işletmenin profilini bulun.<br>
      • “Yorum Yaz” butonuna tıklayın.<br>
      • Yıldız puanınızı belirleyin.<br>
      • Deneyiminizi kısa ve açık şekilde paylaşın.<br>
      • (Varsa) sipariş numarası, rezervasyon kodu gibi ek detayları ekleyin.<br>
      • “Gönder” butonuna tıklayarak incelemenizi yayınlayın.<br>
    </p>

    <p><strong>Tebrikler!</strong> Deneyiminizi paylaşarak hem diğer tüketicilere yol gösterdiniz hem de markaların gelişimine katkıda bulundunuz.</p>

  </div>
</div>
<!-- 4 -->





    <!-- 5 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-comment-dots" style="margin-right:10px;"></i> Bir İnceleme Yazın</button>
      <div class="accordion-content">

        <p>
          Bir işletme deneyiminizi paylaşmak ister misiniz?  
          Bir ürün satın almış ya da bir hizmet almış olun; yaşadığınız deneyimi Puandeks'te paylaşmak, diğer kullanıcıların daha bilinçli kararlar almasına 
          ve işletmelerin kendilerini geliştirmesine yardımcı olur.  
        </p>

        <p>
          İnceleme yazmak için özel bir davet almanıza gerek yok. Gerçek bir deneyim yaşadıysanız, sesiniz Puandeks’te değerlidir.
        </p>

        <strong>Deneyiminiz Fark Yaratır</strong>
        <p>
          Puandeks’e yazılan her yorum; diğer kullanıcıların güvenle seçim yapmasını sağlarken, işletmelere neyi iyi yaptıkları 
          ya da hangi alanlarda gelişmeleri gerektiği konusunda içgörüler sunar.  
          İnceleme yazabilmeniz için tek yapmanız gereken: <em>ücretsiz bir Puandeks profili oluşturmak.</em>
        </p>

        <strong>Hizmet İncelemesi Nasıl Yazılır?</strong>
        <p>
          • Puandeks.com adresine giriş yapın.<br>
          • Arama çubuğuna işletmenin adını yazın. Listede çıkarsa seçin, çıkmazsa "Tüm sonuçları göster"e tıklayın.<br>
          • İşletme yoksa, web adresini girerek ilk incelemeyi siz yazabilirsiniz.<br>
          • Doğru işletme sayfasında olduğunuza emin olun (isim, web sitesi, konum bilgilerini kontrol edin).<br>
          • “İnceleme Yaz” butonuna tıklayın.<br>
          • Deneyiminizi yansıtan yıldız puanını seçin.<br>
          • Yorum kutusuna açık, dürüst ve saygılı bir dille deneyiminizi yazın.<br>
          <em>Karakter sınırı: 4.000</em><br>
          • Yorumunuza başlık ve deneyim tarihini ekleyin (son 12 ay içinde olmalıdır).<br>
          • Şartları onaylayın ve “İncelemeni Gönder” butonuna tıklayın.<br>
        </p>

        <strong>Lokasyon Bazlı İnceleme Nasıl Yazılır?</strong>
        <p>
          • İşletmenin birden fazla şubesi varsa, doğru lokasyonu seçin.<br>
          • Seçilen lokasyondan “İnceleme Yaz” butonuna tıklayın.<br>
          • Yukarıdaki adımları izleyerek deneyiminizi paylaşın.<br>
        </p>

        <strong>Ürün İncelemesi Nasıl Yazılır?</strong>
        <p>
          • Ürün incelemesi yazabilmek için işletmeden davet almış olmanız gerekir.<br>
          • E-postadaki “Hemen Değerlendir” bağlantısına tıklayın.<br>
          • Ürünü yıldızlarla puanlayın, yorumunuzu yazın ve gönderin.<br>
        </p>

        <strong>Aynı İşletmeye Birden Fazla Yorum Yazabilir miyim?</strong>
        <p>
          Evet! Farklı zamanlarda farklı deneyimler yaşadıysanız, her bir deneyim için yeni bir yorum yazabilirsiniz.  
          <em>Günde bir kez aynı işletme için inceleme yazabilirsiniz (özel davetle kural genişletilebilir).</em>
        </p>

        <strong>Daha Etkili Bir Yorum İçin</strong>
        <p>
          • Deneyimim hangi aşamalardan oluştu?<br>
          • Neler hoşuma gitti?<br>
          • Geliştirilebilecek yönler nelerdi?<br>
          • Diğer kullanıcılar için faydalı bir ipucu bırakabilir miyim?<br>
        </p>

        <p>
          Puandeks’te değerlendirme yapmak sadece paylaşmak değil, değiştirmektir.  
          <strong>Gelin, birlikte daha iyi bir hizmet kültürü inşa edelim.</strong>
        </p>

      </div>
    </div>
    <!-- 5 -->


      
    <!-- 6 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-edit" style="margin-right:10px;"></i> Bir İncelemeyi Düzenleyin veya Silin</button>
      <div class="accordion-content">

        <p>
          Puandeks’te yazdığınız her inceleme tamamen sizin kontrolünüzdedir. 
          Deneyiminizi yeniden düzenleyebilir ya da yorumunuzu tamamen silebilirsiniz.
        </p>

        <strong>İncelemenizi Nasıl Düzenleyebilirsiniz?</strong>
        <p>
          • Sağ üst köşeden <em>Giriş Yap</em> butonuna tıklayın ve hesabınıza giriş yapın.<br>
          • Kullanıcı simgesine tıklayarak “Yorumlarım” sekmesine geçin.<br>
          • Düzenlemek istediğiniz yorumu bulun.<br>
          • Yorum kutusunun altında bulunan “Düzenle” butonuna tıklayın.<br>
          • Değişiklikleri yaptıktan sonra “Güncellenmiş Yorumu Gönder” butonuna basın.<br>
          <em>Şeffaflık için yorum içinde “(Güncellendi)” ibaresi eklemenizi öneririz.</em>
        </p>

        <strong>Ürün Yorumlarını Güncelleme</strong>
        <p>
          • “Yorumlarım” sayfasında “Ürün Yorumları” sekmesine geçin.<br>
          • İlgili yorumu bulun ve Düzenle butonuna tıklayın.<br>
          • Açılan pencereden değişikliklerinizi yapın.<br>
          • “Güncellenmiş Yorumu Gönder” ile işlemi tamamlayın.<br>
          <em>Not: 1 yıldan daha eski ürün yorumları düzenlenemez.</em>
        </p>

        <strong>İncelemenizi Kalıcı Olarak Silmek</strong>
        <p>
          • Puandeks hesabınıza giriş yapın.<br>
          • Profil menüsünden “Yorumlarım” bölümüne geçin.<br>
          • Silmek istediğiniz yorumu bulun ve “Sil” butonuna tıklayın.<br>
          • Çıkan uyarı kutusunda işlemi onaylayın.<br>
          <em>Silinen yorumlar geri getirilemez. Bu işlemi dikkatli yapmanızı öneririz.</em>
        </p>

        <strong>Sıkça Sorulan Sorular</strong>
        <p>
          • Aynı işletmeye birden fazla yorum yazabilir miyim? → Evet, farklı tarihlerdeki deneyimlerinizi paylaşabilirsiniz (günde 1 kez).<br>
          • Başkasının adına yorum yazabilir miyim? → Hayır, sadece kendi deneyiminizi paylaşabilirsiniz.<br>
        </p>

        <p>
          <strong>Unutmayın:</strong>  
          Puandeks’te sesiniz değerlidir.  
          Yorumlarınız işletmelerin gelişmesine, kullanıcıların ise daha bilinçli tercihler yapmasına yardımcı olur.
        </p>

      </div>
    </div>
    <!-- 6 -->


    <!-- 7 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-map-location-dot" style="margin-right:10px;"></i> İnceleme Yazma İpuçları (Konum İncelemeleri)</button>
      <div class="accordion-content">

        <p>
          Puandeks’te değerlendirmek istediğiniz bir işletmenin birden fazla şubesi veya fiziksel lokasyonu varsa, 
          doğrudan o konuma özel bir yorum bırakabilirsiniz. Bu tür yorumlar, hem tüketicilerin bilinçli tercihler yapmasına 
          hem de işletmelerin lokasyon bazlı hizmet kalitesini artırmasına yardımcı olur.
        </p>

        <strong>Doğru Konumu Seçtiğinizden Emin Olun</strong>
        <p>
          • Aynı markanın birçok şubesi olabilir ve benzer görünebilir.<br>
          • Yorumunuzu gerçekten ziyaret ettiğiniz şubeye özel yazdığınızdan emin olun.<br>
          • Eğer konum bazlı sistem yoksa, yorumda bulunduğunuz lokasyonu açıkça belirtin.<br>
        </p>

        <strong>Tüm Deneyimi Detaylı Anlatın</strong>
        <p>
          • Aldığınız hizmetin yanı sıra ortam, hijyen, personel yaklaşımı, sıra düzeni, otopark kolaylığı gibi 
          lokasyona özel detayları da paylaşın.<br>
          • Bu, yorumunuzun diğer kullanıcılar için daha faydalı olmasını sağlar.<br>
        </p>

        <strong>Farklı Şubeleri Karşılaştırın</strong>
        <p>
          • Aynı markanın farklı şubelerinde hizmet aldıysanız, deneyimlerinizi karşılaştırarak yazın.<br>
          • Hangi şubenin hangi yönlerde daha iyi olduğunu belirtin.<br>
          • Ancak her şube için ayrı yorum bırakmayı unutmayın.<br>
        </p>

        <strong>Geri Bildiriminizi Yapıcı Paylaşın</strong>
        <p>
          • Olumlu veya olumsuz düşüncelerinizi yapıcı bir dille ifade edin.<br>
          • Eleştirilerin geliştirme amacı taşımasına dikkat edin.<br>
        </p>

        <strong>Kişisel Bilgilere Yer Vermeyin</strong>
        <p>
          • Çalışan isimleri, iletişim bilgileri veya özel içerikleri paylaşmayın.<br>
          • Yalnızca deneyiminize odaklanın.<br>
        </p>

        <p>
          Konum bazlı yorumlar, işletmelerin her lokasyonda tutarlı hizmet sunmasını sağlar.  
          Ziyaret ettiğiniz yeri objektif şekilde değerlendirerek hem diğer kullanıcıları bilgilendirebilir 
          hem de markaların gelişimine katkı sağlayabilirsiniz.
        </p>

      </div>
    </div>
    <!-- 7 -->


    <!-- 8 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-layer-group" style="margin-right:10px;"></i> Yeni İşletmeleri Keşfedin</button>
      <div class="accordion-content">

        <p>
          Puandeks’te farklı sektörlerdeki işletmeleri keşfetmek ve karşılaştırmak artık çok kolay. 
          İlgilendiğiniz alandaki işletmelere göz atabilir, değerlendirmeleri inceleyerek doğru kararı verebilirsiniz.  
          İster kuaför, ister otel, isterse teknoloji mağazası arıyor olun — kategori sayfalarımız sayesinde size en uygun işletmeyi bulabilirsiniz.
        </p>

        <strong>Kategoriler Nasıl Belirleniyor?</strong>
        <p>
          • İşletmeler, sundukları ana ürün veya hizmete göre kategori seçer.<br>
          • Eğer işletme kategori seçmezse, sistem otomatik olarak uygun kategoriye yerleştirir.<br>
        </p>

        <strong>Kategori Nasıl Bulunur ve Keşfedilir?</strong>
        <p>
          • Ana sayfadan “Kategoriler” bağlantısına tıklayın.<br>
          • Arama çubuğu ile kategori arayın veya tüm listeyi inceleyin.<br>
          • Sonuçları alakaya göre, en çok yorum alan ya da en son yorumlanan olarak filtreleyebilirsiniz.<br>
        </p>

        <strong>Sonuçları Nasıl Filtreleyebilirsiniz?</strong>
        <p>
          • Puanlama: Yüksekten düşüğe veya düşükten yükseğe.<br>
          • Konum: Belirli şehir/bölgeye göre.<br>
          • Profil Durumu: Sadece doğrulanmış işletmeleri görmek için.<br>
          • Alt Kategoriler: Daha dar alanlarda arama yapmak için.<br>
          • İlgili Kategoriler: Benzer kategorilere göz atarak keşfi genişletebilirsiniz.<br>
        </p>

        <strong>"En İlgili" Sıralaması Neye Göre Yapılır?</strong>
        <p>
          • Son 12 ayda en az 25 yorum almış olmalı.<br>
          • “Yorum Talep Ediyor” rozeti taşıyor olmalı.<br>
          Bu koşullar işletmenin aktif ve şeffaf olduğunu gösterir.<br>
        </p>

        <strong>Neden Kategori Sayfalarını Kullanmalısınız?</strong>
        <p>
          • Kullanıcılar için güvenilir işletmeleri bulmayı kolaylaştırır.<br>
          • İşletmelerin hedef kitlelerine ulaşmasına yardımcı olur.<br>
          • Aynı sektördeki işletmeleri karşılaştırmak için referans noktasıdır.<br>
        </p>

        <p>
          Hemen kategorilere göz atın, ihtiyacınıza en uygun işletmeyi seçin.  
          İşletmeyseniz, kategori sayfasında yer almak için işletme profili oluşturabilirsiniz.
        </p>

      </div>
    </div>
    <!-- 8 -->


    <!-- 9 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-lightbulb" style="margin-right:10px;"></i> Puandeks’i Kullanma İpuçları</button>
      <div class="accordion-content">

        <p>
          Puandeks'te yapılan kullanıcı yorumları, daha bilinçli alışveriş kararları almanıza yardımcı olur. 
          Aşağıdaki ipuçları sayesinde platformu en etkili şekilde kullanabilir, aradığınız güvenilir işletmelere kolayca ulaşabilirsiniz.
        </p>

        <strong>İstediğiniz İşletmeyi Kolayca Bulun</strong>
        <p>
          • Arama çubuğunu kullanarak işletmenin adını yazabilir ya da kategoriye göre genel arama yapabilirsiniz.<br>
          • Hangi sektörde hizmet veren firmaları arıyorsanız, kategori adını girerek ilgili tüm işletmeleri listeleyebilirsiniz.<br>
        </p>

        <strong>Yorum Yazın ve Deneyiminizi Paylaşın</strong>
        <p>
          • Bir ürün satın aldıysanız veya bir hizmetten faydalandıysanız, deneyiminizi paylaşabilirsiniz.<br>
          • Yorum yazmak hem diğer kullanıcılar için yol gösterici olur hem de işletmelere geri bildirim sağlar.<br>
          • Yorum yazarken dikkat etmeniz gerekenler:<br>
          – Kullandığınız ürün/hizmetin adı<br>
          – Deneyiminizin beklentilerinizle karşılaştırılması<br>
          – Olumlu/olumsuz öne çıkan detaylar<br>
          – Deneyim tarihi<br>
        </p>

        <strong>Kaldığınız Yerden Devam Edin</strong>
        <p>
          • Daha önce baktığınız işletmeleri kolayca bulabilirsiniz. Puandeks, yakın zamanda görüntülediğiniz işletmeleri hatırlar.<br>
        </p>

        <strong>Önerilen İşletmeleri Keşfedin</strong>
        <p>
          • Daha önce incelediğiniz işletmelere benzer yüksek puanlı firmalar size önerilir.<br>
        </p>

        <strong>Kategoriler Arasında Gezin</strong>
        <p>
          • Ana sayfadaki popüler kategorilere göz atabilir, alt kategorilerle daha hedefli sonuçlara ulaşabilirsiniz.<br>
        </p>

        <strong>Sonuçları Filtreleyin</strong>
        <p>
          • Konum: Belirli şehir/bölgeye göre<br>
          • Puan: Yüksek/düşük puan sıralaması<br>
          • Doğrulanmış Profil: Sadece onaylı işletmeleri görmek için<br>
          • Filtre sonrası sonuçlar şu şekilde sıralanabilir: En İlgili, En Fazla Yoruma Sahip, En Son Yorumlananlar<br>
        </p>

        <strong>Yorumları Okuyun ve Değerlendirin</strong>
        <p>
          • Yorum içeriklerine, tarihlerine ve detaylara dikkat edin.<br>
          • Toplam yorum sayısını inceleyin.<br>
          • Orta dereceli yorumlara da bakın, işletmenin gelişim trendini görün.<br>
        </p>

        <strong>Fotoğraf ve Videolara Göz Atın</strong>
        <p>
          • Bazı kullanıcılar yorumlarına görseller veya videolar ekleyebilir.<br>
          • Görsel içerikler karar verme sürecinde daha faydalıdır.<br>
        </p>

        <p>
          Puandeks’i daha etkili kullanarak güvenilir işletmeleri seçin.  
          Yorum yazmak ya da işletmenizi öne çıkarmak için yardım merkezimizin diğer içeriklerine de göz atabilirsiniz.
        </p>

      </div>
    </div>
    <!-- 9 -->


     <!-- 10 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-cart-shopping" style="margin-right:10px;"></i> Yorumları Kullanarak Daha Akıllıca Alışveriş Yapın</button>
      <div class="accordion-content">

        <p>
          Çevrimiçi alışverişte "fazla iyi görünen" bir teklifle karşılaştınız mı?  
          Hemen tıklamak yerine biraz araştırma yapmak sizi olası dolandırıcılıklardan koruyabilir.  
          Puandeks incelemelerini kullanarak güvenle alışveriş yapmanız için ipuçları burada.
        </p>

        <strong>Temel Kontroller</strong>
        <p>
          • Puanlara genel bakın: Yıldız ortalamasına ve güven skoruna dikkat edin.<br>
          • Toplam inceleme sayısını kontrol edin: Sağlıklı fikir için en az 10-20 yorum okuyun.<br>
          • Yorumları detaylı okuyun: Şikayet veya övgülerin konusuna dikkat edin.<br>
          • Yorumların tarihine bakın: 1 yıldan eski yorumlar güncelliğini kaybetmiş olabilir.<br>
          • Puandeks puanı ile işletmenin kendi sitesindeki puanı karşılaştırın. Uyum yoksa dikkatli olun.<br>
        </p>

        <strong>İşletme Puandeks’i Nasıl Kullanıyor?</strong>
        <p>
          • Profil sayfası onaylı mı? İşletme hesabını aktif yönetiyor mu?<br>
          • Müşterilerinden yorum istiyor mu?<br>
          • Yorumları işaretliyor mu? “Şeffaf İşaretleme” sayesinde hangi yorumlara itiraz ettiğini görebilirsiniz.<br>
        </p>

        <strong>Başka Nelere Dikkat Etmelisiniz?</strong>
        <p>
          • Web sitesindeki dil garip mi? Kötü çeviriler sahte sitelerin işareti olabilir.<br>
          • Web adresi satılan ürünlerle uyumlu mu?<br>
          • Geçerli telefon, adres, e-posta var mı?<br>
          • Google’da site adı + “şikayet” araması yapın.<br>
          • Who.is gibi araçlarla domain sahibini kontrol edin.<br>
          • İçgüdülerinize güvenin: Fazla iyi görünüyorsa, muhtemelen öyledir!<br>
        </p>

        <strong>Dolandırıldıysanız Ne Yapmalısınız?</strong>
        <p>
          • Polise bildirin.<br>
          • Tüketici hakları derneklerine başvurun.<br>
          <em>Puandeks resmi yaptırım gücüne sahip değildir, ancak şüpheli profilleri kalıcı olarak engeller.</em>
        </p>

        <p>
          Online inceleme siteleri doğru kullanıldığında çok faydalıdır.  
          Ancak sağduyunuzun yerini asla tutmaz. Yorumları araştırma sürecinizin bir parçası yapın.
        </p>

      </div>
    </div>
    <!-- 10 -->

    <!-- 11 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-shield-halved" style="margin-right:10px;"></i> Dolandırıcılıklardan Korunma & Güvenlik İpuçları</button>
      <div class="accordion-content">

        <p>
          İnternette alışveriş yaparken veya yeni işletmeleri keşfederken dolandırıcılık riskine karşı dikkatli olmalısınız. 
          Puandeks yorumları size yardımcı olsa da, aşağıdaki güvenlik ipuçlarını da uygulayın:
        </p>

        <strong>Web Sitesini Kontrol Edin</strong>
        <p>
          • Web adresi (URL) ile satılan ürünlerin uyumlu olup olmadığını inceleyin.<br>
          • Garip veya bozuk dil kullanımı sahte sitelerin işareti olabilir.<br>
          • Site güvenli mi? Adres çubuğunda “https://” olduğundan emin olun.<br>
        </p>

        <strong>İletişim Bilgilerini Doğrulayın</strong>
        <p>
          • Gerçek işletmeler geçerli telefon numarası, adres ve e-posta paylaşır.<br>
          • İletişim bilgisi bulunmayan sitelerden uzak durun.<br>
        </p>

        <strong>Ek Araştırma Yapın</strong>
        <p>
          • Google’da “[site adı] şikayet” araması yapın.<br>
          • Who.is ile domain sahibini kontrol edin.<br>
          • Sosyal medya hesaplarının aktif olup olmadığını inceleyin.<br>
        </p>

        <strong>Kendi İçgüdülerinize Güvenin</strong>
        <p>
          • Bir teklif “fazla iyi” görünüyorsa, muhtemelen öyledir.<br>
          • Çok düşük fiyatlı kampanyalara şüpheyle yaklaşın.<br>
        </p>

        <strong>Dolandırıcılığa Uğradığınızda</strong>
        <p>
          • Derhal polise başvurun.<br>
          • Tüketici hakları derneklerinden destek alın.<br>
          <em>Puandeks şikayet platformu değildir, ancak şüpheli profilleri kalıcı olarak engeller.</em>
        </p>

        <p>
          Güvenli alışveriş için yorumları inceleyin, ek araştırmalar yapın ve sağduyunuzu kullanın.  
          Böylece hem paranızı hem de zamanınızı koruyabilirsiniz.
        </p>

      </div>
    </div>
    <!-- 11 -->

    <!-- 12 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-circle-question" style="margin-right:10px;"></i> Sıkça Sorulan Sorular (SSS)</button>
      <div class="accordion-content">

        <strong>Aynı işletmeye birden fazla yorum yazabilir miyim?</strong>
        <p>
          Evet. Farklı zamanlarda farklı deneyimler yaşadıysanız, her bir deneyim için yeni yorum yazabilirsiniz.  
          Ancak günde yalnızca bir yorum yazabilirsiniz (özel davetle bu kural genişletilebilir).
        </p>

        <strong>Başkasının adına yorum yazabilir miyim?</strong>
        <p>
          Hayır. Yorumlar sadece kendi kişisel deneyiminizi yansıtmalıdır. 
          Başkalarının adına yapılan yorumlar kaldırılır.
        </p>

        <strong>Yorumlar ne kadar süreyle görünür?</strong>
        <p>
          Yorumlarınız platformda kalıcıdır. Ancak işletme tarafından itiraz edilirse, 
          yönetici incelemesi sonucunda kaldırılabilir.
        </p>

        <strong>Yorumum reddedilirse ne olur?</strong>
        <p>
          Eğer yorumunuz kurallara uymuyorsa, reddedildi bilgisi tarafınıza e-posta ile gönderilir.  
          Dilerseniz kurallara uygun şekilde yeniden yorum bırakabilirsiniz.
        </p>

        <strong>Yorumları düzenleyebilir miyim?</strong>
        <p>
          Evet. “Yorumlarım” bölümünden incelemelerinizi düzenleyebilir veya silebilirsiniz.  
          Ürün yorumları için 1 yıl sınırı bulunmaktadır.
        </p>

        <p>
          <strong>Unutmayın:</strong> Puandeks’te sesiniz değerlidir.  
          Yorumlarınız, işletmelerin gelişmesine ve kullanıcıların daha bilinçli kararlar almasına katkı sağlar.
        </p>

      </div>
    </div>
    <!-- 12 -->

    <!-- 13 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-star" style="margin-right:10px;"></i> Yorumlarınızla Fark Yaratın</button>
      <div class="accordion-content">

        <p>
          Puandeks topluluğunun bir parçası olun, güveni birlikte inşa edelim.  
          Yorumlarınız yalnızca diğer kullanıcıların seçimlerine yön vermekle kalmaz, 
          aynı zamanda işletmelerin gelişimine de katkı sağlar.
        </p>

        <strong>Gerçek Deneyimler Önemlidir</strong>
        <p>
          • Yorumlarınızın gerçek deneyimlere dayanması, platformun güvenilirliğini artırır.<br>
          • Her değerlendirme, işletmelerin kendilerini geliştirmesi için değerli bir geri bildirimdir.<br>
        </p>

        <strong>Neden Katılmalısınız?</strong>
        <p>
          • Şeffaf ve güvenilir bir topluluğun parçası olursunuz.<br>
          • Deneyimleriniz, diğer kullanıcıların bilinçli kararlar almasına yardımcı olur.<br>
          • Markaların sunduğu hizmetlerin iyileştirilmesine doğrudan katkıda bulunursunuz.<br>
        </p>

        <strong>Nasıl Başlarsınız?</strong>
        <p>
          • Hemen ücretsiz bir Puandeks profili oluşturun.<br>
          • Deneyimlerinizi paylaşarak toplulukta aktif rol alın.<br>
          • Yorumlarınızla markalara ışık tutun.<br>
        </p>

        <p>
          Unutmayın: Her yorum bir fark yaratır.  
          Siz de sesinizi paylaşarak daha şeffaf ve adil bir hizmet ekosisteminin oluşmasına katkıda bulunun.
        </p>

      </div>
    </div>
    <!-- 13 -->


    <!-- 14 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-rocket" style="margin-right:10px;"></i> Hızlı Başlangıç İpuçları (İncelemeciler için)</button>
      <div class="accordion-content">

        <p>
          Puandeks’te yeni misiniz?  
          Ya da sistemin nasıl çalıştığını daha iyi öğrenmek mi istiyorsunuz?  
          Bu ipuçları ile işletmeler hakkında yorum yazmaya ve değerlendirmeleri keşfetmeye hemen başlayabilirsiniz.
        </p>

        <strong>Başlamak İçin Adımlar</strong>
        <p>
          1. <strong>Profil Oluşturun:</strong> Yorum yapabilmek için önce bir kullanıcı profili oluşturmanız gerekir.<br>
          2. <strong>Neden Profil Gerekli?</strong> Gerçek kullanıcıları ve deneyimleri ön planda tutmak için her yorum bir profille bağlantılıdır.<br>
          3. <strong>Profilinizi Güncelleyin:</strong> Kullanıcı adı, konum, dil tercihleri ve e-posta bildirim ayarlarını değiştirebilirsiniz.<br>
        </p>

        <strong>Bir İşletmeyi Nasıl Bulabilirsiniz?</strong>
        <p>
          • Puandeks.com ana sayfasındaki arama çubuğuna işletmenin adını veya web adresini yazın.<br>
          • Doğru işletmeyi seçtiğinizden emin olun (isim, web sitesi, konum kontrolü).<br>
          • İşletme profili sayfasında yıldız ortalamasını, yorumları ve diğer bilgileri görüntüleyebilirsiniz.<br>
        </p>

        <strong>Nasıl Yorum Yazılır?</strong>
        <p>
          • İşletme profiline gidin ve “Yorum Yaz” butonuna tıklayın.<br>
          • Yıldız puanınızı seçin.<br>
          • Deneyiminizi açık ve dürüst bir şekilde yazın.<br>
          • Varsa sipariş veya rezervasyon numarası gibi detayları ekleyin.<br>
          • Gönder butonuna tıklayarak incelemenizi yayınlayın.<br>
        </p>

        <p>
          <strong>Tebrikler!</strong> İlk yorumunuzu bıraktığınızda, Puandeks topluluğuna katkıda bulunmuş olacaksınız.  
          Deneyimleriniz yalnızca diğer kullanıcılar için değil, işletmelerin gelişimi için de çok değerlidir.
        </p>

      </div>
    </div>
    <!-- 14 -->


    <!-- 15 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-file-lines" style="margin-right:10px;"></i> Bir İnceleme Yazın (Detaylı Rehber)</button>
      <div class="accordion-content">

        <p>
          Bir işletmeyle deneyiminizi paylaşmak ister misiniz?  
          Puandeks’te inceleme yazmak için ihtiyacınız olan adımlar burada.
        </p>

        <strong>Adım Adım İnceleme Yazma</strong>
        <p>
          1. İncelemek istediğiniz işletmeyi arayın ve profil sayfasını açın.<br>
          2. “İnceleme Yaz” butonuna tıklayın.<br>
          3. Deneyiminizi en iyi yansıtan yıldız puanını seçin.<br>
          4. Yorum kutusuna deneyiminizi açık, dürüst ve saygılı bir dille yazın.<br>
          5. Yorumunuza başlık ekleyin ve deneyim tarihini belirtin (son 12 ay içinde olmalı).<br>
          6. Şartları onaylayın ve “Gönder” butonuna basın.<br>
        </p>

        <strong>Karakter Sınırı</strong>
        <p>
          İnceleme metni için <em>4.000 karakter</em> sınırı vardır (harf, rakam, boşluk ve noktalama dahil).
        </p>

        <strong>Lokasyon Bazlı İnceleme</strong>
        <p>
          • İşletmenin birden fazla şubesi varsa, doğru lokasyonu seçin.<br>
          • Lokasyon sayfasındaki “İnceleme Yaz” butonunu kullanın.<br>
          • Şube özelliği yoksa yorumda bulunduğunuz konumu açıkça belirtin.<br>
        </p>

        <strong>Ürün İncelemesi</strong>
        <p>
          • Ürün incelemesi için işletmeden davet almanız gerekir.<br>
          • E-posta davetindeki “Hemen Değerlendir” bağlantısına tıklayın.<br>
          • Ürünü yıldızlarla puanlayın ve yorumunuzu paylaşın.<br>
        </p>

        <strong>Birden Fazla Yorum</strong>
        <p>
          • Aynı işletmeye farklı zamanlarda farklı deneyimlerle tekrar yorum yazabilirsiniz.<br>
          • Günde yalnızca bir inceleme yazabilirsiniz (özel davetle genişletilebilir).<br>
        </p>

        <strong>Daha Etkili Bir Yorum İçin</strong>
        <p>
          • Deneyiminizi adım adım anlatın.<br>
          • Olumlu ve olumsuz noktaları açıkça belirtin.<br>
          • Diğer kullanıcılar için faydalı olabilecek ipuçları ekleyin.<br>
        </p>

        <p>
          <strong>Unutmayın:</strong> İncelemeleriniz yalnızca tüketicilere değil, işletmelere de değer katar.  
          Deneyimlerinizi paylaşarak şeffaf bir topluluğun oluşmasına katkıda bulunabilirsiniz.
        </p>

      </div>
    </div>
    <!-- 15 -->


    <!-- 16 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-eraser" style="margin-right:10px;"></i> Bir İncelemeyi Düzenleyin veya Silin (Detaylı Rehber)</button>
      <div class="accordion-content">

        <p>
          Puandeks’te yazdığınız her inceleme tamamen sizin kontrolünüz altındadır.  
          İstediğiniz zaman düzenleyebilir veya kalıcı olarak silebilirsiniz.
        </p>

        <strong>İncelemeyi Düzenleme</strong>
        <p>
          1. Hesabınıza giriş yapın.<br>
          2. Profil menüsünden “Yorumlarım” bölümüne gidin.<br>
          3. Düzenlemek istediğiniz yorumu bulun.<br>
          4. Yorum kutusunun altındaki “Düzenle” butonuna tıklayın.<br>
          5. Değişiklikleri yaptıktan sonra “Güncellenmiş Yorumu Gönder” butonuna basın.<br>
          <em>Not:</em> Şeffaflık için yoruma “(Güncellendi)” ibaresi ekleyebilirsiniz.<br>
        </p>

        <strong>Ürün Yorumlarını Güncelleme</strong>
        <p>
          • “Yorumlarım” sayfasında “Ürün Yorumları” sekmesine gidin.<br>
          • İlgili ürünü seçin ve “Düzenle” butonuna basın.<br>
          • Açılan pencerede gerekli değişiklikleri yapın.<br>
          • “Güncellenmiş Yorumu Gönder” ile işlemi tamamlayın.<br>
          <em>Not:</em> 1 yıldan daha eski ürün yorumları düzenlenemez.<br>
        </p>

        <strong>İncelemeyi Silme</strong>
        <p>
          1. Hesabınıza giriş yapın.<br>
          2. Profil menüsünden “Yorumlarım” sekmesine gidin.<br>
          3. Silmek istediğiniz yorumu seçin.<br>
          4. “Sil” butonuna basın.<br>
          5. Çıkan uyarı kutusunda işlemi onaylayın.<br>
          <em>Uyarı:</em> Silinen yorumlar geri getirilemez.<br>
        </p>

        <strong>Sıkça Sorulanlar</strong>
        <p>
          • Aynı işletmeye birden fazla yorum yazabilir miyim? → Evet, farklı deneyimler için mümkündür.<br>
          • Başkasının adına yorum yazabilir miyim? → Hayır, yalnızca kendi deneyiminizi paylaşabilirsiniz.<br>
        </p>

        <p>
          <strong>Unutmayın:</strong> Yorumlarınız işletmelerin gelişmesine katkı sağlar.  
          Düzenleme ve silme hakkınız her zaman sizdedir.
        </p>

      </div>
    </div>
    <!-- 16 -->


    <!-- 17 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-location-dot" style="margin-right:10px;"></i> Konum İncelemeleri Yazmak İçin İpuçları</button>
      <div class="accordion-content">

        <p>
          Puandeks’te bir işletmenin birden fazla şubesi veya lokasyonu olabilir.  
          Deneyiminiz belirli bir yerde geçtiyse, konum incelemesi yazarak hem diğer kullanıcılara 
          hem de işletmeye daha doğru geri bildirim sağlayabilirsiniz.
        </p>

        <strong>Doğru Konumu Seçin</strong>
        <p>
          • Yorum yaparken gerçekten bulunduğunuz şubeyi seçtiğinizden emin olun.<br>
          • Eğer şube seçme özelliği yoksa, yorumun içinde bulunduğunuz lokasyonu belirtin.<br>
        </p>

        <strong>Deneyimi Detaylandırın</strong>
        <p>
          • Aldığınız hizmet dışında şubenin ortamı, hijyen durumu, personel ilgisi, sıra düzeni, otopark kolaylığı gibi ayrıntıları da ekleyin.<br>
          • Böylece yorumunuz diğer kullanıcılar için daha faydalı olur.<br>
        </p>

        <strong>Farklı Şubeleri Karşılaştırın</strong>
        <p>
          • Daha önce aynı markanın farklı şubelerini ziyaret ettiyseniz, aralarındaki farkları yazın.<br>
          • Her şube için ayrı yorum bırakmaya dikkat edin.<br>
        </p>

        <strong>Yapıcı Olun</strong>
        <p>
          • Olumlu ve olumsuz noktaları dengeli şekilde ifade edin.<br>
          • Eleştirilerinizi geliştirme amacıyla yazın.<br>
        </p>

        <strong>Kişisel Bilgilerden Kaçının</strong>
        <p>
          • Çalışanların isimleri, iletişim bilgileri veya özel detayları paylaşmayın.<br>
          • Yorumda yalnızca deneyiminize odaklanın.<br>
        </p>

        <p>
          Konum bazlı incelemeler, işletmelerin tüm şubelerde tutarlı hizmet sunmasına yardımcı olur.  
          Deneyiminizi paylaşarak diğer kullanıcıların bilinçli kararlar almasına katkıda bulunabilirsiniz.
        </p>

      </div>
    </div>
    <!-- 17 -->

    <!-- 18 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-folder-open" style="margin-right:10px;"></i> Yeni İşletmeleri Kategorilerde Keşfetmek</button>
      <div class="accordion-content">

        <p>
          Puandeks’te farklı sektörlerdeki işletmeleri kolayca keşfedebilirsiniz.  
          Kategori sayfaları sayesinde aradığınız alandaki işletmeleri bulabilir, yorumlarını inceleyebilir 
          ve bilinçli seçimler yapabilirsiniz.
        </p>

        <strong>Kategoriler Nasıl Belirleniyor?</strong>
        <p>
          • İşletmeler, sundukları ana ürün veya hizmete göre kategori seçer.<br>
          • Eğer işletme seçim yapmazsa, sistem en uygun kategoriyi otomatik belirler.<br>
        </p>

        <strong>Kategori Nasıl Bulunur?</strong>
        <p>
          • Ana sayfadan “Kategoriler” bağlantısına tıklayın.<br>
          • Arama çubuğu ile belirli bir kategori arayın ya da tüm listeyi görüntüleyin.<br>
        </p>

        <strong>Sonuçları Nasıl Filtreleyebilirsiniz?</strong>
        <p>
          • Puanlama: Yüksekten düşüğe veya düşükten yükseğe sıralayın.<br>
          • Konum: Belirli şehir veya bölgeye göre filtreleyin.<br>
          • Profil Durumu: Sadece doğrulanmış işletmeleri görün.<br>
          • Alt Kategoriler: Daha dar alanlarda arama yapın.<br>
          • İlgili Kategoriler: Benzer kategorileri keşfedin.<br>
        </p>

        <strong>"En İlgili" Sıralaması Neye Göre Yapılır?</strong>
        <p>
          • Son 12 ayda en az 25 yorum almış işletmeler listelenir.<br>
          • İşletmenin “Yorum Talep Ediyor” rozetine sahip olması gerekir.<br>
        </p>

        <strong>Neden Kategori Sayfalarını Kullanmalısınız?</strong>
        <p>
          • Güvenilir işletmeleri bulmayı kolaylaştırır.<br>
          • İşletmelerin hedef kitlelerine ulaşmasını sağlar.<br>
          • Aynı sektördeki işletmeleri karşılaştırma fırsatı verir.<br>
        </p>

        <p>
          Kategorilere göz atarak ihtiyacınıza en uygun işletmeyi seçebilir, yorumları inceleyerek güvenle karar verebilirsiniz.
        </p>

      </div>
    </div>
    <!-- 18 -->


    <!-- 19 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-thumbs-up" style="margin-right:10px;"></i> Puandeks’te En İyi Deneyimi Yaşamanız İçin İpuçları</button>
      <div class="accordion-content">

        <p>
          Puandeks'te yapılan kullanıcı yorumları, daha bilinçli alışveriş kararları almanıza yardımcı olur.  
          İşte platformu en verimli şekilde kullanmanız için bazı ipuçları:
        </p>

        <strong>İşletmeleri Hızlıca Bulun</strong>
        <p>
          • Arama çubuğunu kullanarak işletme adı veya kategoriye göre arama yapabilirsiniz.<br>
          • İlgilendiğiniz sektöre ait tüm işletmeleri listeleyebilirsiniz.<br>
        </p>

        <strong>Yorum Yazın ve Deneyiminizi Paylaşın</strong>
        <p>
          • Ürün veya hizmet aldıysanız, deneyiminizi yorum olarak paylaşın.<br>
          • Deneyim tarihi, olumlu/olumsuz detaylar ve beklentilerinizi eklemeyi unutmayın.<br>
          • Yorumlar, hem diğer kullanıcılar hem de işletmeler için yol göstericidir.<br>
        </p>

        <strong>Kaldığınız Yerden Devam Edin</strong>
        <p>
          • Daha önce görüntülediğiniz işletmelere kolayca yeniden erişebilirsiniz.<br>
        </p>

        <strong>Önerilen İşletmeleri Keşfedin</strong>
        <p>
          • Daha önce baktığınız işletmelere benzer yüksek puanlı işletmeleri keşfedebilirsiniz.<br>
        </p>

        <strong>Kategoriler Arasında Gezin</strong>
        <p>
          • Popüler kategorilere göz atın, alt kategorilerle aramanızı daraltın.<br>
        </p>

        <strong>Sonuçları Filtreleyin</strong>
        <p>
          • Konum: Belirli şehir/bölgedeki işletmeleri görün.<br>
          • Puan: Yüksek/düşük puan sıralaması yapın.<br>
          • Doğrulanmış Profil: Sadece onaylı işletmeleri filtreleyin.<br>
          • Sıralama: En İlgili, En Fazla Yoruma Sahip, En Son Yorumlananlar.<br>
        </p>

        <strong>Yorumları Okuyun ve Değerlendirin</strong>
        <p>
          • Yorum içeriklerine, tarihlere ve yıldız puanlarının detaylarına bakın.<br>
          • Orta dereceli yorumları da inceleyin, işletmenin gelişim sürecini görün.<br>
        </p>

        <strong>Fotoğraf ve Videolara Dikkat Edin</strong>
        <p>
          • Kullanıcıların yüklediği görseller ve videolar karar verme sürecinizi kolaylaştırır.<br>
          • Görsel içerikli yorumlar genellikle daha güvenilir bilgiler sunar.<br>
        </p>

        <p>
          Bu ipuçlarını kullanarak Puandeks’te aradığınız güvenilir işletmelere kolayca ulaşabilir 
          ve daha güvenli alışveriş yapabilirsiniz.
        </p>

      </div>
    </div>
    <!-- 19 -->


    <!-- 20 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-bag-shopping" style="margin-right:10px;"></i> Online Alışveriş Deneyiminizi İncelemelerle Geliştirin</button>
      <div class="accordion-content">

        <p>
          Çevrimiçi alışveriş yaparken güvenilir işletmeleri seçmek için Puandeks yorumlarını kullanabilirsiniz.  
          Yorumlar, sahte sitelerden korunmanıza ve daha doğru tercihler yapmanıza yardımcı olur.
        </p>

        <strong>Nelere Dikkat Etmelisiniz?</strong>
        <p>
          • Puan ortalamasına ve güven skoruna bakın.<br>
          • 10–20 yorum okuyarak genel fikir edinin.<br>
          • Yorumların tarihine dikkat edin; çok eski yorumlar güncelliğini yitirmiş olabilir.<br>
          • Yorumların detaylarını okuyun, sadece yıldız puanına bakmayın.<br>
        </p>

        <strong>Dolandırıcı Siteleri Nasıl Fark Edersiniz?</strong>
        <p>
          • Web sitesindeki dil bozuk ve garipse dikkatli olun.<br>
          • Domain adresini kontrol edin; kapanan mağaza isimleriyle açılmış sahte siteler olabilir.<br>
          • İletişim bilgileri eksik veya geçersizse uzak durun.<br>
        </p>

        <strong>Ek Araştırma Yapın</strong>
        <p>
          • Google’da “[site adı] şikayet” araması yapın.<br>
          • Who.is ile domain sahibini sorgulayın.<br>
          • İşletmenin sosyal medya hesaplarını inceleyin.<br>
        </p>

        <strong>Dolandırıldıysanız Ne Yapmalısınız?</strong>
        <p>
          • Derhal polise bildirin.<br>
          • Tüketici hakları derneklerinden destek alın.<br>
          <em>Puandeks resmi yaptırım gücüne sahip değildir, ancak şüpheli profilleri engeller.</em>
        </p>

        <p>
          Online alışverişte sağduyunuzu kullanın.  
          Yorumları araştırma sürecinize dahil ederek daha güvenli ve keyifli bir alışveriş deneyimi yaşayabilirsiniz.
        </p>

      </div>
    </div>
    <!-- 20 -->


    <!-- 21 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-comments" style="margin-right:10px;"></i> Yorumların Gücünü Kullanarak Bilinçli Karar Verin</button>
      <div class="accordion-content">

        <p>
          Puandeks’te yapılan yorumlar sadece yıldız puanlarından ibaret değildir.  
          Yorumların detaylarını inceleyerek, alışveriş ve hizmet deneyimlerinizi daha güvenli hale getirebilirsiniz.
        </p>

        <strong>Neden Yorumlar Önemlidir?</strong>
        <p>
          • İşletmelerin sunduğu hizmetlerin güçlü ve zayıf yönlerini görmenizi sağlar.<br>
          • Diğer kullanıcıların deneyimlerinden faydalanarak daha doğru kararlar alırsınız.<br>
          • İşletmeler, yorumlardan öğrenerek hizmetlerini geliştirme imkânı bulur.<br>
        </p>

        <strong>Nelere Dikkat Etmelisiniz?</strong>
        <p>
          • Yorumun tarihi: Güncel mi, eski mi?<br>
          • Yorumun içeriği: Sadece “iyi” veya “kötü” demek yerine detaylı mı?<br>
          • Yıldız dağılımı: Ortalama puan ile birlikte farklı puanlardaki yorumları da okuyun.<br>
        </p>

        <strong>Orta Dereceli Yorumlara Bakın</strong>
        <p>
          • Sadece 5 yıldızlı övgüler veya 1 yıldızlı şikayetlerle yetinmeyin.<br>
          • 3–4 yıldızlı yorumlar genellikle en objektif geri bildirimleri içerir.<br>
        </p>

        <strong>Fotoğraf ve Video İçerikleri</strong>
        <p>
          • Kullanıcıların eklediği görseller ürün veya hizmeti daha yakından görmenizi sağlar.<br>
          • Görsel yorumlar karar verme sürecinde çok daha faydalıdır.<br>
        </p>

        <p>
          <strong>Unutmayın:</strong> Yorumların gücü, hem sizin hem de diğer kullanıcıların bilinçli seçimler yapmasına yardımcı olur.  
          Sesinizle topluluğa katkı sağlayın, güveni birlikte inşa edin.
        </p>

      </div>
    </div>
    <!-- 21 -->


    <!-- 22 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-list-check" style="margin-right:10px;"></i> Güvenli Alışveriş İçin Kontrol Listesi</button>
      <div class="accordion-content">

        <p>
          Çevrimiçi alışveriş yaparken dolandırıcılıklardan korunmak için bu kontrol listesini takip edin.  
          Puandeks yorumları size rehberlik eder, ancak kendi kontrollerinizi yapmak da çok önemlidir.
        </p>

        <strong>Web Sitesi Kontrolü</strong>
        <p>
          • URL “https://” ile başlamalı.<br>
          • Domain adı, satılan ürünlerle uyumlu olmalı.<br>
          • Google Translate tarzı bozuk çevirilerden şüphelenin.<br>
        </p>

        <strong>İletişim Bilgileri</strong>
        <p>
          • Telefon numarası, adres ve e-posta bilgisi bulunmalı.<br>
          • İletişim bilgisi olmayan sitelerden uzak durun.<br>
        </p>

        <strong>Yorum ve Puan Kontrolü</strong>
        <p>
          • Puandeks’te işletmenin yorumlarını okuyun.<br>
          • Ortalama puana değil, detaylı içeriklere odaklanın.<br>
          • Son 12 ay içindeki yorumlara öncelik verin.<br>
        </p>

        <strong>Ek Araştırma</strong>
        <p>
          • Google’da “[site adı] şikayet” araması yapın.<br>
          • Who.is ile domain sahibini sorgulayın.<br>
          • Sosyal medya hesaplarının aktif olup olmadığını kontrol edin.<br>
        </p>

        <strong>İçgüdülerinize Güvenin</strong>
        <p>
          • “Fazla iyi” görünen fırsatlar genellikle güvenilir değildir.<br>
          • Çok düşük fiyatlı ürünlerde dikkatli olun.<br>
        </p>

        <p>
          <strong>Unutmayın:</strong> Bu kontrol listesini takip ederek hem paranızı hem de zamanınızı koruyabilirsiniz.  
          Puandeks yorumları ile birleştiğinde, alışverişleriniz çok daha güvenli olacaktır.
        </p>

      </div>
    </div>
    <!-- 22 -->


    <!-- 23 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-triangle-exclamation" style="margin-right:10px;"></i> Dolandırıcılıkla Karşılaşırsanız Ne Yapmalısınız?</button>
      <div class="accordion-content">

        <p>
          İnternette dolandırıcılıkla karşılaşmak can sıkıcı olabilir.  
          Puandeks, sahte profilleri engelleyerek topluluğu korur, ancak resmi yaptırım gücü yoktur.  
          Bu yüzden aşağıdaki adımları takip etmeniz önemlidir:
        </p>

        <strong>Hemen Yapmanız Gerekenler</strong>
        <p>
          • Dolandırıcılığı fark ettiğiniz anda alışverişi durdurun.<br>
          • Kredi kartınızı veya ödeme yöntemlerinizi kontrol edin, gerekirse bankayla iletişime geçin.<br>
        </p>

        <strong>Resmi Mercilere Başvurun</strong>
        <p>
          • Polise şikayette bulunun.<br>
          • Tüketici hakları derneklerine başvurun.<br>
          • Gerekirse avukattan hukuki destek alın.<br>
        </p>

        <strong>Ek Önlemler</strong>
        <p>
          • Dolandırıldığınız siteyi ve detaylarını ekran görüntüsü ile belgeleyin.<br>
          • Benzer mağduriyetleri önlemek için durumu çevrenizle paylaşın.<br>
          • Sahte profilleri Puandeks’e bildirin, ekip inceleme sonrası hesabı kalıcı olarak engeller.<br>
        </p>

        <p>
          <strong>Unutmayın:</strong>  
          İnternet alışverişinde yorumları dikkate almak kadar, şüpheli durumlarda hızlı ve resmi adımlar atmak da önemlidir.  
          Güvende kalmak için daima araştırın ve dikkatli olun.
        </p>

      </div>
    </div>
    <!-- 23 -->


    <!-- 24 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-people-group" style="margin-right:10px;"></i> İncelemelerin Topluluk İçin Önemi</button>
      <div class="accordion-content">

        <p>
          Puandeks yalnızca bir değerlendirme platformu değil, aynı zamanda şeffaf ve güvenilir bir topluluğun merkezidir.  
          Yazdığınız her inceleme, hem diğer tüketicilere hem de işletmelere fayda sağlar.
        </p>

        <strong>Tüketiciler İçin</strong>
        <p>
          • Gerçek kullanıcı deneyimlerini görerek daha bilinçli kararlar alın.<br>
          • Hizmet veya ürün satın almadan önce başkalarının deneyimlerinden faydalanın.<br>
          • Orta ve detaylı yorumlara bakarak işletmenin gerçek durumunu öğrenin.<br>
        </p>

        <strong>İşletmeler İçin</strong>
        <p>
          • Hangi alanlarda başarılı olduklarını ve hangi alanlarda gelişmeleri gerektiğini görürler.<br>
          • Olumlu yorumlarla motivasyon kazanır, olumsuz yorumlarla iyileştirme fırsatı yakalarlar.<br>
        </p>

        <strong>Topluluk İçin</strong>
        <p>
          • Daha güvenilir bir hizmet ekosistemi oluşur.<br>
          • Şeffaflık ve güven, platformun sürdürülebilirliğini sağlar.<br>
          • Herkesin sesini duyurabildiği, adil bir ortam yaratılır.<br>
        </p>

        <p>
          <strong>Unutmayın:</strong>  
          Gerçek deneyimlerinizle yalnızca kendiniz için değil, tüm Puandeks topluluğu için fark yaratırsınız.  
          Katkınız, daha güvenilir bir gelecek inşa eder.
        </p>

      </div>
    </div>
    <!-- 24 -->


    <!-- 25 -->
    <div class="accordion-item">
      <button class="accordion-header"><i class="fa-solid fa-hand-holding-heart" style="margin-right:10px;"></i> Yorumlarınızla İşletmelere Katkı Sağlayın</button>
      <div class="accordion-content">

        <p>
          Puandeks’te paylaştığınız her yorum, yalnızca diğer kullanıcılar için değil, işletmeler için de çok değerlidir.  
          İşletmeler geri bildirimleriniz sayesinde kendilerini geliştirme fırsatı bulur.
        </p>

        <strong>İşletmeler İçin Sağladığınız Katkılar</strong>
        <p>
          • Olumlu yorumlar, işletmelerin güçlü yönlerini görmelerini sağlar.<br>
          • Eleştiriler, gelişime açık alanları belirlemelerine yardımcı olur.<br>
          • Detaylı yorumlar, hizmet ve ürünlerin iyileştirilmesine doğrudan katkı verir.<br>
        </p>

        <strong>Topluluk İçin Katkılarınız</strong>
        <p>
          • Daha şeffaf bir ekosistem oluşmasına destek olursunuz.<br>
          • Deneyimlerinizi paylaşarak diğer tüketicilerin bilinçli seçimler yapmasına yardımcı olursunuz.<br>
          • İşletmelerin müşteri odaklı yaklaşım geliştirmesine katkıda bulunursunuz.<br>
        </p>

        <p>
          <strong>Unutmayın:</strong> Yorumlarınız işletmeler için bir yol haritasıdır.  
          Siz paylaştıkça, işletmeler hizmet kalitesini artırır ve tüm topluluk bundan fayda sağlar.
        </p>

      </div>
    </div>
    <!-- 25 -->


      

    </div>
  </div>
</section>
<!-- Accordion -->



</main>


</main>
<!-- /main -->

      
  
<!-- FOOTER -->	
<?php include('footer-main.php'); ?>
<!-- FOOTER -->	
      
</div>

<!-- COMMON SCRIPTS -->
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
<!-- Accordion -->

<!-- Search box -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("accordionSearch");
  const searchBtn = document.getElementById("accordionSearchBtn");
  const searchMessage = document.getElementById("searchMessage");

  // Highlight temizliği
  function clearHighlights() {
    document.querySelectorAll("mark").forEach(m => {
      const parent = m.parentNode;
      parent.replaceChild(document.createTextNode(m.textContent), m);
      parent.normalize();
    });
  }

  // Accordion reset
  function resetAccordion() {
    clearHighlights();
    document.querySelectorAll(".accordion-item").forEach(item => {
      item.querySelector(".accordion-header").classList.remove("active");
      item.querySelector(".accordion-content").classList.remove("open");
    });
    searchMessage.style.display = "none";
  }

  // Highlight fonksiyonu
  function highlightText(el, query) {
    const regex = new RegExp(query, "gi");
    el.innerHTML = el.innerHTML.replace(regex, "<mark style='background:yellow;'>$&</mark>");
  }

  // Arama fonksiyonu
  function searchAccordion() {
    const query = searchInput.value.trim();
    if (!query) {
      resetAccordion();
      searchMessage.innerText = "Lütfen bir arama terimi girin.";
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

    // Tüm accordionları kapat
    document.querySelectorAll(".accordion-item").forEach(item => {
      item.querySelector(".accordion-header").classList.remove("active");
      item.querySelector(".accordion-content").classList.remove("open");
    });

    // 1. Başlıklar
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

    // 2. Alt başlıklar (strong)
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

    // 3. İçerikler (p)
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

    // Scroll ilk eşleşmeye
    if (firstMatchElement) {
      firstMatchElement.scrollIntoView({behavior: "smooth", block: "start"});
    }

    // Mesajlar
    if (found) {
      searchMessage.innerText = `Arama kriterlerinize uygun ${foundCount} eşleşme bulundu.`;
      searchMessage.style.display = "block";
      searchMessage.style.color = "#04DA8D";
    } else {
      searchMessage.innerText = "Aradığınız kriterlerde bir içerik bulunamadı.";
      searchMessage.style.display = "block";
      searchMessage.style.color = "#d00";
    }
  }

  // Ara butonu
  searchBtn.addEventListener("click", searchAccordion);

  // Enter ile arama
  searchInput.addEventListener("keypress", function(e) {
    if (e.key === "Enter") {
      searchAccordion();
    }
  });

  // Input boşalınca reset
  searchInput.addEventListener("input", function() {
    if (!this.value.trim()) {
      resetAccordion();
    }
  });
});
</script>
<!-- Search box -->
	
</body>
</html>