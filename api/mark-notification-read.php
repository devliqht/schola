<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

if (!isset($_POST['notification_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Notification ID is required']);
    exit;
}

$user_id = $_SESSION['id'];
$notification_id = intval($_POST['notification_id']);
$conn = establish_connection();

$check_query = "SELECT * FROM notifications WHERE id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $notification_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Notification not found or not authorized']);
    $check_stmt->close();
    $conn->close();
    exit;
}

$update_query = "UPDATE notifications SET read_status = 1 WHERE id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("i", $notification_id);
$success = $update_stmt->execute();

if ($success) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to mark notification as read']);
}

$check_stmt->close();
$update_stmt->close();
$conn->close(); 