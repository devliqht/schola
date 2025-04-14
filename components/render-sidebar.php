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
        "groups.php"  => ["icon" => "fa-layer-group", "label" => "Groups"],
        "tags.php"    => ["icon" => "fa-tags", "label" => "Tags"],
        "calendar.php" => ["icon" => "fa-calendar", "label" => "Calendar"],
    ];

    $universityLinks = [
        "announcements.php"    => ["icon" => "fa-bullhorn", "label" => "Announcements"],
        "posts.php" => ["icon" => "fa-comments", "label" => "Posts"],
    ];

    $adminLinks = [
        "admin.php" => ["icon" => "fa-users", "label" => "Manage Users"],
        "create-events.php" => ["icon" => "fa-sliders", "label" => "Manage Events"],
        "active-users.php" => ["icon" => "fa-user", "label" => "Active Users"],
    ];

    $sidebarHTML = <<<HTML
<div id="sidebar-overlay" class="sidebar-overlay"></div>
<div class="right-sidebar" id="sidebar">
        <script>
            (function() {     
                const sidebar = document.getElementById('sidebar');
                const gridContainer = document.querySelector('.grid-container');
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    sidebar.classList.add('collapsed');
                    gridContainer.style.gridTemplateColumns = "100px 1fr 300px";
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

    foreach ($links as $file => $data) {
        $activeClass = isActive($file, $current_page); // Call the function first
        $sidebarHTML .= <<<HTML
        <li class="p-1">
            <a href="{$file}" class="{$activeClass}">
                <i class="fa-solid {$data['icon']}"></i> <span class="link-label inter-300 text-sm">{$data['label']}</span>
            </a>
        </li>
HTML;
    }

    $sidebarHTML .= <<<HTML
    </ul>
    <div class="p-4"><hr/></div>
    <h1 class="text-lg text-muted inter-600 sidebar-header-title tracking-tight">University</h1>
    <ul class="quick-links">
HTML;

    foreach ($universityLinks as $file => $data) {
        $activeClass = isActive($file, $current_page); // Call the function first
        $sidebarHTML .= <<<HTML
        <li class="p-1">
            <a href="{$file}" class="{$activeClass}">
                <i class="fa-solid {$data['icon']}"></i> <span class="link-label inter-300 text-sm">{$data['label']}</span>
            </a>
        </li>
HTML;
    }

    if ($role === 'admin') {
        $sidebarHTML .= <<<HTML
    </ul>
    <div class="p-4"><hr/></div>
    <h1 class="text-lg text-muted inter-600 sidebar-header-title tracking-tight">Admin</h1>
    <ul class="quick-links">
HTML;
        foreach ($adminLinks as $file => $data) {
            $sidebarHTML .= <<<HTML
            <li class="inter-300 text-sm p-1">
                <a href="{$file}" class="{isActive($file, $current_page)}">
                    <i class="fa-solid {$data['icon']}"></i> <span class="link-label inter-300 text-sm">{$data['label']}</span>
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

<?php
function render_navbar() {
    ?>
    <nav class="bottom-nav">
    <ul class="bottom-nav-list">
        <li>
            <a href="home.php" class="<?php echo isActive('home.php', basename($_SERVER['PHP_SELF'])); ?>">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
        </li>
        <li>
            <a href="posts.php" class="<?php echo isActive('posts.php', basename($_SERVER['PHP_SELF'])); ?>">
                <i class="fa-solid fa-comments"></i>
                <span>Posts</span>
            </a>
        </li>
        <li>
            <a href="announcements.php" class="<?php echo isActive('announcements.php', basename($_SERVER['PHP_SELF'])); ?>">
                <i class="fa-solid fa-bullhorn"></i>
                <span>Announce</span>
            </a>
        </li>
        <li>
            <a href="groups.php" class="<?php echo isActive('groups.php', basename($_SERVER['PHP_SELF'])); ?>">
                <i class="fa-solid fa-layer-group"></i>
                <span>Groups</span>
            </a>
        </li>
        <li>
            <a href="connect.php" class="<?php echo isActive('connect.php', basename($_SERVER['PHP_SELF'])); ?>">
                <i class="fa-solid fa-users"></i>
                <span>Connect</span>
            </a>
        </li>
    </ul>
</nav>
<?php
}
?>