<?php
require_once("/home/puandeks.com/backend/config.php");

$type = $_GET['type'] ?? '';
session_start();
$companyId = $_SESSION["company_id"] ?? 0;

if (!$companyId || !$type) {
    http_response_code(400);
    exit;
}

/* AKTIF PAKET */
$stmt = $pdo->prepare("
    SELECT p.slug
    FROM company_subscriptions cs
    JOIN packages p ON p.id = cs.package_id
    WHERE cs.company_id = ?
      AND cs.status IN ('active','trial')
    ORDER BY cs.id DESC
    LIMIT 1
");
$stmt->execute([$companyId]);

$currentPackage = $stmt->fetchColumn();

/* YETKI KONTROL */
$allowed = false;

switch ($currentPackage) {
    case 'plus':
        if ($type === 'flex') $allowed = true;
        break;

    case 'premium':
        if (in_array($type, ['flex','carousel','slider'])) $allowed = true;
        break;

    case 'advanced':
        if (in_array($type, ['flex','carousel','slider','list'])) $allowed = true;
        break;

    case 'enterprise':
        $allowed = true;
        break;
}

if (!$allowed) {
    http_response_code(403);
    exit;
}

/* EMBEDS */
$embeds = [

'flex' => '
<iframe 
  src="https://widget.puandeks.com/flex.php?company='.$companyId.'" 
  id="pd-flex-'.$companyId.'" 
  style="width:100%;border:0;display:block;">
</iframe>
',

'carousel' => '
<iframe 
  src="https://widget.puandeks.com/carousel.php?company='.$companyId.'" 
  id="pd-carousel-'.$companyId.'" 
  style="width:100%;border:0;display:block;">
</iframe>
',

'list' => '
<iframe 
  src="https://widget.puandeks.com/list.php?company='.$companyId.'" 
  id="pd-list-'.$companyId.'" 
  style="width:100%;border:0;display:block;">
</iframe>
',

'slider' => '
<iframe 
  src="https://widget.puandeks.com/slider.php?company='.$companyId.'" 
  id="pd-slider-'.$companyId.'" 
  style="width:100%;border:0;display:block;">
</iframe>
'

];

/* GLOBAL HEIGHT SCRIPT */
$script = '
<script>
if (!window.puandeksWidgetListener) {

  window.puandeksWidgetListener = true;

  window.addEventListener("message", function(e){
    if(e.data.type === "puandeks-widget-height"){
      const iframe = document.querySelector(`iframe[src="${e.data.src}"]`);
      if(iframe){
        iframe.style.height = e.data.height + "px";
      }
    }
  });

}
</script>
';


if (!isset($embeds[$type])) {
    http_response_code(404);
    exit;
}

echo $embeds[$type] . $script;