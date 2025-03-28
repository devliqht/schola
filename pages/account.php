<?php 
    require_once '../api/config.php';
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/render-sidebar.php';
    require_once '../components/get-breadcrumbs.php';
    $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; 
    $profilePicture = isset($_SESSION['profile_picture']) ? "../uploads/profile_pictures/" . $_SESSION['profile_picture'] : $defaultProfilePicture;
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
    <link rel="stylesheet" href="../css/form.css" />
    <link rel="stylesheet" href="../css/colors.css" />
    <link rel="stylesheet" href="../css/sidebar.css" />
    <link rel="stylesheet" href="../css/header.css" />
    <link rel="stylesheet" href="../css/admin.css" />
    <link rel="stylesheet" href="../css/account.css" />
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
    </head>
<body>
<?php if (isset($_SESSION['role']) && !empty($_SESSION['role'])): ?>
    <?php 
        $conn = establish_connection();

        $query = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $query->bind_param("s", $_SESSION['username']);
        $query->execute();
        $res = $query->get_result();
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
        }
    ?>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <h1 class="text-xl gradient-text inter-700 pb-4">My Account</h1>
            <div class="account-details p-4">
                <div class="flex flex-col gap-4">
                    <div class="account-picture">
                        <img class="account-img" src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture">
                    </div>
                    <div class="account-information">
                        <div class="flex flex-col pb-2">
                            <div class="name">
                                <h1 class="gradient-text text-2xl inter-700"><?php echo $row['full_name']; ?></h1>
                            </div>
                            <?php if (!empty($row['course'])): ?>
                            <h1 class="text-lg inter-700"><?php echo $row['course']; ?></h1>
                            <?php else: ?>
                                <h1 class="text-base inter-300">No course set yet.</h1>
                            <?php endif; ?>
                        </div>
                        <div class="acc-info flex flex-row justify-between">
                            <h2 class="text-md inter-700">ID</h2>
                            <p class="text-md inter-400"><?php echo $row['id']; ?></p>
                        </div>
                        <div class="acc-info flex flex-row justify-between">
                            <h2 class="text-md inter-700">Username</h2>
                            <h2 class="text-md inter-400">@<?php echo $row['username']; ?></h2>
                        </div>
                        <div class="acc-info flex flex-row justify-between">
                            <h2 class="text-md inter-700">Full Name</h2>
                            <h2 class="text-md inter-400"><?php echo $row['full_name']; ?></h2>
                        </div>
                        <div class="acc-info flex flex-row justify-between">
                            <h2 class="text-md inter-700">Email</h2>
                            <h2 class="text-md inter-400"><?php echo $row['email']; ?></h2>
                        </div>
                        <div class="acc-info flex flex-row justify-between">
                            <h2 class="text-md inter-700">Role</h2>
                            <p class="text-md inter-400" style="text-transform: capitalize;"><?php echo $row['role']; ?></p>
                        </div>
                        <div class="acc-info flex flex-row justify-between">
                            <h2 class="text-md inter-700">Created at</h2>
                            <p class="text-md inter-400"><?php echo $row['created_at'] ?></p>
                        </div>

                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-xl gradient-text inter-700">Set a Profile Picture</h1>
                        <form action="../validation/upload_profile_picture.php" method="POST" enctype="multipart/form-data">
                            <input type="file" name="profile_picture" accept="image/*" required>
                            <button type="submit" class="action-button">Upload</button>
                        </form>
                    </div>

                    <div class="flex flex-col">
                        <h1 class="text-xl gradient-text inter-700">Set a Course</h1>
                        <form action="../validation/update-course.php" method="POST">
                            <input type="text" name="course" required>
                            <button type="submit" class="action-button">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
    <?php else: ?>
    <?php  
        header("Location: ../index.php");    
    ?>
    <?php endif; ?>
</body>
</html>