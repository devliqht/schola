<?php 
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-post.php';
require_once '../components/render-sidebar.php';
require_once '../components/get-breadcrumbs.php';
require_once '../components/render-posts.php';
require_once '../components/render-pup.php';

$conn = establish_connection();

// Get event ID from URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch event details
$event = null;
if ($event_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title>Event - <?= $event ? htmlspecialchars($event['title']) : 'Not Found' ?></title>
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
    <style>
        .event-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .event-header {
            display: flex;
            gap: 2rem;
        }
        .event-date {
            background: var(--gradient-main);
            padding: 1rem;
            border-radius: 8px;
        }
        .event-date .day {
            font-size: 2rem;
            font-weight: 600;
            color: white;
        }
        .event-date .month {
            font-size: 1rem;
            text-transform: uppercase;
            color: white;
        }
        .event-content {
            border-radius: 8px;
        }
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
            
            <?php if ($event): ?>
                <div class="event-container">
                    <div class="event-header">
                        <div class="event-date">
                            <?php
                            $event_date = DateTime::createFromFormat('Y-m-d', $event['event_date']);
                            $day = $event_date->format('d');
                            $month = $event_date->format('M');
                            ?>
                            <div class="day"><?= $day ?></div>
                            <div class="month"><?= $month ?></div>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-2xl gradient-text inter-700"><?= htmlspecialchars($event['title']) ?></h1>
                            <div class="event-content">
                                <p class="text-white inter-400 text-base"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                            </div>
                        </div>
                    </div>
                    
                </div>
            <?php else: ?>
                <div class="event-container">
                    <h1 class="text-2xl gradient-text inter-700">Event Not Found</h1>
                    <p class="text-white inter-400">The requested event could not be found.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php render_pup(); ?>
    </div>
    <?php render_navbar(); ?>
<?php else: ?>
    <?php header("Location: ../index.php"); ?>
<?php endif; ?>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
</body>
</html>
<?php $conn->close(); ?>