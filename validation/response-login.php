<?php
session_start();

require_once '../api/db_connection.php';
$conn = establish_connection();

$username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING) ?? '';
$password = filter_input(INPUT_GET, 'password', FILTER_SANITIZE_STRING) ?? '';

$query = $conn->prepare("SELECT id, username, password, role, full_name, profile_picture FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); 
}

$errors = [];

if (empty($username)) {
    $errors[] = "Username is required";
}

if (empty($password)) {
    $errors[] = "Password is required";
}

if (!empty($errors)) {
    $redirect_url = "../index.php?" . http_build_query(['errors' => $errors]);
    header("Location: " . $redirect_url); 
    exit();
}

if ($user && $password === $user['password']) {
    $_SESSION['role'] = $user['role'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['id'] = $user['id'];
    $_SESSION['profile_picture'] = $user['profile_picture'];
    $redirect_url = "../pages/home.php";
    header("Location: " . $redirect_url);
    exit();
} else {
    $redirect_url = "../signup.php";
    header("Location: " . $redirect_url);
    exit();
}
?>