<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';
require_once '../components/render-post.php';

$conn = establish_connection();
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid tag.");
}

$tag_id = intval($_GET['id']);

// Fetch tag name
$tag_query = $conn->prepare("SELECT name FROM tags WHERE id = ?");
$tag_query->bind_param("i", $tag_id);
$tag_query->execute();
$tag_result = $tag_query->get_result();
$tag_data = $tag_result->fetch_assoc();
if (!$tag_data) {
    die("Tag not found.");
}
$tag_name = htmlspecialchars($tag_data['name']);

$posts_query = $conn->prepare("SELECT p.id, p.title, p.content, p.created_at, u.profile_picture, u.username, u.full_name,
                                        COUNT(DISTINCT pl.id) AS like_count, 
                                        COUNT(DISTINCT c.id) AS comment_count 
                                        FROM posts p
                                        JOIN post_tags pt ON p.id = pt.post_id
                                        JOIN users u ON p.author_id = u.id
                                        LEFT JOIN post_likes pl ON p.id = pl.post_id
                                        LEFT JOIN comments c ON p.id = c.post_id
                                        WHERE pt.tag_id = ?
                                        GROUP BY p.id, p.title, p.content, p.created_at, u.profile_picture, u.username, u.full_name");
$posts_query->bind_param("i", $tag_id);
$posts_query->execute();
$posts_result = $posts_query->get_result();
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
    <title>Posts tagged '<?= $tag_name ?>'</title>
</head>
</head>
<body>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>

            <h1 class="text-xl gradient-text inter-700">Posts tagged '<?= $tag_name ?>'</h1>
            <hr/>
            <?php if ($posts_result->num_rows > 0): ?>
                <ul>
                    <?php while ($post = $posts_result->fetch_assoc()): ?>
                        <?php 
                            render_post($post); 
                        ?>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-base inter-300 text-white">No posts found for this tag.</p>
            <?php endif; ?>

        </div>
    </div>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
</body>
</html>
<?php $conn->close(); ?>