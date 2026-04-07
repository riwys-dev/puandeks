<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$company_display = '';
$company_logo = 'https://puandeks.com/img/placeholder/user.png';

if (isset($_SESSION['company_id'])) {
    require_once('/home/puandeks.com/backend/config.php');

    try {
        $stmt = $pdo->prepare("SELECT name, logo FROM companies WHERE id = ?");
        $stmt->execute([$_SESSION['company_id']]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($company) {
            $company_display = htmlspecialchars($company['name']);
            if (!empty($company['logo'])) {
                $company_logo = htmlspecialchars($company['logo']);
            }
        }
    } catch (PDOException $e) {
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="description" content="">
   <title>İş Ortaklığı - Puandeks</title>

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

<style>
.affiliate-hero {
  width: 100%;
  background: #44c486; /* geçici yeşil, sonra ton netleştiririz */
  padding: 120px 0;
}

.affiliate-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: grid;
  grid-template-columns: 1fr 520px;
  gap: 60px;
  align-items: center;
}


.affiliate-left h1 {
  font-size: 32px;
  font-weight: 700;
  color: #111;
  margin-bottom: 16px;
  line-height: 1.2;
}

.affiliate-left p {
  font-size: 16px;
  color: #222;
  max-width: 520px;
  line-height: 1.6;
}


.affiliate-card {
  background: #f3f3f3;
  padding: 40px;
  border-radius: 12px;
}

.affiliate-form-title {
  text-align: center;
  font-size: 26px;
  font-weight: 700;
  margin-bottom: 30px;
  color: #333;
}


@media (max-width: 991px) {
  .affiliate-container {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 991px) {

  .affiliate-section-grid {
    grid-template-columns: 1fr !important;
  }

  .affiliate-section-grid img {
    max-width: 100% !important;
  }

  .affiliate-section-grid div {
    text-align: left;
  }

}
</style>




</head>
<body>
<div id="page">

<?php include 'inc/header.php'; ?>

<!-- main -->      
<main style="padding:0; margin:0;">

<section class="affiliate-hero">
    <div class="affiliate-container">
       <div class="affiliate-left">
        <h1>
          Puandeks partnerleri arasında <br> yerinizi alın!
        </h1>
        <p>
          Puandeks’in büyüyen iş ve teknoloji partnerleri ağına dahil olun.
        </p>
      </div>
         

 <div class="affiliate-right">

  <div style="background:#f2f2f2; padding:28px; border-radius:10px; max-width:440px; margin-left:auto;">

 <h2 id="affiliate-form-title"
    style="
      scroll-margin-top:120px;
      text-align:left;
      font-size:22px;
      font-weight:700;
      margin-bottom:22px;
      color:#333;">
  Bugün Partner Olun
</h2>


 <form id="affiliateForm">

    <div style="margin-bottom:16px;">
      <label style="display:block; font-size:14px; font-weight:600; margin-bottom:6px;">İsim *</label>
      <input type="text" 
             name="first_name"
             required
             placeholder="İsim"
             style="width:100%; height:44px; padding:0 12px; border:1px solid #dcdcdc; border-radius:2px; background:#e9e6e3; font-size:14px;">
    </div>

    <div style="margin-bottom:16px;">
      <label style="display:block; font-size:14px; font-weight:600; margin-bottom:6px;">Soyisim *</label>
      <input type="text" 
             name="last_name"
             required
             placeholder="Soyisim"
             style="width:100%; height:44px; padding:0 12px; border:1px solid #dcdcdc; border-radius:2px; background:#e9e6e3; font-size:14px;">
    </div>

    <div style="margin-bottom:16px;">
      <label style="display:block; font-size:14px; font-weight:600; margin-bottom:6px;">E-posta *</label>
      <input type="email" 
             name="email"
             required
             placeholder="E-posta adresinizi girin"
             style="width:100%; height:44px; padding:0 12px; border:1px solid #dcdcdc; border-radius:2px; background:#e9e6e3; font-size:14px;">
    </div>

    <div style="margin-bottom:16px;">
      <label style="display:block; font-size:14px; font-weight:600; margin-bottom:6px;">Telefon Numarası *</label>
      <input type="text" 
             name="phone"
             required
             placeholder="Telefon numarası"
             style="width:100%; height:44px; padding:0 12px; border:1px solid #dcdcdc; border-radius:2px; background:#e9e6e3; font-size:14px;">
    </div>

    <div style="margin-bottom:16px;">
      <label style="display:block; font-size:14px; font-weight:600; margin-bottom:2px;">
        Web Sitesi URL'si <span style="font-weight:400; color:#777;">(isteğe bağlı)</span>
      </label>
      <input type="text" 
             name="website"
             placeholder="Web sitesi URL'si"
             style="width:100%; height:44px; padding:0 12px; border:1px solid #dcdcdc; border-radius:2px; background:#e9e6e3; font-size:14px;">
    </div>

    <div style="margin-bottom:22px;">
      <label style="display:block; font-size:14px; font-weight:600; margin-bottom:2px;">
        Firma Adı <span style="font-weight:400; color:#777;">(isteğe bağlı)</span>
      </label>
      <input type="text" 
             name="company_name"
             placeholder="Firma adı"
             style="width:100%; height:44px; padding:0 12px; border:1px solid #dcdcdc; border-radius:2px; background:#e9e6e3; font-size:14px;">
    </div>

    <button type="submit"
      style="width:100%; height:46px; background:#4f6fd5; color:#fff; border:none; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer;">
      Partner Olun
    </button>

    <hr style="margin:24px 0; border:none; border-top:1px solid #ddd;">

    <p style="font-size:12px; line-height:1.7; color:#666; text-align:center;">
      Yukarıdaki butona tıklayarak 
      <a href="https://puandeks.com/privacy-policy" target="_blank" style="color:#4f6fd5; text-decoration:none;">Gizlilik Politikamızı</a> 
      ve 
      <a href="#" style="color:#4f6fd5; text-decoration:none;">İş ortaklığı sözleşmemizi</a> 
      kabul etmiş olursunuz. Başvurunuzla ilgili olarak sizinle telefon veya e-posta yoluyla iletişime geçebiliriz.
    </p>

</form>


</div>

</div>

</div>
</section>



<section style="background:#f6f6f6; padding:100px 0;">

<div class="affiliate-section-grid"
     style="max-width:1200px; margin:0 auto; padding:0 20px; display:grid; grid-template-columns: 560px 1fr; gap:60px; align-items:center;">


    <!-- LEFT -->
    <div>
      <img src="img/affiliate1.webp" 
           alt="Affiliate Partner"
           style="width:100%; max-width:560px; height:auto; border-radius:10px; display:block;">
    </div>

    <!-- RIGHT -->
    <div>

      <h2 style="font-size:32px; font-weight:700; margin-bottom:24px; color:#222;">
        Neden Puandeks Partneri Olmalısınız?
      </h2>

      <ul style="list-style:none; padding:0; margin:0; font-size:16px; line-height:1.8; color:#333;">

        <li style="margin-bottom:12px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Türkiye'nin ilk dijital güven platformu
        </li>

        <li style="margin-bottom:12px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Tüm sektörlere uygun
        </li>

        <li style="margin-bottom:12px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Yüksek gelir fırsatı
        </li>

        <li style="margin-bottom:12px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Çok geniş müşteri portföyü
        </li>

        <li style="display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Özel eğitim ve pazarlama desteği
        </li>

      </ul>

    </div>

  </div>

</section>


<section style="background:#ffffff; padding:100px 0;">

  <div class="affiliate-section-grid" style="max-width:1200px; margin:0 auto; padding:0 20px; display:grid; grid-template-columns: 560px 1fr; gap:60px; align-items:center;">


    <!-- LEFT -->
    <div>

      <h2 style="font-size:32px; font-weight:700; margin-bottom:24px; color:#222;">
        Kimler Puandeks partneri olabilir?
      </h2>

      <ul style="list-style:none; padding:0; margin:0; font-size:16px; line-height:1.9; color:#333;">

        <li style="margin-bottom:10px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Yazılım ajansları
        </li>

        <li style="margin-bottom:10px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Dijital pazarlama ajansları
        </li>

        <li style="margin-bottom:10px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Hosting firmaları
        </li>

        <li style="margin-bottom:10px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          E-ticaret danışmanları
        </li>

        <li style="margin-bottom:10px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Muhasebe ve ERP yazılımları
        </li>

        <li style="margin-bottom:10px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Entegrasyon firmaları
        </li>

        <li style="margin-bottom:10px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Ödeme ve sanal POS şirketleri
        </li>

        <li style="margin-bottom:10px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Teknoloji alanında içerik üreticileri
        </li>

        <li style="margin-bottom:10px; display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Freelancer’lar
        </li>

        <li style="display:flex; align-items:flex-start;">
          <span style="color:#25c17e; font-size:18px; margin-right:10px;">✔</span>
          Influencer & Vlogger’lar
        </li>

      </ul>

    </div>

    <!-- RIGHT -->
    <div>
      <img src="img/affiliate2.webp"
           alt="Affiliate Network"
           style="width:100%; max-width:560px; height:auto; border-radius:10px; display:block;">
    </div>

  </div>

</section>

<section style="background:#f6f6f6; padding:100px 0;">

  <div style="max-width:900px; margin:0 auto; padding:0 20px;">

    <h2 style="font-size:32px; font-weight:700; margin-bottom:50px; color:#222; text-align:center;">
      Sıkça Sorulan Sorular
    </h2>

    <div style="display:flex; flex-direction:column; gap:0;">

      <!-- ITEM -->
      <details style="border-bottom:1px solid #ddd; padding:22px 0;">
        <summary style="cursor:pointer; font-size:18px; font-weight:600; list-style:none; display:flex; justify-content:space-between; align-items:center;">
          Partner olmak ücretsiz mi?
          <span style="font-size:22px;">+</span>
        </summary>
        <p style="margin-top:18px; font-size:15px; line-height:1.8; color:#555;">
          Puandeks partneri olmak ve partner olarak kalmak tamamen ücretsizdir. 
        </p>
      </details>

      <!-- ITEM -->
      <details style="border-bottom:1px solid #ddd; padding:22px 0;">
        <summary style="cursor:pointer; font-size:18px; font-weight:600; list-style:none; display:flex; justify-content:space-between; align-items:center;">
          Satış ortaklığı (Affiliate) linki veriyor musunuz?
          <span style="font-size:22px;">+</span>
        </summary>
        <p style="margin-top:18px; font-size:15px; line-height:1.8; color:#555;">
          Evet sadece size özel tanımlanan link paylaşıyoruz. Bu link üzerinden gelen satışlardan komisyon kazanıyorsunuz.
        </p>
      </details>

      <!-- ITEM -->
      <details style="border-bottom:1px solid #ddd; padding:22px 0;">
        <summary style="cursor:pointer; font-size:18px; font-weight:600; list-style:none; display:flex; justify-content:space-between; align-items:center;">
          Puandeks'e yeni markaları nasıl kaydettireceğim?
          <span style="font-size:22px;">+</span>
        </summary>
        <p style="margin-top:18px; font-size:15px; line-height:1.8; color:#555;">
         Size özel bir referans linkiniz olacak ve bunu online olarak dilediğiniz gibi paylaşabilirsiniz. Bu linke tıklayarak kaydolan herkes, sizin altınızdaki bir müşteri olarak kaydedilecektir. Ayrıca satış ortaklığı panelinden, manuel olarak da müşteri ekleyebileceksiniz.
        </p>
      </details>

      <!-- ITEM -->
      <details style="border-bottom:1px solid #ddd; padding:22px 0;">
        <summary style="cursor:pointer; font-size:18px; font-weight:600; list-style:none; display:flex; justify-content:space-between; align-items:center;">
          Komisyonumu nasıl ve ne zaman çekebilirim?
          <span style="font-size:22px;">+</span>
        </summary>
        <p style="margin-top:18px; font-size:15px; line-height:1.8; color:#555;">
          <ul style="list-style-type: disc; padding-left: 20px; margin: 0;">
            <li style="margin-bottom: 8px;">
              <strong>Asgari Çekim Tutarı:</strong> Hesabınızdan para çekebilmeniz için bakiyenizin en az
              <strong>1.000 TL</strong> olması gerekiyor.
            </li>

            <li style="margin-bottom: 8px;">
              <strong>Ödeme Zamanı:</strong> Ödeme talebinizi ilettiğiniz tarihten itibaren <strong>30 gün içinde</strong> ödemeyi gerçekleştiriyoruz. Bu süre, müşterinin iade veya iptal gibi işlem yapma olasılığını ortadan kaldırmak içindir.
            </li>

            <li style="margin-bottom: 0;">
              <strong>Kim Kazanır?</strong> Bir müşteriye ulaşmak için birden fazla partner çalışıyorsa, komisyonu, müşterinin <strong>kayıt olmadan önce son tıkladığı bağlantının sahibi</strong> olan partner kazanır.
            </li>
          </ul>
        </p>
      </details>

      <!-- ITEM -->
      <details style="border-bottom:1px solid #ddd; padding:22px 0;">
        <summary style="cursor:pointer; font-size:18px; font-weight:600; list-style:none; display:flex; justify-content:space-between; align-items:center;">
          Puandeks'i nasıl duyurabilirim?
          <span style="font-size:22px;">+</span>
        </summary>
        <p style="margin-top:18px; font-size:15px; line-height:1.8; color:#555;">
          Size atanmış Satış Ortaklığı İlişkileri Uzmanımız size yardımcı olacak. İçerik oluşturma, sosyal medya paylaşımları, e-posta pazarlama, reklam çıkma gibi seçeneklerle Punakdeks'i tanıtabilir, pazarlayabilirsiniz. Ürün bizden, tanıtım sizden!
        </p>
      </details>

      <!-- ITEM -->
      <details style="border-bottom:1px solid #ddd; padding:22px 0;">
        <summary style="cursor:pointer; font-size:18px; font-weight:600; list-style:none; display:flex; justify-content:space-between; align-items:center;">
          Reklam ve pazarlamaya dair uymam gereken kurallar var mı?
          <span style="font-size:22px;">+</span>
        </summary>
        <p style="margin-top:18px; font-size:15px; line-height:1.8; color:#555;">
          Evet. Tüm kurallar için satış ortaklığı sözleşmemizi inceleyiniz.
        </p>
      </details>

      <!-- ITEM -->
      <details style="padding:22px 0;">
        <summary style="cursor:pointer; font-size:18px; font-weight:600; list-style:none; display:flex; justify-content:space-between; align-items:center;">
          Tekrarlayan komisyon ödemeleri nedir?
          <span style="font-size:22px;">+</span>
        </summary>
        <p style="margin-top:18px; font-size:15px; line-height:1.8; color:#555;">
        <p>
  Puandeks Partner Programı'nda, sizin referansınızla kaydolan her işletme, size düzenli ve uzun vadeli bir gelir kapısı açar.
</p>

    <p>
      Sadece <strong>bir</strong> işletmeyi Puandeks'e getirdiğinizi ve bu işletmenin aylık <strong>1.000 TL'lik</strong> bir paket satın aldığını düşünelim. Bu işletme, satın aldığı her paket ödemesinden size <strong>%20 komisyon</strong> kazandıracak.
    </p>

    <p>
      <strong>Yani, her ay 200 TL komisyon elde edersiniz.</strong>
    </p>

    <p>
      Bu gelir, işletme Puandeks'te kaldığı sürece <strong>1 yıl boyunca</strong> kesintisiz devam eder. Getireceğiniz her yeni müşteri, size 1 yıl sürecek güçlü bir pasif gelir kaynağı yaratır. Enterprise dahil <strong>tüm paketler</strong> bu komisyona dahildir.
    </p>

    <p>
      <strong>Önemli Avantaj:</strong> Partner linkleriniz <strong>90 gün boyunca</strong> etkilidir. Bir kullanıcı linkinize tıkladıktan sonra 90 gün içinde Puandeks'te kayıt olup paket satın alırsa, komisyon size ait olur.
    </p>

    <p>
      Bu nedenle, uzun vadeli ve güvenilir kazanç için en iyi satış ortaklığı fırsatlarından birine sahip olduğunuzu söyleyebiliriz.
    </p>

        </p>
      </details>

    </div>

  </div>

</section>

<section style="background:#44c486; padding:90px 0;">

  <div style="max-width:900px; margin:0 auto; padding:0 20px; text-align:center;">

    <h2 style="font-size:28px; font-weight:700; color:#1c1c1c; margin-bottom:35px; line-height:1.4;">
      Puandeks partner programına katılmak için bugün başvurun!
    </h2>

<button type="button"
        onclick="document.getElementById('affiliate-form-title').scrollIntoView({behavior:'smooth'});"
        style="
          background:#ffffff;
          color:#1c1c1c;
          padding:14px 32px;
          font-size:15px;
          font-weight:600;
          border-radius:8px;
          border:none;
          cursor:pointer;
          transition:all 0.3s ease;
        ">
  Puandeks Ortağı Olun
</button>

  </div>

</section>



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

<script>
document.addEventListener("DOMContentLoaded", function() {

  const form = document.getElementById("affiliateForm");

  if(form){
    form.addEventListener("submit", function(e){

      e.preventDefault();

      const formData = new FormData(form);

      fetch("api/affiliate-apply.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(data => {

        if(data.success){
          alert("Başvurunuz alındı.");
          form.reset();
        } else {
          alert(data.message || "Bir hata oluştu.");
        }

      })
      .catch(() => {
        alert("Sunucu hatası oluştu.");
      });

    });
  }

});
</script>




</body>
</html>
