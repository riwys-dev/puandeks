document.addEventListener("DOMContentLoaded", function () {
  const dropdown = document.querySelector(".dropdown-user");
  const menu = document.querySelector(".dropdown-menu-user");

  if (dropdown && menu) {
    dropdown.addEventListener("click", function (e) {
      e.stopPropagation();
      menu.style.display = (menu.style.display === "block") ? "none" : "block";
    });

    document.addEventListener("click", function () {
      menu.style.display = "none";
    });
  }
});
