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

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebar-collapse');
    const gridContainer = document.querySelector('.grid-container');

    if (!sidebar || !collapseBtn || !gridContainer) {
        console.error('Required elements not found');
        return;
    }

    // Sync grid container gap with initial state
    const isCollapsed = sidebar.classList.contains('collapsed');
    const isMobile = window.matchMedia('(max-width: 768px)').matches;

    if (isCollapsed && !isMobile) {
        if (isMobile) {
            gridContainer.style.gridTemplateColumns = '';
        } else {
            gridContainer.style.gridTemplateColumns = '180x 1fr 300px';
        }
    } else {
        gridContainer.style.gridTemplateColumns = '';
    }

    // Handle toggle
    collapseBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        const isCollapsed = sidebar.classList.contains('collapsed');
        gridContainer.style.gridTemplateColumns = isCollapsed ? '180px 1fr 300px' : '260px 1fr 300px';
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    });
});