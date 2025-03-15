<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$conn = establish_connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $course = $_POST['course'];


    if ($role === 'admin') {
        header("Location: ../pages/admin.php?error=invalid_role");
        exit();
    }

    $sql = "UPDATE users SET username = ?, full_name = ?, role = ?, course = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $username, $name, $role, $course, $user_id);

    if ($stmt->execute()) {
        header("Location: ../pages/admin.php?success=user_updated");
    } else {
        header("Location: ../pages/admin.php?error=update_failed");
    }

    $stmt->close();
}

$conn->close();
?>