<?php 
    session_start();
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/render-posts.php';
    require_once '../components/render-sidebar.php';
    require_once '../components/get-breadcrumbs.php';
    require_once '../components/render-announcements.php';
?>
<!DOCTYPE html>
<html>
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
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
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
    <!-- <div class="subheader p-4">
        <div class="flex flex-row">
            <i class="fa-solid fa-circle-user" style="color: #242424;"></i>
            <a href="#" class="inter-600 pl-2">My Account</a>  
        </div>
    </div> -->
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <div class="flex flex-row gap-4 align-center justify-between">
                <h2 class="text-xl gradient-text inter-700">Announcements</h2>
                <?php if ($_SESSION['role'] === 'officer' || $_SESSION['role'] === 'admin'): ?>
                    <a href="create-post.php" ><button class="interaction inter-700">Create Announcement</button></a>
                        <div class="modal" id="postModal">
                            <form action="../validation/add-post.php" method="POST">
                                <div class="modal-content">
                                    <span class="close-btn" onclick="closeModal()">&times;</span>
                                    <h2 class="gradient-text inter-600 text-xl">Create a Post</h2>
                                    <input type="text" id="postTitle" name="postTitle" placeholder="Post Title" required>
                                    <textarea id="postContent" name="postContent" placeholder="Post Content" rows="4" required></textarea>
                                    <input type="submit" class="submit-btn" />
                                </div>
                            </form>
                        </div>
                <?php endif; ?>
            </div>
            <hr/>
            <h2 class="text-xl gradient-text inter-700">University Announcements</h2>
                <hr/>
                <div class="latest-container">
                    <?php 
                        $conn = establish_connection();
                        $announcement_sql = "SELECT posts.*, users.username FROM posts 
                        JOIN users ON posts.author_id = users.id
                        WHERE post_type = 'announcement' AND post_category = 'university'
                        ORDER BY pinned DESC, created_at DESC
                        LIMIT 3";
                        
                        // $announcement_sql = "SELECT p.*, u.username,
                        //                     FROM posts p
                        //                     JOIN post_tags pt ON p.id = pt.post_id
                        //                     JOIN users u ON p.author_id = u.id
                        //                     WHERE pt.tag_id = ?
                        //                     ORDER BY p.pinned DESC, p.created_at DESC
                        // ";
      

                        $announcement_result = $conn->query($announcement_sql);
                        if ($announcement_result->num_rows > 0) {
                            while ($row = $announcement_result->fetch_assoc()) {
                                render_announcement($row, "university");
                            }
                        } else {
                            echo "<p class='pb-4'>No announcements available.</p>";
                        }
                    ?>
                </div>

                <h2 class="text-xl gradient-text inter-700">Student Government Announcements</h2>
                <hr/>

                <div class="latest-container">
                    <?php 
                        $conn = establish_connection();
                        $government_sql = "SELECT posts.*, users.username FROM posts 
                        JOIN users ON posts.author_id = users.id
                        WHERE post_type = 'announcement' AND post_category = 'ssg'
                        ORDER BY created_at DESC
                        LIMIT 3";

                        $government_result = $conn->query($government_sql);
                        if ($government_result->num_rows > 0) {
                            while ($row = $government_result->fetch_assoc()) {
                                render_announcement($row, "ssg");
                            }
                        } else {
                            echo "<p class='pb-4'>No announcements available.</p>";
                        }
                    ?>
                </div>

        </div>
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