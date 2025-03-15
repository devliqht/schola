<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';

if (!isset($_SESSION['id']) || !isset($_POST['post_id'])) {
    header("Location: ../index.php");
    exit();
}

$conn = establish_connection();
$post_id = intval($_POST['post_id']);
$title = trim($_POST['title']);
$content = trim($_POST['content']);
$pinned = isset($_POST['pinned']) ? 1 : 0; // If checked, set to 1; otherwise, 0


$query = "UPDATE posts SET title = ?, content = ?, pinned = ? WHERE id = ? AND author_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssiii", $title, $content, $pinned, $post_id, $_SESSION['id']);

if ($stmt->execute()) {
    header("Location: ../pages/post.php?id=$post_id&updated=true");
} else {
    echo "<p>Failed to update post.</p>";
}
?>