<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

if ($comment_id <= 0 || empty($content) || $post_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit();
}

$conn = establish_connection();

$check_stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ? AND post_id = ?");
$check_stmt->bind_param("ii", $comment_id, $post_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$comment = $check_result->fetch_assoc();

if (!$comment) {
    echo json_encode(['status' => 'error', 'message' => 'Comment not found']);
    $check_stmt->close();
    $conn->close();
    exit();
}

if ($comment['user_id'] != $_SESSION['id'] && $_SESSION['role'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized to edit this comment']);
    $check_stmt->close();
    $conn->close();
    exit();
}

$stmt = $conn->prepare("UPDATE comments SET content = ? WHERE id = ?");
$stmt->bind_param("si", $content, $comment_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update comment']);
}

$stmt->close();
$check_stmt->close();
$conn->close();
?>