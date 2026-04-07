<?php
require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

date_default_timezone_set('Europe/Istanbul');

$today = date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT * FROM company_subscriptions
    WHERE status IN ('active','trial')
");
$stmt->execute();

$subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($subscriptions as $sub) {

    // şirket mailini çek
    $companyStmt = $pdo->prepare("SELECT email FROM companies WHERE id = ?");
    $companyStmt->execute([$sub['company_id']]);
    $company = $companyStmt->fetch(PDO::FETCH_ASSOC);

    if (!$company || empty($company['email'])) {
        continue;
    }

    $email = $company['email'];

    $endDate = $sub['end_date'];

    $diff = (strtotime($endDate) - strtotime($today)) / (60 * 60 * 24);
    $daysLeft = (int)$diff;



// 10 gün
if ($daysLeft == 10 && $sub['reminder_10_sent'] == 0) {

    $html = "
    <div style='font-family:Arial; font-size:15px; color:#333;'>

        <p><strong>Ödeme Hatırlatma</strong></p>

        <p>
            Paketinizin bitmesine <strong>10 gün</strong> kaldı.
        </p>

        <p>
            Hizmetinizin kesintisiz devam etmesi için ödemenizi yapmayı unutmayın.
        </p>

        <p style='margin-top:20px;'>
            <a href='https://business.puandeks.com/plans'
               style='display:inline-block;
                      padding:12px 20px;
                      background:#04DA8D;
                      color:#ffffff;
                      text-decoration:none;
                      border-radius:6px;
                      font-weight:bold;'>
                Planları Gör
            </a>
        </p>

        <p style='margin-top:20px;'>Puandeks Ekibi</p>

    </div>
    ";

    sendMail(
        $email,
        "Ödeme Hatırlatma",
        $html
    );

    $update = $pdo->prepare("
        UPDATE company_subscriptions 
        SET reminder_10_sent = 1 
        WHERE id = ?
    ");
    $update->execute([$sub['id']]);
}


// 5 gün
 if ($daysLeft == 5 && $sub['reminder_5_sent'] == 0) {

    $html = "
    <div style='font-family:Arial; font-size:15px; color:#333;'>

        <p><strong>Ödeme Hatırlatma</strong></p>

        <p>
            Paketinizin bitmesine <strong>5 gün</strong> kaldı.
        </p>

        <p>
            Hizmetinizin kesintisiz devam etmesi için ödemenizi yapmayı unutmayın.
        </p>

        <p style='margin-top:20px;'>
            <a href='https://business.puandeks.com/plans'
               style='display:inline-block;
                      padding:12px 20px;
                      background:#04DA8D;
                      color:#ffffff;
                      text-decoration:none;
                      border-radius:6px;
                      font-weight:bold;'>
                Planları Gör
            </a>
        </p>

        <p style='margin-top:20px;'>Puandeks Ekibi</p>

    </div>
    ";

    sendMail(
        $email,
        "Ödeme Hatırlatma",
        $html
    );

    $update = $pdo->prepare("
        UPDATE company_subscriptions 
        SET reminder_5_sent = 1 
        WHERE id = ?
    ");
    $update->execute([$sub['id']]);
}



// 3 gün
if ($daysLeft == 3 && $sub['reminder_3_sent'] == 0) {

    $html = "
    <div style='font-family:Arial; font-size:15px; color:#333;'>

        <p><strong>Ödeme Hatırlatma</strong></p>

        <p>
            Paketinizin bitmesine <strong>3 gün</strong> kaldı.
        </p>

        <p>
            Hizmetinizin kesintisiz devam etmesi için ödemenizi yapmayı unutmayın.
        </p>

        <p style='margin-top:20px;'>
            <a href='https://business.puandeks.com/plans'
               style='display:inline-block;
                      padding:12px 20px;
                      background:#04DA8D;
                      color:#ffffff;
                      text-decoration:none;
                      border-radius:6px;
                      font-weight:bold;'>
                Planları Gör 
            </a>
        </p>

        <p style='margin-top:20px;'>Puandeks Ekibi</p>

    </div>
    ";

    sendMail(
        $email,
        "Ödeme Hatırlatma",
        $html
    );

    $update = $pdo->prepare("
        UPDATE company_subscriptions 
        SET reminder_3_sent = 1 
        WHERE id = ?
    ");
    $update->execute([$sub['id']]);
}


// son gün
if ($daysLeft == 0 && $sub['reminder_0_sent'] == 0) {

    $html = "
    <div style='font-family:Arial; font-size:15px; color:#333;'>

        <p><strong>Son Gün Hatırlatma</strong></p>

        <p>
            Paketiniz bugün sona eriyor.
        </p>

        <p>
            Hizmetinizin kesintiye uğramaması için Puandeks <strong>Planlar</strong> sayfasını ziyaret ederek size uygun paketi seçebilirsiniz.
        </p>

        <p style='margin-top:20px;'>
            <a href='https://business.puandeks.com/plans'
               style='display:inline-block;
                      padding:12px 20px;
                      background:#04DA8D;
                      color:#ffffff;
                      text-decoration:none;
                      border-radius:6px;
                      font-weight:bold;'>
                Şimdi Planları Gör
            </a>
        </p>

        <p style='margin-top:20px;'>Puandeks Ekibi</p>

    </div>
    ";

    sendMail(
        $email,
        "Son Gün Hatırlatma",
        $html
    );

    $update = $pdo->prepare("
        UPDATE company_subscriptions 
        SET reminder_0_sent = 1 
        WHERE id = ?
    ");
    $update->execute([$sub['id']]);
}


// süresi geçti
if ($daysLeft < 0 && $sub['status'] != 'expired') {

    $html = "
    <div style='font-family:Arial; font-size:15px; color:#333;'>

        <p><strong>Hizmet Durduruldu</strong></p>

        <p>
            Paket süreniz sona erdiği için hizmetiniz durdurulmuştur.
        </p>

        <p>
            Tekrar aktif etmek için Puandeks <strong>Planlar</strong> sayfasını ziyaret ederek size uygun paketi seçebilirsiniz.
        </p>

        <p style='margin-top:20px;'>
            <a href='https://business.puandeks.com/plans'
               style='display:inline-block;
                      padding:12px 20px;
                      background:#04DA8D;
                      color:#ffffff;
                      text-decoration:none;
                      border-radius:6px;
                      font-weight:bold;'>
                Puandeks İşletmeler için Planlar
            </a>
        </p>

        <p style='margin-top:20px;'>Puandeks Ekibi</p>

    </div>
    ";

    sendMail(
        $email,
        "Hizmet Durduruldu",
        $html
    );

    $update = $pdo->prepare("
        UPDATE company_subscriptions 
        SET status = 'expired' 
        WHERE id = ?
    ");
    $update->execute([$sub['id']]);
}

}