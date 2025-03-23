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
    <title>Create Group</title>
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
    <link rel="stylesheet" href="../css/groups.css" />
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../vendor/cropperjs/cropper.min.css">
    <script src="../vendor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="../vendor/cropperjs/cropper.min.js"></script>
    <link rel="icon" type="image/png" href="../assets/logo.png">
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
            <h2 class="text-xl gradient-text inter-700">Create Group</h2>
            <form action="../validation/add-group.php" method="POST" enctype="multipart/form-data">
                <div class="group-picture-container">
                    <img src="../uploads/group_pictures/default_group.svg" alt="Group Picture" class="group-picture" id="groupPicturePreview">
                    <button type="button" class="edit-picture-btn" onclick="openPictureModal()">
                        <i class="fas fa-pen"></i>
                    </button>
                </div>
                <input type="text" name="group_name" placeholder="Enter group name" class="input-field">
                <textarea type="text" name="group_description" rows="4" placeholder="Enter group description..." class="input-field"></textarea> 
                
                <!-- Hidden input to store the cropped image data -->
                <input type="hidden" name="group_picture_data" id="groupPictureData">
                <button type="submit" class="action-button">Create</button>
            </form>
            <div class="modal" id="pictureModal">
                <div class="modal-content">
                    <span class="modal-close" onclick="closePictureModal()">&times;</span>
                    <h3 class="text-md inter-600 text-white">Upload and Crop Group Picture</h3>
                    <input type="file" id="pictureInput" accept="image/*" style="margin: 1rem 0;">
                    <div class="cropper-container" id="cropperContainer" style="display: none;">
                        <img id="cropperImage">
                    </div>
                    <div class="cropper-actions" id="cropperActions" style="display: none;">
                        <button type="button" class="interaction" onclick="saveCroppedImage()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let cropper = null;

        function openPictureModal() {
            document.getElementById('pictureModal').style.display = 'flex';
        }

        function closePictureModal() {
            document.getElementById('pictureModal').style.display = 'none';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            document.getElementById('cropperContainer').style.display = 'none';
            document.getElementById('cropperActions').style.display = 'none';
            document.getElementById('pictureInput').value = ''; // Reset file input
        }

        document.getElementById('pictureInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const maxSize = 2 * 1024 * 1024; 
            if (file.size > maxSize) {
                alert('File size exceeds 2MB limit. Please choose a smaller image.');
                e.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                const img = document.getElementById('cropperImage');
                img.src = event.target.result;
                document.getElementById('cropperContainer').style.display = 'block';
                document.getElementById('cropperActions').style.display = 'flex';

                if (cropper) cropper.destroy();
                cropper = new Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 1, 
                    dragMode: 'move', 
                    autoCropArea: 0.8, 
                    movable: true,
                    zoomable: true,
                    scalable: false,
                    rotatable: false
                });
            };
            reader.readAsDataURL(file);
        });

        function saveCroppedImage() {
            if (!cropper) return;
            const canvas = cropper.getCroppedCanvas({
                width: 200, 
                height: 200
            });
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

            document.getElementById('groupPicturePreview').src = dataUrl;

            document.getElementById('groupPictureData').value = dataUrl;
            closePictureModal();
        }
    </script>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
<?php else: ?>
    <?php header("Location: ../index.php"); exit(); ?>
<?php endif; ?>
</body>
</html>