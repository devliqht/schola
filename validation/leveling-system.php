<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../api/level_config.php';

function getUserLevel($userId) {
    $conn = establish_connection();
    $query = $conn->prepare("SELECT virtus_points FROM users WHERE id = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    if (!$user) return 1; // Default level if user is not found

    return calculateLevel($user['virtus_points']);
}

function calculateLevel($points) {
    $conn = establish_connection();
    $thresholds = getLevelThresholds();
    $level = 1;
    foreach ($thresholds as $lvl => $reqPoints) {
        if ($points >= $reqPoints) {
            $level = $lvl;
        } else {
            break;
        }
    }
    return $level + ($points / $thresholds[$level]);
}

function updateUserPoints($userId, $points) {
    $conn = establish_connection();
    $query = $conn->prepare("UPDATE users SET virtus_points = virtus_points + ? WHERE id = ?");
    $query->bind_param("ii", $points, $userId);
    $query->execute();
    
    // Update Devotio Level
    $newLevel = calculateLevel(getVirtusPoints($userId));
    $query = $conn->prepare("UPDATE users SET devotio_level = ? WHERE id = ?");
    $query->bind_param("di", $newLevel, $userId);
    $query->execute();
}

function getVirtusPoints($userId) {
    $conn = establish_connection();
    $query = $conn->prepare("SELECT virtus_points FROM users WHERE id = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();
    return $user ? $user['virtus_points'] : 0;
}

// Function to add points for specific actions
function addPoints($userId, $action) {
    $pointsMap = [
        'like' => 2,
        'post' => 10,
        'comment' => 5
    ];

    if (isset($pointsMap[$action])) {
        updateUserPoints($userId, $pointsMap[$action]);
    }
}

function removePoints($userId, $points) {
    $conn = establish_connection();
    $query = $conn->prepare("UPDATE users SET virtus_points = GREATEST(0, virtus_points - ?) WHERE id = ?");
    $query->bind_param("ii", $points, $userId);
    $query->execute();
}
?>