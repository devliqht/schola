<?php
session_start();
require_once '../api/db_connection.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'officers' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php");
    exit();
}

$conn = establish_connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = intval($_POST['group_id']);
    $group_name = trim($_POST['group_name']);
    $user_id = $_SESSION['id'];

    $stmt = $conn->prepare("SELECT creator_id FROM member_groups WHERE id = ?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $group = $result->fetch_assoc();

    if ($group && ($group['creator_id'] == $user_id || $_SESSION['role'] == 'admin')) {
        $stmt = $conn->prepare("UPDATE member_groups SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $group_name, $group_id);
        if ($stmt->execute()) {
            header("Location: ../pages/group.php?id=$group_id&success=group_updated");
        } else {
            header("Location: ../pages/group.php?id=$group_id&error=update_failed");
        }
    } else {
        header("Location: ../pages/group.php?id=$group_id&error=unauthorized");
    }
    $stmt->close();
}

$conn->close();
?>