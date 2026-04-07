document.addEventListener('DOMContentLoaded', function () {
  console.log("DOM hazır");

  let companyCategoryId = null;

  fetch('api/get-business-data.php')
    .then(res => res.json())
    .then(data => {
      const c = data.company;
      companyCategoryId = c.category_id;

      document.querySelector('[name="business_name"]').value = c.name || '';
      document.querySelector('[name="full_name"]').value = c.owner_name || '';
      document.querySelector('[name="title"]').value = c.title || '';
      document.querySelector('[name="email"]').value = c.email || '';
      document.querySelector('[name="phone"]').value = c.phone || '';
      document.querySelector('[name="phone_prefix"]').value = c.phone_prefix || '';
      document.querySelector('[name="annual_revenue"]').value = c.annual_revenue || '';
      document.querySelector('[name="currency"]').value = c.currency || '';
      document.querySelector('[name="address"]').value = c.address || '';
      document.querySelector('[name="city"]').value = c.city || '';
      document.querySelector('[name="district"]').value = c.district || '';
      document.querySelector('[name="postal_code"]').value = c.postal_code || '';
      document.querySelector('[name="about"]').value = c.about || '';
      document.querySelector('[name="website"]').value = c.domain || '';
      document.querySelector('[name="linkedin"]').value = c.linkedin_url || '';
      document.querySelector('[name="facebook"]').value = c.facebook_url || '';
      document.querySelector('[name="instagram"]').value = c.instagram_url || '';
      document.querySelector('[name="x"]').value = c.x_url || '';
      document.querySelector('[name="youtube"]').value = c.youtube_url || '';
      if (c.logo) {
        document.getElementById('company-logo-preview').src = c.logo;
      }

      return fetch('api/get-categories.php');
    })
    .then(res => res.json())
    .then(catData => {
      const select = document.querySelector('[name="category"]');
      catData.categories.forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat.id;
        opt.textContent = cat.name;
        if (cat.id == companyCategoryId) opt.selected = true;
        select.appendChild(opt);
      });
    });

  // Telefon prefix IP tabanlı
  let phonePrefixTouched = false;
  const prefixInput = document.querySelector('[name="phone_prefix"]');
  if (prefixInput) {
    prefixInput.addEventListener('focus', () => {
      if (!phonePrefixTouched) {
        phonePrefixTouched = true;
        fetch('https://ipapi.co/json')
          .then(res => res.json())
          .then(data => {
            if (data && data.country_calling_code) {
              prefixInput.value = data.country_calling_code;
            }
          });
      }
    });
  }

  // Currency IP tabanlı
  let currencyTouched = false;
  const currencyInput = document.querySelector('[name="currency"]');
  if (currencyInput) {
    currencyInput.addEventListener('focus', () => {
      if (!currencyTouched) {
        currencyTouched = true;
        fetch('https://ipapi.co/json')
          .then(res => res.json())
          .then(data => {
            if (data && data.currency) {
              currencyInput.value = data.currency;
            } else {
              currencyInput.value = "USD";
            }
          });
      }
    });
  }

  // Buton aktiflik kontrolü
  const form = document.querySelector('form');
  const submitButton = form.querySelector('button[type="submit"]');
  const initialFormData = new FormData(form);

  submitButton.disabled = true;

  form.addEventListener('input', () => {
    const currentFormData = new FormData(form);
    let changed = false;

    for (let [key, value] of currentFormData.entries()) {
      if (value !== initialFormData.get(key)) {
        changed = true;
        break;
      }
    }

    submitButton.disabled = !changed;
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    fetch('api/update-business-data.php', {
      method: 'POST',
      body: formData
    })
      .then(resp => resp.json())
      .then(data => {
        if (data.success) {
          alert('Değişiklikler kaydedildi.');
        } else {
          alert('Hata: ' + (data.message || 'Güncelleme başarısız.'));
        }
      })
      .catch(err => {
        console.error(err);
        alert('Beklenmeyen bir hata oluştu.');
      });
  });
});

function initPasswordLogic() {
  const current = document.getElementById("currentPassword");
  const next    = document.getElementById("newPassword");
  const confirm = document.getElementById("confirmPassword");
  const btn     = document.getElementById("changePasswordBtn");

  const errCurrent = document.getElementById("currentError");
  const errRule    = document.getElementById("ruleError");
  const errMatch   = document.getElementById("matchError");

  if (!current || !next || !confirm || !btn) return;

  function validRule(pwd) {
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(pwd);
  }

  function validate() {
    let ok = true;

    errCurrent.style.display = "none";
    errRule.style.display = "none";
    errMatch.style.display = "none";

    if (!validRule(next.value)) {
      errRule.style.display = "block";
      ok = false;
    }

    if (next.value !== confirm.value) {
      errMatch.style.display = "block";
      ok = false;
    }

    btn.disabled = !ok;
  }

  next.addEventListener("input", validate);
  confirm.addEventListener("input", validate);

  btn.addEventListener("click", () => {
    const fd = new FormData();
    fd.append("current_password", current.value);
    fd.append("new_password", next.value);

    fetch("api/change-password.php", {
      method: "POST",
      body: fd
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        alert("Şifreniz değiştirilmiştir. Bir dahaki girişinizde yeni şifrenizi kullanabilirsiniz.");
        document.getElementById("settingsModal").style.display = "none";
      } else {
        errCurrent.style.display = "block";
      }
    });
  });
}

