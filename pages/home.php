<?php 
    require_once '../api/config.php';
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/render-posts-min.php';
    require_once '../components/render-sidebar.php';
    require_once '../components/render-post.php';
    require_once '../components/get-breadcrumbs.php';
    require_once '../components/render-announcements.php';
?>
<!DOCTYPE html>
<html>
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
</head>
<body>
    <?php if (isset($_SESSION['role']) && !empty($_SESSION['role'])): ?>
        <?php  render_header();?>
        <div class="grid-container">
            <?php render_sidebar(); ?>
            <div class="main-content animate__animated animate__fadeIn animate__faster">
                <nav class="breadcrumb">
                    <?php echo get_breadcrumbs(); ?>
                </nav>

                <div class="flex flex-row gap-4 justify-between align-center">
                    <h2 class="text-xl text-white inter-700 pb-4">Welcome <span class="gradient-text"><?= $_SESSION['full_name']; ?></span></h2>
                </div>
                <div class="discussions-wrapper">
                    <?php 
                        $conn = establish_connection();
                
                        $posts_query = "SELECT posts.*, users.username, users.full_name, users.profile_picture FROM posts 
                                        JOIN users ON posts.author_id = users.id 
                                        WHERE post_type = 'regular'
                                        ORDER BY posts.created_at DESC";
                        $posts_result = $conn->query($posts_query);

                        while ($post = $posts_result->fetch_assoc()) {
                            render_post($post); // Render each post 
                        }
                        $conn->close();
                    ?>

                </div>
            </div>
            <!-- <div class="flex flex-col p-4 recent-posts-sidebar">
                <h1 class="gradient-text inter-700 text-xl tracking-tight pb-2">Recent Posts</h1>
                <?php 
                        $conn = establish_connection();
                
                        $posts_query = "SELECT posts.*, users.username FROM posts 
                                        JOIN users ON posts.author_id = users.id 
                                        WHERE post_type = 'regular'
                                        ORDER BY posts.created_at DESC";

                        $posts_result = $conn->query($posts_query);

                        if ($posts_result->num_rows > 0) {
                            while ($post = $posts_result->fetch_assoc()) {                            
                                renderPost_min(
                                    $post['title'],
                                    $post['username'],
                                    $post['created_at'],
                                    $post['id']
                                );
                            }
                        } else {
                            echo "<p>No posts available.</p>";
                        }
                ?>
            </div> -->
        </div>
    <script>
        function openModal() {
            document.getElementById("postModal").style.display = "flex";
        }
        
        function closeModal() {
            document.getElementById("postModal").style.display = "none";
        }
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