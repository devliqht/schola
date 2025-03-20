<?php
function render_sidebar() {
    $role = $_SESSION['role'];
    $current_page = basename($_SERVER['PHP_SELF']);
    $user_id = $_SESSION['id'];

    function isActive($page, $current_page) {
        return $current_page === $page ? 'active-link' : '';
    }

    $defaultProfilePicture = "../uploads/profile_pictures/default.svg";
    $profilePicture = isset($_SESSION['profile_picture']) 
        ? "../uploads/profile_pictures/" . $_SESSION['profile_picture'] 
        : $defaultProfilePicture;

    $links = [
        "home.php"    => ["icon" => "fa-house", "label" => "Home"],
        "connect.php" => ["icon" => "fa-users", "label" => "Connect"],
        "groups.php"  => ["icon" => "fa-layer-group", "label" => "Your Groups"],
        "tags.php"    => ["icon" => "fa-tags", "label" => "Tags"],
        "calendar.php" => ["icon" => "fa-calendar", "label" => "Calendar"],
    ];

    $adminLinks = [
        "admin.php" => ["icon" => "fa-users", "label" => "Manage Users"],
        "posts.php" => ["icon" => "fa-sliders", "label" => "Manage Posts"],
        "create-events.php" => ["icon" => "fa-sliders", "label" => "Manage Events"],
    ];

    // Using heredoc for cleaner HTML output
    $sidebarHTML = <<<HTML
<div id="sidebar-overlay" class="sidebar-overlay"></div>
<div class="right-sidebar" id="sidebar">
<script>
            // Run immediately to set initial class
            (function() {
                const sidebar = document.getElementById('sidebar');
                const gridContainer = document.querySelector('.grid-container');
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    sidebar.classList.add('collapsed');
                    gridContainer.style.gridTemplateColumns = "200px 1fr 200px";
                }
            })();
        </script>
    <div class="p-1"></div>
    <a class="decoration-none" href="profile.php?id={$user_id}">
    <div class="user-info" style="margin: 0.25rem;">
        <img class="header-account-picture" src="{$profilePicture}" alt="Pfp"/>
        <div class="user-details text-base inter-700 tracking-tight" >
               <span class="gradient-text"> {$_SESSION['full_name']} </span>
            <p class="text-xs inter-400" style="color: var(--text-light);">Profile</p>
        </div>
    </div>
    </a>
    <button id="sidebar-close" class="sidebar-close"><i class="fa-solid fa-times"></i></button>
    <ul class="quick-links">
HTML;

    // Generate regular links
    foreach ($links as $file => $data) {
        $sidebarHTML .= <<<HTML
        <li class="inter-300 text-sm p-1">
            <a href="{$file}" class="{isActive($file, $current_page)}">
                <i class="fa-solid {$data['icon']}"></i> <span class="link-label">{$data['label']}</span>
            </a>
        </li>
HTML;
    }

    $sidebarHTML .= '<div class="p-4"></div>';

    // Admin section
    if ($role === 'admin') {
        $sidebarHTML .= <<<HTML
        <h1 class="gradient-text text-lg inter-700 pl-4 tracking-wide" style="text-transform: capitalize;">Admin</h1>
HTML;
        
        foreach ($adminLinks as $file => $data) {
            $sidebarHTML .= <<<HTML
            <li class="inter-300 text-sm p-1">
                <a href="{$file}" class="{isActive($file, $current_page)}">
                    <i class="fa-solid {$data['icon']}"></i> <span class="link-label">{$data['label']}</span>
                </a>
            </li>
HTML;
        }
    }

    $sidebarHTML .= <<<HTML
    </ul>
    <button id="sidebar-collapse" class="sidebar-collapse" title="Collapse sidebar">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
</div>
HTML;

    echo $sidebarHTML;
}
?>