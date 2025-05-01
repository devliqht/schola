<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../validation/leveling-system.php';
require_once '../api/send-notification.php';

$conn = establish_connection();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['id'])) {
    if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
        die("Error: post_id is missing!");
    }
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['id'];
    $content = trim($_POST['commentContent']);


    if (!empty($content)) {
        $query = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $post_id, $user_id, $content);

        if ($stmt->execute()) {
            $comment_id = $conn->insert_id;
            
            // Notify subscribers about the new comment
            notify_post_subscribers($conn, $post_id, $comment_id, $user_id, $content);
            
            addPoints($_SESSION['id'], 'comment');
            header("Location: ../pages/post.php?id=" . $post_id);
            exit();
        }
    }
}
// var_dump($_SESSION);
header("Location: ../pages/post.php?id=" . $post_id);
exit();
?>