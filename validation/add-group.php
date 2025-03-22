<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/resize-and-crop.php';

if (!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

$conn = establish_connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = trim($_POST['group_name']);
    $group_description = trim($_POST['group_description']);
    $creator_id = $_SESSION['id'];
    $group_picture_data = $_POST['group_picture_data'] ?? '';

    if (empty($group_name)) {
        header("Location: ../pages/groups.php?error=empty_name");
        exit();
    }

    $group_picture = 'default_group.svg'; 
    if (!empty($group_picture_data) && strpos($group_picture_data, 'data:image') === 0) {
        list($type, $data) = explode(';', $group_picture_data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        $uploadDir = __DIR__ . '/../uploads/group_pictures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (!is_writable($uploadDir)) {
            header("Location: ../pages/groups.php?error=upload_dir_not_writable");
            exit();
        }

        $fileName = "group_{$creator_id}_" . time() . ".jpg";
        $filePath = $uploadDir . $fileName;

        if (file_put_contents($filePath, $data)) {
            if (resizeAndCropImage($filePath, $filePath, 200)) {
                $group_picture = $fileName;
            } else {
                unlink($filePath); 
                header("Location: ../pages/groups.php?error=resize_failed");
                exit();
            }
        } else {
            header("Location: ../pages/groups.php?error=upload_failed");
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO member_groups (name, description, creator_id, group_picture) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $group_name, $group_description, $creator_id, $group_picture);

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