<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: /business-login");
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$PACKAGE_STATUS = 'free';

$api_url = "https://business.puandeks.com/api/get-company-subscription.php?company_id=" . $_SESSION['company_id'];
$response = file_get_contents($api_url);
$decoded = json_decode($response, true);

if ($decoded && $decoded['success'] && !empty($decoded['data'])) {
    $PACKAGE_STATUS = $decoded['data']['status'];
}

/* business name */
if (!isset($_SESSION['company_name'])) {
    $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
    $stmt->execute([$_SESSION['company_id']]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['company_name'] = $company['name'] ?? 'İşletme';
}

$BUSINESS_NAME = $_SESSION['company_name'] ?? 'İşletme';

/* auto reply + review invite settings */
$stmt = $pdo->prepare("
    SELECT auto_reply_enabled,
           auto_reply_message,
           invite_enabled,
           invite_delay_days,
           invite_frequency_days,
           reminder_enabled,
           reminder_delay_days
    FROM companies
    WHERE id = ?
");
$stmt->execute([$_SESSION['company_id']]);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

/* Auto Reply */
$autoReplyEnabled = (int)($settings['auto_reply_enabled'] ?? 0);
$autoReplyMessage = $settings['auto_reply_message'] ?? '';

/* Review Invite */
$inviteEnabled        = (int)($settings['invite_enabled'] ?? 1);
$inviteDelayDays      = (int)($settings['invite_delay_days'] ?? 0);
$inviteFrequencyDays  = (int)($settings['invite_frequency_days'] ?? 0);
$reminderEnabled      = (int)($settings['reminder_enabled'] ?? 0);
$reminderDelayDays    = (int)($settings['reminder_delay_days'] ?? 0);
?>

<script>
window.AUTO_REPLY_ENABLED = <?= $autoReplyEnabled ?>;
window.AUTO_REPLY_MESSAGE = <?= json_encode($autoReplyMessage) ?>;

window.INVITE_ENABLED = <?= $inviteEnabled ?>;
window.INVITE_DELAY_DAYS = <?= $inviteDelayDays ?>;
window.INVITE_FREQUENCY_DAYS = <?= $inviteFrequencyDays ?>;
window.REMINDER_ENABLED = <?= $reminderEnabled ?>;
window.REMINDER_DELAY_DAYS = <?= $reminderDelayDays ?>;
window.PACKAGE_STATUS = "<?= $PACKAGE_STATUS ?>";
</script>



<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Puandeks - <?= htmlspecialchars($BUSINESS_NAME, ENT_QUOTES, 'UTF-8') ?> | Otomasyonlar </title>
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link href="css/business-admin.css" rel="stylesheet">
  <link rel="icon" href="img/favicon.png">

<style>
  .filter-dropdown .dropdown-options {
    display: none;
  }

  .filter-dropdown.open .dropdown-options {
    display: block;
  }
</style>

<!-- Switch CSS -->
<style>
  .switch { position: relative; display: inline-block; width: 42px; height: 24px; }
  .switch input { opacity: 0; width: 0; height: 0; }
  .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
  .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
  input:checked + .slider { background-color: #4CAF50; }
  input:checked + .slider:before { transform: translateX(18px); }
  .slider.round { border-radius: 24px; }
  .slider.round:before { border-radius: 50%; }
</style>

<style>
.automation-dropdown .dropdown-select {
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 10px 14px;
  background: #fff;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.automation-dropdown .dropdown-options {
  margin-top: 6px;
  border-radius: 10px;
  overflow: hidden;
}

.automation-dropdown .dropdown-options li {
  padding: 12px 14px;
  cursor: pointer;
}

.automation-dropdown .dropdown-options li:hover {
  background: #f3f4f6;
}

#otomatikMesaj {
  padding-left: 0 !important;
  padding-right: 0 !important;
}

/* Auto Reply Box */
.auto-reply-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:24px; max-width:900px; }
.auto-reply-grid .full { grid-column:1 / span 2; }
@media (max-width:768px){ .auto-reply-grid{ grid-template-columns:1fr; } .auto-reply-grid .full{ grid-column:auto; } }
</style>

<style>
/* Review Invite responsive fix */
@media (max-width: 768px) {

  /* Tüm flex satırlar dikey olsun */
  #tab-review-invite div[style*="display:flex"] {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 8px !important;
  }

  /* Number inputlar full width */
  #tab-review-invite input[type="number"] {
    width: 100% !important;
    max-width: 160px;
  }

  /* İç container padding düşür */
  #tab-review-invite > div {
    padding: 20px !important;
  }

}
</style>

