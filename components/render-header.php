<?php 
function render_header() {
    ?>
    <header class="header px-4"> 
        <div class="flex flex-row align-center" style="gap: 0.4rem;">
            <div class="sidebar-toggle-wrapper">
                <button id="sidebar-toggle" class="sidebar-toggle"><i class="fa-solid fa-bars fa-xs text-white" style="text-decoration: none;"></i></button>
            </div>
            <div class="branding p-1">
                <img class="logo" src="../assets/logo.png"/>
            </div>
        </div>
        <div class="header-search-container" style="flex-grow: 0.5;">
            <form id="header-search-form" action="../validation/search.php" method="GET">
                <div class="header-search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" class="header-search-input text-sm w-full" placeholder="Search USC..." id="search-input">
                    <i class="fa-solid fa-xmark clear-search" id="clear-search"></i>
                </div>
            </form>
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
            <div class="flex flex-row" style="gap: 0.4rem;">
                <a href="create-post.php" ><button class="interaction inter-600 text-sm"><i class="fa-solid fa-plus"></i> <span class="disappear-768px">Create</span></button></a>
                <a href="create-post.php" ><button class="interaction inter-600 text-sm"><i class="fa-solid fa-bell"></i> </button></a>
            </div>
            <div class="header-account-container">
                <img class="header-account-picture" src="<?php echo $profilePicture; ?>" alt="Profile Picture">
                <div class="account-modal" id="accountModal">
                    <div class="account-modal-content">
                        <h1 class="gradient-text text-lg inter-700">My Account</h1>
                        <div class="user-info">
                            <img class="header-account-picture" src="<?php echo $profilePicture; ?>" alt="Pfp"/>
                                <div class="user-details">
                                    <a class="text-base inter-700 gradient-text" href="profile.php?id=<?= $_SESSION['id']; ?>">
                                        <?= htmlspecialchars($_SESSION['full_name']) ?>
                                    </a>
                                    <p class="text-xs inter-400 text-white">Profile</p>
                                </div>
                            </div>
                        <button id="theme-toggle" class="modal-item interaction text-base"><i class="fa-solid fa-moon"></i> Dark Mode</button>
                        <a href="account.php" class="modal-item"><i class="fa-solid fa-user-gear"></i>Settings</a>
                        <a href="../logout.php" class="modal-item"><i class="fa-solid fa-right-from-bracket"></i>Sign out</a>
                    </div>
                </div>
            </div>
        </div>  
    </header>
    <?php
}
?>