<?php 
    require_once '../api/config.php';
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/render-posts.php';
    require_once '../components/render-sidebar.php';
    require_once '../components/get-breadcrumbs.php';

    // Check user role
    $role = $_SESSION['role'] ?? 'user'; 
?>
<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>"> 
<head>
    <title>Create Post</title>
    <link rel="stylesheet" href="../css/utilities/fonts.css" />
    <link rel="stylesheet" href="../css/utilities/util-text.css" />
    <link rel="stylesheet" href="../css/utilities/util-padding.css" />
    <link rel="stylesheet" href="../css/utilities/inputs.css" />
    <link rel="stylesheet" href="../css/utilities/utility.css" />
    <link rel="stylesheet" href="../css/posts.css" />
    <link rel="stylesheet" href="../css/colors.css" />
    <link rel="stylesheet" href="../css/sidebar.css" />
    <link rel="stylesheet" href="../css/header.css" />
    <link rel="stylesheet" href="../css/form.css" />
    <link rel="stylesheet" href="../css/admin.css" />
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
</head>
<body>
<?php if (!empty($_SESSION['role'])): ?>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <h2 class="text-xl gradient-text inter-700">Create Post</h2>
            <hr/>
            <form action="../validation/add-post.php" method="POST" class="add-post-form">
                <div class="flex flex-col w-full">
                    <input type="text" maxlength=64 id="postTitle" name="postTitle" placeholder="Post Title" required>
                    <textarea id="postContent" name="postContent" placeholder="Post Content" rows="10" required></textarea>
                    <input type="text" id="tagsInput" placeholder="Add tags (comma-separated)">
                    <input type="hidden" name="postTags" id="postTags"> 

                    <?php if ($role === 'officer' || $role === 'admin'): ?>
                        <h1 class="text-base gradient-text inter-700">Post Type:</h1>
                        <select id="postType" name="postType">
                            <option value="regular">Regular Post</option>
                            <option value="announcement">Announcement</option>
                        </select>

                        <h1 class="text-base gradient-text inter-700">Announcement From:</h1>
                        <select id="postCategory" name="postCategory">
                            <option value="university">University</option>
                            <option value="ssg">Supreme Student Government</option>
                        </select>
                    <?php endif; ?>

                    <input type="submit" id="submit" />
                </div>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('tagsInput').addEventListener('change', function() {
            document.getElementById('postTags').value = this.value;
        });
    </script>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
<?php else: ?>
    <?php header("Location: ../index.php"); exit(); ?>
<?php endif; ?>
</body>
</html>