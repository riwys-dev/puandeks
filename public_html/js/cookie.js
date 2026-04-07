// js/cookie.js
document.addEventListener("DOMContentLoaded", function () {
  const banner   = document.getElementById("cookieConsentBanner");
  const acceptBtn = document.getElementById("acceptCookies");
  const rejectBtn = document.getElementById("rejectCookies");

  // Banner daha önce kabul/ret edilmemişse göster
  if (!localStorage.getItem("cookieConsent")) {
    if (banner) banner.style.display = "block";
  }

  // Kabul Et
  if (acceptBtn) {
    acceptBtn.addEventListener("click", function () {
      localStorage.setItem("cookieConsent", "accepted");
      if (banner) banner.style.display = "none";
    });
  }

  // Reddet
  if (rejectBtn) {
    rejectBtn.addEventListener("click", function () {
      localStorage.setItem("cookieConsent", "rejected");
      if (banner) banner.style.display = "none";
    });
  }
});
