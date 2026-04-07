<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

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


// -----------------------------------------------------------
//  Pagination + Arama (Kategori adına göre)
// -----------------------------------------------------------

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 10;
$offset = ($page - 1) * $limit;

$params = [];
$filterSql = "";

// Arama filtresi
if ($search !== '') {
    $filterSql = " WHERE c.name LIKE :search ";
    $params['search'] = "%$search%";
}

// Toplam kayıt sayısı
$countQuery = "SELECT COUNT(*) FROM categories c" . $filterSql;
$stmtCount = $pdo->prepare($countQuery);
$stmtCount->execute($params);
$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Listeyi ekiyoruz
$listQuery = "
    SELECT 
        c.id,
        c.name,
        c.icon_class,
        (SELECT COUNT(*) FROM companies WHERE category_id = c.id) AS company_count
    FROM categories c
    $filterSql
    ORDER BY c.name ASC
    LIMIT $limit OFFSET $offset
";


$stmtList = $pdo->prepare($listQuery);

// Parametreleri bağla
foreach ($params as $key => $value) {
    $stmtList->bindValue(":$key", $value);
}

$stmtList->execute();
$categoryRows = $stmtList->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - Kategoriler</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png">

    <style>
    .icon-item{font-size:22px!important;padding:14px!important;border:1px solid #e0e0e0!important;border-radius:8px!important;cursor:pointer!important;text-align:center!important;}
    .icon-item:hover{background:#f5f7ff!important;}
    .icon-item.selected{border-color:#4e73df!important;background:#eef2ff!important;}
    </style>

    
</head>
<body id="page-top">
<div id="wrapper">

    <?php include('admin-sidebar.php'); ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">
            <?php include('includes/topbar.php'); ?>

            <div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Kategoriler</h1>
     <!-- Arama -->
        <form method="GET" style="display: flex; gap: 12px; max-width: 320px; margin-bottom: 20px;">
            <input 
                type="text" 
                name="search" 
                value="<?= htmlspecialchars($search) ?>" 
                class="form-control" 
                placeholder="Kategori Ara..."
            >
            <button class="btn btn-primary">Ara</button>
        </form>

  <div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
    
    <!-- Yeni Kategori Ekleme -->
    <div style="margin-bottom: 30px;">
      <h5>Yeni Kategori Ekle +</h5>
      <div class="form-inline">
        <input type="text" class="form-control mr-2" id="newCategoryName" placeholder="Kategori adı girin" />
        <button class="btn btn-primary" id="addCategoryBtn"> + Ekle</button>
      </div>
    </div>
    <!-- Yeni Kategori Ekleme -->
    


    <!-- Kategori Tablosu -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead style="background-color: #f8f9fc;">
          <tr>
            <th>Kategori Adı</th>
            <th>ID</th>
            <th>İşletme Sayısı</th>
            <th>İşlem</th>
          </tr>
        </thead>

        <tbody>
        <?php if (empty($categoryRows)): ?>
            <tr><td colspan="4" class="text-center">Kategori bulunamadı.</td></tr>
        <?php else: ?>
            <?php foreach ($categoryRows as $cat): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= $cat['id'] ?></td>
                    <td><?= $cat['company_count'] ?></td>
                    <td>
                        <button class="btn btn-sm btn-secondary"
                          onclick="editCategory(
                            <?= $cat['id'] ?>,
                            '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($cat['icon_class'] ?? '', ENT_QUOTES) ?>'
                          )">
                          Düzenle
                        </button>


                        <button class="btn btn-sm btn-info" onclick="moveCompanies(<?= $cat['id'] ?>)">İşletmeleri Taşı</button>

                        <button class="btn btn-sm btn-danger delete-category-btn" data-id="<?= $cat['id'] ?>" style="font-size:13px;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>

      </table>
    </div>

    <!-- Sayfalama -->
      <?php if ($search === ''): ?>
      <nav>
        <ul class="pagination justify-content-center">

          <?php
            $isDisabled = ($totalPages <= 1);
            $searchParam = ($search !== '') ? '&search=' . urlencode($search) : '';
          ?>

          <!-- İlk -->
          <li class="page-item <?= $isDisabled ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $isDisabled ? '#' : '?page=1' . $searchParam ?>">İlk</a>
          </li>

          <!-- Sayı numaraları (SON HARİÇ) -->
          <?php for ($i = 1; $i < $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i . $searchParam ?>">
                <?= $i ?>
              </a>
            </li>
          <?php endfor; ?>

          <!-- Son -->
          <?php if ($totalPages > 1): ?>
            <li class="page-item <?= ($page == $totalPages) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $totalPages . $searchParam ?>">
                Son
              </a>
            </li>
          <?php endif; ?>

        </ul>
      </nav>
      <?php endif; ?>
      <!-- Sayfalama -->

      <!-- Aramayı Temizle (sadece arama varsa görünür) -->
      <?php if ($search !== ''): ?>
          <div class="text-center" style="margin-top: 15px;">
              <a href="?page=1" class="btn btn-secondary">Aramayı Temizle</a>
          </div>
      <?php endif; ?>


  </div>
</div>


        </div>
        <!-- End of Main Content -->


        <!-- Footer -->
        <footer class="sticky-footer bg-white mt-auto">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>© <?php echo date('Y'); ?> Puandeks</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Category edit Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Kategori Düzenle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="editCategoryId">

        <div class="form-group" style="margin-bottom:14px!important;">
          <label style="font-weight:600!important;margin-bottom:6px!important;display:block!important;">
            Kategori Adı
          </label>
          <input type="text" class="form-control" id="editCategoryName">
        </div>

        <div class="form-group" style="margin-bottom:14px!important;">
          <label style="font-weight:600!important;margin-bottom:6px!important;display:block!important;">
            İkon
          </label>
          <button
            type="button"
            class="btn btn-outline-primary btn-sm"
            id="openIconLibrary"
            style="padding:6px 14px!important;font-size:13px!important;border-radius:6px!important;">
            İkon Seç
          </button>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" disabled>Kaydet</button>
      </div>

  </div>
</div>
<!-- Category edit Modal -->

<!-- Icaon libary -->
<div class="modal fade" id="iconLibraryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">İkon Seç</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;">
         
          <i class="fas fa-car-side icon-item"></i>
          <i class="fas fa-shipping-fast icon-item"></i>
          <i class="fas fa-gamepad icon-item"></i>  
          <i class="fas fa-shopping-basket icon-item"></i>
          <i class="fas fa-store-alt icon-item"></i>
          <i class="fas fa-laptop-house icon-item"></i>
          <i class="fas fa-ring icon-item"></i>
          <i class="fas fa-couch icon-item"></i>
          <i class="fas fa-bullhorn icon-item"></i>
          <i class="fas fa-play-circle icon-item"></i>
          <i class="fas fa-ticket-alt icon-item"></i>
          <i class="fas fa-gem icon-item"></i>
          <i class="fas fa-coins icon-item"></i>
          <i class="fas fa-server icon-item"></i>
          <i class="fas fa-credit-card icon-item"></i>
          <i class="fas fa-dice icon-item"></i>
          <i class="fas fa-mobile-alt icon-item"></i>
          <i class="fas fa-robot icon-item"></i>
          <i class="fas fa-code icon-item"></i>
          

          
          <i class="fas fa-car icon-item"></i>
          <i class="fas fa-home icon-item"></i>
          <i class="fas fa-store icon-item"></i>
          <i class="fas fa-utensils icon-item"></i>
          <i class="fas fa-heartbeat icon-item"></i>
          <i class="fas fa-laptop icon-item"></i>

          <i class="fas fa-dumbbell icon-item"></i>
          <i class="fas fa-shopping-bag icon-item"></i>
          <i class="fas fa-graduation-cap icon-item"></i>
          <i class="fas fa-briefcase icon-item"></i>
          <i class="fas fa-wrench icon-item"></i>
          <i class="fas fa-hospital icon-item"></i>

          <i class="fas fa-truck icon-item"></i>
          <i class="fas fa-plane icon-item"></i>
          <i class="fas fa-hotel icon-item"></i>
          <i class="fas fa-mobile-alt icon-item"></i>
          <i class="fas fa-tv icon-item"></i>
          <i class="fas fa-headphones icon-item"></i>

          <i class="fas fa-book icon-item"></i>
          <i class="fas fa-gem icon-item"></i>
          <i class="fas fa-tshirt icon-item"></i>
          <i class="fas fa-cut icon-item"></i>
          <i class="fas fa-book-open icon-item"></i>
          <i class="fas fa-spa icon-item"></i>
          <i class="fas fa-chair icon-item"></i>
          <i class="fas fa-print icon-item"></i>
          <i class="fas fa-balance-scale icon-item"></i>
          <i class="fas fa-snowflake icon-item"></i>
          <i class="fas fa-bolt icon-item"></i>
          <i class="fas fa-leaf icon-item"></i>

          <i class="fas fa-paw icon-item"></i>
          <i class="fas fa-futbol icon-item"></i>
          <i class="fas fa-music icon-item"></i>
          <i class="fas fa-film icon-item"></i>

          <!-- +70 -->

          <i class="fas fa-bicycle icon-item"></i>
          <i class="fas fa-bus icon-item"></i>
          <i class="fas fa-subway icon-item"></i>
          <i class="fas fa-ship icon-item"></i>
          <i class="fas fa-gas-pump icon-item"></i>

          <i class="fas fa-coffee icon-item"></i>
          <i class="fas fa-pizza-slice icon-item"></i>
          <i class="fas fa-ice-cream icon-item"></i>
          <i class="fas fa-hamburger icon-item"></i>

          <i class="fas fa-clinic-medical icon-item"></i>
          <i class="fas fa-user-md icon-item"></i>
          <i class="fas fa-tooth icon-item"></i>
          <i class="fas fa-eye icon-item"></i>

          <i class="fas fa-tools icon-item"></i>
          <i class="fas fa-hammer icon-item"></i>
          <i class="fas fa-paint-roller icon-item"></i>
          <i class="fas fa-hard-hat icon-item"></i>

          <i class="fas fa-camera icon-item"></i>
          <i class="fas fa-video icon-item"></i>
          <i class="fas fa-microphone icon-item"></i>
          <i class="fas fa-broadcast-tower icon-item"></i>

          <i class="fas fa-code icon-item"></i>
          <i class="fas fa-server icon-item"></i>
          <i class="fas fa-database icon-item"></i>
          <i class="fas fa-cloud icon-item"></i>

          <i class="fas fa-shield-alt icon-item"></i>
          <i class="fas fa-lock icon-item"></i>
          <i class="fas fa-key icon-item"></i>

          <i class="fas fa-tree icon-item"></i>
          <i class="fas fa-seedling icon-item"></i>
          <i class="fas fa-sun icon-item"></i>
          <i class="fas fa-moon icon-item"></i>

          <i class="fas fa-child icon-item"></i>
          <i class="fas fa-users icon-item"></i>
          <i class="fas fa-user-friends icon-item"></i>

          <i class="fas fa-gift icon-item"></i>
          <i class="fas fa-tags icon-item"></i>
          <i class="fas fa-percent icon-item"></i>

          <i class="fas fa-chart-line icon-item"></i>
          <i class="fas fa-chart-bar icon-item"></i>
          <i class="fas fa-wallet icon-item"></i>
          <i class="fas fa-credit-card icon-item"></i>

          <i class="fas fa-map-marker-alt icon-item"></i>
          <i class="fas fa-compass icon-item"></i>
          <i class="fas fa-globe icon-item"></i>


        </div>
      </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
      </div>

    </div>
  </div>
</div>
<!-- Icaon libary -->

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<script>
// ===============================
// Category Management (UPDATED)
// ===============================
function editCategory(id, name, iconClass) {
  document.getElementById("editCategoryId").value = id;
  document.getElementById("editCategoryName").value = name;

  // icon state
  selectedIconClass = iconClass || null;

  // preview alanı
  let preview = document.getElementById("selectedIconPreview");
  if (!preview) {
    preview = document.createElement("div");
    preview.id = "selectedIconPreview";
    preview.style.marginTop = "10px";
    preview.style.fontSize = "26px";
    document
      .querySelector("#editCategoryModal .modal-body")
      .appendChild(preview);
  }

  // eğer ikon varsa göster
  if (iconClass) {
    preview.innerHTML = `<i class="${iconClass}"></i>`;
    document.querySelector("#editCategoryModal .btn-primary").disabled = false;
  } else {
    preview.innerHTML = "";
    document.querySelector("#editCategoryModal .btn-primary").disabled = true;
  }

  $('#editCategoryModal').modal('show');
}
</script>

<script>
// ===============================
// Move C -
// ===============================
function moveCompanies(id) {
  const newId = prompt("İşletmeleri taşımak istediğiniz hedef kategori ID'sini girin:");
  if (!newId || isNaN(newId)) return;

  fetch("api/move-companies-to-category.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `from=${id}&to=${newId}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      alert("İşletmeler taşındı.");
      location.reload();
    } else {
      alert("Taşıma işlemi başarısız.");
    }
  });
}

// ===============================
// Delete Category 
// ===============================
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".delete-category-btn");
  if (!btn) return;

  const id = btn.dataset.id;

  const confirmCheck = confirm("Kategoriyi silmek istediğinizden emin misiniz?");
  if (!confirmCheck) return;

  fetch("api/delete-category.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${id}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === "has_companies") {
        alert("Bu kategoride işletme mevcut. Önce işletmeleri taşıyın veya silin.");
      } else if (data.status === "no_companies") {
        const secondConfirm = confirm("Bu kategoride işletme bulunmuyor. Yine de silmek istiyor musunuz?");
        if (!secondConfirm) return;

        fetch("api/delete-category.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${id}&force=1`
        })
        .then(r => r.json())
        .then(result => {
          if (result.status === "success") {
            alert("Kategori başarıyla silindi.");
            location.reload();
          } else {
            alert(result.message || "Silme işlemi başarısız.");
          }
        });
      } else {
        alert(data.message || "İşlem başarısız.");
      }
    });
});

