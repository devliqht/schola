<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/format-date.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';
require_once '../components/render-posts.php';


$conn = establish_connection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tags_query = $conn->query("SELECT t.id, t.name, COUNT(pt.post_id) AS post_count
    FROM tags t
    LEFT JOIN post_tags pt ON t.id = pt.tag_id
    GROUP BY t.id, t.name
");

if (!$tags_query) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
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
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
    <title>Tags</title>
</head>
</head>
<body>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content w-bg">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <h2 class="gradient-text inter-700 text-xl">Filter posts by tags</h2>
            <hr />
            <div class="tags-grid">
                <?php while ($tag = $tags_query->fetch_assoc()): ?>
                    <a href="tag.php?id=<?= htmlspecialchars($tag['id']) ?>" class="tag-box inter-700 flex flex-col">
                        #<?= htmlspecialchars($tag['name']) ?> <br/>
                        <span class="inter-300"><?= $tag['post_count'] ?> posts</span>  
                    </a>
                <?php endwhile; ?>
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
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
</body>
</html>
<?php $conn->close(); ?>