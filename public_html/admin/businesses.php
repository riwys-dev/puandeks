<?php
session_start();

// Status filter bugfix
if (isset($_GET['status']) && $_GET['status'] === '') {
    unset($_GET['status']);
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

// --- Admin bilgisi ---
$admin_id = $_SESSION['admin_id'];
$admin_name = 'Admin';

$stmt = $pdo->prepare("SELECT full_name FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    $admin_name = $admin['full_name'];
}

$notifStmt = $pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0");
$unreadCount = $notifStmt->fetchColumn();

// --- Filtreler ---
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$params = [];
$whereSql = "";

// Durum filtre
if (in_array($status, ['approved', 'pending', 'rejected'])) {
    $whereSql .= " AND c.status = :status";
    $params['status'] = $status;
}

// Arama filtre
if (!empty($search)) {
    $whereSql .= " AND c.name LIKE :search";
    $params['search'] = '%' . $search . '%';
}

// --- Sayfalama ---
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Toplam kayıt
$countStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM companies c
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE 1=1 $whereSql
");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

// C
$sql = "
    SELECT 
        c.id, 
        c.slug,
        c.name, 
        c.logo, 
        c.status, 
        c.category_id,
        cat.name AS category_name,
        c.documents
    FROM companies c
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE 1=1 $whereSql
    ORDER BY c.id DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

// Bind 
foreach ($params as $key => $value) {
    $stmt->bindValue(":$key", $value);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kategoriler ---
$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$allCategories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - İşletmeler</title>
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
          
<!-- container-fluid -->
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">İşletmeler</h1>

<!-- Arama ve Filtre -->
  <?php
$currentStatus = $_GET['status'] ?? '';
function selected($value, $current) {
  return $value === $current ? 'selected' : '';
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
<!-- Search box -->
  <form method="GET" style="display: flex; gap: 16px; align-items: center; margin-bottom: 24px;">
  <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="İşletme adıyla ara..." style="max-width: 300px;">
  <button class="btn btn-primary">Filtrele</button>
  <a href="businesses.php" class="btn btn-secondary" style="display: inline-block; height: 38px; line-height: 24px; white-space: nowrap;">Filtreyi Temizle</a>
</form>
<!-- Search box -->

<!-- Otomatik Filtre -->
  <select class="form-control" style="max-width: 200px;" onchange="location = '?status=' + this.value;">
    <option value="" <?= selected('', $currentStatus) ?>>Hepsi</option>
    <option value="approved" <?= selected('approved', $currentStatus) ?>>Onaylandı</option>
    <option value="pending" <?= selected('pending', $currentStatus) ?>>Beklemede</option>
    <option value="rejected" <?= selected('rejected', $currentStatus) ?>>Reddedildi</option>
  </select>
</div>
<!-- Otomatik Filtre -->

  <!-- İşletme Tablosu -->
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th>Logo</th>
              <th>İşletme Adı</th>
              <th>Kategori</th>
              <th>Belgeler</th>
              <th>Durum</th>
              <th>Profil</th>
              <th>İşlemler</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($companies)): ?>
              <tr><td colspan="5" class="text-center">Hiç işletme bulunamadı.</td></tr>
            <?php endif; ?>

            <?php foreach ($companies as $company): ?>
            <tr>
              <td style="display:flex; align-items:center; gap:8px;">
                <img 
                    src="<?= $company['logo'] ?: 'img/placeholder/company-profile.png' ?>" 
                    alt="Logo" 
                    height="40"
                    id="company-logo-<?= $company['id'] ?>"
                >

                <i 
                    class="fas fa-pen"
                    style="cursor:pointer; color:#6c757d;"
                    title="Logo değiştir"
                    data-company-id="<?= $company['id'] ?>"
                ></i>

                <input 
                    type="file"
                    accept="image/png,image/jpeg,image/webp"
                    style="display:none"
                    id="logo-input-<?= $company['id'] ?>"
                >
                </td>

              
              <td class="editable-name" data-id="<?= $company['id'] ?>">
                  <?= htmlspecialchars($company['name']) ?>
              </td>

              <td class="editable-category" data-id="<?= $company['id'] ?>" data-category-id="<?= $company['category_id'] ?>">
                  <?= htmlspecialchars($company['category_name']) ?>
              </td>


<td>
  <?php if (!empty($company['documents'])): ?>
    
    <?php 
      $docs = json_decode($company['documents'], true);

      if (!is_array($docs)) {
        $docs = [];
      }

      $map = [
        'vergi' => 'Vergi',
        'faaliyet' => 'Faaliyet',
        'sicil' => 'Sicil'
      ];

      $count = 0;
    ?>

    <?php foreach ($map as $k => $label): ?>
      
      <?php if (!empty($docs[$k])): ?>
        <?php 
          $link = $docs[$k];
          $count++;
        ?>

        <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
          
          <a href="<?= htmlspecialchars($link) ?>?v=<?= time() ?>" 
             target="_blank" 
             style="text-decoration:none; color:#000; display:flex; align-items:center; gap:6px;">
            
            <i class="fas fa-file-alt"></i>
            <span><?= $label ?></span>

          </a>

        </div>

      <?php endif; ?>

    <?php endforeach; ?>

    <?php if ($count < 3): ?>
      <span style="color:#dc3545; font-size:12px;">
        <?= 3 - $count ?> belge eksik
      </span>
    <?php endif; ?>

  <?php else: ?>
    <span class="badge badge-danger">Yok</span>
  <?php endif; ?>
</td>





              
               <td>
                <?php if ($company['status'] === 'approved'): ?>
                  <span class="badge badge-success">Onaylandı</span>

                <?php elseif ($company['status'] === 'rejected'): ?>
                  <span class="badge badge-danger">Reddedildi</span>

                <?php else: ?>
                  <span class="badge badge-warning">Beklemede</span>
                <?php endif; ?>
              </td>

              <td>
                <a href="https://puandeks.com/company/<?= htmlspecialchars($company['slug'], ENT_QUOTES, 'UTF-8') ?>"
                target="_blank"
                class="btn btn-sm btn-primary">
                Görüntüle
               </a>

              </td>
              
              <td style="text-align:center;">

                <?php if ($company['status'] === 'approved'): ?>

                    <!-- Onaylanmış işletme -->
                    <button class="btn btn-sm btn-success" disabled>Onayla</button>
                    <button class="btn btn-sm btn-danger update-status" 
                            data-id="<?= $company['id'] ?>" 
                            data-status="rejected">
                        Reddet
                    </button>

                <?php elseif ($company['status'] === 'rejected'): ?>

                    <!-- Reddedilmiş işletme -->
                    <button class="btn btn-sm btn-success update-status" 
                            data-id="<?= $company['id'] ?>" 
                            data-status="approved">
                        Onayla
                    </button>
                    <button class="btn btn-sm btn-danger" disabled>Reddet</button>

                <?php else: ?>

                    <!-- Beklemede işletme -->
                    <button class="btn btn-sm btn-success update-status" 
                            data-id="<?= $company['id'] ?>" 
                            data-status="approved">
                        Onayla
                    </button>
                    <button class="btn btn-sm btn-danger update-status" 
                            data-id="<?= $company['id'] ?>" 
                            data-status="rejected">
                        Reddet
                    </button>

                <?php endif; ?>

            </td>


            </tr>
            <?php endforeach; ?>
            
            </tbody>
        </table>
      </div>

      
      <?php if ($totalPages > 1): ?>
      <nav>
        <ul class="pagination justify-content-center w-100">
          <?php
          $query = $_GET;

          // İLK
          if ($page > 1) {
              $query['page'] = 1;
              echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query) . '">İlk</a></li>';
          }

          // GERİ
          if ($page > 1) {
              $query['page'] = $page - 1;
              echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query) . '">&laquo;</a></li>';
          }

          // ORTA SAYFALAR
          $visiblePages = 5;
          $start = max(1, $page - 2);
          $end = min($totalPages, $page + 2);

          for ($i = $start; $i <= $end; $i++) {
              $query['page'] = $i;
              $active = ($i == $page) ? 'active' : '';
              echo '<li class="page-item ' . $active . '">
                      <a class="page-link" href="?' . http_build_query($query) . '">' . $i . '</a>
                    </li>';
          }

          // İLERİ
          if ($page < $totalPages) {
              $query['page'] = $page + 1;
              echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query) . '">&raquo;</a></li>';
          }

          // SON
          if ($page < $totalPages) {
              $query['page'] = $totalPages;
              echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query) . '">Son</a></li>';
          }
          ?>

          </ul>
      </nav>
      <?php endif; ?>


    </div>
  </div>

