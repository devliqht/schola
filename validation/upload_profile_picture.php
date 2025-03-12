<?php
    session_start();
    require '../api/db_connection.php'; 
    require '../components/resize-and-crop.php'; 

    $user_id = $_SESSION['id']; 
    $conn = establish_connection();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes) || $file['size'] > 2 * 1024 * 1024) {
            die("Invalid file type or file too large.");
        }

        $uploadDir = __DIR__ . '/../uploads/profile_pictures/'; 

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!is_writable($uploadDir)) {
            die("Upload directory is not writable.");
        }

        $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($oldProfilePicture);
        $stmt->fetch();
        $stmt->close();

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = "user_{$user_id}_" . time() . ".$fileExtension";
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            resizeAndCropImage($filePath, $filePath, 200); // Resize to 200x200
            if (!empty($oldProfilePicture) && $oldProfilePicture !== "default.svg") {
                $oldFilePath = $uploadDir . $oldProfilePicture;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $fileName, $user_id);

            if ($stmt->execute()) {
                $_SESSION['profile_picture'] = $fileName;
                header("Location: ../pages/account.php");
                exit();
            } else {
                die("Database update failed.");
            }
        } else {
            die("File upload failed. Debug Info: " . $_FILES['profile_picture']['error']);
        }
    }
?>