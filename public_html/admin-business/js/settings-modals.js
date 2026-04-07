/* ======================================================
   SETTINGS MODAL
====================================================== */
document.addEventListener("DOMContentLoaded", () => {

  const modal = document.getElementById("settingsModal");
  const title = document.getElementById("modalTitle");
  const body  = document.getElementById("modalBody");

  /* OPEN MODAL */
  document.querySelectorAll("[data-modal]").forEach(btn => {
   btn.addEventListener("click", () => {
  const type = btn.dataset.modal;

  title.innerText = "";
  body.innerHTML  = getContent(type);
  modal.style.display = "flex";

  if (type === "adress-info") {
    const cityInput    = document.getElementById("cityInput");
    const addressInput = document.getElementById("addressInput");

    if (cityInput)    cityInput.value = window.COMPANY_CITY_NAME || "";
    if (addressInput) addressInput.value = window.COMPANY_ADDRESS || "";

    initAddressPopup();

    setTimeout(() => {
      if (typeof initMap === "function") {
        initMap();
      }
    }, 300);
  }

  if (type === "profile") initCountryPhoneLogic();
  if (type === "password" && typeof initPasswordLogic === "function") initPasswordLogic();
  if (type === "category") initCategoryPopup();
});

  });

  /* CLOSE MODAL — SADECE 1 KEZ */
  modal.addEventListener("click", (e) => {
    if (e.target === modal || e.target.closest(".js-modal-cancel")) {
      modal.style.display = "none";
      body.innerHTML = "";
    }
  });

});


/* ======================================================
   TITLES
====================================================== */
function getTitle(type) {
  switch (type) {
    case "profile": return "Profil Bilgileri";
    case "logo": return "İşletme Logosu";
    case "password": return "Şifre Değiştir";
    case "category": return "şletme Kategorisi";
    case "business-info": return "İşletme Hakkında";
    case "adress-info": return "Adres bilgileri";
    case "social": return "Web & Sosyal Medya";
    case "documents": return "Doğrulama Belgeleri";
    case "freeze": return "Hesab Dondur";
    case "delete": return "Üyeliği Sil";
    default: return "Ayarlar";
  }
}