</head>

<body id="page-top">
<div id="wrapper">
  
<!-- Sidebar -->
<?php include("inc/sidebar.php"); ?>
<!-- Sidebar -->
<div id="sidebar-overlay"></div>

<!-- Content -->
<div id="content-wrapper" class="d-flex flex-column" style="margin-left:280px; margin-top:100px; padding:24px; min-height:calc(100vh - 100px); background:#f9fafb;">

<div id="content">

<!-- Topbar -->
    <?php include("inc/topbar.php"); ?>
<!-- Topbar -->

<!-- Main Content -->
<div class="container-fluid">

  <h1 class="h3 mb-4 text-gray-800">Otomasyonlar</h1>

    <!-- Automation Selector -->
    <div class="automation-selector" style="max-width:320px; margin-bottom:24px;">
      <div class="custom-dropdown automation-dropdown">
        <div class="dropdown-select" id="automationSelect">
          <span class="selected-text">Otomatik İnceleme Yanıtı</span>
          <span class="dropdown-arrow">▼</span>
        </div>

        <ul class="dropdown-options" id="automationOptions">
          <li data-target="auto-reply">Otomatik İnceleme Yanıtı</li>
          <li data-target="review-invite">İnceleme Davet Maili</li>
        </ul>
      </div>
    </div>
    <!-- Automation Selector -->


  <!-- TAB: Otomatik Yanıt Ayarları -->
  <div class="automation-content" id="tab-auto-reply">

    <div style="background:#fff; border:1px solid #ddd; border-radius:12px; padding:24px; margin-bottom:40px;">

      <h2 style="font-size:18px; font-weight:bold; margin-bottom:16px; color:#444;">
        Otomatik Yanıtlar 
      </h2>

      <div id="automationLockWarning" style="
        display:none;
        background:#fee2e2;
        color:#991b1b;
        padding:12px 16px;
        border-radius:8px;
        margin-bottom:16px;
        font-size:14px;
      ">
      Bu özellik mevcut paketinize dahil değildir. Kullanmak için paketinizi yükseltin.
    </div>

      <!-- AKTİF / PASİF -->
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
        <label class="switch">
          <input type="checkbox" id="otomatikAktif">
          <span class="slider round"></span>
        </label>
        <label for="otomatikAktif" style="margin:0;">
          Aktif / Pasif yap
        </label>
      </div>

      <small class="text-muted" style="display:block; margin-bottom:20px;">
        * Otomatik yanıtlar, incelemenin verdiği puana göre otomatik gönderilir. <br>
        * Eğer otomatik yanıtları aktif ederseniz gelen incelemeler girdiğiniz cevaplara göre otomatik yanıtlanır. <br>
      Eğer otomatik yanıtları pasif yaparsanız, gelen incelemeleri manual olarak yanıtlayabilirsiniz. 
      </small>

      <!-- GRID -->
      <div class="auto-reply-grid">

        <!-- 1  -->
        <div>
          <div style="margin-bottom:10px;">
          <img src="https://puandeks.com/img/core/vote_1.svg" width="100px">  
          </div>
          
          <textarea class="form-control" rows="3"></textarea>
        </div>

        <!-- 2  -->
        <div>
         <div style="margin-bottom:10px;">
          <img src="https://puandeks.com/img/core/vote_2.svg" width="100px">  
         </div>
          
          <textarea class="form-control" rows="3"></textarea>
        </div>

        <!-- 3  -->
        <div>
          <div style="margin-bottom:10px;">
          <img src="https://puandeks.com/img/core/vote_3.svg" width="100px">  
          </div>
          
           <textarea class="form-control" rows="3"></textarea>
        </div>

        <!-- 4  -->
        <div>
          <div style="margin-bottom:10px;">
          <img src="https://puandeks.com/img/core/vote_4.svg" width="100px">  
          </div>
          
          <textarea class="form-control" rows="3"></textarea>
        </div>

        <!-- 5  -->
        <div class="full">
          <div style="margin-bottom:10px;">
          <img src="https://puandeks.com/img/core/vote_5.svg" width="100px">  
          </div>
          
          <textarea class="form-control" rows="3"></textarea>
        </div>

      </div>

      <!-- KAYDET -->
      <div class="mt-4 text-left">
        <button type="button" class="btn btn-success">
          <i class="fas fa-save"></i> Otomatik yanıtları kaydet
        </button>
      </div>

    </div>


    </div>

  </div>
  <!-- / TAB: Otomatik Yanıt Ayarları -->





