document.addEventListener("DOMContentLoaded", function () {
  // === KATEGORİ DROPDOWN ===
  const dropdown = document.getElementById("customDropdown");
  const options = document.getElementById("dropdownOptions");
  const selected = document.getElementById("dropdownSelected");
  const hiddenInput = document.getElementById("categoryInput");

  if (!dropdown || !options || !selected || !hiddenInput) return;

  // Dropdown aç / kapa
  dropdown.addEventListener("click", function (e) {
    e.stopPropagation();
    options.style.display =
      options.style.display === "block" ? "none" : "block";
  });

  // Kategori seçimi
  options.querySelectorAll("li").forEach(function (item) {
    item.addEventListener("click", function (e) {
      e.stopPropagation();

      const value = item.getAttribute("data-value");
      const text = item.textContent;

      selected.textContent = text.replace(/&/g, "ve");
      hiddenInput.value = value;

      options.style.display = "none";
    });
  });

  // Dışarı tıklanınca kapat
  document.addEventListener("click", function () {
    options.style.display = "none";
  });

  // === URL'DEN KATEGORİYİ AL VE DROPDOWN'U OTOMATİK SEÇ ===
  const urlParams = new URLSearchParams(window.location.search);
  const selectedCategoryId = urlParams.get("category");

  if (selectedCategoryId) {
    const matchedOption = options.querySelector(
      `li[data-value="${selectedCategoryId}"]`
    );
    if (matchedOption) {
      selected.textContent = matchedOption.textContent.replace(/&/g, "ve");
      hiddenInput.value = selectedCategoryId;
    }
  }

  // === İŞLETME ADI ===
  const companyInput = document.getElementById("companyInput");
  const companyIdInput = document.getElementById("companyIdInput");

  if (companyInput) {
    companyInput.addEventListener("input", function () {
      if (companyIdInput) companyIdInput.value = "";
    });
  }

  // === İNCELEME SAYISI ve PUAN FİLTRELERİ ===
  function getCheckedValues(name) {
    return Array.from(
      document.querySelectorAll(`input[name='${name}']:checked`)
    ).map(el => el.value);
  }

  // === FİLTRELE BUTONU ===
  const filterButton = document.getElementById("filterButton");
  if (filterButton) {
    filterButton.addEventListener("click", function () {
      const form = this.closest("form");
      if (form) form.submit();
    });
  }
});
