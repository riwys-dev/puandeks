<?php
session_start();

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

header('Content-Type: application/json; charset=utf-8');

try {

    /* ============================================================
       1) CLAIM MODE: company_id zorunlu
    ============================================================ */
    $company_id = intval($_POST['company_id'] ?? 0);
    if ($company_id <= 0) {
        throw new Exception("Geçersiz işletme ID.");
    }

    // İşletme DB'de var mi ve pending mi?
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ? AND status = 'pending' LIMIT 1");
    $stmt->execute([$company_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        throw new Exception("Bu işletme bulunamadı veya sahiplenilemez.");
    }

    /* ============================================================
       POST Data
    ============================================================ */
    $business_name = $company['name']; // değiştirilemez
    $full_name     = trim($_POST['full_name'] ?? '');
    $website       = trim($_POST['website'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $country       = intval($_POST['country'] ?? 0);
    $phone_prefix  = preg_replace('/[^0-9]/', '', $_POST['phone_prefix'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $password      = $_POST['password'] ?? '';
    $confirm       = $_POST['confirmPassword'] ?? '';
    $agreement     = isset($_POST['agreement']);

    $category_id   = intval($_POST['category_id'] ?? 0);
    $annual_income = floatval($_POST['annual_income'] ?? 0);

    if (!$agreement)
        throw new Exception("Sözleşme onayı gereklidir.");

    if ($password !== $confirm)
        throw new Exception("Şifreler eşleşmiyor.");

    if (strlen($password) < 8)
        throw new Exception("Şifre en az 8 karakter olmalıdır.");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        throw new Exception("Geçersiz e-posta adresi.");

    /* ============================================================
      Domain Otomasyonu
    ============================================================ */
    $domain = strtolower($website);
    $domain = str_replace(["https://", "http://", "www."], "", $domain);
    $domain = explode("/", $domain)[0];

    if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/', $domain)) {
        throw new Exception("Geçerli bir domain adresi girin (örnek: firmaadi.com)");
    }

    /* ============================================================
       E-mail benzersiz  
    ============================================================ */
    $check = $pdo->prepare("SELECT id FROM companies WHERE email = ? AND id != ?");
    $check->execute([$email, $company_id]);
    if ($check->rowCount() > 0) {
        throw new Exception("Bu e-posta adresi başka bir işletme tarafından kullanılıyor.");
    }


    /* ============================================================
       Update + Email verification token
    ============================================================ */
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(32));

    $update = $pdo->prepare("
        UPDATE companies 
        SET owner_name        = :owner_name,
            website           = :website,
            domain            = :domain,
            email             = :email,
            country           = :country,
            phone_prefix      = :phone_prefix,
            phone             = :phone,
            password          = :password,
            category_id       = :category_id,
            annual_income     = :annual_income,
            email_verified    = 0,
            verification_token = :token,
            status            = 'waiting_approval'
        WHERE id = :id
    ");

    $update->execute([
        ':owner_name'        => $full_name,
        ':website'           => $website,
        ':domain'            => $domain,
        ':email'             => $email,
        ':country'           => $country,
        ':phone_prefix'      => $phone_prefix,
        ':phone'             => $phone,
        ':password'          => $password_hashed,
        ':category_id'       => $category_id,
        ':annual_income'     => $annual_income,
        ':token'             => $token,
        ':id'                => $company_id
    ]);

  /* ============================================================
   7) CLAIM → Doğrulama e-maili
    ============================================================ */

    // SESSION burada set edilmeli (kritik)
    $_SESSION['pending_business_email'] = $email;
    $_SESSION['register_mode'] = 'claim';

    $verify_link = "https://business.puandeks.com/api/business-claim-verify.php?token=" . urlencode($token);

    $subject = "İşletme Sahiplenme Doğrulaması – Puandeks";

    $message = "
        <h2>İşletme Sahiplenme Onayı</h2>
        <p>Merhaba <b>{$full_name}</b>,</p>
        <p><b>{$business_name}</b> işletmesini sahiplenme işlemini tamamlamak için aşağıdaki bağlantıya tklayın:</p>

        <p>
            <a href='{$verify_link}' 
               style='padding:10px 18px;background:#0C7C59;color:#fff;border-radius:5px;text-decoration:none;'>
               Sahiplenmeyi Doğrula
            </a>
        </p>

        <p>İşletme giriş sayfasına yönlendirileceksiniz:</p>
    ";

    sendMail($email, $subject, $message);




    /* ============================================================
       8) Admin bildirimi
    ============================================================ */
    try {
        $notify_stmt = $pdo->prepare("
            INSERT INTO admin_notifications (title, content, created_at, is_read)
            VALUES (:title, :content, NOW(), 0)
        ");
        $notify_stmt->execute([
            ':title'   => 'Yeni İşletme Sahiplenildi',
            ':content' => "{$business_name} adlı işletme, {$full_name} tarafından sahiplenildi ve onay bekliyor."
        ]);
    } catch (Exception $e) {
        error_log("Admin notification error: " . $e->getMessage());
    }

    /* ============================================================
       9) RESPONSE
    ============================================================ */
    echo json_encode([
        'success' => true,
        'message' => 'İşletme sahiplenildi. Lütfen e-posta doğrulaması yapın.',
        'redirect' => '/verify-pending'
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