/* ======================================================
   MODAL CONTENT
====================================================== */
function getContent(type) {

/* LOGO POPUP */
if (type === "logo") {
  return `
    <div class="form-group">
      <div style="margin-bottom:12px">
        <img
          id="currentLogoPreview"
          src="${window.COMPANY_LOGO ? window.COMPANY_LOGO : '/img/placeholder/company-profile.png'}"
          style="width:120px;height:120px;object-fit:cover;border-radius:12px;border:1px solid #ddd">
      </div>
    </div>

    <div class="form-group">
      <label>Yeni Logo (JPG / PNG / WEBP)</label>
      <input
        type="file"
        id="logoInput"
        class="form-control"
        accept="image/png,image/jpeg,image/webp">
    </div>

    <div style="text-align:right">
      <button class="btn btn-secondary js-modal-cancel">İptal</button>
      <button class="btn btn-success" id="saveLogo" disabled>Kaydet</button>
    </div>
  `;
}

if (type === "password") {
  return `
    <form id="passwordForm">

      <!-- Current password -->
      <div class="form-group">
        <label>Mevcut şifre</label>
        <input type="password"
               id="currentPassword"
               class="form-control">
        <small class="text-danger" id="currentError" style="display:none">
          Mevcut şifre hatalı
        </small>
      </div>

      <!-- New password -->
      <div class="form-group">
        <label>Yeni Şifre</label>
        <input type="password"
               id="newPassword"
               class="form-control">
        <small class="text-muted">
          En az 8 karakter, büyük harf, küçük harf, rakam ve özel karakter
        </small>
        <small class="text-danger" id="ruleError" style="display:none">
          Şifre kurallara uymuyor
        </small>
      </div>

      <!-- Confirm password -->
      <div class="form-group">
        <label>Yeni şifre (tekrar)</label>
        <input type="password"
               id="confirmPassword"
               class="form-control">
        <small class="text-danger" id="matchError" style="display:none">
          ifreler eşleşmiyor
        </small>
      </div>

      <button type="button"
              id="changePasswordBtn"
              class="btn btn-success btn-block"
              disabled>
        Şifreyi Değiştir
      </button>

    </form>
  `;
}

if (type === "category") {
  return `
    <div id="categoryList"
         style="max-height:360px; overflow-y:auto; padding-right:8px;">
      <p>Yükleniyor...</p>
    </div>

    <div style="margin-top:20px;text-align:right">
      <button class="btn btn-secondary js-modal-cancel">İptal</button>
      <button class="btn btn-success" id="saveCategory">
        Kategoriyi Deiştir
      </button>
    </div>
  `;
}

if (type === "business-info") {
  return `
    <div class="form-group">
      <textarea
        id="companyAbout"
        class="form-control"
        rows="6"
        placeholder="İşletmeniz hakkında ksa bir açıklama yazın..."
        style="resize:none;"
      >${window.COMPANY_ABOUT || ''}</textarea>

      <small style="display:block;margin-top:6px;color:#777;">
        Maksimum 120 kelime
      </small>
    </div>

    <div style="text-align:right">
      <button class="btn btn-secondary js-modal-cancel">İptal</button>
      <button class="btn btn-success" id="saveAbout">
        Kaydet
      </button>
    </div>
  `;
}

if (type === "adress-info") {
  return `
    <div class="form-group">
      <label>Şehir *</label>
      <input
        type="text"
        id="cityInput"
        class="form-control"
        placeholder="Şehir adını birebir giriniz (örn: İstanbul)"
        autocomplete="off"
      >
      <small id="cityError" class="text-danger" style="display:none">
        Şehir adını doğru ve eksiksiz giriniz (örn: İstanbul)
      </small>
    </div>

    <div class="form-group" style="margin-top:12px;">
      <label>Açık Adres</label>
      <textarea
        id="addressInput"
        class="form-control"
        rows="4"
        style="padding:14px 16px;"
        placeholder="Örn: Kadıköy, Moda Mah., Bahariye Cad. No:12"></textarea>
      <small class="text-muted">
        İl bilgisi yazmayınız. Sadece ilçe, mahalle, cadde/sokak ve bina numarası giriniz.
      </small>
    </div>

    <hr style="margin:20px 0">

    <div class="form-group">
      <label>İşletmenizi Haritada Pinleyin</label>

      <div id="map"
          style="
            width:100%;
            height:350px;
            border-radius:8px;
            border:1px solid #ddd;
          ">
      </div>

      <input type="hidden" name="latitude" id="latitude">
      <input type="hidden" name="longitude" id="longitude">
    </div>

    <div style="text-align:right; margin-top:20px">
      <button class="btn btn-secondary js-modal-cancel">İptal</button>
      <button class="btn btn-success" id="saveAddress" disabled>
        Kaydet
      </button>
    </div>
  `;
}


if (type === "social") {
  return `
    <!-- WEBSITE -->
    <div class="form-group">
      <label style="font-weight:600; margin-bottom:6px; display:block;">
        Web Sitesi
      </label>

      <input
        type="text"
        id="websiteInput"
        placeholder="örn: thefitoz.com"
        value="${window.COMPANY_WEBSITE || ''}"
        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"
      >

      <small style="display:block; margin-top:6px; color:#777; font-size:12px;">
        https:// veya www yazmanıza gerek yoktur.
      </small>

      <small style="display:block; margin-top:6px; color:#c0392b; font-size:12px;">
        Domain adresinizin değişmesi halinde lütfen domain adresinizle
        uyuşan yeni e-posta adresinizi de güncelleyin.
      </small>
    </div>

    <hr style="margin:24px 0; border:0; border-top:1px solid #eee;">

    <!-- SOCIAL MEDIA -->
    <div class="form-group">
      <label style="font-weight:600; margin-bottom:12px; display:block;">
        Sosyal Medya Hesapları
      </label>

      <div style="margin-bottom:10px;">
        <span style="font-size:13px; color:#555;">linkedin.com/in/</span>
        <input
          type="text"
          id="linkedinInput"
          value="${window.COMPANY_LINKEDIN || ''}"
          placeholder="kullaniciadi"
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"
        >
      </div>

      <div style="margin-bottom:10px;">
        <span style="font-size:13px; color:#555;">facebook.com/</span>
        <input
          type="text"
          id="facebookInput"
          value="${window.COMPANY_FACEBOOK || ''}"
          placeholder="sayfaadi"
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"
        >
      </div>

      <div style="margin-bottom:10px;">
        <span style="font-size:13px; color:#555;">instagram.com/</span>
        <input
          type="text"
          id="instagramInput"
          value="${window.COMPANY_INSTAGRAM || ''}"
          placeholder="kullaniciadi"
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"
        >
      </div>

      <div style="margin-bottom:10px;">
        <span style="font-size:13px; color:#555;">x.com/</span>
        <input
          type="text"
          id="xInput"
          value="${window.COMPANY_X || ''}"
          placeholder="kullaniciadi"
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"
        >
      </div>

      <div style="margin-bottom:10px;">
        <span style="font-size:13px; color:#555;">youtube.com/</span>
        <input
          type="text"
          id="youtubeInput"
          value="${window.COMPANY_YOUTUBE || ''}"
          placeholder="kanaladi"
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"
        >
      </div>
    </div>

    <div style="margin-top:24px; text-align:right;">
     <button class="btn btn-secondary js-modal-cancel">İptal</button>
      <button id="saveWebsiteAndSocial" class="btn btn-success">
        Kaydet
      </button>
    </div>
  `;
}

if (type === "documents") {

  let docs = {};
  try {
    docs = window.COMPANY_DOCUMENTS
      ? JSON.parse(window.COMPANY_DOCUMENTS)
      : {};
  } catch (e) {
    docs = {};
  }

  const docRow = (key, label) => {
    const hasDoc = !!docs[key];

    return `
      <div style="margin-bottom:18px;">
        <label style="font-weight:600; display:block; margin-bottom:6px;">
          ${label}
        </label>

        ${
          hasDoc
            ? `
              <a href="${docs[key]}" target="_blank"
                 style="display:inline-block;margin-bottom:6px;color:#0C7C59;font-size:13px;">
                Mevcut belgeyi görüntüle
              </a>
            `
            : `
              <div style="color:#c0392b;font-size:13px;margin-bottom:6px;">
                Belge eklenmemiş, lütfen yükleyin.
              </div>
            `
        }

        <input
          type="file"
          data-doc="${key}"
          class="form-control"
          accept="application/pdf,image/*"
        >

        <small style="display:block;margin-top:4px;color:#777;font-size:12px;">
          PDF veya görsel dosya yükleyebilirsiniz.
        </small>
      </div>
    `;
  };

  return `
    <div class="form-group">

      ${docRow("vergi", "Vergi Levhası")}
      ${docRow("faaliyet", "Faaliyet Belgesi")}
      ${docRow("sicil", "Ticaret Sicil Belgesi")}

      <small style="
        display:block;
        margin-top:10px;
        color:#777;
        font-size:12px;
      ">
        Belgeleriniz eksik, hatalı veya güncel değilse yeniden yükleyebilirsiniz.
      </small>
    </div>

    <div style="margin-top:24px;text-align:right;">
      <button class="btn btn-secondary js-modal-cancel">İptal</button>
      <button class="btn btn-success" id="saveDocuments">
        Kaydet
      </button>
    </div>
  `;
}


/* ======================================================
   ACCOUNT DELETE
====================================================== */
if (type === "delete") {
  return `
    <div style="font-size:14px; color:#555; line-height:1.5;">
      <p>Hesabınızı silmek üzeresiniz.</p>

      <ul style="padding-left:18px;">
        <li>Bu işlem geri alınamaz</li>
        <li>Tüm verileriniz silinir</li>
      </ul>
    </div>

    <div style="margin-top:24px; text-align:right;">
      <button class="btn btn-secondary js-modal-cancel">
        Vazgeç
      </button>
      <button
        type="button"
        class="btn btn-danger"
        id="confirmDeleteAccount"
      >
        Hesabımı Sil
      </button>
    </div>
  `;
}

/* ======================================================
   ACCOUNT FREEZE
====================================================== */
if (type === "freeze") {
  return `
    <div style="font-size:14px; color:#555; line-height:1.5;">
      <p>
        Hesabınızı dondurduğunuzda:
      </p>
      <ul style="padding-left:18px;">
        <li>Profiliniz yayında görünmez</li>
        <li>Aramalarda çıkmaz</li>
        <li>Yeni yorum alamazsınız</li>
        <li>Widget’lar çalışmaz</li>
      </ul>

      <p style="margin-top:12px;">
        Bu işlem <strong>geri alınabilir</strong>.
      </p>
    </div>

    <div style="margin-top:24px; text-align:right;">
      <button class="btn btn-secondary js-modal-cancel">
        Vazgeç
      </button>
      <button
        type="button"
        class="btn btn-warning"
        id="confirmFreezeAccount"
      >
        Hesabı Dondur
      </button>
    </div>
  `;
}


/* ======================================================
   ACCOUNT REACTIVATE
====================================================== */
if (type === "reactivate") {
  return `
    <div style="font-size:14px; color:#555; line-height:1.5;">
      <p>
        Hesabınızı tekrar aktifleştirmek üzeresiniz.
      </p>
      <ul style="padding-left:18px;">
        <li>Profiliniz yeniden yayına alnır</li>
        <li>Aramalarda tekrar görünürsünüz</li>
        <li>Yorum almaya devam edersiniz</li>
        <li>Widget’lar tekrar çalışır</li>
      </ul>
    </div>

    <div style="margin-top:24px; text-align:right;">
      <button class="btn btn-secondary js-modal-cancel">
        Vazgeç
      </button>
      <button
        type="button"
        class="btn btn-success"
        id="confirmReactivateAccount"
      >
        Hesabı Aktifleştir
      </button>
    </div>
  `;
}

if (type === "phone-otp") {
  return `
    <h4 style="margin-bottom:16px;">Telefon Doğrulama</h4>

    <p style="font-size:14px;color:#666;margin-bottom:12px;">
      Telefonunuza gönderilen 6 haneli kodu girin.
    </p>

    <input type="text"
           id="otpInput"
           maxlength="6"
           style="
             width:100%;
             padding:12px;
             font-size:18px;
             text-align:center;
             letter-spacing:6px;
             border:1px solid #ddd;
             border-radius:8px;
           ">

    <div id="otpError"
         style="color:#c0392b;font-size:13px;margin-top:8px;display:none;">
    </div>

    <div style="margin-top:12px;font-size:13px;color:#777;">
      Kalan süre: <span id="otpTimer">180</span> sn
    </div>

    <div style="margin-top:16px;display:flex;justify-content:space-between;">
      <button class="btn btn-secondary" id="backToProfile">
        Geri
      </button>

      <button class="btn btn-success" id="verifyOtpBtn">
        Doğrula
      </button>
    </div>
  `;
}


  if (type !== "profile") return `<p>İçerik bulunamadı.</p>`;

  return `
    <!-- OWNER -->
    <div class="form-group">
      <label>Yetkili Adı *</label>
      <input type="text" id="owner_name" class="form-control"
        value="${window.COMPANY_OWNER_NAME || ''}">
    </div>

    <div style="text-align:right">
      <button class="btn btn-secondary js-modal-cancel">İptal</button>
      <button class="btn btn-success" id="saveOwnerName">Kaydet</button>
    </div>

    <hr>

    <!-- EMAIL -->
    <div class="form-group">
      <label>E-posta *</label>
      <input type="email" id="email" class="form-control"
        value="${window.COMPANY_EMAIL || ''}">
    </div>

    <div style="text-align:right">
      <button class="btn btn-secondary js-modal-cancel">İptal</button>
      <button class="btn btn-success" id="saveEmail">Kaydet</button>
    </div>

    <hr>

    <!-- COUNTRY -->
    <div class="form-group">
      <label>Ülke *</label>
      <input type="text" id="country_input" class="form-control"
        value="${window.COMPANY_COUNTRY || ''}" autocomplete="off">
      <small id="countryError" class="text-danger" style="display:none">
        Country not found
      </small>
    </div>

   <!-- PHONE -->
<div class="form-group">
  <label>Telefon *</label>

  ${Number(window.COMPANY_PHONE_VERIFIED) === 0 ? `
    <div
      style="
        background:#fff3cd;
        color:#856404;
        padding:8px 12px;
        border-radius:6px;
        font-size:13px;
        margin-bottom:8px;
        border:1px solid #ffeeba;
      ">
      ⚠ Telefonunuz doğrulanmamış. Bir sonraki girişte doğrulamanız istenecek.
    </div>
  ` : `
    <div
      style="
        background:#e6f9f0;
        color:#0C7C59;
        padding:8px 12px;
        border-radius:6px;
        font-size:13px;
        margin-bottom:8px;
        border:1px solid #b7ebd3;
      ">
      ✔ Telefon numaranız doğrulanmış.
    </div>
  `}

  <div style="display:flex;gap:8px">
    <input type="text"
          id="phone_prefix"
          class="form-control"
          style="max-width:90px"
          readonly
          value="${window.COMPANY_PHONE_PREFIX || ''}">

    <input type="text"
          id="phone"
          class="form-control"
          value="${window.COMPANY_PHONE || ''}">
  </div>
</div>

  <div style="text-align:right">
    <button class="btn btn-secondary js-modal-cancel">İptal</button>
    <button class="btn btn-success" id="savePhone" disabled>
      Kaydet
    </button>
  </div>
  `;
  
}



