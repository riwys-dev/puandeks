document.addEventListener("DOMContentLoaded", function () {
  // Cookie oku
  function getCookie(name) {
    let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
  }

  // Cookie yaz
  function setCookie(name, value, days) {
    let d = new Date();
    d.setTime(d.getTime() + (days*24*60*60*1000));
    document.cookie = name + "=" + value + ";path=/;expires=" + d.toUTCString();
  }

  // UUID üret
  function generateUUID() {
    return 'xxxxxx4xyx'.replace(/[xy]/g, function(c) {
      var r = Math.random() * 16 | 0,
          v = c === 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }

  // Visitor ID kontrol
  let visitorId = getCookie("visitor_id") || localStorage.getItem("visitor_id");
  if (!visitorId) {
    visitorId = generateUUID();
    setCookie("visitor_id", visitorId, 365);
    localStorage.setItem("visitor_id", visitorId);
  }

  // PHP tarafından gönderilen company_id
  let companyId = document.body.getAttribute("data-company-id");

  if (companyId && visitorId) {
    fetch("/backend/api/save-visitor.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ visitor_id: visitorId, company_id: companyId })
    }).then(res => res.json())
      .then(data => console.log("Visitor kaydedildi:", data))
      .catch(err => console.error("Visitor kayıt hatası:", err));
  }
});
