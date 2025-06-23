<?php
function get_breadcrumbs() {
    $breadcrumbs = ['<a href="home.php">Home</a>']; 
    $script_name = basename($_SERVER['SCRIPT_NAME'], '.php'); 
    
    if ($script_name === 'home') {
        return implode(' <span> &gt; </span> ', $breadcrumbs); 
    }

    if ($script_name === 'post' && isset($_GET['id'])) {
        require_once '../api/db_connection.php'; 
        $conn = establish_connection();

        if (!$conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        $post_id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT title FROM posts WHERE id = ?");
        
        if (!$stmt) {
            die("SQL Error: " . $conn->error); 
        }

        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->bind_result($post_title);
        
        if ($stmt->fetch()) {
            $breadcrumbs[] = '<a href="posts.php">Posts</a>';
            $breadcrumbs[] = '<span class="inter-700">' . htmlspecialchars($post_title) . '</span>';
        } else {
            $breadcrumbs[] = '<span class="current">Post Not Found</span>';
        }

        $stmt->close();
        $conn->close();
    } else {
        $pretty_name = ucfirst(str_replace("-", " ", $script_name)); 
        $breadcrumbs[] = '<span class="current">' . htmlspecialchars($pretty_name) . '</span>';
    }

    return implode(' <span class="text-black"> &gt; </span> ', $breadcrumbs);
}

// Enable error reporting (REMOVE this in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>