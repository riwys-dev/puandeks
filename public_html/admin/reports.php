<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');


// =============================
// Admin info
// =============================
$admin_id = $_SESSION['admin_id'];
$admin_name = 'Admin';

$stmt = $pdo->prepare("SELECT full_name FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    $admin_name = $admin['full_name'];
}

// unread notifications
$notifStmt = $pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0");
$unreadCount = $notifStmt->fetchColumn();


// =============================
// Dynamic Year–Month lists
// =============================

// earliest year across users + companies + reviews
$minYearStmt = $pdo->query("
    SELECT MIN(YEAR(created_at)) AS min_year 
    FROM (
        SELECT created_at FROM users
        UNION ALL
        SELECT created_at FROM companies
        UNION ALL
        SELECT created_at FROM reviews
    ) AS all_dates
");
$minYear = (int)($minYearStmt->fetchColumn() ?: date("Y"));
$currentYear = (int)date("Y");

// build year list
$yearList = [];
for ($y = $minYear; $y <= $currentYear; $y++) {
    $yearList[] = $y;
}

// month names
$monthNames = [
    1=>"Ocak",2=>"Şubat",3=>"Mart",4=>"Nisan",
    5=>"Mayıs",6=>"Haziran",7=>"Temmuz",8=>"Ağustos",
    9=>"Eylül",10=>"Ekim",11=>"Kasım",12=>"Aralık"
];

// read filter params
$year  = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';


// =============================
// Build date filter per table
// =============================

// users filter
$whereUsers = "";
$paramsUsers = [];

if ($year !== '') {
    $whereUsers .= " AND YEAR(u.created_at) = :year";
    $paramsUsers['year'] = $year;
}
if ($month !== '' && $month !== 'all') {
    $whereUsers .= " AND MONTH(u.created_at) = :month";
    $paramsUsers['month'] = $month;
}

// companies filter
$whereCompanies = "";
$paramsCompanies = [];

if ($year !== '') {
    $whereCompanies .= " AND YEAR(c.created_at) = :year";
    $paramsCompanies['year'] = $year;
}
if ($month !== '' && $month !== 'all') {
    $whereCompanies .= " AND MONTH(c.created_at) = :month";
    $paramsCompanies['month'] = $month;
}

// reviews filter
$whereReviews = "";
$paramsReviews = [];

if ($year !== '') {
    $whereReviews .= " AND YEAR(r.created_at) = :year";
    $paramsReviews['year'] = $year;
}
if ($month !== '' && $month !== 'all') {
    $whereReviews .= " AND MONTH(r.created_at) = :month";
    $paramsReviews['month'] = $month;
}


// =============================
// REPORT 1: New Users
// =============================
$sql = "SELECT COUNT(*) FROM users u WHERE 1=1 $whereUsers";
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsUsers);
$newUsers = $stmt->fetchColumn();


// =============================
// REPORT 2: New Companies
// =============================
$sql = "SELECT COUNT(*) FROM companies c WHERE 1=1 $whereCompanies";
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsCompanies);
$newCompanies = $stmt->fetchColumn();


// =============================
// REPORT 3: Reviews summary
// =============================

// total reviews
$sql = "SELECT COUNT(*) 
        FROM reviews r 
        WHERE r.parent_id IS NULL $whereReviews";
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsReviews);
$totalReviews = $stmt->fetchColumn();

// approved
$sql = "SELECT COUNT(*) 
        FROM reviews r 
        WHERE r.status='approved' AND r.parent_id IS NULL $whereReviews";
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsReviews);
$approvedReviews = $stmt->fetchColumn();

// pending
$sql = "SELECT COUNT(*) 
        FROM reviews r 
        WHERE r.status='pending' AND r.parent_id IS NULL $whereReviews";
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsReviews);
$pendingReviews = $stmt->fetchColumn();

// rejected
$sql = "SELECT COUNT(*) 
        FROM reviews r 
        WHERE r.status='rejected' AND r.parent_id IS NULL $whereReviews";
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsReviews);
$rejectedReviews = $stmt->fetchColumn();


// =============================
// REPORT 4: Top 10 Businesses
// =============================
$sql = "
    SELECT 
        c.name,
        COUNT(r.id) AS total_reviews,
        ROUND(AVG(r.rating),1) AS avg_rating
    FROM companies c
    LEFT JOIN reviews r 
        ON r.company_id = c.id 
        AND r.parent_id IS NULL
        $whereReviews
    GROUP BY c.id
    ORDER BY total_reviews DESC
    LIMIT 10
";
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsReviews);
$topBusinesses = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =============================
// REPORT 5: Top 10 Categories
// =============================
$sql = "
    SELECT 
        cat.name AS category_name,
        COUNT(r.id) AS total_reviews,
        ROUND(AVG(r.rating),1) AS avg_rating
    FROM categories cat
    LEFT JOIN companies c ON c.category_id = cat.id
    LEFT JOIN reviews r 
        ON r.company_id = c.id 
        AND r.parent_id IS NULL
        $whereReviews
    GROUP BY cat.id
    ORDER BY total_reviews DESC
    LIMIT 10
