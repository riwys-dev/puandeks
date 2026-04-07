document.addEventListener("DOMContentLoaded", function(){
  const hamburger = document.getElementById("hamburger");
  const sidebar = document.getElementById("sidebar");
  const sidebarClose = document.getElementById("sidebarClose");
  const overlay = document.getElementById("overlay");

  function openMenu() {
    sidebar.classList.add("open");
    overlay.classList.add("show");
  }

  function closeMenu() {
    sidebar.classList.remove("open");
    overlay.classList.remove("show");
  }

  if(hamburger){
    hamburger.addEventListener("click", openMenu);
  }
  if(sidebarClose){
    sidebarClose.addEventListener("click", closeMenu);
  }
  if(overlay){
    overlay.addEventListener("click", closeMenu);
  }
});
