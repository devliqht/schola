<?php 
    session_start();
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/render-sidebar.php';
    require_once '../components/get-breadcrumbs.php';
    require_once '../components/render-posts.php';
    require_once '../api/level_config.php';
    require_once '../validation/leveling-system.php';

    // Check if user is logged in
    if (!isset($_SESSION['role']) || empty($_SESSION['role'])) {
        header("Location: ../index.php");
        exit();
    }

    $conn = establish_connection();
    $user_id = intval($_GET['id']) ?? null;
    $query = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $query->bind_param("s", $user_id);
    $query->execute();
    $res = $query->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }

    $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; 
    $profilePicture = !empty($row['profile_picture']) ? "../uploads/profile_pictures/" . $row['profile_picture'] : $defaultProfilePicture;

    // Fetch user posts
    $postQuery = $conn->prepare("SELECT * FROM posts WHERE author_id = ? ORDER BY created_at DESC");
    $postQuery->bind_param("i", $user_id);
    $postQuery->execute();
    $postRes = $postQuery->get_result();

    // Fetch user comments
    $commentQuery = $conn->prepare("SELECT c.*, p.title FROM comments c JOIN posts p ON c.post_id = p.id WHERE c.user_id = ? ORDER BY c.created_at DESC");
    $commentQuery->bind_param("i", $user_id);
    $commentQuery->execute();
    $commentRes = $commentQuery->get_result();

    $virtusPoints = getVirtusPoints($user_id);
    $devotioLevel = getUserLevel($user_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile - <?= htmlspecialchars($row['full_name']) ?></title>
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
</head>
<body>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <div class="cover-photo"></div>
            <div class="profile-details py-4">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-row gap-4 profile-info-container">
                        <div class="profile-picture">
                            <img class="account-img" src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture">
                        </div>
                        <div class="flex flex-col">
                            <h1 class="gradient-text inter-700 profile-name"><?= htmlspecialchars($row['full_name']) ?></h1>
                            <h2 class="inter-500 profile-username">@<?= htmlspecialchars($row['username']) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col pr-4">
                    <h1 class="text-lg gradient-text inter-700"><?php echo $row['course']; ?></h1>
                    <h1 class="text-sm inter-300 text-white">Joined <?php echo date("F j, Y", strtotime($row['created_at'])); ?></h1>
                </div>
            </div>
            <hr />
            <div class="flex flex-col">
                <div class="flex flex-row align-center justify-between">
                    <p class="gradient-text text-xl inter-700">Devotio Level <?= floor($devotioLevel) ?></p>
                    <p class="gradient-text text-xl inter-700">Virtus Points: <?= $virtusPoints ?></p>
                </div>
                <div class="progress-bar">
                    <div class="progress" style="width: <?= ($devotioLevel - floor($devotioLevel)) * 100 ?>%"></div>
                </div>
                <p class="gradient-text text-xl inter-700"></p>
            </div>
            <hr />
            <div class="flex flex-col py-2">
                <h2 class="text-base inter-300 pb-4 text-white">Posts by <span class="inter-700 gradient-text"><?= htmlspecialchars($row['full_name']) ?></span></h2>
                <?php if ($postRes->num_rows > 0): ?>
                <?php while ($post = $postRes->fetch_assoc()) {
                    $authorQuery = $conn->prepare("SELECT username, profile_picture from users WHERE id = ?");
                    $authorQuery->bind_param("i", $post['author_id']);
                    $authorQuery->execute();
                    $authorRes = $authorQuery->get_result();
                    $author = $authorRes->fetch_assoc();


                    renderPost($post['title'], $post['content'], $author['username'], date("F j, Y", strtotime($post['created_at'])), $post['id'], $author['profile_picture'], $post['pinned']);
                }
                ?>
                <?php else: ?>
                    <p class="inter-300 text-white">No posts yet.</p>
                <?php endif; ?>
            </div>
            <div class="flex flex-col pt-4">
            <h2 class="text-base inter-300 pb-4 text-white">Comments by <span class="inter-700 gradient-text"><?= htmlspecialchars($row['full_name']) ?></span></h2>
            <?php if ($commentRes->num_rows > 0): ?>
                <?php while ($comment = $commentRes->fetch_assoc()): ?>
                    <div class="profile-comment-item">
                        <p class="text-base">
                            <span class="inter-600"><?php echo $row['username']; ?></span> commented on <a href="../pages/post.php?id=<?= $comment['post_id'] ?>" class="decoration-none text-base gradient-text"><?= htmlspecialchars($comment['title']) ?></a>
                        </p>
                        <hr />
                        <p class="text-base inter-300"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                        <p class="text-xs inter-300 pt-2" style="color:rgb(149, 149, 149);"><?= date("F j, Y", strtotime($comment['created_at'])); ?></p>
                    </div>
                <?php endwhile; ?>
                <?php else: ?>
                    <p class="inter-300 text-white">No comments posted yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
</body>
</html>