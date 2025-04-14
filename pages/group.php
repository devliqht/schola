<?php 
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-post.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';
require_once '../components/render-posts.php';

if (!isset($_GET['id'])) {
    header("Location: groups.php");
    exit();
}

$conn = establish_connection();
$group_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT mg.*, u.username FROM member_groups mg JOIN users u ON mg.creator_id = u.id WHERE mg.id = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$group = $stmt->get_result()->fetch_assoc();

if (!$group) {
    header("Location: groups.php?error=group_not_found");
    exit();
}

if (isset($_GET['join']) && $_GET['join'] == 1) {
    $user_count = $conn->query("SELECT COUNT(*) FROM group_members WHERE user_id = " . $_SESSION['id'])->fetch_row()[0];
    if ($user_count < 3) {
        $stmt = $conn->prepare("INSERT IGNORE INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $group_id, $_SESSION['id']);
        $stmt->execute();
    }
    header("Location: group.php?id=$group_id");
    exit();
} elseif (isset($_GET['leave']) && $_GET['leave'] == 1) {
    $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $group_id, $_SESSION['id']);
    $stmt->execute();
    header("Location: group.php?id=$group_id");
    exit();
}

$members_stmt = $conn->prepare("SELECT u.id, u.username, u.full_name, u.profile_picture FROM group_members gm JOIN users u ON gm.user_id = u.id WHERE gm.group_id = ?");
$members_stmt->bind_param("i", $group_id);
$members_stmt->execute();
$members = $members_stmt->get_result();

$is_member = $conn->query("SELECT 1 FROM group_members WHERE group_id = $group_id AND user_id = " . $_SESSION['id'])->num_rows > 0;

$posts_stmt = $conn->prepare("
    SELECT p.*, u.username, u.full_name, u.profile_picture, 
           (SELECT COUNT(*) FROM post_likes pl WHERE pl.post_id = p.id) as like_count,
           (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comment_count
    FROM posts p 
    JOIN users u ON p.author_id = u.id 
    WHERE p.group_id = ? 
    ORDER BY p.created_at DESC
");
$posts_stmt->bind_param("i", $group_id);
$posts_stmt->execute();
$posts = $posts_stmt->get_result();

$defaultProfilePicture = "../uploads/profile_pictures/default.svg";
$groupProfilePicture = !empty($group['group_picture']) ? "../uploads/group_pictures/" . $group['group_picture'] : $defaultProfilePicture; 

?>

<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title><?php echo htmlspecialchars($group['name']); ?></title>
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
        .group-table { width: 100%; border-collapse: collapse; }
        .group-table th, .group-table td { padding: 10px; text-align: left; border-bottom: 1px solid var(--border-main-dark); }
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
            <div class="flex flex-row justify-between pb-4">
                <div class="flex flex-row gap-4 align-center">   
                    <img class="header-account-picture" src="<?php echo $groupProfilePicture; ?>">
                    <div class="flex flex-col">
                        <h2 class="text-2xl gradient-text inter-700"><?php echo htmlspecialchars($group['name']); ?></h2>
                        <p class="text-sm text-muted">Created by <?php echo htmlspecialchars($group['username']); ?> on <?php echo (new DateTime($group['created_at']))->format('M d, Y'); ?></p>
                    </div>
                </div>
            <?php if ($is_member): ?>   
                <a href="create-post.php" ><button class="interaction inter-600 text-sm"><i class="fa-solid fa-plus"></i> <span class="disappear-768px">Create Post</span></button></a>
            <?php else: ?>
                <a href="group.php?id=<?php echo $group_id; ?>&join=1" ><button class="interaction inter-600 text-sm"><i class="fa-solid fa-plus"></i> <span class="disappear-768px">Join Group</span></button></a>
            <?php endif; ?>
            </div>
            <p class="text-base text-white inter-300"><?php echo htmlspecialchars($group['description']); ?></p>
            <h3 class="text-xl gradient-text inter-700 py-4">Group Feed</h3>
            <?php if ($posts->num_rows > 0): ?>
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <?php render_post($post); ?>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-base inter-300 text-white">No posts in this group yet.</p>
            <?php endif; ?>
        </div>
        <div class="pup-wrapper">
            <h3 class="text-2xl gradient-text inter-700 py-4">Members</h3>
            <div class="flex flex-col gap-4">
                <?php while ($member = $members->fetch_assoc()): ?>
                    <?php $memberProfilePicture = !empty($member['profile_picture']) ? "../uploads/profile_pictures/" . $member['profile_picture'] : $defaultProfilePicture; ?>
                    <div class="flex flex-row gap-4">
                        <img class="header-account-picture" src="<?php echo $memberProfilePicture ?>" />
                        <div class="flex flex-col">
                            <h2 class="text-base gradient-text inter-600"><?= $member['full_name']; ?></h2>
                            <a href="profile.php?id=<?php echo $member['id']; ?>" class="text-sm text-white inter-400 decoration-none hover-underline">@<?= $member['username']; ?></a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php render_navbar(); ?>
<?php else: ?>
    <?php header("Location: ../index.php"); ?>
<?php endif; ?>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
</body>
</html>
<?php $conn->close(); ?>