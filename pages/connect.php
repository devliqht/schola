<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';

$conn = establish_connection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users except the logged-in user
$user_id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$defaultProfilePicture = "../uploads/profile_pictures/default.svg"; // Set a default image
?>

<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title>Connect with Users</title>
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
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
    <link rel="stylesheet" href="../css/connect.css" />
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
</head>
<body>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content animate__animated animate__fadeIn animate__faster">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>

            <h2 class="gradient-text inter-700 text-xl pb-4">Connect with Users</h2>
            <div class="users-grid">
                <?php while ($user = $result->fetch_assoc()): ?>
                    <div class="user-card">

                        <?php 
                            $profilePicture = isset($user['profile_picture']) ? "../uploads/profile_pictures/" . $user['profile_picture'] : $defaultProfilePicture;
                        ?>
                        <img src="<?php echo $profilePicture; ?>" alt="Profile">
                        <div class="flex flex-col">
                            <h3 class="user-card-f-name gradient-text text-base inter-700"><?= htmlspecialchars($user['full_name']) ?></h3>
                            <h3 class="user-card-course text-xs inter-700 pb-1"><?= htmlspecialchars($user['course'] ?? '') ?></h3>
                            <h3 class="text-sm inter-300 pb-4">@<?= htmlspecialchars($user['username']) ?></h3>
                        </div>
                        <a href="profile.php?id=<?= $user['id'] ?>" class="interaction text-sm decoration-none text-black text-center">View Profile</a>
                    </div>
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