<?php
session_start();

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_POST['csrf']) || !isset($_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'CSRF doğrulama hatası.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$formType = $_POST['form_type'] ?? '';
$topic    = trim($_POST['topic'] ?? '');
$message  = trim($_POST['message'] ?? '');

if ($formType === '' || $topic === '' || $message === '') {
    echo json_encode(['status' => 'error', 'message' => 'Eksik alanlar var.'], JSON_UNESCAPED_UNICODE);
    exit;
}

/* LEGAL */
$legalTopicMap = [
    'kotuye-kullanim' => 'Kötüye Kullanım Bildirimi',
    'inceleme-itiraz' => 'İnceleme İtirazı',
    'gizlilik-talebi' => 'Gizlilik / Veri Talebi',
    'yasal-destek'    => 'Yasal Destek Talebi'
];

/* TECHNICAL */
$technicalTopicMap = [
    'giris-sorunu'     => 'Giriş / Oturum Sorunu',
    'hesap-dogrulama'  => 'Hesap Doğrulama Problemi',
    'kurulum-yardimi'  => 'Kurulum Desteği',
    'widget-sorunu'    => 'Widget Çalışmıyor / Görünmüyor',
    'isletme-paneli'   => 'İşletme Paneli Hatası',
    'teknik-hata'      => 'Genel Teknik Hata'
];

/* SALES */
$salesTopicMap = [
    'paket-sorusu'     => 'Paketler Hakkında Soru',
    'fiyatlandirma'    => 'Fiyatlandırma Bilgisi',
    'kurumsal-teklif'  => 'Kurumsal Teklif Talebi',
    'paket-yukseltme'  => 'Paket Yükseltme İsteği',
    'indirim-sorusu'   => 'İndirim / Kampanya Sorusu',
    'diger'            => 'Diğer Satış Konusu'
];

if ($formType === 'legal') {

    $topicText = $legalTopicMap[$topic] ?? 'Genel Yasal Talep';
    $formTitle = 'İncelemeler ve Yasal Destek';
    $subjectPrefix = 'Legal Form';

} elseif ($formType === 'technical') {

    $topicText = $technicalTopicMap[$topic] ?? 'Genel Teknik Talep';
    $formTitle = 'Teknik Destek Talebi';
    $subjectPrefix = 'Technical Form';

} elseif ($formType === 'sales') {

    $topicText = $salesTopicMap[$topic] ?? 'Genel Satış Talebi';
    $formTitle = 'Satış ve Fiyatlandırma Talebi';
    $subjectPrefix = 'Sales Form';

} else {

    echo json_encode(['status' => 'error', 'message' => 'Geçersiz form tipi.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$userInfo = 'Ziyaretçi';

if (isset($_SESSION['user_id'])) {
    $userInfo = $_SESSION['name'] . ' (' . $_SESSION['email'] . ')';
}

$subject = "{$subjectPrefix} - {$topicText} - Puandeks";

$html = "
<div style='font-family:Arial; font-size:14px; color:#333;'>
    <h2>{$formTitle}</h2>
    <p><strong>Konu:</strong> {$topicText}</p>
    <p><strong>Gönderen:</strong> {$userInfo}</p>
    <hr>
    <p><strong>Açıklama:</strong></p>
    <p>" . nl2br(htmlspecialchars($message)) . "</p>
</div>
";

$mailSent = sendMail(MAIL_LEGAL_ADDRESS, $subject, $html);

if ($mailSent) {
    echo json_encode(['status' => 'success', 'message' => 'Talebiniz başarıyla gönderildi.'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Mail gönderilemedi.'], JSON_UNESCAPED_UNICODE);
}
