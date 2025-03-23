<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';

if (!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit();
}

$conn = establish_connection();

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['id'];

if ($post_id <= 0 || $parent_id <= 0 || empty($content)) {
    header("Location: ../pages/post.php?id=$post_id&error=invalid_input");
    exit();
}

$stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content, parent_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iisi", $post_id, $user_id, $content, $parent_id);

if ($stmt->execute()) {
    $comment_count_stmt = $conn->prepare("UPDATE posts SET comment_count = comment_count + 1 WHERE id = ?");
    $comment_count_stmt->bind_param("i", $post_id);
    $comment_count_stmt->execute();
    $comment_count_stmt->close();

    header("Location: ../pages/post.php?id=$post_id#comments-section");
} else {
    header("Location: ../pages/post.php?id=$post_id&error=reply_failed");
}

$stmt->close();
$conn->close();
?>