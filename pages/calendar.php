<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-posts.php';
require_once '../components/get-breadcrumbs.php';
require_once '../components/render-sidebar.php';

$conn = establish_connection();

// Get current month and year
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get the first day of the month and total days
$firstDay = strtotime("$year-$month-01");
$daysInMonth = date('t', $firstDay);
$startDay = date('N', $firstDay);

// Fetch events for the current month
$events_query = $conn->prepare("SELECT id, title, event_date FROM events WHERE MONTH(event_date) = ? AND YEAR(event_date) = ?");
$events_query->bind_param("ii", $month, $year);
$events_query->execute();
$events_result = $events_query->get_result();
$events = [];

while ($event = $events_result->fetch_assoc()) {
    $events[date('j', strtotime($event['event_date']))][] = $event;
}

function getNavLink($offset) {
    global $month, $year;
    $newMonth = $month + $offset;
    $newYear = $year;
    if ($newMonth < 1) { $newMonth = 12; $newYear--; }
    if ($newMonth > 12) { $newMonth = 1; $newYear++; }
    return "?month=$newMonth&year=$newYear";
}
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
    <title>Event Calendar</title>
</head>
<body>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content">
        <nav class="breadcrumb">
            <?php echo get_breadcrumbs(); ?>
        </nav>
        <h2 class="text-xl gradient-text inter-700">School Calendar</h2>
        <hr />
            <div class="calendar-container">
                <div class="calendar-header">
                    <a href="<?= getNavLink(-1) ?>" class="action-button decoration-none">←</a>
                    <h2><?= date('F Y', $firstDay) ?></h2>
                    <a href="<?= getNavLink(1) ?>" class="action-button decoration-none">→</a>
                </div>
                <div class="calendar-grid">
                    <?php 
                    $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    foreach ($weekDays as $day) { echo "<div class='calendar-day-header'>$day</div>"; }
        
                    for ($i = 1; $i < $startDay; $i++) { echo "<div class='empty'></div>"; }
        
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        echo "<div class='calendar-cell'>";
                        echo "<span class='date'>$day</span>";
                        if (!empty($events[$day])) {
                            foreach ($events[$day] as $event) {
                                echo "<div class='event'>" . htmlspecialchars($event['title']) . "</div>";
                            }
                        }
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
        </div>
</body>
</html>