<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';

$conn = establish_connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['id'])) {
        die("Unauthorized access. Please log in.");
    }

    $tags = isset($_POST['postTags']) ? explode(',', trim($_POST['postTags'])) : [];
    $title = trim($_POST['postTitle']);
    $content = $_POST['postContent']; 
    $author_id = $_SESSION['id'];
    $post_type = $_POST['postType'] ?? 'regular'; 
    $post_category = null; 

    if ($post_type === "announcement") {
        $post_category = $_POST['postCategory'] ?? null;
    }

    if (empty($title) || empty(strip_tags($content))) {
        die("Title and content cannot be empty.");
    }

    if ($post_type === 'announcement' && ($_SESSION['role'] !== 'officer' && $_SESSION['role'] !== 'admin')) {
        die("You do not have permission to create announcements.");
    }

    $stmt = $conn->prepare("INSERT INTO posts (title, content, author_id, created_at, post_type, post_category) VALUES (?, ?, ?, UTC_TIMESTAMP(), ?, ?)");
    $stmt->bind_param("ssiss", $title, $content, $author_id, $post_type, $post_category);

    if ($stmt->execute()) {    
        $new_post_id = $stmt->insert_id;
        $stmt->close();

        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (empty($tag)) continue;
            
            $stmt = $conn->prepare("INSERT INTO tags (name) VALUES (?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
            $stmt->bind_param("s", $tag);
            $stmt->execute();
            $tag_id = $conn->insert_id;
            
            $stmt = $conn->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $new_post_id, $tag_id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: ../pages/post.php?id=" . $new_post_id);
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
}
$conn->close();
?>