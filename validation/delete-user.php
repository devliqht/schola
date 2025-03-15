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

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: ../pages/admin.php?success=user_deleted");
    } else {
        header("Location: ../pages/admin.php?error=delete_failed");
    }

    $stmt->close();
}

$conn->close();
?>