<!-- TAB: İnceleme Daveti Ayarları -->
<div class="automation-content" id="tab-review-invite" style="display:none;">

  <div style="background:#fff;border:1px solid #ddd;border-radius:16px;padding:30px;max-width:900px;width:100%;margin-bottom:40px;">

    <h2 style="font-size:18px; font-weight:bold; margin-bottom:20px; color:#444;">
      Otomatik İnceleme Daveti
    </h2>

    <div id="inviteLockWarning" style="
        display:none;
        background:#fee2e2;
        color:#991b1b;
        padding:12px 16px;
        border-radius:8px;
        margin-bottom:16px;
        font-size:14px;
      ">
        Bu özellik mevcut paketinize dahil değildir. Kullanmak için paketinizi yükseltin.
      </div>

    <!-- Invite Aktif / Pasif -->
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
      <label class="switch">
        <input type="checkbox" id="inviteEnabledSwitch">
        <span class="slider round"></span>
      </label>
      <label for="inviteEnabledSwitch" style="margin:0;">
        İnceleme davet sistemini aktif / pasif yap
      </label>
    </div>

    <!-- Entegrasyon -->
    <div style="background:#eef2ff;border:1px solid #c7d2fe;padding:18px;border-radius:14px;margin-bottom:30px;">
      <div style="font-weight:600; margin-bottom:8px;">Entegrasyon Bilgisi</div>

      <div style="font-size:14px; color:#444; margin-bottom:12px;">
        Sipariş tamamlandığında aşağıdaki webhook adresine veri gönderilmelidir.
      </div>

      <div style="padding:12px;background:#fff;border-radius:10px;border:1px solid #ddd;font-family:monospace;font-size:13px;word-break:break-all;">
        https://puandeks.com/backend/api/review-invite-webhook.php
      </div>

      <div style="margin-top:12px; font-size:13px; color:#666;">
        WooCommerce, Shopify veya kullandığınız altyapının webhook ayarına ekleyin.
      </div>
    </div>

    <!-- İlk Gönderim Zamanı -->
    <div style="margin-bottom:30px;">
      <label style="font-weight:bold; display:block; margin-bottom:10px;">
        İlk davet ne zaman gönderilsin?
      </label>

      <div style="margin-bottom:8px;">
        <input type="radio" name="send_time" id="send_now">
        <label for="send_now">Hemen</label>
      </div>

      <div style="display:flex; align-items:center; gap:10px;">
        <input type="radio" name="send_time" id="send_later">
        <label for="send_later">Şu kadar gün sonra:</label>

        <input type="number" min="1" id="inviteDelayInput"
          style="width:70px;padding:6px;border-radius:6px;border:1px solid #ccc;">
        <span>gün</span>
      </div>
    </div>

    <!-- Frequency -->
    <div style="margin-bottom:30px;">
      <label style="font-weight:bold; display:block; margin-bottom:10px;">
        Aynı müşteriye ne sıklıkla davet gönderilsin?
      </label>

      <div style="margin-bottom:8px;">
        <input type="radio" name="frequency" id="frequency_every" checked>
        <label for="frequency_every">Her sipariş için gönder</label>
      </div>

      <div style="display:flex; align-items:center; gap:10px;">
        <input type="radio" name="frequency" id="frequency_limit">
        <label for="frequency_limit">En fazla</label>

        <input type="number" min="1" id="frequency_days"
          style="width:70px;padding:6px;border-radius:6px;border:1px solid #ccc;">
        <span>günde bir kez</span>
      </div>
    </div>

    <!-- Reminder -->
    <div style="margin-bottom:30px;">
      <label style="font-weight:bold; display:block; margin-bottom:10px;">
        Hatırlatma gönderilsin mi?
      </label>

      <div style="display:flex; align-items:center; gap:10px;">
        <input type="checkbox" id="reminder_enabled">
        <label for="reminder_enabled">Bir hatırlatma gönder</label>

        <input type="number" min="1" id="reminder_days"
          style="width:70px;padding:6px;border-radius:6px;border:1px solid #ccc;">
        <span>gün sonra (yorum bırakılmadıysa)</span>
      </div>
    </div>

    <!-- Kaydet -->
    <button type="button" class="btn btn-primary" id="reviewInviteSave">
      Kaydet
    </button>

  </div>
