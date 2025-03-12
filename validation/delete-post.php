<?php
session_start();

include '../api/db_connection.php';

$conn = establish_connection();
$post_id = intval($_POST['post_id']);
$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['id'])) {
    $query = "SELECT author_id FROM posts WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($post_owner);
        $stmt->fetch();

        if ($user_id == $post_owner || $user_role == 'admin') {
            $delete_query = "DELETE FROM posts WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("i", $post_id);
            $delete_stmt->execute();
        }
    }
}

header("Location: ../pages/posts.php");
exit();

?>