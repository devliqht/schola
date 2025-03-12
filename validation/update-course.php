<?php
session_start();
require_once '../api/db_connection.php';

$conn = establish_connection();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id']; 
    $course = $_POST['course'];
    

    $sql = "UPDATE users SET course = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $course, $user_id);

    if ($stmt->execute()) {
        header("Location: ../pages/account.php");
    }

    $stmt->close();
}

$conn->close();
?>