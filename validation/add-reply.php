<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../api/send-notification.php';

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
    $comment_id = $conn->insert_id;
    
    // Notify subscribers about the new reply
    notify_post_subscribers($conn, $post_id, $comment_id, $user_id, $content);
    
    // Also notify the parent comment author
    $parent_query = "SELECT c.user_id, u.username, u.full_name, p.title
                    FROM comments c
                    JOIN users u ON c.user_id = u.id
                    JOIN posts p ON c.post_id = p.id
                    WHERE c.id = ?";
    $parent_stmt = $conn->prepare($parent_query);
    $parent_stmt->bind_param("i", $parent_id);
    $parent_stmt->execute();
    $parent_result = $parent_stmt->get_result();
    
    if ($parent_data = $parent_result->fetch_assoc()) {
        $parent_user_id = $parent_data['user_id'];
        
        // Only notify if it's not the same user
        if ($parent_user_id != $user_id) {
            // Get commenter information
            $commenter_query = "SELECT username, full_name FROM users WHERE id = ?";
            $commenter_stmt = $conn->prepare($commenter_query);
            $commenter_stmt->bind_param("i", $user_id);
            $commenter_stmt->execute();
            $commenter_result = $commenter_stmt->get_result();
            $commenter_data = $commenter_result->fetch_assoc();
            
            // Create notification message
            $short_content = strlen($content) > 100 ? substr($content, 0, 97) . '...' : $content;
            $message = "<strong>{$commenter_data['full_name']}</strong> replied to your comment on <strong>{$parent_data['title']}</strong>: \"{$short_content}\"";
            $link = "../pages/post.php?id={$post_id}#comment-{$comment_id}";
            
            // Create notification
            create_notification($conn, $parent_user_id, $user_id, 'reply', $post_id, $comment_id, $message, $link);
            
            $commenter_stmt->close();
        }
    }
    
    $parent_stmt->close();
    
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