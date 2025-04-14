<?php 
    require_once '../api/config.php';
    require_once '../api/db_connection.php';
    require_once '../components/format-date.php';
    require_once '../components/render-header.php';
    require_once '../components/render-posts-min.php';
    require_once '../components/render-sidebar.php';
    require_once '../components/render-post.php';
    require_once '../components/render-pup.php';
    require_once '../components/get-breadcrumbs.php';
    require_once '../components/render-announcements.php';

    $conn = establish_connection();
    $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; 
    $user_query = "SELECT profile_picture from users where id = ?";
    $user_result = $conn->prepare($user_query);
    $user_result->bind_param("i", $_SESSION['id']);
    $user_result->execute();
    $user_result = $user_result->get_result();
    
    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $profilePicture = "../uploads/profile_pictures/" . $user_data['profile_picture'];
    } else {
        $profilePicture = $defaultProfilePicture;
    }
?>
<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title>Forum Dashboard</title>
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
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
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
    <style>
        
    </style>
    </head>
<body>
    <?php if (isset($_SESSION['role']) && !empty($_SESSION['role'])): ?>
        <?php  render_header();?>
        <div class="grid-container">
            <?php render_sidebar(); ?>
            <div class="main-content animate__animated animate__fadeIn animate__faster">

                <div class="flex flex-col w-full pt-2">
                <h2 class="text-xl text-white inter-700">Welcome <span class="gradient-text"><?= $_SESSION['full_name']; ?></span></h2>
                <form action="create-post.php" id="post-form">
                    <div class="flex flex-row gap-4 align-center">
                            <img class="header-account-picture" src="<?php echo $profilePicture; ?>" alt="Pfp"/>
                            <input type="text" placeholder="What's on your mind, <?php echo $_SESSION['username']; ?>?"/>
                        </div>
                    </div>
                </form>    
                <div class="discussions-wrapper">
                    <?php 
                        $conn = establish_connection();
                        $posts_query = "SELECT posts.*, users.username, users.full_name, users.profile_picture, 
                                        COUNT(DISTINCT post_likes.id) AS like_count, 
                                        COUNT(DISTINCT comments.id) AS comment_count 
                                        FROM posts 
                                        JOIN users ON posts.author_id = users.id 
                                        LEFT JOIN post_likes ON post_likes.post_id = posts.id 
                                        LEFT JOIN comments ON comments.post_id = posts.id 
                                        WHERE post_type = 'regular' OR post_type = 'announcement'
                                        GROUP BY posts.id, users.username, users.full_name, users.profile_picture 
                                        ORDER BY posts.created_at DESC";
                        $posts_result = $conn->query($posts_query);

                        if (!$posts_result) {
                            die("Query failed: " . $conn->error); 
                        }
                        while ($post = $posts_result->fetch_assoc()) {
                            render_post($post); 
                        }
                        $conn->close();
                    ?>
                </div>
            </div>
            <?php render_pup(); ?>
        </div>
        <?php render_navbar(); ?>
    <script>
        const form = document.getElementById("post-form");

        function openModal() {
            document.getElementById("postModal").style.display = "flex";
        }
        
        function closeModal() {
            document.getElementById("postModal").style.display = "none";
        }

        form.addEventListener('keypress', function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                form.submit();
            }
        });
    </script>
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