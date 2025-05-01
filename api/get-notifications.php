<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in', 'notifications' => []]);
    exit;
}

$user_id = $_SESSION['id'];
$conn = establish_connection();

$query = "SELECT n.*, u.username, u.full_name, u.profile_picture 
          FROM notifications n
          LEFT JOIN users u ON n.sender_id = u.id
          WHERE n.user_id = ?
          ORDER BY n.created_at DESC
          LIMIT 30";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode(['status' => 'success', 'notifications' => $notifications]);

$stmt->close();
$conn->close(); 