<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($comment_id <= 0 || $post_id <= 0 || !in_array($action, ['pin', 'unpin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit();
}

$conn = establish_connection();

$check_post_stmt = $conn->prepare("SELECT author_id FROM posts WHERE id = ?");
$check_post_stmt->bind_param("i", $post_id);
$check_post_stmt->execute();
$check_post_result = $check_post_stmt->get_result();
$post = $check_post_result->fetch_assoc();

if (!$post || $post['author_id'] != $_SESSION['id']) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized to pin comments']);
    $check_post_stmt->close();
    $conn->close();
    exit();
}

$check_comment_stmt = $conn->prepare("SELECT parent_id FROM comments WHERE id = ? AND post_id = ?");
$check_comment_stmt->bind_param("ii", $comment_id, $post_id);
$check_comment_stmt->execute();
$check_comment_result = $check_comment_stmt->get_result();
$comment = $check_comment_result->fetch_assoc();

if (!$comment) {
    echo json_encode(['status' => 'error', 'message' => 'Comment not found']);
    $check_comment_stmt->close();
    $check_post_stmt->close();
    $conn->close();
    exit();
}

if ($comment['parent_id'] !== null) {
    echo json_encode(['status' => 'error', 'message' => 'Cannot pin a reply']);
    $check_comment_stmt->close();
    $check_post_stmt->close();
    $conn->close();
    exit();
}

if ($action === 'pin') {
    $count_stmt = $conn->prepare("SELECT COUNT(*) as pinned_count FROM comments WHERE post_id = ? AND pinned = 1");
    $count_stmt->bind_param("i", $post_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $pinned_count = $count_result->fetch_assoc()['pinned_count'];

    if ($pinned_count >= 5) {
        echo json_encode(['status' => 'error', 'message' => 'Maximum of 5 pinned comments reached']);
        $count_stmt->close();
        $check_comment_stmt->close();
        $check_post_stmt->close();
        $conn->close();
        exit();
    }

    $update_stmt = $conn->prepare("UPDATE comments SET pinned = 1 WHERE id = ?");
    $update_stmt->bind_param("i", $comment_id);
    $update_stmt->execute();
    $update_stmt->close();

    $count_stmt->close();
} else {
    $update_stmt = $conn->prepare("UPDATE comments SET pinned = 0 WHERE id = ?");
    $update_stmt->bind_param("i", $comment_id);
    $update_stmt->execute();
    $update_stmt->close();
}

$check_comment_stmt->close();
$check_post_stmt->close();
$conn->close();

echo json_encode(['status' => 'success']);
?>