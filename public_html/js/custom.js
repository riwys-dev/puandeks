<script>
    // DOM yüklendiğinde çalışacak
    document.addEventListener('DOMContentLoaded', function() {
      const cookieSettingsLink = document.getElementById('cookieSettingsLink'); 
      const cookieModal = document.getElementById('cookieModal');
      const cookieClose = document.querySelector('.cookie-modal-close');
      const saveCookiePreferences = document.getElementById('saveCookiePreferences');
      
      // Linke tıklayınca modal aç
      cookieSettingsLink.addEventListener('click', function(e){
        e.preventDefault();
        cookieModal.style.display = 'flex'; 
      });
      
      // Kapatma ikonu
      cookieClose.addEventListener('click', function(){
        cookieModal.style.display = 'none';
      });
      
      // Modalın dışına tıklayınca kapat
      window.addEventListener('click', function(e){
        if(e.target === cookieModal){
          cookieModal.style.display = 'none';
        }
      });
      
      // Tercihleri kaydet
      saveCookiePreferences.addEventListener('click', function(){
        // Burada checkbox’ların değerlerini okuyup localStorage veya cookie’de saklayabilirsiniz.
        // Örneğin:
        const performance = document.getElementById('performanceCookies').checked;
        const functional = document.getElementById('functionalCookies').checked;
        const marketing = document.getElementById('marketingCookies').checked;
        
        console.log('Performans:', performance, 'İşlevsellik:', functional, 'Pazarlama:', marketing);
        
        // Modal kapat
        cookieModal.style.display = 'none';
        alert('Çerez tercihleri kaydedildi!');
      });
    });
    </script>