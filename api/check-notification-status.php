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

// Check if already subscribed
$check_query = "SELECT * FROM post_subscriptions WHERE user_id = ? AND post_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $user_id, $post_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

$subscribed = $check_result->num_rows > 0;

echo json_encode(['status' => 'success', 'subscribed' => $subscribed]);

$check_stmt->close();
$conn->close(); 