</div>
<!-- / TAB: İnceleme Daveti Ayarları -->

</div>
<!-- / container-fluid -->





<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>


<script>
// Notif count
function updateNotificationCount() {
  fetch('api/get-company-unread-count.php')
    .then(res => res.json())
    .then(data => {
      const count = parseInt(data.count || 0);
      const el = document.getElementById('notification-count');
      if (el) {
        el.innerText = count;
        el.style.display = count > 0 ? 'inline-block' : 'none';
      }
    });
}
updateNotificationCount();
setInterval(updateNotificationCount, 30000);
</script>


<!-- Sidebar Aç/Kapat -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.getElementById("menuToggle");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebar-overlay");

  if (menuToggle && sidebar && overlay) {
    menuToggle.addEventListener("click", function () {
      sidebar.classList.toggle("open");
      overlay.classList.toggle("active");
    });

    overlay.addEventListener("click", function () {
      sidebar.classList.remove("open");
      overlay.classList.remove("active");
    });
  }
});
</script>
<!-- Sidebar Aç/Kapat -->




<script>
document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("automationSelect");
  const options = document.getElementById("automationOptions");
  const selectedText = select.querySelector(".selected-text");

  const sections = {
    "auto-reply": document.getElementById("tab-auto-reply"),
    "review-invite": document.getElementById("tab-review-invite")
  };

  select.addEventListener("click", () => {
    options.style.display = options.style.display === "block" ? "none" : "block";
  });

  options.querySelectorAll("li").forEach(item => {
    item.addEventListener("click", () => {
      const target = item.dataset.target;
      selectedText.textContent = item.textContent;

      Object.keys(sections).forEach(key => {
        sections[key].style.display = key === target ? "block" : "none";
      });

      options.style.display = "none";
    });
  });

  document.addEventListener("click", (e) => {
    if (!select.contains(e.target)) {
      options.style.display = "none";
    }
  });
});
</script>

