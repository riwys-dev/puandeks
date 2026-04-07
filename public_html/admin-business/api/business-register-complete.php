<?php
require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

session_start();
header('Content-Type: application/json; charset=utf-8');

try {

    // ======================================================
    // 1) POST datas
    // ======================================================

    $company_id    = intval($_POST['company_id'] ?? 0);
    $category_id   = intval($_POST['category_id'] ?? 0);
    $annual_income = floatval($_POST['annual_income'] ?? 0);

    if ($company_id <= 0) {
        throw new Exception("Geçersiz işletme ID.");
    }

    // ======================================================
    // 2) Bu isletme var mı?
    // ======================================================

    $stmt = $pdo->prepare("SELECT id, email FROM companies WHERE id = ? LIMIT 1");
    $stmt->execute([$company_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        throw new Exception("İşletme kaydı bulunamadı.");
    }


    // ======================================================
    // 5) DB Update
    // ======================================================


    $update = $pdo->prepare("
       UPDATE companies 
        SET category_id   = :category_id,
            annual_income = :annual_income
        WHERE id = :id
    ");


    $update->execute([
        ':category_id'   => $category_id,
        ':annual_income' => $annual_income,
        ':id'            => $company_id
    ]);

    // ======================================================
    // 6) Onay Mail
    // ======================================================

    try {
        $stmtInfo = $pdo->prepare("
            SELECT name, owner_name, email 
            FROM companies 
            WHERE id = :id LIMIT 1
        ");
        $stmtInfo->execute([':id' => $company_id]);
        $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);

        if ($info) {

            $subject = "Kaydınız Alındı – Puandeks İşletme Onay Süreci Başladı";
            $message = "
                <h2>Merhaba {$info['owner_name']},</h2>
                <p><b>{$info['name']}</b> işletme kaydınız başarıyla tamamlandı.</p>
                <p>Bilgileriniz doğrulama ekibimiz tarafından incelenmektedir.</p>
                <p>Onay işlemi tamamlandığında size bir bilgilendirme e-postası gönderilecektir.</p>
                <br>
                <p>Saygılarımzla,<br>Puandeks Destek Ekibi</p>
            ";

            sendMail($info['email'], $subject, $message);
        }
    } catch (Exception $ex) {
        error_log("Onay maili gönderilemedi: " . $ex->getMessage());
    }

    // ======================================================
    // 7) Session temizle
    // ======================================================

    unset($_SESSION['company_id']);

    // ======================================================
    // 8) Başarılı donus
    // ======================================================

    echo json_encode([
        'success'  => true,
        'message'  => 'Kayıt başarıyla tamamlandı. Giriş yapabilirsiniz.',
        'redirect' => 'https://business.puandeks.com/login'
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
