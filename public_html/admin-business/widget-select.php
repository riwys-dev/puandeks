<!DOCTYPE html>

<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Puandeks - Widget seçimi</title>

    <!-- Favicon  -->
    <link rel="icon" href="img/favicon.png">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/business-admin.css" rel="stylesheet">

    <style>
    .close-popup{position:fixed;top:16px;right:26px;font-size:24px;color:#666;cursor:pointer;z-index:999999;}
    .close-popup:hover { color:#000; transform:scale(1.1); }

    .widget-card{cursor:pointer;}
    .widget-card.selected{border:3px solid #04DA8D;}



        /* =========================
           GRID WRAPPERS
        ========================= */
        .integration-section {
          margin-bottom: 48px;
        }

        .integration-grid {
          display: flex;
          flex-wrap: wrap;
          gap: 20px;
        }

        /* =========================
           WIDGET BOX 
        ========================= */
        .widget-box {
          position: relative;
          flex: 0 0 calc(25% - 15px);
          background: #fff;
          border: 1px solid #e5e5e5;
          border-radius: 16px;
          overflow: hidden;

          display: flex;
          flex-direction: column;
        }

        /* =========================
           WIDGET PREVIEW 
        ========================= */
        .widget-preview {
          height: 140px;
          background: #f4f6f8;
          padding: 20px;

          display: flex;
          align-items: center;
          justify-content: center;
        }

        .widget-preview img {
          max-width: 100%;
          max-height: 100%;
          object-fit: contain;
        }

        .widget-preview i {
          font-size: 52px;
          color: #444;
        }

        /* =========================
           TITLE 
        ========================= */
        .widget-title {
          height: 52px;
          display: flex;
          align-items: center;
          justify-content: flex-start;

          padding: 0 16px;
          font-size: 16px;
          font-weight: 500;
          color: #111;
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 992px) {
          .widget-box {
            flex: 0 0 calc(50% - 10px);
          }
        }

        @media (max-width: 576px) {
          .widget-box {
            flex: 0 0 100%;
          }
        }

        </style>

 

</head>

<body>
   <div id="page">
     <i class="fas fa-times close-popup" id="closePopup"></i>


        <main>
          
         <section style="max-width:1100px; margin:120px auto 0 auto; padding:0 24px;">

             <!-- Başlık -->
            <h1 style="font-size:32px; color:#1C1C1C; margin-bottom:12px;">Widget Türleri</h1>
            <p style="color:#1C1C1C; margin-bottom:48px;">Widget türünü önizlemek için lütfen seçim yapın.</p>

             <!-- Main Content -->
        <div class="container-fluid">
   
           <div class="tab-content" id="tab-website">
            <div class="integration-section">
              <div class="integration-grid">

                <div class="widget-box widget-card" data-type="carousel">
                  <div class="widget-preview">
                    <img src="https://puandeks.com/img/brands/widget-brands/Carousel.svg">
                  </div>
                  <div class="widget-title"><span>Carousel</span></div>

                </div> 

                <div class="widget-box widget-card" data-type="slider">
                  <div class="widget-preview">
                    <img src="https://puandeks.com/img/brands/widget-brands/Slider.svg">
                  </div>
                  <div class="widget-title"><span>Slider</span></div>
                </div>

                <div class="widget-box widget-card" data-type="list">
                  <div class="widget-preview">
                    <img src="https://puandeks.com/img/brands/widget-brands/List.svg">
                  </div>
                  <div class="widget-title"><span>List</span></div>
                </div>

                <div class="widget-box widget-card" data-type="flex">
                  <div class="widget-preview">
                    <img src="https://puandeks.com/img/brands/widget-brands/Flex-carousel.svg">
                  </div>
                 <div class="widget-title"><span>Flex</span></div>
                </div>

              </div>
            </div>
          </div>
         </div>

  


    </section>
  </main>
</div>
	


    <script>
    document.getElementById("closePopup").addEventListener("click", function () {
      window.location.href = "/widget-manager";
    });
    </script>

   
<script>
document.querySelectorAll('.widget-card').forEach(card => {
  card.addEventListener('click', () => {

    document.querySelectorAll('.widget-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');

    const type = card.dataset.type;

    setTimeout(() => {
      window.location.href = `/widget-preview?type=${type}`;
    }, 200);

  });
});
</script>




</body>
</html>