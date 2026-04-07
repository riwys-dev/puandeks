<?php
$type = $_GET['type'] ?? 'flex';

$allowedWidgets = [
  'flex',
  'carousel',
  'slider',
  'list'
];

if (!in_array($type, $allowedWidgets)) {
  $type = 'flex';
}
?>

<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Puandeks - Widget Önizleme</title>

    <!-- Favicon  -->
    <link rel="icon" href="img/favicon.png">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/business-admin.css" rel="stylesheet">

    <style>
    .close-popup{position:fixed;top:16px;right:26px;font-size:24px;color:#666;cursor:pointer;z-index:999999;}
    .close-popup:hover{color:#000;transform:scale(1.1);}

    .widget-preview-section{max-width:1100px;margin:120px auto 0;}
    .widget-preview-wrapper{display:flex;justify-content:center;}
    @media (max-width:768px){.widget-preview-section{margin-top:80px;padding:0;}}

   </style>


</head>

<body>

  <div id="page">
    <i class="fas fa-times close-popup" id="closePopup"></i>
    

    <main>

      <section class="widget-preview-section">
        
        <!-- Header -->
          <?php
          $widgetTitles = [
            'flex' => 'Flex',
            'slider' => 'Slider',
            'carousel' => 'Carousel(Dokunmatik kaydırma)',
            'list' => 'List'
          ];
          ?>
         <p style="text-align:center;color:#1C1C1C;margin-bottom:24px;font-weight:500;font-size:28px;">
          <?= $widgetTitles[$type] ?>
        </p>


        <!-- Preview alanı -->
        <div class="widget-preview-wrapper">

          <?php
            $type = $_GET['type'] ?? 'flex';

            $allowedWidgets = ['flex','carousel','slider','list'];

            if (!in_array($type, $allowedWidgets)) {
              $type = 'flex';
            }

            include $type . '.php';
          ?>

        </div>

      </section>
    </main>
  </div>

  <!-- Close -->

 
<script>
document.getElementById("closePopup").addEventListener("click", function () {
  window.location.href = "/widget-select";
});
</script>






</body>
</html>
