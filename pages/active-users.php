<?php 
require_once '../api/config.php';
require_once '../api/update_user_activity.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';
require_once '../components/format-date.php';

$conn = establish_connection();
$users_query = "SELECT u.id, u.username, u.full_name, u.profile_picture, u.is_active, u.last_active
                FROM users u
                ORDER BY u.last_active DESC
";
$users_stmt = $conn->prepare($users_query);
$users_stmt->execute();
$users = $users_stmt->get_result();

$defaultProfilePicture = "../uploads/profile_pictures/default.svg";
?>

<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title>Active Users</title>
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
    <link rel="stylesheet" href="../css/groups.css" />
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
            <h2 class="text-2xl gradient-text inter-700">Active Users</h2>
            <div class="flex flex-col gap-4 pt-4">
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
<?php else: ?>
    <?php header("Location: ../index.php"); ?>
<?php endif; ?>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
</body>
</html>
<?php $conn->close(); ?>