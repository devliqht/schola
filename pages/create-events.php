<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-posts.php';
require_once '../components/get-breadcrumbs.php';
require_once '../components/render-sidebar.php';

$conn = establish_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $title = $_POST['title'];
    $event_date = $_POST['event_date'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO events (title, event_date, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $event_date, $description);
    $stmt->execute();
    header("Location: admin-events.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event'])) {
    $event_id = $_POST['event_id'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    header("Location: create-events.php");
}

// Fetch all events
$events_result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/calendar.css">
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
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <title>Manage Events</title>
</head>
<body>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content">
        <nav class="breadcrumb">
            <?php echo get_breadcrumbs(); ?>
        </nav>
            <h2 class="gradient-text text-xl inter-700 pb-4">Manage Events</h2> 
            <form method="POST">
                <label class="inter-400 text-base text-white">Event Title:</label>
                <input type="text" name="title" required>
                
                <label class="inter-400 text-base text-white">Date:</label>
                <input type="date" name="event_date" required>
                
                <label class="inter-400 text-base text-white">Description:</label>
                <textarea name="description" rows="5"></textarea>
                
                <button type="submit" name="add_event" class="action-button">Add Event</button>
            </form>
            <hr/>
            <h3 class="gradient-text text-lg inter-700">Existing Events</h3>
            <ul>
                <?php while ($event = $events_result->fetch_assoc()): ?>
                    <li>
                        <h1 class="text-white pb-4"><?= htmlspecialchars($event['title']) ?>  - <?= $event['event_date'] ?></h1>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                            <button type="submit" name="delete_event" id="submit">Delete</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>