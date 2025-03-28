<?php 
    require_once '../api/config.php';
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/format-date.php';
    require_once '../components/render-posts.php';
    require_once '../components/render-sidebar.php';
    require_once '../components/get-breadcrumbs.php';
    require_once '../components/render-announcements.php';
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
    <!-- <div class="subheader p-4">
        <div class="flex flex-row">
            <i class="fa-solid fa-circle-user" style="color: #242424;"></i>
            <a href="#" class="inter-600 pl-2">My Account</a>  
        </div>
    </div> -->
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content w-bg">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <h2 class="text-xl gradient-text inter-700 pb-4">University Announcements</h2>
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

                <h2 class="text-xl gradient-text inter-700 pb-4">Student Government Announcements</h2>

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
                            echo "<p class='pb-4 text-white inter-400'>No announcements available.</p>";
                        }
                    ?>
                </div>

        </div>
        <div class="pup-wrapper" style="position: fixed; width: 280px;">
                <h3 class="text-xl text-white inter-700 pt-4 pb-2">Your Groups</h3>
                <div class="flex flex-col gap-4">
                    <?php 
                        $conn = establish_connection();
                        $group_query = "SELECT mg.*, u.username as creator_username
                                        FROM group_members gm
                                        JOIN member_groups mg ON gm.group_id = mg.id
                                        JOIN users u ON mg.creator_id = u.id
                                        WHERE gm.user_id = ?
                        ";
                        $group_stmt = $conn->prepare($group_query);
                        $group_stmt->bind_param("i", $_SESSION['id']);
                        $group_stmt->execute();
                        $groups = $group_stmt->get_result();
                        $defaultProfilePicture = "../uploads/profile_pictures/default.svg";
                    ?>
                    <?php while ($group = $groups->fetch_assoc()): ?>
                        <?php $groupProfilePicture = !empty($group['group_picture']) ? "../uploads/group_pictures/" . $group['group_picture'] : $defaultProfilePicture; ?>
                        <div class="group-card">
                        <a href="group.php?id=<?php echo $group['id']; ?>" class="group-link"></a>
                            <img class="header-account-picture" src="<?php echo $groupProfilePicture ?>" />
                            <div class="flex flex-col">
                                <h2 class="text-base gradient-text inter-600"><?= $group['name']; ?></h2>
                                <p class="group-card-desc text-sm inter-300 text-white"><?= $group['description']; ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <h2 class="text-xl text-white inter-700 pt-4 pb-2">Active Users</h2>
                <div class="flex flex-col gap-4">
                    <?php
                        $users_query = "SELECT u.id, u.username, u.full_name, u.profile_picture, u.is_active, u.last_active
                        FROM users u
                        ORDER BY u.last_active DESC
                        LIMIT 8
                        ";
                        $users_stmt = $conn->prepare($users_query);
                        $users_stmt->execute();
                        $users = $users_stmt->get_result();

                    ?>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <?php 
                        $profilePicture = !empty($user['profile_picture']) ? "../uploads/profile_pictures/" . $user['profile_picture'] : $defaultProfilePicture;
                        $status = $user['is_active'] ? "Active now" : "Last active " . formatRelativeTime($user['last_active']);
                        ?>
                        <div class="flex flex-row gap-4 align-center">
                            <img class="header-account-picture" src="<?php echo $profilePicture; ?>" alt="Profile Picture"/>
                            <div class="flex flex-col">
                                <h2 class="text-base gradient-text inter-600"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                                <a href="profile.php?id=<?php echo $user['id']; ?>" class="text-sm text-white inter-400 decoration-none hover-underline">@<?php echo htmlspecialchars($user['username']); ?></a>
                                <div class="user-status pt-1">
                                    <span class="status-dot <?php echo $user['is_active'] ? 'active' : 'inactive'; ?>"></span>
                                    <span class="status-text post-date" data-timestamp="<?php echo $user['last_active']; ?>">
                                        <?php echo $user['is_active'] ? 'Active now' : 'Last active ' . formatRelativeTime($user['last_active']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
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