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

// JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const profilePic = document.querySelector('.header-account-picture');
    const modal = document.getElementById('accountModal');
    
    // Toggle modal on profile picture click
    profilePic.addEventListener('click', function(e) {
        e.preventDefault();
        modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
    });

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (!profilePic.contains(e.target) && !modal.contains(e.target)) {
            modal.style.display = 'none';
        }
    });
});