<?php 
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-posts.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';

if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$conn = establish_connection();
$post_id = intval($_GET['id']);

$query = "SELECT * FROM posts WHERE id = ? AND author_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $post_id, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    echo "<p>Post not found or unauthorized.</p>";
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title>Edit Post</title>
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
    <script src="../vendor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
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
            <h2 class="text-xl gradient-text inter-700">Edit Post</h2>
            <form action="../validation/update-post.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                <h1 class="text-base gradient-text inter-700">Title:</h1>
                <input type="text" maxlength="64" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                
                <h1 class="text-base gradient-text inter-700">Content:</h1>
                <textarea name="content" id="postContent"><?php echo htmlspecialchars($post['content']); ?></textarea>
                
                <div class="flex flex-row gap-4 align-center">
                    <h1 class="text-base gradient-text inter-700">Pin Post</h1>
                    <input type="checkbox" name="pinned" style="width: fit-content;" value="1" <?php echo $post['pinned'] ? 'checked' : ''; ?>>
                </div>
                
                <button type="submit" id="submit">Update Post</button>
            </form>
        </div>
    </div>
    <script>
       tinymce.init({
        selector: '#postContent',
        plugins: 'image link lists',
        toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | image link | bullist numlist',
        menubar: false,
        height: 300,
        images_upload_url: '../validation/upload-image.php',
        images_upload_handler: function(blobInfo) {
            console.log('Starting upload for:', blobInfo.filename()); // Debug start
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                const fetchPromise = fetch('../validation/upload-image.php', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Fetch initiated'); // Debug fetch start
                
                fetchPromise
                    .then(response => {
                        console.log('Response Status:', response.status);
                        console.log('Response OK:', response.ok);
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.text();
                    })
                    .then(text => {
                        console.log('Raw Response:', text);
                        let result;
                        try {
                            result = JSON.parse(text);
                        } catch (e) {
                            console.error('JSON Parse Error:', e);
                            reject('Invalid JSON response: ' + text);
                            return;
                        }
                        if (result.location) {
                            console.log('Resolving with location:', result.location);
                            resolve(result.location);
                        } else {
                            console.log('Rejecting with error:', result.error);
                            reject('Upload failed: ' + (result.error || 'Unknown error') + (result.details ? ' - ' + result.details.message : ''));
                        }
                    })
                    .catch(error => {
                        console.error('Upload Error:', error);
                        reject('Image upload failed: ' + error.message);
                    });
            });
        },
        setup: function(editor) {
            editor.on('submit', function(e) {
                if (!editor.getContent().trim()) {
                    e.preventDefault();
                    alert('Content cannot be empty');
                }
            });
        }
    });
    </script>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
<?php else: ?>
    <?php header("Location: ../index.php"); exit(); ?>
<?php endif; ?>
<?php $conn->close(); ?>
</body>
</html>