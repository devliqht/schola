<?php 
session_start();
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';

$conn = establish_connection();
$stmt = $conn->prepare("SELECT mg.id, mg.name, mg.created_at, u.username 
                       FROM member_groups mg 
                       JOIN users u ON mg.creator_id = u.id");
$stmt->execute();
$groups = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
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
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        .group-table { width: 100%; border-collapse: collapse; }
        .group-table th, .group-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 15% auto; padding: 20px; width: 300px; border-radius: 5px; }
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
            <h2 class="text-xl gradient-text inter-700">Member Groups</h2>
            <hr />
            <?php if ($_SESSION['role'] === 'officer' || $_SESSION['role'] === 'admin'): ?>
                <form action="../validation/add-group.php" method="POST" class="pb-4">
                    <input type="text" name="group_name" placeholder="Enter group name" class="input-field" required>
                    <button type="submit" class="action-button">Create Group</button>
                </form>
            <?php endif; ?>
            <div class="flex flex-col">
                <div class="flex flex-col">
                    <?php while ($group = $groups->fetch_assoc()): ?>
                        <?php 
                        $count_stmt = $conn->prepare("SELECT COUNT(*) as member_count FROM group_members WHERE group_id = ?");
                        $count_stmt->bind_param("i", $group['id']);
                        $count_stmt->execute();
                        $member_count = $count_stmt->get_result()->fetch_assoc()['member_count'];
                        ?>
                            <div class="flex flex-col p-4 border-1">
                                <h1><a href="group.php?id=<?php echo $group['id']; ?>" class="gradient-text inter-700 text-2xl"><?php echo htmlspecialchars($group['name']); ?></a></h1>
                                <p class="inter-300 text-lg">Created by <?php echo htmlspecialchars($group['username']); ?></p>
                                <p><?php echo (new DateTime($group['created_at']))->format('M d, Y'); ?></p>
                                <div class="py-4">
                                    <?php 
                                    $user_count = $conn->query("SELECT COUNT(*) FROM group_members WHERE user_id = " . $_SESSION['id'])->fetch_row()[0];
                                    $is_member = $conn->query("SELECT 1 FROM group_members WHERE group_id = {$group['id']} AND user_id = " . $_SESSION['id'])->num_rows > 0;
                                    ?>
                                    <?php if (!$is_member && $user_count < 3): ?>
                                        <a href="group.php?id=<?php echo $group['id']; ?>&join=1" class="action-button decoration-none text-black">Join</a>
                                    <?php elseif ($is_member): ?>
                                        <a href="group.php?id=<?php echo $group['id']; ?>&leave=1" class="action-button decoration-none text-black">Leave</a>
                                    <?php endif; ?>
                                    <?php if ($_SESSION['role'] === 'admin' || ($_SESSION['role'] === 'officer' && $group['username'] === $_SESSION['username'])): ?>
                                        <button onclick="showEditModal(<?php echo $group['id']; ?>, '<?php echo htmlspecialchars($group['name'], ENT_QUOTES); ?>')" class="action-button">Edit</button>

                                        <button class="interaction inter-600" onclick='showDeleteModal(<?php echo $group["id"]; ?>, "<?php echo htmlspecialchars($group["name"], ENT_QUOTES); ?>")'>
                                            <i class="fa-solid fa-trash pr-1"></i>Delete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                    <?php endwhile; ?>
                </div>
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
                    <p id="deleteModalText"></p> <!-- Display group name -->
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
    function showEditModal(id, name) {
        document.getElementById('edit_group_id').value = id;
        document.getElementById('edit_group_name').value = name;
        document.getElementById('editModal').style.display = 'block';
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
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