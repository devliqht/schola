<?php 
function render_header() {
    ?>
    <header class="header px-4"> 
        <div class="flex flex-row align-center" style="gap: 0.4rem;">
            <div class="sidebar-toggle-wrapper">
                <button id="sidebar-toggle" class="sidebar-toggle"><i class="fa-solid fa-bars fa-xs" style="color: black; text-decoration: none;"></i></button>
            </div>
            <div class="branding p-1">
                <img class="logo" src="../assets/logo.png" />
                <div class="header-search-container">
                    <form id="header-search-form" action="../validation/search.php" method="GET">
                        <input type="text" class="header-search-input text-sm w-full" style="margin: 0;" placeholder="Search USC..." id="search-input">
                        <button type="submit" class="interaction inter-400 rounded-full" style="background:rgb(255, 255, 255);"><i class="fa-solid fa-magnifying-glass" style="color: black; text-decoration: none;"></i></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="header-nav-links">
            <div class="link-wrapper"><a href="announcements.php" class="text-base inter-600">Announcements</a></div>
            <div class="link-wrapper"><a href="posts.php" class="text-base inter-600">Posts</a></div>
            <div class="link-wrapper"><a href="groups.php" class="text-base inter-600">Groups</a></div>
        </div>
        <div class="search-results-overlay" id="search-results-overlay">
            <div class="search-results-content">
                <h1 class="text-xl inter-600 gradient-text pb-4">Search Results for "<span id="search-query"></span>"</h1>
                <button class="search-close-btn" id="search-close-btn">×</button>
                <div class="search-results-list" id="search-results-list">
                    <!-- Results will be populated by JS -->
                </div>
            </div>
        </div>
        <?php 
         $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; // Set a default image
         $profilePicture = isset($_SESSION['profile_picture']) ? "../uploads/profile_pictures/" . $_SESSION['profile_picture'] : $defaultProfilePicture;
        ?>
        <div class="flex flex-row align-center gap-4">
            <div class="flex flex-col header-text">
                <a href="home.php" class="decoration-none"><h1 class="gradient-text inter-700 text-xl tracking-tight">University of San Carlos</h1></a>
            </div>
            <img class="header-account-picture" src="<?php echo $profilePicture; ?>" alt="Pfp"/>
        </div>  
    </header>
    <?php
}
?>