<script>
/* =========================================================
   AUTO REPLY
========================================================= */
document.addEventListener("DOMContentLoaded", function () {

  const toggle  = document.getElementById("otomatikAktif");
  const isLocked = (window.PACKAGE_STATUS !== 'active' && window.PACKAGE_STATUS !== 'trial');
  const inviteWarning = document.getElementById("inviteLockWarning");
  if (isLocked && inviteWarning) {
    inviteWarning.style.display = "block";
  }
  const warning = document.getElementById("automationLockWarning");
    if (isLocked && warning) {
      warning.style.display = "block";
    }
  const saveBtn = document.querySelector(".btn-success");
  const areas   = document.querySelectorAll(".auto-reply-grid textarea");

  if (!toggle || !saveBtn || areas.length !== 5) return;

  /* ---- INITIAL STATE  ---- */
  toggle.checked = window.AUTO_REPLY_ENABLED === 1;
  if (isLocked) {
  toggle.disabled = true;
}

if (isLocked) {
  areas.forEach(t => t.disabled = true);
  saveBtn.disabled = true;
}

  function allFilled() {
    return Array.from(areas).every(t => t.value.trim() !== "");
  }

  function syncUI() {
    if (!toggle.checked) {
      areas.forEach(t => t.disabled = true);
      saveBtn.disabled = true;
      return;
    }

    areas.forEach(t => t.disabled = false);
    saveBtn.disabled = false;
  }

  syncUI();

  /* ---- TOGGLE CHANGE ---- */
  toggle.addEventListener("change", async function () {

    // SWITCH KAPATILDIYSA → DB'YE OFF YAZ
    if (!toggle.checked) {
      areas.forEach(t => t.disabled = true);
      saveBtn.disabled = true;

      await fetch("api/update-auto-reply.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          enabled: 0
        })
      });

      return;
    }

    // SWITCH AÇILDIYSA → SADECE EDIT MODU
    syncUI();
  });

  /* ---- SAVE ---- */
  saveBtn.addEventListener("click", async function () {

    if (!allFilled()) {
      alert("Otomatik yanıtları açmak için 1–5 tüm alanlar dolu olmalıdır.");
      return;
    }

    const messages = {};
    areas.forEach((t, i) => {
      messages[String(i + 1)] = t.value.trim();
    });

    const res = await fetch("api/update-auto-reply.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        enabled: 1,
        messages: messages
      })
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Otomatik yanıtlar kaydedildi.");
      toggle.checked = true;
      syncUI();
    } else {
      alert("Kayıt sırasında hata oluştu.");
    }
  });

});
</script>

<script>
/* =========================================================
   AUTO REPLY – LOAD MESSAGES (DB + UI)
========================================================= */
document.addEventListener("DOMContentLoaded", function () {

  const toggle = document.getElementById("otomatikAktif");

  if (typeof window.AUTO_REPLY_ENABLED !== "undefined") {
    toggle.checked = window.AUTO_REPLY_ENABLED === 1;
  }

  if (!window.AUTO_REPLY_MESSAGE) return;

  let data;
  try {
    data = JSON.parse(window.AUTO_REPLY_MESSAGE);
  } catch (e) {
    return;
  }

  const areas = document.querySelectorAll(".auto-reply-grid textarea");
  areas.forEach((t, i) => {
    const star = String(i + 1);
    if (data[star]) {
      t.value = data[star];
    }
  });

});
</script>