/* ======================================================
   COUNTRY + PHONE LOGIC
====================================================== */
function initCountryPhoneLogic() {

  const modal        = document.getElementById("settingsModal");
  const countryInput = document.getElementById("country_input");
  const phonePrefix  = document.getElementById("phone_prefix");
  const phoneInput   = document.getElementById("phone");
  const saveBtn      = document.getElementById("savePhone");
  const errorBox     = document.getElementById("countryError");

  if (!countryInput) return;

  let countries = [];
  let lastCountry = "";

  fetch("/api/suggest-country.php?q=all")
    .then(r => r.json())
    .then(data => {
      if (!data.success) return;
      countries = data.results;

      if (countryInput.value.trim()) {
        handleCountryChange(countryInput.value);
      }
    });


    countryInput.addEventListener("input", () => {
    countryInput.value =
      countryInput.value.charAt(0).toUpperCase() +
      countryInput.value.slice(1);

    handleCountryChange(countryInput.value);
  });


    phoneInput.addEventListener("input", () => {
    saveBtn.disabled = !(
      phoneInput.value.trim() &&
      countryInput.value.trim() &&
      errorBox.style.display !== "block"
    );
  });


  function normalize(str) {
    return str.toLowerCase().trim();
  }

  function handleCountryChange(value) {
    const val = normalize(value);
    saveBtn.disabled = true;

    if (!countries.length) return;

    const match = countries.find(c => normalize(c.name) === val);

    if (!match) {
      errorBox.style.display = val ? "block" : "none";
      phonePrefix.value = "";
      return;
    }

    errorBox.style.display = "none";

    phonePrefix.value = "+" + match.phone_prefix;
    lastCountry = match.name;

    if (phoneInput.value.trim()) {
      saveBtn.disabled = false;
    }
  }
 
  saveBtn.addEventListener("click", () => {
    const formData = new FormData();
    formData.append("country", countryInput.value);
    formData.append("phone_prefix", phonePrefix.value.replace("+", ""));
    formData.append("phone", phoneInput.value);

    fetch("/api/update-phone.php", {
      method: "POST",
      body: formData
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        alert("Ülke ve Telefon bilgileri kaydedildi");
        modal.style.display = "none";
      } else {
        alert(res.message || "Kayıt başarısız");
      }
    });
  });


}

