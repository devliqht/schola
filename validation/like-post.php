<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../api/level_config.php'; // Ensure database connection is included
require_once '../api/db_connection.php'; 
require_once 'leveling-system.php'; 

$conn = establish_connection();

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['id'];
$post_id = $_POST['post_id'] ?? null;

if (!$post_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid post ID']);
    exit();
}

$checkQuery = $conn->prepare("SELECT * FROM post_likes WHERE user_id = ? AND post_id = ?");
$checkQuery->bind_param("ii", $user_id, $post_id);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();

if ($checkResult->num_rows === 0) {
    // Like the post
    $query = $conn->prepare("INSERT INTO post_likes (user_id, post_id) VALUES (?, ?)");
    $query->bind_param("ii", $user_id, $post_id);
    $query->execute();

    addPoints($user_id, 'like');
    $status = 'liked';
} else {
    // Unlike the post
    $query = $conn->prepare("DELETE FROM post_likes WHERE user_id = ? AND post_id = ?");
    $query->bind_param("ii", $user_id, $post_id);
    $query->execute();

    removePoints($user_id, 2);
    $status = 'unliked';
}

// Get updated like count
$likeCountQuery = $conn->prepare("SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ?");
$likeCountQuery->bind_param("i", $post_id);
$likeCountQuery->execute();
$likeCountResult = $likeCountQuery->get_result();
$likeCount = $likeCountResult->fetch_assoc()['like_count'];

echo json_encode(['status' => 'success', 'action' => $status, 'like_count' => $likeCount]);
?>