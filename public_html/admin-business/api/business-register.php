<?php
session_start();

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/slug.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

header('Content-Type: application/json; charset=utf-8');

try {
    // === Form datas ===
    $business_name = trim($_POST['business_name'] ?? '');
    $full_name     = trim($_POST['full_name'] ?? '');
    $website       = trim($_POST['website'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $country       = intval($_POST['country'] ?? 0);
    $phone_prefix  = preg_replace('/[^0-9]/', '', $_POST['phone_prefix'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $password      = $_POST['password'] ?? '';
    $confirm       = $_POST['confirmPassword'] ?? '';
    $agreement     = isset($_POST['agreement']);

    // === validation controls ===
    if (!$agreement)
        throw new Exception("Sözleşme onayı gereklidir.");

    if ($password !== $confirm)
        throw new Exception("Şifreler eşlemiyor.");

    if (strlen($password) < 8)
        throw new Exception("Şifre en az 8 karakter olmalıdır.");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        throw new Exception("Geçersiz e-posta adresi.");

    // Same email?
    $check = $pdo->prepare("SELECT id FROM companies WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0)
        throw new Exception("Bu e-posta adresiyle zaten bir hesap mevcut.");

    // === DOMAIN AUTOMATION (kritik!) ===
    $domain = strtolower($website);
    $domain = str_replace(["https://", "http://", "www."], "", $domain);
    $domain = explode("/", $domain)[0]; // sadece thefitoz.com kısmı

    if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/', $domain)) {
        throw new Exception("Geçerli bir domain adresi girin. Örnek: thefitoz.com");
    }

    // === datas ===
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(32));
    $created_at = date('Y-m-d H:i:s');

    // Create Slug 
   $slug = generateSlug($business_name, $pdo, 'companies');

    // === Add to DB ===
    $stmt = $pdo->prepare("
        INSERT INTO companies 
        (name, slug, owner_name, website, domain, email, country, phone_prefix, phone, password, email_verified, verification_token, status, created_at) 
        VALUES 
        (:name, :slug, :owner_name, :website, :domain, :email, :country, :phone_prefix, :phone, :password, 0, :token, 'pending', :created_at)
    ");

    $stmt->execute([
        ':name'         => $business_name,
        ':slug'         => $slug,
        ':owner_name'   => $full_name,
        ':website'      => $website,
        ':domain'       => $domain,
        ':email'        => $email,
        ':country'      => $country,
        ':phone_prefix' => $phone_prefix,
        ':phone'        => $phone,
        ':password'     => $password_hashed,
        ':token'        => $token,
        ':created_at'   => $created_at
    ]);


    // === Admin bildirimi ===
    try {
        $notify_stmt = $pdo->prepare("
            INSERT INTO admin_notifications (title, content, created_at, is_read)
            VALUES (:title, :content, NOW(), 0)
        ");
        $notify_stmt->execute([
            ':title'   => 'Yeni işletme kaydı alındı',
            ':content' => "{$business_name} adlı işletme onay bekliyor. Yetkili: {$full_name} ({$email})"
        ]);
    } catch (Exception $e) {
        error_log('Admin bildirimi DB hatası: ' . $e->getMessage());
    }

    // Admin email
    $adminSubject = "Yeni İşletme Kaydı - {$business_name}";
    $adminMessage = "
        <h2>Yeni işletme kaydı yapıldı</h2>
        <p><b>İşletme adı:</b> {$business_name}</p>
        <p><b>Yetkili kişi:</b> {$full_name}</p>
        <p><b>E-posta:</b> {$email}</p>
        <p>Durum: Onay bekliyor</p>
    ";
    sendMail('info@puandeks.com', $adminSubject, $adminMessage);

    // Business verify email
    $verify_link = "https://business.puandeks.com/api/business-register-verify.php?token=" . urlencode($token);

    $subject = "İşletme Hesabınızı Doğrulayın – Puandeks";
    $message = "
        <h2>İşletme hesabınızı doğrulayın</h2>
        <p>Merhaba <b>{$full_name}</b>,</p>
        <p>Puandeks işletme hesabınızı etkinleştirmek için aşağıdaki bağlantıya tıklayın:</p>
        <p><a href='{$verify_link}' style='padding:10px 18px;background:#0044cc;color:#fff;border-radius:5px;text-decoration:none;'>Hesabımı Doğrula</a></p>
    ";
    sendMail($email, $subject, $message);

    $_SESSION['pending_business_email'] = $email;

    echo json_encode(['success' => true, 'message' => 'Kayıt başarılı! Lütfen e-posta adresinizi doğrulayın.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
