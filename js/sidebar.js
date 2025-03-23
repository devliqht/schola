document.addEventListener("DOMContentLoaded", function() {
    const sidebarToggle = document.getElementById("sidebar-toggle");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    const closeBtn = document.getElementById("sidebar-close");
    const collapseBtn = document.getElementById('sidebar-collapse');
    const gridContainer = document.querySelector('.grid-container');
    const commentTextareas = document.querySelectorAll('.comment-textarea');
    const profilePic = document.querySelector('.header-account-picture');
    const modal = document.getElementById('accountModal');
    const savedTheme = localStorage.getItem("theme") || document.documentElement.getAttribute("data-theme") || "dark";

    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        gridContainer.style.gridTemplateColumns = "180px 1fr 260px";
    }
    
    function openSidebar() {
        sidebar.classList.toggle("open");
        overlay.classList.toggle("active");
    }
    
    if (sidebarToggle) sidebarToggle.addEventListener("click", openSidebar);
    if (closeBtn) closeBtn.addEventListener("click", openSidebar);
    if (overlay) overlay.addEventListener("click", openSidebar);

    if (profilePic && modal) {
        profilePic.addEventListener("click", () => {
            modal.classList.toggle("active");
        });
        document.addEventListener("click", (event) => {
            if (!modal.contains(event.target) && !profilePic.contains(event.target)) {
                modal.classList.remove("active");
            }
        });
    }

    document.documentElement.setAttribute("data-theme", savedTheme);

    const themeToggle = document.getElementById("theme-toggle");
    if (themeToggle) {
        themeToggle.addEventListener("click", function() {
            const currentTheme = document.documentElement.getAttribute("data-theme");
            const newTheme = currentTheme === "dark" ? "light" : "dark";
            
            document.documentElement.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
            document.cookie = `theme=${newTheme}; path=/; max-age=31536000`;
        });
    }

    if (sidebar && collapseBtn && gridContainer) {
        const isCollapsed = sidebar.classList.contains('collapsed');
        const isMobile = window.matchMedia('(max-width: 768px)').matches;

        if (isCollapsed && !isMobile) {
            gridContainer.style.gridTemplateColumns = isMobile ? '' : '100px 1fr 360px';
        } else {
            gridContainer.style.gridTemplateColumns = '';
        }

        collapseBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            gridContainer.style.gridTemplateColumns = isCollapsed ? '100px 1fr 360px' : '260px 1fr 300px';
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
    } else {
        console.error('Required sidebar elements not found');
    }

    commentTextareas.forEach(textarea => {
        const form = textarea.closest('form');
        if (form) {
            textarea.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    form.submit();
                }
            });
            function adjustHeight() {
                textarea.style.height = 'auto';
                textarea.style.height = `${textarea.scrollHeight}px`;
            }
            textarea.addEventListener('input', adjustHeight);
            adjustHeight();
        }
    });

    document.querySelectorAll('.clear-button').forEach(button => {
        const tooltip = button.nextElementSibling;

        button.addEventListener('click', (event) => {
            event.preventDefault(); 
            tooltip.classList.toggle('active');
        });

        document.addEventListener('click', (event) => {
            if (!button.contains(event.target) && !tooltip.contains(event.target)) {
                tooltip.classList.remove('active');
            }
        });

        const editButton = tooltip.querySelector('.edit-comment');
        editButton.addEventListener('click', () => {
            alert('Edit functionality to be implemented');
            tooltip.classList.remove('active');
        });
    });
});