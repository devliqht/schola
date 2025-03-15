<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['id'])) {
    $comment_id = intval($_POST['comment_id']);
    $post_id = intval($_POST['post_id']); 
    $user_id = $_SESSION['id'];
    $user_role = $_SESSION['role'];

    $conn = establish_connection();

    $query = "SELECT user_id FROM comments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($comment_owner);
        $stmt->fetch();

        if ($user_id == $comment_owner || $user_role == 'admin') {
            $delete_query = "DELETE FROM comments WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("i", $comment_id);
            $delete_stmt->execute();
        }
    }
}

header("Location: ../pages/post.php?id=" . $post_id);
exit();
?>