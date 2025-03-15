document.addEventListener("DOMContentLoaded", function() {
    const sidebarToggle = document.getElementById("sidebar-toggle");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    const closeBtn = document.getElementById("sidebar-close");
    
    function openSidebar() {
        sidebar.classList.toggle("open");
        overlay.classList.toggle("active");
    }
    sidebarToggle.addEventListener("click", openSidebar);
    closeBtn.addEventListener("click", openSidebar);
    overlay.addEventListener("click", openSidebar);
});

// JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const profilePic = document.querySelector('.header-account-picture');
    const modal = document.getElementById('accountModal');
    
    profilePic.addEventListener("click", () => {
        modal.classList.toggle("active");
    });


    // Close modal when clicking outside
    document.addEventListener("click", (event) => {
        if (!modal.contains(event.target) && !profilePic.contains(event.target)) {
            modal.classList.remove("active");
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const savedTheme = localStorage.getItem("theme") || document.documentElement.getAttribute("data-theme") || "dark";
    document.documentElement.setAttribute("data-theme", savedTheme);

    document.getElementById("theme-toggle").addEventListener("click", function () {
        const currentTheme = document.documentElement.getAttribute("data-theme");
        const newTheme = currentTheme === "dark" ? "light" : "dark";

        // Apply new theme
        document.documentElement.setAttribute("data-theme", newTheme);

        // Save to localStorage
        localStorage.setItem("theme", newTheme);

        // Save to PHP cookie (expires in 1 year)
        document.cookie = `theme=${newTheme}; path=/; max-age=31536000`;
    });
});