// ===============================
// ADD Category
// ===============================
document.addEventListener("DOMContentLoaded", function () {
  const addBtn = document.getElementById("addCategoryBtn");
  const nameInput = document.getElementById("newCategoryName");

  addBtn.addEventListener("click", function () {
    const name = nameInput.value.trim();
    if (!name) {
      alert("Lütfen bir kategori adı girin.");
      return;
    }

    fetch("api/add-category.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name })
    })
      .then(r => r.json())
      .then(data => {
        if (data.status === "success") {
          location.reload();
        } else {
          alert(data.message || "Kategori eklenemedi.");
        }
      });
  });
});

document.getElementById("openIconLibrary").addEventListener("click", function () {
  $('#iconLibraryModal').modal('show');
});
</script>

<script>
let selectedIconClass = null;

document.addEventListener("click", function (e) {
  const icon = e.target.closest(".icon-item");
  if (!icon) return;

  // clear previous selection
  document.querySelectorAll(".icon-item").forEach(i => i.classList.remove("selected"));
  icon.classList.add("selected");

  // get icon class (e.g. "fas fa-cut")
  selectedIconClass = Array.from(icon.classList)
    .filter(c => c !== "icon-item" && c !== "selected")
    .join(" ");

  // preview inside edit modal
  let preview = document.getElementById("selectedIconPreview");
  if (!preview) {
    preview = document.createElement("div");
    preview.id = "selectedIconPreview";
    preview.style.marginTop = "10px";
    preview.style.fontSize = "26px";
    document.querySelector("#editCategoryModal .modal-body").appendChild(preview);
  }

  preview.innerHTML = `<i class="${selectedIconClass}"></i>`;

  // enable save button
  const saveBtn = document.querySelector("#editCategoryModal .btn-primary");
  if (saveBtn) saveBtn.disabled = false;

  // close icon modal
  $('#iconLibraryModal').modal('hide');
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const saveBtn = document.querySelector("#editCategoryModal .btn-primary");

  if (!saveBtn) return;

  saveBtn.addEventListener("click", function () {
    const id = document.getElementById("editCategoryId").value;
    const name = document.getElementById("editCategoryName").value.trim();

    if (!id || !name || !selectedIconClass) {
      alert("Kategori adı ve ikon seçilmelidir.");
      return;
    }

    fetch("api/update-category.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body:
        "id=" + encodeURIComponent(id) +
        "&name=" + encodeURIComponent(name) +
        "&icon_class=" + encodeURIComponent(selectedIconClass)
    })
    .then(r => r.json())
    .then(data => {
      if (data.status === "success") {
        $('#editCategoryModal').modal('hide');
        location.reload();
      } else {
        alert(data.message || "Güncelleme başarısız.");
      }
    });
  });
});
</script>


</body>
</html>
