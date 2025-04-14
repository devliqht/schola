<?php 
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-posts.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';

$conn = establish_connection();
$role = $_SESSION['role'] ?? 'user'; 

$groups_stmt = $conn->prepare("SELECT mg.id, mg.name FROM member_groups mg JOIN group_members gm ON mg.id = gm.group_id WHERE gm.user_id = ?");
$groups_stmt->bind_param("i", $_SESSION['id']);
$groups_stmt->execute();
$groups = $groups_stmt->get_result();
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
    <!-- Self-hosted TinyMCE -->
    <link rel="icon" type="image/png" href="../assets/logo.png">
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
            <h2 class="text-xl gradient-text inter-700 pb-4">Create Post</h2>
            <form action="../validation/add-post.php" method="POST" class="add-post-form" enctype="multipart/form-data">
                <div class="flex flex-col w-full">
                    <h1 class="text-base text-white inter-700 pb-2">Select a group</h1>
                    <select name="group_id" class="input-field">
                        <option value="">Global</option>
                        <?php while ($group = $groups->fetch_assoc()): ?>
                            <option value="<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <p class="pt-4 text-white text-base inter-700">Post Content</p>
                    <input type="text" maxlength="64" id="postTitle" name="postTitle" placeholder="Post Title" required>
                    <textarea id="postContent" name="postContent" placeholder="Post Content"></textarea>
                    <input type="text" id="tagsInput" placeholder="Add tags (comma-separated)">
                    <input type="hidden" name="postTags" id="postTags"> 

                    
                    <?php if ($role === 'officer' || $role === 'admin'): ?>
                        <h1 class="pt-4 text-xl gradient-text inter-700 pb-4">Admin Controls</h1>
                        <h1 class="text-base text-white inter-700 pb-2">Post Type:</h1>
                        <select id="postType" name="postType">
                            <option value="regular">Regular Post</option>
                            <option value="announcement">Announcement</option>
                        </select>
                        <div class="p-2"></div>
                        <h1 class="text-base text-white inter-700 pb-2">Announcement From:</h1>
                        <select id="postCategory" name="postCategory">
                            <option value="university">University</option>
                            <option value="ssg">Supreme Student Government</option>
                        </select>
                    <?php endif; ?>

                    <input type="submit" id="submit" value="Submit" />
                </div>
            </form>
        </div>
    </div>
    <?php render_navbar(); ?>
    <script>
       tinymce.init({
        selector: '#postContent',
        plugins: 'image link lists autoresize',
        toolbar: 'undo redo | h1 h2 h3 h4 | bold italic underline | alignleft aligncenter alignright | image link | bullist numlist',
        menubar: false,
        height: 300,
        images_upload_url: '../validation/upload-image.php',
        image_dimensions: false, // Prevent hardcoded width/height
        image_class_list: [
            { title: 'Responsive', value: 'responsive-image' } // Add a class for styling
        ],
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
        // Tags handling
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