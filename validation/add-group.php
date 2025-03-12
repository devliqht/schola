<?php
session_start();
require_once '../api/db_connection.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'officer' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php");
    exit();
}

$conn = establish_connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = trim($_POST['group_name']);
    $creator_id = $_SESSION['id'];

    if (empty($group_name)) {
        header("Location: ../pages/groups.php?error=empty_name");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO member_groups (name, creator_id) VALUES (?, ?)");
    $stmt->bind_param("si", $group_name, $creator_id);

    if ($stmt->execute()) {

        $group_id = $conn->insert_id;
        $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $group_id, $creator_id);
        $stmt->execute();
        header("Location: ../pages/groups.php?success=group_created");
    } else {
        header("Location: ../pages/groups.php?error=create_failed");
    }
    $stmt->close();
}

$conn->close();
?>