";
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsReviews);
$topCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =============================
// Frontend variables
// =============================
$rep_new_users        = $newUsers;
$rep_new_companies    = $newCompanies;
$rep_total_reviews    = $totalReviews;
$rep_approved_reviews = $approvedReviews;
$rep_pending_reviews  = $pendingReviews;
$rep_rejected_reviews = $rejectedReviews;

$top_companies  = $topBusinesses;
$top_categories = $topCategories;

?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - Raporlar </title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png">
</head>
<body id="page-top">
<div id="wrapper">

<!-- Sidebar -->
<?php include('admin-sidebar.php'); ?>
<!-- Sidebar -->

<!-- Content -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <?php include('includes/topbar.php'); ?>

        <!-- =================================== -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid" style="max-width: 1000px; margin: 0 auto; padding-top: 24px;">


           <h1 class="h3 mb-4 text-gray-800">Raporlar</h1>

            <!-- Tarih Filtresi -->
            <div style="margin-bottom: 24px;">
                <label><strong>Tarih Filtresi:</strong></label>

                <!-- Yıl -->
                <select name="year" class="form-control" style="display:inline-block;width:140px;margin-right:8px;"
                        onchange="window.location='?year='+this.value+'&month=<?= $month ?>'">
                    <option value="">Tüm Yıllar</option>
                    <?php foreach ($yearList as $y): ?>
                        <option value="<?= $y ?>" <?= ($year == $y ? "selected" : "") ?>>
                            <?= $y ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Ay -->
                <select name="month" class="form-control" style="display:inline-block;width:160px;"
                        onchange="window.location='?year=<?= $year ?>&month='+this.value">
                    <option value="">Tüm Aylar</option>
                    <option value="all" <?= ($month == "all" ? "selected" : "") ?>>Tüm Yıl</option>

                    <?php foreach ($monthNames as $m => $mn): ?>
                        <option value="<?= $m ?>" <?= ($month == $m ? "selected" : "") ?>>
                            <?= $mn ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <!-- Sistem Genel Raporları -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <strong>Sistem Genel Raporları</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Rapor Başlığı</th>
                                <th style="width: 150px;">Değer</th>
                                <th style="width: 280px;">Not</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td>Yeni Tüketici</td>
                                <td><?= $rep_new_users ?></td>
                                <td>Seçilen dönemde eklenen kullanıcı</td>
                            </tr>

                            <tr>
                                <td>Yeni İşletme</td>
                                <td><?= $rep_new_companies ?></td>
                                <td>Seçilen dönemde eklenen işletme</td>
                            </tr>

                            <tr>
                                <td>Toplam İnceleme</td>
                                <td><?= $rep_total_reviews ?></td>
                                <td>Seçilen tarih aralığındaki tüm incelemeler</td>
                            </tr>

                            <tr>
                                <td>Onaylanan İncelemeler</td>
                                <td><?= $rep_approved_reviews ?></td>
                                <td>Status = approved</td>
                            </tr>

                            <tr>
                                <td>Bekleyen İncelemeler</td>
                                <td><?= $rep_pending_reviews ?></td>
                                <td>Status = pending</td>
                            </tr>

                            <tr>
                                <td>Reddedilen İncelemeler</td>
                                <td><?= $rep_rejected_reviews ?></td>
                                <td>Status = rejected</td>
                            </tr>

                            <tr>
                                <td>Toplam Gelir</td>
                                <td>₺0</td>
                                <td>Ödeme sistemi sonrası</td>
                            </tr>

                            <tr>
                                <td>Aktif Abonelik</td>
                                <td>0</td>
                                <td>Paket sistemi sonrası</td>
                            </tr>

                            <tr>
                                <td>Gecikmiş Abonelik</td>
                                <td>0</td>
                                <td>Paket sistemi sonrası</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>



            <!-- En Çok Yorum Alan İşletmeler -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <strong>En Çok Yorum Alan İşletmeler (Top 10)</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0 table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>İşletme</th>
                                <th style="width:150px;">Yorum</th>
                                <th style="width:150px;">Puan</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if (empty($top_companies)): ?>
                                <tr><td colspan="3">—</td></tr>
                            <?php else: ?>
                                <?php foreach ($top_companies as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= $row['total_reviews'] ?></td>
                                        <td><?= $row['avg_rating'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>



            <!-- En Çok Yorum Alan Kategoriler -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <strong>En Çok Yorum Alan Kategoriler (Top 10)</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0 table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Kategori</th>
                                <th style="width:150px;">Yorum Sayısı</th>
                                <th style="width:150px;">Ortalama Puan</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if (empty($top_categories)): ?>
                                <tr><td colspan="3">—</td></tr>
                            <?php else: ?>
                                <?php foreach ($top_categories as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['category_name']) ?></td>
                                        <td><?= $row['total_reviews'] ?></td>
                                        <td><?= $row['avg_rating'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>




                </div>
            </div>
        </div>
        <!-- =================================== -->

    </div>

    <!-- Footer -->
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>© <?php echo date('Y'); ?> Puandeks</span>
            </div>
        </div>
    </footer>
</div>
</div>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
