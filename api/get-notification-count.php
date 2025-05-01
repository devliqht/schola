<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in', 'count' => 0]);
    exit;
}

$user_id = $_SESSION['id'];
$conn = establish_connection();

$query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND read_status = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['status' => 'success', 'count' => (int)$row['count']]);

$stmt->close();
$conn->close(); 