/* Category Change */
function initCategoryPopup() {
  const list = document.getElementById("categoryList");
  if (!list) return;

  fetch("api/get-categories.php")
    .then(r => r.json())
    .then(res => {
      if (!res.success) {
        list.innerHTML = "<p>Kategoriler alınamadı.</p>";
        return;
      }

      list.innerHTML = "";

      res.categories.forEach(cat => {
        const row = document.createElement("label");
        row.style.display = "flex";
        row.style.justifyContent = "space-between";
        row.style.alignItems = "center";
        row.style.marginBottom = "12px";

        const checked =
          window.COMPANY_CATEGORY_ID &&
          Number(window.COMPANY_CATEGORY_ID) === Number(cat.id)
            ? "checked"
            : "";

        row.innerHTML = `
          <span>${cat.name}</span>
          <input type="radio"
                 name="category_id"
                 value="${cat.id}"
                 ${checked}>
        `;

        list.appendChild(row);
      });
    });
}

document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "saveCategory") {
    const selected = document.querySelector('input[name="category_id"]:checked');
    if (!selected) {
      alert("Lütfen bir kategori seçin");
      return;
    }

    const fd = new FormData();
    fd.append("category_id", selected.value);

    fetch("api/update-category.php", {
      method: "POST",
      body: fd
    })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          alert("Kategori güncellendi");
          document.getElementById("settingsModal").style.display = "none";
          window.COMPANY_CATEGORY_ID = selected.value;
        } else {
          alert(res.message || "Güncelleme başarısız");
        }
      });
  }
});


