<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// config yükle (MAIL sabitleri buradan gelecek)
require_once('/home/puandeks.com/backend/config.php');

// PHPMailer dosyaları
require_once(__DIR__ . '/phpmailer/PHPMailer.php');
require_once(__DIR__ . '/phpmailer/SMTP.php');
require_once(__DIR__ . '/phpmailer/Exception.php');

/**
 * Genel mail gönderim fonksiyonu
 */
function sendMail($to, $subject, $htmlContent, $toName = '') {

    $mail = new PHPMailer(true);

    try {

        // SMTP Ayarları
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';

        // Encryption
        if (MAIL_ENCRYPTION === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif (MAIL_ENCRYPTION === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // Gönderen
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);

        // Alıcı
        $mail->addAddress($to, $toName);

        // İçerik
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlContent;
        $mail->AltBody = strip_tags($htmlContent);

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}


/* ===================================================
   AFFILIATE FORM ADMIN BİLDİRİM MAİLİ
=================================================== */

function sendAffiliateAdminMail($formData) {

    $subject = "Yeni Affiliate Başvurusu - Puandeks";

    $html = "
    <div style='font-family:Arial; font-size:14px; color:#333;'>
        <h2>Yeni Affiliate Bavurusu</h2>
        <p><strong>İsim:</strong> {$formData['first_name']}</p>
        <p><strong>Soyisim:</strong> {$formData['last_name']}</p>
        <p><strong>E-posta:</strong> {$formData['email']}</p>
        <p><strong>Telefon:</strong> {$formData['phone']}</p>
        <p><strong>Web Sitesi:</strong> {$formData['website']}</p>
        <p><strong>Firma:</strong> {$formData['company']}</p>
    </div>
    ";

    return sendMail(MAIL_ADMIN_ADDRESS, $subject, $html);
}


/* ===================================================
   CLAIM MAIL (Mevcut sistem için)
=================================================== */

function sendClaimMail($email, $businessName) {

    $subject = "İşletme Sahiplenme Onay - Puandeks";

    $html = "
        <div style='font-family:Arial; font-size:15px; color:#333;'>
            <p>Merhaba,</p>

            <p><strong>{$businessName}</strong> işletmesini başarıyla sahiplendiniz.</p>

            <p>
                Puandeks İşletme Paneline giriş yapmak için aşağıdaki bağlantıyı kullanabilirsiniz:
            </p>

            <p>
                <a href='https://business.puandeks.com/business-login.php' 
                   style='display:inline-block; padding:10px 15px; background:#0C7C59; color:white; 
                          text-decoration:none; border-radius:5px;'>
                    İşletme Paneline Giriş Yap
                </a>
            </p>

            <p>Teşekkürler,<br>Puandeks Ekibi</p>
        </div>
    ";

    return sendMail($email, $subject, $html);
}

/* ===================================================
   REVIEW INVITE MAIL (Brevo SMTP)
=================================================== */

function sendReviewInviteMail($to, $companyName, $reviewLink) {

    $mail = new PHPMailer(true);

    try {

        // Use Brevo SMTP
        $mail->isSMTP();
        $mail->Host       = BREVO_SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = BREVO_SMTP_USER;
        $mail->Password   = BREVO_SMTP_PASS;
        $mail->Port       = BREVO_SMTP_PORT;
        $mail->CharSet    = 'UTF-8';
        if (BREVO_SMTP_ENCRYPTION === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif (BREVO_SMTP_ENCRYPTION === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // Sender
        $mail->setFrom('no-reply@puandeks.com', 'Puandeks');

        // Recipient
        $mail->addAddress($to);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Deneyiminizi Paylaşın - {$companyName}";

        $html = "
            <div style='font-family:Arial; font-size:15px; color:#333;'>
                <p>Merhaba,</p>

                <p>
                    <strong>{$companyName}</strong> ile yaşadığınız deneyimi
                    paylaşmak ister misiniz?
                </p>

                <p>
                    Aşağıdaki bağlantıya tıklayarak hızlıca yorum bırakabilirsiniz:
                </p>

                <p>
                    <a href='{$reviewLink}'
                       style='display:inline-block;
                              padding:12px 18px;
                              background:#0C7C59;
                              color:#ffffff;
                              text-decoration:none;
                              border-radius:6px;'>
                        Yorum Bırak
                    </a>
                </p>

                <p>Teşekkürler,<br>Puandeks Ekibi</p>
            </div>
        ";

        $mail->Body    = $html;
        $mail->AltBody = strip_tags($html);

        $mail->send();
        return true;

        } catch (Exception $e) {
        return false;
    }
}

?>
