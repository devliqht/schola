<?php 
require_once '../api/config.php';
require_once '../api/db_connection.php';
$conn = establish_connection();

$username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING) ?? '';
$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_STRING) ?? '';
$password = filter_input(INPUT_GET, 'password', FILTER_SANITIZE_STRING) ?? '';
$full_name = filter_input(INPUT_GET, 'full_name', FILTER_SANITIZE_STRING) ?? '';

$errors = [];

if (empty($username)) {
    $errors[] = "Username is required.";
}

if (empty($password)) {
    $errors[] = "Password is required.";
}

if (empty($full_name)) {
    $errors[] = "Full Name is Required";
}

if (empty($email)) {
    $errors[] = "Email is required.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
} 

$query = $conn->prepare("SELECT username FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();

if ($query->fetch()) {
    $errors[] = "Username already exists.";
}

if (empty($errors)) {
    try {
        $query = $conn->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
        $query->bind_param("ssss", $username, $email, $password, $full_name);
        $query->execute();

        $q_Role = $conn->prepare("SELECT id, role FROM users WHERE username = ?");
        $q_Role->bind_param("s", $username);
        $q_Role->execute(); 
        $res = $q_Role->get_result();
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $_SESSION['role'] = $row['role']; 
            $_SESSION['id'] = $row['id'];
        }
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $full_name;
        $redirect_url = "../pages/home.php";
        header("Location: " . $redirect_url);
        exit();

    } catch (mysqli_sql_exception $e) {
        $errors[] = "An error occurred during registration. Please try again later.";
    }
} else {
    $redirect_url = "../signup.php?" . http_build_query(['errors' => $errors]);
    header("Location: " . $redirect_url); 
}
?>