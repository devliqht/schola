<?php
require_once '../api/config.php';
require_once '../api/db_connection.php';
header('Content-Type: application/json');

try {
    $conn = establish_connection();
    if (isset($_GET['q'])) {  
        $searchTerm = "%" . $_GET['q'] . "%";

        // Fetch posts with author's name
        $stmt = $conn->prepare("
            SELECT posts.id, posts.title, posts.content, posts.created_at, users.username AS author 
            FROM posts
            JOIN users ON posts.author_id = users.id
            WHERE posts.title LIKE ? OR posts.content LIKE ? 
            ORDER BY posts.created_at DESC
            LIMIT 10
        ");
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $results = [];
        while ($row = $result->fetch_assoc()) {
            $results[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'content' => $row['content'],
                'created_at' => ($row['created_at']),
                'author' => $row['author'], 
            ];
        }
        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}


?>