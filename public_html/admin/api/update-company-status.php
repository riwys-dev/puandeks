<?php
session_start();
header('Content-Type: application/json');

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');


// ============================
// 1) ALLOW ONLY POST REQUESTS
// ============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Geçersiz istek türü"]);
    exit;
}


// ============================
// 2) BLOCK ANY GET STATUS PARAMETER
// ============================
if (isset($_GET['status'])) {
    unset($_GET['status']);
}


// ============================
// 3) SAFELY READ POST DATA
// ============================
$company_id = isset($_POST['company_id']) ? $_POST['company_id'] : null;
$status     = isset($_POST['status']) ? $_POST['status'] : null;

if (!$status || trim($status) === '') {
    echo json_encode([
        "success" => false,
        "message" => "HATA: API boş status aldı."
    ]);
    exit;
}



// Validate allowed status values
$allowed_status = ['approved', 'rejected', 'pending'];
if (!$company_id || !in_array($status, $allowed_status)) {
    echo json_encode(["success" => false, "message" => "Geçersiz istek verisi"]);
    exit;
}



try {

    // ======================================================
    // UPDATE COMPANY STATUS + VERIFIED FLAG
    // ======================================================
    $stmt = $pdo->prepare("
        UPDATE companies 
        SET 
            status = :status,
            verified = CASE 
                WHEN :status = 'approved' THEN 1 
                ELSE 0 
            END
        WHERE id = :id
    ");

    $stmt->execute([
        ':status' => $status,
        ':id' => $company_id
    ]);



    // ======================================================
    // HANDLE EMAIL / NOTIFICATIONS AFTER APPROVE OR REJECT
    // ======================================================
    if ($status === 'approved' || $status === 'rejected') {

        // Fetch company info
        $info = $pdo->prepare("SELECT name, owner_name, email FROM companies WHERE id = :id LIMIT 1");
        $info->execute([':id' => $company_id]);
        $company = $info->fetch(PDO::FETCH_ASSOC);

        if ($company) {

            $company_name = htmlspecialchars($company['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $owner_name   = htmlspecialchars($company['owner_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $email        = $company['email'];

            // ===================================
            // IF APPROVED
            // ===================================
            if ($status === 'approved') {

                // Add admin notification
                $notifAdmin = $pdo->prepare("
                    INSERT INTO admin_notifications (title, content, created_at, is_read)
                    VALUES (:title, :content, NOW(), 0)
                ");
                $notifAdmin->execute([
                    ':title'   => 'İşletme Onaylandı',
                    ':content' => "{$company_name} adlı işletme başarıyla onaylandı ve aktif edildi."
                ]);

                // Send mail to company (Turkish)
                $subject = "Kaydınız Onaylandı – Puandeks";
                $message = "
                    <h2>Merhaba {$owner_name},</h2>
                    <p><b>{$company_name}</b> işletme kaydınız incelenmiş ve <b>onaylanmıştır</b>.</p>
                    <p>Artık giriş yapabilir ve işletme panelinizi kullanmaya başlayabilirsiniz.</p>
                    <p>
                      <a href='https://business.puandeks.com/login' 
                         style='display:inline-block;padding:10px 18px;background:#28a745;
                                color:#fff;border-radius:5px;text-decoration:none;'>
                        Panele Giriş Yap
                      </a>
                    </p>
                    <br>
                    <p>Saygılarımızla,<br>Puandeks Destek Ekibi</p>
                ";
                sendMail($email, $subject, $message);

                // Add company panel notification
                $notifCompany = $pdo->prepare("
                    INSERT INTO company_notifications (company_id, title, content, created_at, is_read)
                    VALUES (:company_id, :title, :content, NOW(), 0)
                ");
                $notifCompany->execute([
                    ':company_id' => $company_id,
                    ':title'      => 'Kayıt Onaylandı',
                    ':content'    => 'Tebrikler! İşletme kaydınız başarıyla onaylandı ve paneliniz aktif edildi.'
                ]);
            }


            // ===================================
            // IF REJECTED
            // ===================================
            elseif ($status === 'rejected') {

                // Send rejection email (Turkish)
                $subject = "Kaydınız Reddedildi – Puandeks";
                $message = "
                    <h2>Merhaba {$owner_name},</h2>
                    <p><b>{$company_name}</b> işletme kaydınız yapılan inceleme sonucunda <b>reddedilmiştir</b>.</p>
                    <p>Eğer bunun bir hata olduğunu düşünüyorsanız bizimle iletişime geçebilirsiniz.</p>
                    <hr>
                    <p>Saygılarımızla,<br>Puandeks Destek Ekibi</p>
                ";
                sendMail($email, $subject, $message);
            }

        }
    }



      // ADMIN NOTIFICATION 
      $niceStatusText = [
          'approved' => 'onayladınız.',
          'rejected' => 'reddettiniz. İşletme engellendi.',
          'pending'  => 'beklemeye aldınız.'
      ];

      $contentText = "{$company_name} işletmesini {$niceStatusText[$status]}";

      $notif = $pdo->prepare("
          INSERT INTO admin_notifications (title, content, is_read)
          VALUES (?, ?, 0)
      ");
      $notif->execute([
          "İşletme Durumu Güncellendi",
          $contentText
      ]);



///  SUCCESS MESSAGE 
$messages = [
    'approved' => 'İşletmeyi onayladınız. İşletmeye bilgilendirme e-postası gönderildi.',
    'rejected' => 'İşletmeyi reddettiniz. İşletmeye bilgilendirme e-postası gönderildi.',
    'pending'  => 'İşlem başarıyla tamamlandı.'
];

echo json_encode([
    "success" => true,
    "message" => $messages[$status] ?? 'İşlem başarıyla tamamlandı.'
]);
exit;




} catch (Exception $e) {

    // Error handler (Turkish)
    echo json_encode([
        "success" => false,
        "message" => "Veritabanı hatası: " . $e->getMessage()
    ]);
}

?>
