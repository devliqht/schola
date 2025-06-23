<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

if (!isset($_POST['post_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Post ID is required']);
    exit;
}

$user_id = $_SESSION['id'];
$post_id = intval($_POST['post_id']);
$conn = establish_connection();

$check_query = "SELECT * FROM post_subscriptions WHERE user_id = ? AND post_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $user_id, $post_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Unsubscribe
    $delete_query = "DELETE FROM post_subscriptions WHERE user_id = ? AND post_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $user_id, $post_id);
    $success = $delete_stmt->execute();
    
    if ($success) {
        echo json_encode(['status' => 'success', 'subscribed' => false]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to unsubscribe']);
    }
    
    $delete_stmt->close();
} else {
    // Subscribe
    $insert_query = "INSERT INTO post_subscriptions (user_id, post_id, created_at) VALUES (?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("ii", $user_id, $post_id);
    $success = $insert_stmt->execute();
    
    if ($success) {
        echo json_encode(['status' => 'success', 'subscribed' => true]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to subscribe']);
    }
    
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close(); 