document.addEventListener("DOMContentLoaded", function () {
  const dropdown = document.querySelector(".custom-dropdown");
  const selectBox = document.getElementById("dropdownSelect");
  const options = document.getElementById("dropdownOptions");
  const hiddenInput = document.getElementById("templateInput");
  const selectedText = selectBox.querySelector(".selected-text");

  // Aç/kapa işlemi
  selectBox.addEventListener("click", function () {
    dropdown.classList.toggle("open");
  });

  // Seçim yapıldığında
  options.querySelectorAll("li").forEach(function (item) {
    item.addEventListener("click", function () {
      const value = this.getAttribute("data-value");
      const text = this.textContent;

      selectedText.textContent = text;
      hiddenInput.value = value;
      dropdown.classList.remove("open");
    });
  });

  // Sayfanın başka yerine tıklanırsa dropdown kapansın
  document.addEventListener("click", function (e) {
    if (!dropdown.contains(e.target)) {
      dropdown.classList.remove("open");
    }
  });
});
