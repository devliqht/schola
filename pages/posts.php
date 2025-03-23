<?php 
    require_once '../api/config.php';
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/render-posts.php';
    require_once '../components/get-breadcrumbs.php';
    require_once '../components/render-sidebar.php';
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
        .logo {
            width: 54px;
        }
    </style>
</head>
<body style="overflow-x: hidden;">
    <?php if (isset($_SESSION['role']) && !empty($_SESSION['role'])): ?>
    <?php 
        render_header();
    ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content w-bg animate__animated animate__fadeIn animate__faster">
        <nav class="breadcrumb">
            <?php echo get_breadcrumbs(); ?>
        </nav>
            <div class="flex flex-row gap-4 align-center justify-between pb-4">
                <h2 class="text-xl gradient-text inter-700">Announcements</h2>
            </div>
            <div class="discussions-wrapper posts">
                <?php 
                    $conn = establish_connection();
                    $result = $conn->query("SELECT id, title, content, author_id, created_at, pinned FROM posts WHERE post_type = 'announcement' ORDER BY posts.pinned DESC, posts.created_at DESC ");
                    while ($row = $result->fetch_assoc()) {
                        $userQuery = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
                        $userQuery->bind_param("i", $row['author_id']);
                        $userQuery->execute();
                        $userResult = $userQuery->get_result();
                        $user = $userResult->fetch_assoc();

                        renderPost($row['title'], $row['content'], $user['username'], date("F j, Y", strtotime($row['created_at'])), $row['id'], $user['profile_picture'], $row['pinned']);
                    }
                    $conn->close();
                ?>
            </div>
            <div class="p-4"></div>
            <div class="flex flex-row gap-4 align-center justify-between pb-4">
                <h2 class="text-xl gradient-text inter-700">Posts</h2>
                    <a href="create-post.php" ><button class="interaction inter-700">Create Post</button></a>
            </div>
            <div class="discussions-wrapper posts">
                <?php 
                    $conn = establish_connection();
            
                    $result = $conn->query("SELECT id, title, content, author_id, created_at, pinned FROM posts WHERE post_type = 'regular' ORDER BY posts.pinned DESC, posts.created_at DESC ");
                    while ($row = $result->fetch_assoc()) {
                        $userQuery = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
                        $userQuery->bind_param("i", $row['author_id']);
                        $userQuery->execute();
                        $userResult = $userQuery->get_result();
                        $user = $userResult->fetch_assoc();

                        renderPost($row['title'], $row['content'], $user['username'], date("F j, Y", strtotime($row['created_at'])), $row['id'], $user['profile_picture'], $row['pinned']);
                    }

                    $conn->close();
                ?>
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