/* About update */
document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "saveAbout") {
    const textarea = document.getElementById("companyAbout");
    if (!textarea) return;

    const text = textarea.value.trim();

    if (!text) {
      alert("Hakkımızda alan bo olamaz");
      return;
    }

    const words = text.split(/\s+/).filter(w => w.length > 0);
    if (words.length > 300) {
      alert("Maksimum 300 kelime yazabilirsiniz");
      return;
    }

    const fd = new FormData();
    fd.append("about", text);

    fetch("api/update-about.php", {
      method: "POST",
      body: fd
    })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          alert("Hakkımızda bilgisi güncellendi");
          window.COMPANY_ABOUT = text;
          document.getElementById("settingsModal").style.display = "none";
        } else {
          alert(res.message || "Güncelleme başarısız");
        }
      });
  }
});

/* Address update */
function initAddressPopup() {
  const cityInput    = document.getElementById("cityInput");
  const addressInput = document.getElementById("addressInput");
  const saveBtn      = document.getElementById("saveAddress");

  if (!cityInput || !addressInput || !saveBtn) return;



  // KAYDET AKTİF
  saveBtn.disabled = false;

  saveBtn.onclick = () => {
    const cityName = cityInput.value.trim();
    const address  = addressInput.value.trim();

    if (!cityName || !address) {
      alert("Şehir ve adres boş olamaz");
      return;
    }

    const fd = new FormData();
    fd.append("city_name", cityName);
    fd.append("address", address);

    // PIN KOORDİNATLARI
    const lat = document.getElementById("latitude")?.value || "";
    const lng = document.getElementById("longitude")?.value || "";

    fd.append("latitude", lat);
    fd.append("longitude", lng);

    fetch("api/update-address.php", {
      method: "POST",
      body: fd
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        window.COMPANY_CITY    = cityName;
        window.COMPANY_ADDRESS = address;
        alert("Adres kaydedildi");
        document.getElementById("settingsModal").style.display = "none";
      } else {
        alert(res.message || "Adres güncellenemedi");
      }
    });
  };
}