<!-- REVIEW INVITE SYSTEM – FULL LOGIC (PACKAGE + UI + SAVE + SYNC) -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  const inviteSwitch = document.getElementById("inviteEnabledSwitch");
  const inviteTab    = document.getElementById("tab-review-invite");
  const btn          = document.getElementById("reviewInviteSave");

  if (!inviteSwitch || !inviteTab || !btn) return;

  const isLocked = (window.PACKAGE_STATUS !== 'active' && window.PACKAGE_STATUS !== 'trial');
  const inputs = inviteTab.querySelectorAll("input, button");

  /* =========================
     INITIAL LOAD (DB → UI)
  ========================= */
 inviteSwitch.checked = isLocked ? false : (window.INVITE_ENABLED === 1);

  // Delay
  const sendNow   = document.getElementById("send_now");
  const sendLater = document.getElementById("send_later");
  const delayInput = document.getElementById("inviteDelayInput");

  if (window.INVITE_DELAY_DAYS === 0) {
    sendNow.checked = true;
    delayInput.disabled = true;
  } else {
    sendLater.checked = true;
    delayInput.value = window.INVITE_DELAY_DAYS;
    delayInput.disabled = false;
  }

  // Frequency
  const freqEvery  = document.getElementById("frequency_every");
  const freqLimit  = document.getElementById("frequency_limit");
  const freqDays   = document.getElementById("frequency_days");

  if (window.INVITE_FREQUENCY_DAYS > 0) {
    freqLimit.checked = true;
    freqDays.value = window.INVITE_FREQUENCY_DAYS;
    freqDays.disabled = false;
  } else {
    freqEvery.checked = true;
    freqDays.disabled = true;
  }

  // Reminder
  const reminderCheckbox = document.getElementById("reminder_enabled");
  const reminderDays     = document.getElementById("reminder_days");

  if (window.REMINDER_ENABLED === 1) {
    reminderCheckbox.checked = true;
    reminderDays.value = window.REMINDER_DELAY_DAYS;
    reminderDays.disabled = false;
  } else {
    reminderCheckbox.checked = false;
    reminderDays.disabled = true;
  }

  /* =========================
     PACKAGE LOCK
  ========================= */
  if (isLocked) {
    inviteSwitch.disabled = true;
    inputs.forEach(el => {
      if (el.id !== "inviteEnabledSwitch") el.disabled = true;
    });
  }

  /* =========================
     UI CONTROL
  ========================= */
  function updateInviteUI() {
    inputs.forEach(el => {
      if (el.id !== "inviteEnabledSwitch") {
        el.disabled = !inviteSwitch.checked || isLocked;
      }
    });
  }

  inviteSwitch.addEventListener("change", updateInviteUI);

  /* Delay toggle */
  sendNow.addEventListener("change", () => delayInput.disabled = true);
  sendLater.addEventListener("change", () => delayInput.disabled = false);

  /* Frequency toggle */
  freqEvery.addEventListener("change", () => freqDays.disabled = true);
  freqLimit.addEventListener("change", () => freqDays.disabled = false);

  /* Reminder toggle */
  reminderCheckbox.addEventListener("change", () => {
    reminderDays.disabled = !reminderCheckbox.checked;
  });

  updateInviteUI();

  /* =========================
     SAVE
  ========================= */
  btn.addEventListener("click", async function () {

    if (isLocked) {
      alert("Bu özellik mevcut paketinize dahil değildir.");
      return;
    }

    const inviteEnabled = inviteSwitch.checked ? 1 : 0;

    if (!inviteEnabled) {
      await fetch("api/update-review-invite-settings.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
          invite_enabled: 0,
          invite_delay_days: 0,
          invite_frequency_days: 0,
          reminder_enabled: 0,
          reminder_delay_days: 0
        })
      });

      alert("İnceleme daveti sistemi kapatıldı.");
      return;
    }

    let delayDays = sendNow.checked ? 0 : parseInt(delayInput.value || 0);
    if (!sendNow.checked && delayDays < 1) {
      alert("Gecikme günü en az 1 olmalıdır.");
      return;
    }

    let frequencyDays = freqLimit.checked ? parseInt(freqDays.value || 0) : 0;
    if (freqLimit.checked && frequencyDays < 1) {
      alert("Frekans günü en az 1 olmalıdır.");
      return;
    }

    let reminderEnabledVal = reminderCheckbox.checked ? 1 : 0;
    let reminderDaysVal = reminderCheckbox.checked ? parseInt(reminderDays.value || 0) : 0;

    if (reminderCheckbox.checked && reminderDaysVal < 1) {
      alert("Hatırlatma günü en az 1 olmalıdır.");
      return;
    }

    const res = await fetch("api/update-review-invite-settings.php", {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify({
        invite_enabled: 1,
        invite_delay_days: delayDays,
        invite_frequency_days: frequencyDays,
        reminder_enabled: reminderEnabledVal,
        reminder_delay_days: reminderDaysVal
      })
    });

    const data = await res.json();

    alert(data.status === "success"
      ? "İnceleme daveti ayarları kaydedildi."
      : "Kayıt sırasında hata oluştu.");
  });

});
</script>

</body>
</html>