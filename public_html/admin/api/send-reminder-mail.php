<?php
require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

header('Content-Type: application/json');

$companyId = $_POST['company_id'] ?? null;

if (!$companyId) {
    echo json_encode(['success' => false, 'message' => 'Company ID missing']);
    exit;
}

$stmt = $pdo->prepare("SELECT name, email FROM companies WHERE id = ?");
$stmt->execute([$companyId]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    echo json_encode(['success' => false, 'message' => 'Company not found']);
    exit;
}

$to      = $company['email'];
$subject = 'Ödeme Hatırlatma';

$message = <<<HTML
<div style="font-family:Arial, Helvetica, sans-serif; color:#333; line-height:1.6;">
  <p>Merhaba <strong>{$company['name']}</strong>,</p>

  <p>
    Puandeks hesabınıza ait paket süresinin
    <strong>yaklaşan bitiş tarihi</strong> olduğunu hatırlatmak isteriz.
  </p>

  <p>
    Hizmetlerinizin kesintisiz devam edebilmesi için
    işletme panelinizden paket bilgilerinizi inceleyebilirsiniz.
  </p>

  <p style="margin:20px 0;">
    <a
      href="https://business.puandeks.com/"
      target="_blank"
      style="
        display:inline-block;
        padding:10px 18px;
        background:#10b981;
        color:#ffffff;
        text-decoration:none;
        border-radius:6px;
        font-weight:600;
      "
    >
      İşletme Paneline Git
    </a>
  </p>

  <p style="font-size:14px; color:#666;">
    Bu e-posta yalnızca bilgilendirme amaçlı gönderilmiştir.
  </p>

  <p style="margin-top:30px;">
    Saygılarımızla,<br>
    <strong>Puandeks</strong>
  </p>
</div>
HTML;


try {
    sendMail($to, $subject, $message);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mail error']);
}
