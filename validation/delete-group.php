<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'officer' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php");
    exit();
}

$conn = establish_connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = intval($_POST['group_id']);
    $user_id = $_SESSION['id'];

    $stmt = $conn->prepare("SELECT creator_id FROM member_groups WHERE id = ?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $group = $result->fetch_assoc();

    if ($group && ($group['creator_id'] == $user_id || $_SESSION['role'] == 'admin')) {
        $stmt = $conn->prepare("DELETE FROM member_groups WHERE id = ?");
        $stmt->bind_param("i", $group_id);
        if ($stmt->execute()) {
            header("Location: ../pages/groups.php?success=group_deleted");
        } else {
            header("Location: ../pages/groups.php?error=delete_failed");
        }
    } else {
        header("Location: ../pages/groups.php?error=unauthorized");
    }
    $stmt->close();
}

$conn->close();

?>