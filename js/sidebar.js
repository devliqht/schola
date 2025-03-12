document.addEventListener("DOMContentLoaded", function() {
    const sidebarToggle = document.getElementById("sidebar-toggle");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    const closeBtn = document.getElementById("sidebar-close");

    sidebarToggle.addEventListener("click", function() {
        sidebar.classList.toggle("open");
    });

    function openSidebar() {
        sidebar.classList.add("open");
        overlay.classList.add("active");
    }

    function closeSidebar() {
        sidebar.classList.remove("open");
        overlay.classList.remove("active");
    }

    sidebarToggle.addEventListener("click", openSidebar);
    closeBtn.addEventListener("click", closeSidebar);
    overlay.addEventListener("click", closeSidebar);
});