const popupBtn = document.getElementById("openPopupBtn");
const popupBox = document.getElementById("popupBox");
const popupOverlay = document.getElementById("popupOverlay");
const closeBtn = document.querySelector(".close-btn");
const submitBtn = document.getElementById("popupSubmitBtn");
const alertBox = document.getElementById("popupAlert");

popupBtn?.addEventListener("click", function (e) {
  e.preventDefault();
  popupOverlay.style.display = "block";
  popupBox.style.display = "block";
});

closeBtn?.addEventListener("click", closePopup);
popupOverlay?.addEventListener("click", closePopup);

function closePopup() {
  popupOverlay.style.display = "none";
  popupBox.style.display = "none";
}

submitBtn?.addEventListener("click", function (e) {
  e.preventDefault();
  closePopup();
  alertBox.style.display = "block";
  setTimeout(() => {
    alertBox.style.display = "none";
  }, 3000);
});
