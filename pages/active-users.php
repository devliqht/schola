<?php 
require_once '../api/config.php';
require_once '../api/update_user_activity.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-sidebar.php';
require_once '../components/render-pup.php';
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
    <style>
        .active-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .active-item {
            display: flex;
            flex-direction: row;
            border-radius: 1rem;
            padding: 1rem;
            color: var(--text-light);
            transition: transform 0.3s ease, background-color 0.3s ease;
            box-sizing: border-box;
            overflow: hidden;
            position: relative;
            text-decoration: none; 
            cursor: pointer; 
            gap: 0.675rem;
        }
        </style>
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
            <div class="active-container">
                <?php while ($user = $users->fetch_assoc()): ?>
                    <?php 
                    $profilePicture = !empty($user['profile_picture']) ? "../uploads/profile_pictures/" . $user['profile_picture'] : $defaultProfilePicture;
                    $status = $user['is_active'] ? "Active now" : "Last active " . formatRelativeTime($user['last_active']);
                    ?>
                    <div class="active-item">
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
        <?php render_pup(); ?>
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