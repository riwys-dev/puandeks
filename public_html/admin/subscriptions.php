<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: https://puandeks.com/admin-login");
    exit;
}


require_once('/home/puandeks.com/backend/config.php');

// Admin info
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
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puandeks Admin - Abonelikler</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png">
</head>
<body id="page-top">

<div id="wrapper">

<?php include('admin-sidebar.php'); ?>

<div id="content-wrapper" class="d-flex flex-column">
  <div id="content">

    <?php include('includes/topbar.php'); ?>

    <div class="container-fluid">
      <h1 class="h3 mb-4 text-gray-800">Abonelikler(Onaylı işletmeler)</h1>

      <div style="background:#fff; padding:24px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.05);">

        <!-- SEARCH -->
        <div style="margin-bottom:16px;">
          <label><strong>İşletme Ara:</strong></label>
          <input
            type="text"
            id="companySearch"
            class="form-control"
            placeholder="İşletme adı yazın..."
            style="max-width:300px; display:inline-block; margin-left:10px;"
          >
        </div>

        <!-- TABLE -->
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead style="background-color:#f8f9fc;">
              <tr>
                <th>İşletme Adı</th>
                <th>Paket</th>
                <th>Başlangıç</th>
                <th>Bitiş</th>
                <th>Durum</th>
                <th>Ödeme</th>
                <th>Fatura No</th>
                <th>Hatırlatma</th>
              </tr>
            </thead>
            <tbody id="subscriptionTableBody">
              <!-- JS -->
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>

  <footer class="sticky-footer bg-white">
    <div class="container my-auto">
      <div class="copyright text-center my-auto">
        <span>© <?php echo date('Y'); ?> Puandeks</span>
      </div>
    </div>
  </footer>

</div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

  const tableBody   = document.getElementById("subscriptionTableBody");
  const searchInput = document.getElementById("companySearch");

  function loadCompanies(search = null) {

    let url = "api/get-company-subscriptions.php";
    if (search) url += "?search=" + encodeURIComponent(search);

    fetch(url)
      .then(res => res.json())
      .then(data => {

        tableBody.innerHTML = "";

        if (!data.companies || data.companies.length === 0) {
          tableBody.innerHTML = `
            <tr>
              <td colspan="8" class="text-center text-muted">Sonuç bulunamadı</td>
            </tr>`;
          return;
        }

        data.companies.forEach(company => {

          const packageText = company.package_type
            ? (company.package_type === "free" ? "Ücretsiz" : company.package_type)
            : "-";

          const start = company.subscription_start || "-";
          const end   = company.subscription_end || "-";

          let statusBadge = '<span class="badge badge-secondary">-</span>';

          if (!company.package_type) {
            statusBadge = '<span class="badge badge-secondary">Ücretsiz</span>';
          } else {
            if (company.subscription_status === "active") {
              statusBadge = '<span class="badge badge-success">Aktif</span>';
            } else if (company.subscription_status === "frozen") {
              statusBadge = '<span class="badge badge-warning">Donduruldu</span>';
            } else if (company.subscription_status === "expired") {
              statusBadge = '<span class="badge badge-danger">Süre Doldu</span>';
            }
          }

          const paymentCell = `
            <span class="badge badge-info">
              ${company.payment_amount || "0"} ₺
            </span>`;

          const reminderCell = `
            <button class="btn btn-sm btn-outline-primary send-reminder-btn"
              data-company-id="${company.id}">
              Gönder
            </button>`;

          const invoiceCell = company.invoice_no && company.pdf_url
  ? `<a href="${company.pdf_url}" target="_blank" style="color:#10b981;font-weight:600;">
      ${company.invoice_no}
    </a>`
            : `
              <div style="display:flex; gap:6px; align-items:center;">
                
                <input type="text" class="form-control form-control-sm invoice-input"
                  placeholder="Fatura No" style="max-width:120px;">

                <input type="date" class="form-control form-control-sm invoice-date"
                  style="max-width:140px;">

                <input type="file" class="form-control form-control-sm invoice-file"
                  accept=".pdf" style="max-width:180px;">

                <button class="btn btn-sm btn-success save-invoice-btn"
                  data-company-id="${company.id}">
                  Kaydet
                </button>

              </div>`;

          const row = document.createElement("tr");

          row.innerHTML = `
            <td>${company.name}</td>
            <td>${packageText}</td>
            <td>${start}</td>
            <td>${end}</td>
            <td>${statusBadge}</td>
            <td>${paymentCell}</td>
            <td>${invoiceCell}</td>
            <td>${reminderCell}</td>
          `;

          tableBody.appendChild(row);
        });

      })
      .catch(() => {
        tableBody.innerHTML = `
          <tr>
            <td colspan="8" class="text-center text-danger">
              Veri yüklenemedi
            </td>
          </tr>`;
      });
  }

  loadCompanies();

  searchInput.addEventListener("input", function () {
    const val = this.value.trim();
    loadCompanies(val || null);
  });

  tableBody.addEventListener("click", function (e) {

    const mailBtn = e.target.closest(".send-reminder-btn");
    if (mailBtn) {

      const companyId = mailBtn.dataset.companyId;

      fetch("api/send-reminder-mail.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "company_id=" + encodeURIComponent(companyId)
      })
      .then(res => res.json())
      .then(resp => {
        alert(resp.success ? "Mail gönderildi" : "Mail gönderilemedi");
      });

      return;
    }

    const saveBtn = e.target.closest(".save-invoice-btn");
    if (!saveBtn) return;

    const row = saveBtn.closest("tr");

    const companyId = saveBtn.dataset.companyId;
    const invoiceNo = row.querySelector(".invoice-input").value.trim();
    const date      = row.querySelector(".invoice-date").value;
    const file      = row.querySelector(".invoice-file").files[0];

    if (!invoiceNo || !date || !file) {
      alert("Tüm alanlar zorunlu.");
      return;
    }

    const formData = new FormData();
    formData.append("company_id", companyId);
    formData.append("invoice_number", invoiceNo);
    formData.append("issue_date", date);
    formData.append("invoice_file", file);

    saveBtn.disabled = true;
    saveBtn.innerText = "Kaydediliyor...";

fetch("api/upload-invoice.php", {
  method: "POST",
  body: formData
})
.then(res => res.text())
.then(text => {
  console.log("UPLOAD RESPONSE:", text);

  try {
    const resp = JSON.parse(text);

    if (resp.success) {
      alert("Fatura kaydedildi");
      loadCompanies();
    } else {
      alert(resp.message || "Hata oluştu");
    }

  } catch (e) {
    alert("JSON parse edilemedi:\n" + text);
  }
})
.catch(() => {
  alert("Sunucu hatası");
})
.finally(() => {
  saveBtn.disabled = false;
  saveBtn.innerText = "Kaydet";
});

  });

});
</script>




</body>
</html>
