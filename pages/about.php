<?php 
    require_once '../api/config.php';
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/render-sidebar.php';
    require_once '../components/get-breadcrumbs.php';
    $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; 
    $profilePicture = isset($_SESSION['profile_picture']) ? "../uploads/profile_pictures/" . $_SESSION['profile_picture'] : $defaultProfilePicture;
?>
<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title>Forum Dashboard</title>
    <link rel="stylesheet" href="../css/utilities/fonts.css" />
    <link rel="stylesheet" href="../css/utilities/util-text.css" />
    <link rel="stylesheet" href="../css/utilities/util-padding.css" />
    <link rel="stylesheet" href="../css/utilities/inputs.css" />
    <link rel="stylesheet" href="../css/utilities/utility.css" />
    <link rel="stylesheet" href="../css/posts.css" />
    <link rel="stylesheet" href="../css/form.css" />
    <link rel="stylesheet" href="../css/colors.css" />
    <link rel="stylesheet" href="../css/sidebar.css" />
    <link rel="stylesheet" href="../css/header.css" />
    <link rel="stylesheet" href="../css/admin.css" />
    <link rel="stylesheet" href="../css/account.css" />
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
    </head>
<body>
<?php if (isset($_SESSION['role']) && !empty($_SESSION['role'])): ?>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <div class="flex flex-col">
                <h1 class="text-2xl text-white inter-600 gradient-text">About Schola</h1>
                <p class="text-base text-muted inter-300 pt-4">
                Schola is a forum application designed for schools, providing a platform for students, teachers, and staff to interact, share information, and participate in discussions. It features forum interactions, user profiles, a leveling system, and more.

This is a school project, but I hope to implement something like this later in the future but with a more modern approach and with modern technologies.
                </p>

                <hr class="w-full" style="margin: 1rem 0 1rem 0;"/>
                <h1 class="text-xl text-white inter-600">Features:</h1>
                <ul class="text-white inter-300 text-muted" style="list-style: disc inside;">
                    <li class="text-base inter-300 pt-2">Forum Interactions: Create and participate in discussions, threads, and posts, special sections dedicated to the University/School</li>
                    <li class="text-base inter-300 pt-2">User Profiles: Customizable user profiles with information and activity tracking</li>
                    <li class="text-base inter-300 pt-2">Leveling System: Reward active users with a leveling system based on their participation</li>
                    <li class="text-base inter-300 pt-2">Community Groups: Community groups for common interests</li>
                </ul>

                <hr class="w-full" style="margin: 1rem 0 1rem 0;"/>
                <h1 class="text-xl text-white inter-600">Tech Stack:</h1>
                <ul class="text-white inter-300 text-muted" style="list-style: disc inside;">
                    <li class="text-base inter-300 pt-2">HTML</li>
                    <li class="text-base inter-300 pt-2">CSS</li>
                    <li class="text-base inter-300 pt-2">JavaScript</li>
                    <li class="text-base inter-300 pt-2">PHP</li>
                    <li class="text-base inter-300 pt-2">MySQL</li>
                </ul>
                <h1 class="text-xl text-white inter-600 pt-4">Third-party Technologies used:</h1>
                <ul class="text-white inter-300 text-muted" style="list-style: disc inside;">
                    <li class="text-base inter-300 pt-2">CropperJS: Image cropping and resizing</li>
                    <li class="text-base inter-300 pt-2">FontAwesome: SVG inline icons</li>
                    <li class="text-base inter-300 pt-2">tinyMCE: Content formatter</li>
                </ul>

                <hr class="w-full" style="margin: 1rem 0 1rem 0;"/>
                <h1 class="text-xl text-white inter-600">Code</h1>
                <ul class="text-white inter-300 text-muted" >
                    <li class="text-base inter-300 pt-2">You can check the github repo for Schola <a href="https://github.com/devliqht/schola">here.</a></li>
                        <li class="text-base inter-300 pt-2">Lines of code written: 7,768</li>
                </ul>

                <hr class="w-full" style="margin: 1rem 0 1rem 0;"/>
                <h1 class="text-xl text-white inter-600">Acknowledgements</h1>
                <ul class="text-white inter-300 text-muted" style="list-style: disc inside;">
                    <li class="text-base inter-300 pt-2">Sir Christian Maderazo</li>
                </ul>

            </div>
        </div>
    </div>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
    <?php else: ?>
    <?php  
        header("Location: ../index.php");    
    ?>
    <?php endif; ?>
</body>
</html>