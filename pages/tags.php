<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';
require_once '../components/render-posts.php';

$conn = establish_connection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tags_query = $conn->query("
    SELECT t.id, t.name, COUNT(pt.post_id) AS post_count
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
        <div class="main-content">
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
    </div>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
</body>
</html>
<?php $conn->close(); ?>