<?php 
function render_sidebar() {
    $role = $_SESSION['role'];
    $current_page = basename($_SERVER['PHP_SELF']); 
    $user_id = $_SESSION['id'];
    function isActive($page, $current_page) {
        return $current_page === $page ? 'active-link' : '';
    }

    echo '<div id="sidebar-overlay" class="sidebar-overlay"></div>';
    echo '<div class="right-sidebar" id="sidebar">';

    $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; // Set a default image
    $profilePicture = isset($_SESSION['profile_picture']) ? "../uploads/profile_pictures/" . $_SESSION['profile_picture'] : $defaultProfilePicture;
    echo '<div class="p-1"></div>';
    echo '<div class="user-info">
        <img class="header-account-picture" src="'. $profilePicture .'" alt="Pfp"/>
        <div class="user-details">
            <a class="text-base inter-700 gradient-text" href="profile.php?id='.$user_id.'">
                '. htmlspecialchars($_SESSION['full_name']) . '
            </a>
            <p class="text-xs inter-400">Profile</p>
        </div>
    </div> ';
    echo '<button id="sidebar-close" class="sidebar-close"><i class="fa-solid fa-times"></i></button>';

    echo '    <ul class="quick-links">';

    $links = [
        "home.php"    => ["icon" => "fa-house", "label" => "Home"],
        "connect.php" => ["icon" => "fa-users", "label" => "Connect"],
        "groups.php"  => ["icon" => "fa-layer-group", "label" => "Your Groups"],
        "tags.php"    => ["icon" => "fa-tags", "label" => "Tags"],
        "calendar.php"    => ["icon" => "fa-calendar", "label" => "Calendar"],
    ];

    foreach ($links as $file => $data) {
        echo '<li class="inter-300 text-sm" >';
        echo '    <a href="' . $file . '" class="' . isActive($file, $current_page) . '">';
        echo '        <i class="fa-solid ' . $data["icon"] . '"></i> ' . $data["label"];
        echo '    </a>';
        echo '</li>';
    }

    echo '<div class="p-4"></div>';

    if ($role === 'admin') {
        $adminLinks = [
            "admin.php" => ["icon" => "fa-users", "label" => "Manage Users"],
            "posts.php" => ["icon" => "fa-sliders", "label" => "Manage Posts"],
            "create-events.php" => ["icon" => "fa-sliders", "label" => "Manage Events"],
        ];

        echo '    <h1 class="gradient-text text-lg inter-700 pl-4 border-b-1" style="text-transform: capitalize;">Admin</h1>';
        foreach ($adminLinks as $file => $data) {
            echo '<li class="inter-300 text-sm">';
            echo '    <a href="' . $file . '" class="' . isActive($file, $current_page) . '">';
            echo '        <i class="fa-solid ' . $data["icon"] . '"></i> ' . $data["label"];
            echo '    </a>';
            echo '</li>';
        }
    }

    echo '<div class="p-4"></div>';

    $otherLinks = [
        "account.php" => ["icon" => "fa-user-gear", "label" => "My Account"],
        "../logout.php"    => ["icon" => "fa-right-from-bracket", "label" => "Sign Out"],
    ];
    echo '    <h1 class="gradient-text text-lg inter-700 pl-4 border-b-1" style="text-transform: capitalize;">Settings</h1>';
    foreach ($otherLinks as $file => $data) {
        echo '<li class="inter-300 text-sm">';
        echo '    <a href="' . $file . '" class="' . isActive($file, $current_page) . '">';
        echo '        <i class="fa-solid ' . $data["icon"] . '"></i> ' . $data["label"];
        echo '    </a>';
        echo '</li>';
    }

    echo '    </ul>';
    echo '</div>';
}
?>