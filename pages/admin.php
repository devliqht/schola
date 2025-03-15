<?php 
    require_once '../api/config.php';
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/render-posts.php';
    require_once '../components/render-sidebar.php';

    $conn = establish_connection();

    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title>Forum Dashboard</title>
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
<?php if (isset($_SESSION['role']) && !empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <?php render_header(); ?>
    <div class="grid-container">
    <?php render_sidebar(); ?>
        <div class="main-content">
            <h2 class="text-3xl gradient-text inter-700">User Control</h2>
            <hr/>
            <table class="user-table">
                <thead>
                    <tr>
                        <th class="gradient-text inter-700 text-2xl">ID</th>
                        <th class="gradient-text inter-700 text-2xl">Username</th>
                        <th class="gradient-text inter-700 text-2xl">Name</th>
                        <th class="gradient-text inter-700 text-2xl">Role</th>
                        <th class="gradient-text inter-700 text-2xl">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="inter-300">
                            <td><?php echo $row['id']; ?></td>
                            <td class="inter-300"><a href="profile.php?id=<?php echo $row['id']; ?>" class="text-white decoration-none text-base"><?php echo $row['username']; ?></a></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td class="text-capitalize"><?php echo $row['role']; ?></td>
                            <td>
                                <button class="action-button" onclick="showEditForm(<?php echo $row['id']; ?>, '<?php echo $row['username']; ?>', '<?php echo $row['full_name']; ?>', '<?php echo $row['role']; ?>', '<?php echo $row['course']; ?>')" class="btn bg-blue text-white p-1">Edit</button>
                                <button class="action-button" onclick="showDeleteModal(<?php echo $row['id']; ?>)" class="btn bg-red text-white p-1">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Edit Form Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <h1 class="gradient-text inter-700 text-xl">Edit User</h1>
                    <hr />
                    <form action="../validation/update-user.php" method="POST">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="form-group">
                            <h1 class="gradient-text inter-700 text-xl">Username</h1>
                            <input type="text" name="username" id="edit_username" class="input-field">
                        </div>
                        <div class="form-group">
                            <h1 class="gradient-text inter-700 text-xl">Name</h1>
                            <input type="text" name="name" id="edit_name" class="input-field">
                        </div>
                        <div class="form-group">
                            <h1 class="gradient-text inter-700 text-xl">Course</h1>
                            <input type="text" name="course" id="edit_course" class="input-field">
                        </div>
                        <div class="form-group">
                            <h1 class="gradient-text inter-700 text-xl">Role</h1>
                            <select name="role" id="edit_role" class="dropdown-field">
                                <option value="user">User</option>
                                <option value="officer">Officer</option>
                            </select>
                        </div>
                        <div class="flex flex-row pt-4 gap-4">
                            <button type="submit" class="action-button">Save</button>
                            <button type="button" onclick="closeEditModal()" class="action-button">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <h1 class="gradient-text text-2xl inter-700">Are you sure you want to delete this user?</h1>
                    <form action="../validation/delete-user.php" method="POST">
                        <div class="flex flex-row pt-4 justify-center gap-4">
                            <input type="hidden" name="user_id" id="delete_user_id">
                            <button type="submit" class="action-button">Yes</button>
                            <button type="button" onclick="closeDeleteModal()" class="action-button">No</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php header("Location: ../index.php"); ?>
<?php endif; ?>
<script src="../js/search.js"></script>
<script src="../js/formatTime.js"></script>
<script src="../js/sidebar.js"></script>
<script>
    function showEditForm(id, username, name, role, course) {
        document.getElementById('edit_user_id').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_course').value = course;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function showDeleteModal(id) {
        document.getElementById('delete_user_id').value = id;
        document.getElementById('deleteModal').style.display = 'block';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            closeEditModal();
            closeDeleteModal();
        }
    }
</script>
</body>
</html>

<?php $conn->close(); ?>