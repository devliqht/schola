<?php
require_once 'db_connection.php';

function create_notification($conn, $user_id, $sender_id, $type, $post_id = null, $comment_id = null, $message = null, $link = null) {
    if ($user_id == $sender_id) {
        return false;
    }
    
    $query = "INSERT INTO notifications (user_id, sender_id, type, post_id, comment_id, message, link, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisisss", $user_id, $sender_id, $type, $post_id, $comment_id, $message, $link);
    $success = $stmt->execute();
    $stmt->close();
    
    return $success;
}

function notify_post_subscribers($conn, $post_id, $comment_id, $sender_id, $comment_content) {
    $post_query = "SELECT p.title, u.username FROM posts p JOIN users u ON p.author_id = u.id WHERE p.id = ?";
    $post_stmt = $conn->prepare($post_query);
    $post_stmt->bind_param("i", $post_id);
    $post_stmt->execute();
    $post_result = $post_stmt->get_result();
    $post_data = $post_result->fetch_assoc();
    $post_stmt->close();
    
    if (!$post_data) {
        return false;
    }
    
    $commenter_query = "SELECT username, full_name FROM users WHERE id = ?";
    $commenter_stmt = $conn->prepare($commenter_query);
    $commenter_stmt->bind_param("i", $sender_id);
    $commenter_stmt->execute();
    $commenter_result = $commenter_stmt->get_result();
    $commenter_data = $commenter_result->fetch_assoc();
    $commenter_stmt->close();
    
    if (!$commenter_data) {
        return false;
    }
    
    $short_content = strlen($comment_content) > 100 ? substr($comment_content, 0, 97) . '...' : $comment_content;
    
    $message = "<strong>{$commenter_data['full_name']}</strong> commented on <strong>{$post_data['title']}</strong>: \"{$short_content}\"";
    $link = "../pages/post.php?id={$post_id}#comment-{$comment_id}";
    
    $subscribers_query = "SELECT user_id FROM post_subscriptions WHERE post_id = ?";
    $subscribers_stmt = $conn->prepare($subscribers_query);
    $subscribers_stmt->bind_param("i", $post_id);
    $subscribers_stmt->execute();
    $subscribers_result = $subscribers_stmt->get_result();
    
    $type = 'comment';
    $success = true;
    
    while ($subscriber = $subscribers_result->fetch_assoc()) {
        $subscriber_id = $subscriber['user_id'];
        $result = create_notification($conn, $subscriber_id, $sender_id, $type, $post_id, $comment_id, $message, $link);
        $success = $success && $result;
    }
    
    $post_author_query = "SELECT p.author_id FROM posts p 
                          LEFT JOIN post_subscriptions ps ON p.id = ps.post_id AND p.author_id = ps.user_id
                          WHERE p.id = ? AND ps.id IS NULL";
    $post_author_stmt = $conn->prepare($post_author_query);
    $post_author_stmt->bind_param("i", $post_id);
    $post_author_stmt->execute();
    $post_author_result = $post_author_stmt->get_result();
    
    if ($post_author_row = $post_author_result->fetch_assoc()) {
        $author_id = $post_author_row['author_id'];
        if ($author_id != $sender_id) { 
            $result = create_notification($conn, $author_id, $sender_id, $type, $post_id, $comment_id, $message, $link);
            $success = $success && $result;
        }
    }
    
    $subscribers_stmt->close();
    $post_author_stmt->close();
    
    return $success;
} 