/* ======================================================
   WEBSITE & SOCIAL SAVE
====================================================== */
document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "saveWebsiteAndSocial") {

    const fd = new FormData();

    fd.append("website", document.getElementById("websiteInput")?.value.trim() || "");
    fd.append("linkedin_url", document.getElementById("linkedinInput")?.value.trim() || "");
    fd.append("facebook_url", document.getElementById("facebookInput")?.value.trim() || "");
    fd.append("instagram_url", document.getElementById("instagramInput")?.value.trim() || "");
    fd.append("x_url", document.getElementById("xInput")?.value.trim() || "");
    fd.append("youtube_url", document.getElementById("youtubeInput")?.value.trim() || "");

    fetch("api/update-website-social.php", {
      method: "POST",
      body: fd
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {

        // update globals
        window.COMPANY_WEBSITE   = fd.get("website");
        window.COMPANY_LINKEDIN  = fd.get("linkedin_url");
        window.COMPANY_FACEBOOK  = fd.get("facebook_url");
        window.COMPANY_INSTAGRAM = fd.get("instagram_url");
        window.COMPANY_X         = fd.get("x_url");
        window.COMPANY_YOUTUBE   = fd.get("youtube_url");

        alert("Web sitesi ve sosyal medya bilgileri güncellendi");
        document.getElementById("settingsModal").style.display = "none";

      } else {
        alert(res.message || "Güncelleme başarısız");
      }
    });
  }
});