</div>
<!-- /container-fluid -->


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


<script>
window.allCategories = <?= json_encode($allCategories) ?>;
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {

/* STATUS UPDATE  */
    document.querySelectorAll('.update-status').forEach(button => {
        button.addEventListener('click', function() {

            const companyId = this.getAttribute('data-id');
            const newStatus = this.getAttribute('data-status');

            if (!confirm("Bu işlemi onaylıyor musunuz?")) return;

            $.post(
                'api/update-company-status.php',
                { company_id: companyId, status: newStatus },
                function (data) {
                    alert(data.message);
                    if (data.success) window.location.href = "businesses";
                },
                'json'
            );
        });
    });



/*  INLINE EDIT (C Name) */
    document.querySelectorAll('.editable-name').forEach(cell => {

        cell.addEventListener('click', function() {

            if (this.querySelector('input')) return;

            const currentText = this.innerText.trim();
            const companyId = this.dataset.id;

            const input = document.createElement('input');
            input.type = "text";
            input.value = currentText;
            input.className = "form-control";
            input.style.minWidth = "180px";

            this.innerHTML = "";
            this.appendChild(input);
            input.focus();

            
            input.addEventListener("blur", function() {
                saveInlineEdit(companyId, "name", input.value, cell);
            });

            input.addEventListener("keydown", function(e) {
                if (e.key === "Enter") {
                    saveInlineEdit(companyId, "name", input.value, cell);
                }
            });

        });

    });



/*  INLINE EDIT (Cat) */
    document.querySelectorAll('.editable-category').forEach(cell => {

        cell.addEventListener('click', function() {

            if (this.querySelector('select')) return;

            const companyId = this.dataset.id;
            const currentCat = this.dataset.categoryId;

            const select = document.createElement('select');
            select.className = "form-control";
            select.style.minWidth = "200px";

            window.allCategories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.id;
                opt.text = cat.name;
                if (cat.id == currentCat) opt.selected = true;
                select.appendChild(opt);
            });

            this.innerHTML = "";
            this.appendChild(select);
            select.focus();

            select.addEventListener("blur", function() {
                saveInlineEdit(companyId, "category_id", select.value, cell);
            });

            select.addEventListener("change", function() {
                saveInlineEdit(companyId, "category_id", select.value, cell);
            });

        });

    });



