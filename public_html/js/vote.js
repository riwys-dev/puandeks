document.addEventListener("DOMContentLoaded", function () {
  const colors = {
    1: "#e74c3c",   // kırmızı
    2: "#e67e22",   // turuncu
    3: "#f1c40f",   // sarı
    4: "#2ecc71",   // açık yeşil
    5: "#006400"    // koyu yeşil
  };

  document.querySelectorAll(".vote-stars").forEach(function (el) {
    const rating = parseFloat(el.dataset.score);
    const fullStars = Math.floor(rating);
    const hasHalf = rating % 1 >= 0.5;
    const color = colors[Math.round(rating)] || "#ccc";

    const icons = el.querySelectorAll(".vote-icon");
    icons.forEach((icon, index) => {
      if (index < fullStars || (index === fullStars && hasHalf)) {
        icon.classList.add("filled");
        icon.style.color = color;
      } else {
        icon.classList.remove("filled");
        icon.style.color = "#ccc";
      }
    });
  });
});
