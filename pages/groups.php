<?php 
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';

$conn = establish_connection();
$stmt = $conn->prepare("SELECT mg.id, mg.name, mg.description, mg.group_picture, mg.created_at, u.username 
                       FROM member_groups mg 
                       JOIN users u ON mg.creator_id = u.id");
$stmt->execute();
$groups = $stmt->get_result();

$defaultGroupPicture = "../uploads/group_pictures/default_group.svg"; 

?>

<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title>Member Groups</title>
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
    <link rel="icon" type="image/png" href="../assets/logo.png">
    <style>
        .group-table { width: 100%; border-collapse: collapse; }
        .group-table th, .group-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }

    </style>
</head>
<body>
<?php if (isset($_SESSION['role']) && !empty($_SESSION['role'])): ?>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content profile">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <div class="flex flex-row justify-between pb-4">
                <h2 class="text-2xl gradient-text inter-700">Member Groups</h2>
                <a class="interaction text-sm inter-600 decoration-none" href="create-group.php">Create Group</a>
            </div>
            <div class="groups-container">
                    <?php while ($group = $groups->fetch_assoc()): ?>
                        <?php 
                        $count_stmt = $conn->prepare("SELECT COUNT(*) as member_count FROM group_members WHERE group_id = ?");
                        $count_stmt->bind_param("i", $group['id']);
                        $count_stmt->execute();
                        $member_count = $count_stmt->get_result()->fetch_assoc()['member_count'];

                        $groupPicture = !empty($group['group_picture']) ? "../uploads/group_pictures/" . $group['group_picture'] : $defaultGroupPicture;
                        $user_count = $conn->query("SELECT COUNT(*) FROM group_members WHERE user_id = " . $_SESSION['id'])->fetch_row()[0];
                        $is_member = $conn->query("SELECT 1 FROM group_members WHERE group_id = {$group['id']} AND user_id = " . $_SESSION['id'])->num_rows > 0;
                        ?>
                        <div class="group-item">
                                <a href="group.php?id=<?php echo $group['id']; ?>" class="group-link"></a>
                                <img class="group-image" src="<?php echo $groupPicture; ?>" alt="Group Picture"/>
                                <div class="group-content">
                                    <h2 class="group-title"><?php echo htmlspecialchars($group['name']); ?></h2>
                                    <p class="group-desc inter-300 text-sm"><?php echo htmlspecialchars($group['description']); ?></p>
                                    <p class="group-date"><?php echo (new DateTime($group['created_at']))->format('M d, Y'); ?></p>
                                </div>
                                <div class="group-actions">
                                    <?php if (!$is_member && $user_count < 3): ?>
                                        <a href="group.php?id=<?php echo $group['id']; ?>&join=1" class="interaction" onclick="event.stopPropagation();">
                                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Join
                                        </a>
                                    <?php elseif ($is_member): ?>
                                        <a href="group.php?id=<?php echo $group['id']; ?>&leave=1" class="interaction" onclick="event.stopPropagation();">
                                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Leave
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($_SESSION['role'] === 'admin' || ($_SESSION['role'] === 'officer' && $group['username'] === $_SESSION['username'])): ?>
                                        <button class="interaction edit-btn" 
                                                data-group-id="<?php echo $group['id']; ?>" 
                                                data-group-name="<?php echo htmlspecialchars(json_encode($group['name']), ENT_QUOTES); ?>">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>
                                        <button class="interaction delete-btn" 
                                                data-group-id="<?php echo $group['id']; ?>" 
                                                data-group-name="<?php echo htmlspecialchars(json_encode($group['name']), ENT_QUOTES); ?>">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                    <?php endwhile; ?>
            </div>
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <form action="../validation/edit-group.php" method="POST">
                        <input type="hidden" name="group_id" id="edit_group_id">
                        <div class="form-group">
                            <h1 class="gradient-text inter-700 text-2xl">Group Name</h1>
                            <input type="text" name="group_name" id="edit_group_name" class="input-field">
                        </div>
                        <button type="submit" class="action-button">Save</button>
                        <button type="button" onclick="closeEditModal()" class="action-button">Cancel</button>
                    </form>
                </div>
            </div>
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <h1 class="gradient-text text-2xl inter-700">Are you sure you want to delete this group?</h1>
                    <p id="deleteModalText" class="text-white"></p> <!-- Display group name -->
                    <form action="../validation/delete-group.php" method="POST">
                        <input type="hidden" name="group_id" id="delete_group_id">
                        <hr />
                        <button type="submit" class="action-button">Yes</button>
                        <button type="button" onclick="closeDeleteModal()" class="action-button">No</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php header("Location: ../index.php"); ?>
<?php endif; ?>

<script>
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation();
            const id = this.getAttribute('data-group-id');
            const name = JSON.parse(this.getAttribute('data-group-name')); // Decode JSON string
            showEditModal(id, name);
        });
    });

    // Delete button listeners
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation();
            const id = this.getAttribute('data-group-id');
            const name = JSON.parse(this.getAttribute('data-group-name')); // Decode JSON string
            showDeleteModal(id, name);
        });
    });

    function showEditModal(id, name) {
        document.getElementById('edit_group_id').value = id;
        document.getElementById('edit_group_name').value = name;
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function openCreateModal() {
        document.getElementById('createModal').style.display = 'flex';
    }

    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
    }
    window.onclick = function(event) {
        if (event.target.className === 'modal') closeEditModal();
    }
    function showDeleteModal(id, name) {
    document.getElementById('delete_group_id').value = id;
    document.getElementById('deleteModalText').innerText = `You are about to delete the group "${name}". This action cannot be undone.`;
    document.getElementById('deleteModal').style.display = 'block';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
</script>
        <script src="../js/search.js"></script>
        <script src="../js/formatTime.js"></script>
        <script src="../js/sidebar.js"></script>
</body>
</html>
<?php $conn->close(); ?>