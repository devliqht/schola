<?php
session_start();
require_once '../api/db_connection.php';

header('Content-Type: application/json');
ob_start(); 
ini_set('display_errors', 0); 
error_reporting(E_ALL);

if (!isset($_SESSION['id'])) {
    $response = ['error' => 'Unauthorized'];
    ob_end_clean();
    echo json_encode($response);
    exit;
}

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $uploadDir = __DIR__ . '/../uploads/post_images/'; 

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); 
    }

    if (!is_writable($uploadDir)) {
        $response = ['error' => 'Upload directory is not writable'];
        ob_end_clean();
        echo json_encode($response);
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes) || $file['size'] > 5 * 1024 * 1024) {
        $response = ['error' => 'Invalid file type or size'];
        ob_end_clean();
        echo json_encode($response);
        exit;
    }

    $fileName = uniqid() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $response = ['location' => "../uploads/post_images/$fileName"];
    } else {
        $response = [
            'error' => 'Failed to upload file',
            'details' => error_get_last(),
            'php_error' => $_FILES['file']['error'] 
        ];
    }
    ob_end_clean();
    echo json_encode($response);
} else {
    $response = ['error' => 'No file uploaded'];
    ob_end_clean();
    echo json_encode($response);
}
exit;
?>