/* INLINE EDIT SAVE FUNCTION */
    function saveInlineEdit(companyId, field, value, cell) {

        $.post(
            "api/update-company-inline.php",
            { company_id: companyId, field: field, value: value },
            function (data) {

                if (!data.success) {
                    alert(data.message);
                    return;
                }

                // UI güncelle
                cell.innerHTML = data.new_value;

            },
            "json"
        );
    }

});
</script>

<!-- C logo change -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  document.querySelectorAll('.fa-pen').forEach(icon => {
    icon.addEventListener('click', function () {
      const companyId = this.getAttribute('data-company-id');
      const input = document.getElementById('logo-input-' + companyId);
      if (input) input.click();
    });
  });

  document.querySelectorAll('input[type="file"][id^="logo-input-"]').forEach(input => {
    input.addEventListener('change', function () {

      if (!this.files || !this.files[0]) return;

      const file = this.files[0];
      const companyId = this.id.replace('logo-input-', '');

      // 1 MB kontrol
      if (file.size > 1024 * 1024) {
        alert("Dosya boyutu en fazla 1 MB olabilir.");
        this.value = "";
        return;
      }

      // Format kontrol
      const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
      if (!allowedTypes.includes(file.type)) {
        alert("Sadece JPG, PNG veya WEBP yükleyebilirsiniz.");
        this.value = "";
        return;
      }

      const formData = new FormData();
      formData.append('company_id', companyId);
      formData.append('logo', file);

      fetch('api/update-company-logo.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert(data.message);
          return;
        }

        // Logo anında güncelle
        const img = document.getElementById('company-logo-' + companyId);
        if (img) {
          img.src = data.logo + '?t=' + Date.now(); // cache kır
        }
      })
      .catch(() => {
        alert('Logo yüklenirken hata oluştu');
      });

    });
  });

});
</script>
<!-- C logo change -->

</body>
</html>
