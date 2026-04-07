// js/cookie-settings.js
document.addEventListener('DOMContentLoaded', function() {
  const saveCookiePrefs = document.getElementById('saveCookiePreferences');

  if (saveCookiePrefs) {
    saveCookiePrefs.addEventListener('click', function() {
      const performance = document.getElementById('performanceCookies').checked;
      const functional  = document.getElementById('functionalCookies').checked;
      const marketing   = document.getElementById('marketingCookies').checked;

      // Toggle durumlarını kaydet
      localStorage.setItem('cookiePerformance', performance);
      localStorage.setItem('cookieFunctional', functional);
      localStorage.setItem('cookieMarketing', marketing);

      // Banner bir daha çıkmasın
      localStorage.setItem('cookieConsent', 'accepted');

      alert('Çerez tercihleri kaydedildi!');

      // Geldiği sayfaya yönlendir (fallback: ana sayfa)
      if (document.referrer) {
        window.location.href = document.referrer;
      } else {
        window.location.href = "/";
      }
    });
  }
});
