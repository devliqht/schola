<?php
require_once 'db_connection.php';

function update_user_activity($user_id) {
    if (!$user_id) return;

    $conn = establish_connection();
    $stmt = $conn->prepare("UPDATE users SET last_active = UTC_TIMESTAMP() WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Update is_active status for all users
    // Set is_active = 1 if last_active is within the last 5 minutes, 0 otherwise
    $conn->query("UPDATE users SET is_active = CASE 
        WHEN last_active >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 5 MINUTE) THEN 1 
        ELSE 0 
        END");

    $conn->close();
}

if (isset($_SESSION['id'])) {
    update_user_activity($_SESSION['id']);
}
?>