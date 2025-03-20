<?php 
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';

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

$members_stmt = $conn->prepare("SELECT u.username, u.role FROM group_members gm JOIN users u ON gm.user_id = u.id WHERE gm.group_id = ?");
$members_stmt->bind_param("i", $group_id);
$members_stmt->execute();
$members = $members_stmt->get_result();
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
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
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
            <h2 class="text-2xl gradient-text inter-700"><?php echo htmlspecialchars($group['name']); ?></h2>
            <p class="text-sm text-white">Created by <?php echo htmlspecialchars($group['username']); ?> on <?php echo (new DateTime($group['created_at']))->format('M d, Y'); ?></p>
            <h3 class="text-2xl gradient-text inter-700 pt-4">Members</h3>
            <table class="group-table">
                <thead>
                    <tr>
                        <th class="gradient-text text-xl inter-500">Username</th>
                        <th class="gradient-text text-xl inter-500">Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($member = $members->fetch_assoc()): ?>
                        <tr>
                            <td class="text-white"><?php echo htmlspecialchars($member['username']); ?></td>
                            <td class="text-white"><?php echo htmlspecialchars($member['role']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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