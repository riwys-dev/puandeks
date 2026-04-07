/* ======================================================
   LOGO UI HANDLER
====================================================== */
document.addEventListener("change", function (e) {

  if (e.target.id !== "logoInput") return;

  const file = e.target.files[0];
  const preview = document.getElementById("currentLogoPreview");
  const saveBtn = document.getElementById("saveLogo");

  if (!file) {
    saveBtn.disabled = true;
    return;
  }

  const allowed = ["image/png", "image/jpeg", "image/webp"];
  if (!allowed.includes(file.type)) {
    alert("Only JPG, PNG or WEBP allowed");
    e.target.value = "";
    saveBtn.disabled = true;
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    alert("Max file size is 5MB");
    e.target.value = "";
    saveBtn.disabled = true;
    return;
  }

  const reader = new FileReader();
  reader.onload = () => {
    preview.src = reader.result;
    saveBtn.disabled = false;
  };
  reader.readAsDataURL(file);
});

/* ======================================================
   LOGO SAVE
====================================================== */
document.addEventListener("click", function (e) {

  if (e.target.id !== "saveLogo") return;

  const input = document.getElementById("logoInput");
  if (!input || !input.files.length) return;

  const formData = new FormData();
  formData.append("logo", input.files[0]);

  e.target.disabled = true;

  fetch("api/update-company-logo.php", {
    method: "POST",
    body: formData
  })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        alert("Logo güncellendi");
      } else {
        alert(res.message || "Upload failed");
      }
    })
    .catch(() => {
      alert("Server error");
    })
    .finally(() => {
      e.target.disabled = false;
    });
});