/* ======================================================
   DOCUMENTS SAVE
====================================================== */
document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "saveDocuments") {

    const fd = new FormData();

    document.querySelectorAll('input[type="file"][data-doc]').forEach(input => {
      if (input.files.length > 0) {
        fd.append(input.dataset.doc, input.files[0]);
      }
    });

    fetch("api/update-documents.php", {
      method: "POST",
      body: fd
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        window.COMPANY_DOCUMENTS = JSON.stringify(res.documents);
        alert("Belgeler başarıyla güncellendi");
        document.getElementById("settingsModal").style.display = "none";
      } else {
        alert(res.message || "Belge yükleme başarısız");
      }
    });
  }
});


/* ======================================================
   ACCOUNT FREEZE
====================================================== */
document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "confirmFreezeAccount") {

    fetch("api/freeze-account.php", {
      method: "POST"
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        alert("Hesabınız donduruldu");
        window.location.href = "/business-login";
      } else {
        alert("İşlem başarısız");
      }
    });
  }
});

/* ======================================================
   ACCOUNT REACTIVATE
====================================================== */
document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "confirmReactivateAccount") {

    fetch("api/reactivate-account.php", {
      method: "POST"
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        alert("Hesabınız tekrar aktifleştirildi");
        window.location.reload();
      } else {
        alert("İşlem başarsız");
      }
    });
  }
});

/* ======================================================
   ACCOUNT DELETE
====================================================== */
document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "confirmDeleteAccount") {

    fetch("api/delete-account.php", {
      method: "POST"
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        alert("Hesabınız silindi");
        window.location.href = "/business-login";
      } else {
        alert("İşlem başarısız");
      }
    });
  }
});

/* ======================================================
   PHONE OTP VERIFICATION
====================================================== */
let otpInterval = null;
let otpRemaining = 180;

document.addEventListener("click", function(e){

  if(e.target && e.target.id === "startPhoneVerification"){

    fetch("api/start-phone-otp.php", { method:"POST" })
      .then(r=>r.json())
      .then(res=>{
        if(res.success){
          document.getElementById("modalBody").innerHTML = getContent("phone-otp");
          startOtpTimer();
        } else {
          alert(res.message || "OTP gönderilemedi");
        }
      });

  }

});

/* OTP ver */
document.addEventListener("click", function(e){

  if(e.target && e.target.id === "verifyOtpBtn"){

    const code = document.getElementById("otpInput").value.trim();
    const errorBox = document.getElementById("otpError");

    if(!/^\d{6}$/.test(code)){
      errorBox.innerText = "6 haneli kod girin";
      errorBox.style.display = "block";
      return;
    }

    const fd = new FormData();
    fd.append("code", code);

    fetch("api/verify-phone-otp.php", {
      method:"POST",
      body:fd
    })
    .then(r=>r.json())
    .then(res=>{
      if(res.success){
        location.reload();
      } else {
        errorBox.innerText = res.message || "Kod hatalı";
        errorBox.style.display = "block";
      }
    });

  }

});

/* Back */
document.addEventListener("click", function(e){

  if(e.target && e.target.id === "backToProfile"){
    document.getElementById("modalBody").innerHTML = getContent("profile");
    initCountryPhoneLogic();
  }

});

/* Timer */
function startOtpTimer(){
  otpRemaining = 180;
  const timer = document.getElementById("otpTimer");
  timer.innerText = otpRemaining;

  otpInterval = setInterval(() => {
    otpRemaining--;
    timer.innerText = otpRemaining;

    if(otpRemaining <= 0){
      clearInterval(otpInterval);
    }
  }, 1000);
}
