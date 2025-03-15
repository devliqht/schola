<?php 
    require_once '../api/config.php';
    require_once '../api/db_connection.php';
    require_once '../components/render-header.php';
    require_once '../components/render-sidebar.php';
    require_once '../components/get-breadcrumbs.php';
    require_once '../validation/leveling-system.php';

    if (!isset($_GET['id'])) {
        header("Location: ../index.php");
        exit();
    }
    $conn = establish_connection();
    
    $post_id = intval($_GET['id']);
    $posts_query = "SELECT posts.*, users.username, users.profile_picture FROM posts 
              JOIN users ON posts.author_id = users.id
              WHERE posts.id = ?";
    
    $posts_stmt = $conn->prepare($posts_query);
    $posts_stmt->bind_param("i", $post_id);
    $posts_stmt->execute();
    $posts_result = $posts_stmt->get_result();
    $post = $posts_result->fetch_assoc();

    $comments_query = "SELECT comments.id, comments.user_id, comments.content, comments.created_at, users.username, users.full_name, users.role, users.profile_picture
          FROM comments
          JOIN users ON comments.user_id = users.id
          WHERE comments.post_id = ?
          ORDER BY comments.created_at DESC";

    $comments_stmt = $conn->prepare($comments_query);
    $comments_stmt->bind_param("i", $post_id);
    $comments_stmt->execute();
    $comments_result = $comments_stmt->get_result();

    $comment_count_query = "SELECT COUNT(*) AS total_comments FROM comments WHERE post_id = ?";
    $comment_count_stmt = $conn->prepare($comment_count_query);
    $comment_count_stmt->bind_param("i", $post_id);
    $comment_count_stmt->execute();
    $comment_count_result = $comment_count_stmt->get_result();
    $comment_count = $comment_count_result->fetch_assoc()['total_comments'];
    
    $view_stmt = $conn->prepare("SELECT views FROM post_views WHERE post_id = ?");
    $view_stmt->bind_param("i", $post_id);
    $view_stmt->execute();
    $view_result = $view_stmt->get_result()->fetch_assoc();

    if ($view_result) {
        $update_stmt = $conn->prepare("UPDATE post_views SET views = views + 1 WHERE post_id = ?");
        $update_stmt->bind_param("i", $post_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO post_views (post_id, views) VALUES (?, 1)");
        $insert_stmt->bind_param("i", $post_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

     $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; // Set a default image
     $authorProfilePicture = !empty($post['profile_picture']) ? "../uploads/profile_pictures/" . $post['profile_picture'] : $defaultProfilePicture;
     
     $liked = '';

     $checkQuery = $conn->prepare("SELECT * FROM post_likes WHERE user_id = ? AND post_id = ?");
     $checkQuery->bind_param("ii", $_SESSION['id'], $post_id);
     $checkQuery->execute();
     $checkResult = $checkQuery->get_result();
     
     if ($checkResult->num_rows > 0) {
         $liked = 'Liked';
     }
     
     $likeCountQuery = $conn->prepare("SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ?");
     $likeCountQuery->bind_param("i", $post_id);
     $likeCountQuery->execute();
     $likeCountResult = $likeCountQuery->get_result();
     $likeCount = $likeCountResult->fetch_assoc()['like_count'];
    
     if (!$post) {
        echo "<p>Post not found.</p>";
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="../css/utilities/fonts.css" />
    <link rel="stylesheet" href="../css/utilities/util-text.css" />
    <link rel="stylesheet" href="../css/utilities/util-padding.css" />
    <link rel="stylesheet" href="../css/utilities/inputs.css" />
    <link rel="stylesheet" href="../css/utilities/utility.css" />
    <link rel="stylesheet" href="../css/form.css" />
    <link rel="stylesheet" href="../css/posts.css" />
    <link rel="stylesheet" href="../css/colors.css" />
    <link rel="stylesheet" href="../css/sidebar.css" />
    <link rel="stylesheet" href="../css/header.css" />
    <link rel="stylesheet" href="../css/admin.css" />
    <link rel="stylesheet" href="../css/utilities/responsive.css" />
    <link rel="stylesheet" href="../css/utilities/reset.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        .logo {
            width: 54px;
        }
    </style>
</head>
<body>
<?php if (isset($_SESSION['role']) && !empty($_SESSION['role'])): ?>
    <?php render_header(); ?>
    <div class="grid-container">
        <?php render_sidebar(); ?>
        <div class="main-content">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <div class="flex flex-row align-center pb-4">
                <img class="header-account-picture " src="<?php echo $authorProfilePicture; ?>" alt="Author Picture"/>
                <div class="flex flex-col pl-3">
                    <h2 class="text-xl inter-700 gradient-text"><?php echo htmlspecialchars($post['title']); ?></h2>
                    <a class="text-sm decoration-none text-white inter-300 post-author-name" href="profile.php?id=<?php echo $post['author_id']; ?>">@<?php echo htmlspecialchars($post['username']); ?></a>
                </div>
            </div>
            <div class="flex flex-col" style="color: var(--text-light);">
                <p class="text-base inter-400 border-1 rounded-lg p-4" style="background: var(--base-bg-two);">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </p>
                <div class="post-tags pt-4">
                <?php 
                    $tags_query = $conn->prepare("SELECT t.name FROM tags t 
                                                        JOIN post_tags pt ON t.id = pt.tag_id 
                                                        WHERE pt.post_id = ?");
                    $tags_query->bind_param("i", $post_id);
                    $tags_query->execute();
                    $tags_result = $tags_query->get_result();
                    if ($tags_result->num_rows > 0) {
                        while ($tag = $tags_result->fetch_assoc()) {
                            echo "<p class='text-xs inter-300 post-tag'>" . "#" . htmlspecialchars($tag['name']) . "</p>";
                        }
                    } else {
                        echo "<p class='text-xs inter-300 post-tag'> No tags set yet. </p>";
                    }

                    $tags_query->close();
                ?>
            </div>
            </div>
            <div class="post-interactions">
                <div class="flex flex-row flex-wrap interactions-row">
                <div class="flex flex-row interaction <?= $liked ? 'active' : '' ?> like-button" 
                    data-id="<?= $post_id ?>" 
                    id="like-interaction-<?= $post_id ?>">
                    <i class="fa-regular fa-thumbs-up pr-1 <?= $liked ? 'liked' : '' ?>"></i>
                    <div class="flex flex-row text-sm">
                        <p class="inter-600 likes-count pr-1"><?= $likeCount; ?></p>
                    </div>
                </div>
                    <a href="#comments-section" class="flex flex-row interaction decoration-none text-black align-center">
                        <i class="fa-regular fa-comments pr-1"></i>
                        <p class="inter-600 text-sm"><?php echo $comment_count; ?> Comments</p>
                    </a>
                    <div class="flex flex-row interaction text-sm align-center">
                        <i class="fa-regular fa-share-from-square pr-1"></i>
                    </div>
                    <?php if ($_SESSION['id'] == $post['author_id'] || $_SESSION['role'] == "admin"): ?>
                        <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="interaction inter-600 decoration-none text-sm text-black flex flex-row align-center">
                            <i class="fa-solid fa-pen pr-1"></i> Edit
                        </a>
                        <button class="interaction inter-600 text-black" onclick='showDeleteModal()'><i class="fa-solid fa-trash pr-1"></i>Delete</button>
                    <?php endif; ?>
                </div>
                <?php 
                    $post_date = new DateTime($post['created_at']);
                    $formatted_post_date = $post_date->format('M d, Y, h:i A');     
                ?>
                <div class="text-sm gradient-text inter-300"><?php echo $formatted_post_date; ?></div>
            </div>
            <hr />
            <div class="comments-section" id="comments-section">
                <h3 class="text-2xl inter-700 tracking-tight gradient-text">Comments</h3>
                <div class="flex flex-row align-start" style="margin-top: 1rem;">
                <?php
                    $currPicture = !empty($_SESSION['profile_picture']) ? "../uploads/profile_pictures/" . $_SESSION['profile_picture'] : $defaultProfilePicture;
                ?>
                <img class="header-account-picture" src="<?php echo $currPicture; ?>" alt="Author Picture"/>
                <form action="../validation/add-comment.php" method="POST" class="add-comment-form pl-3">
                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                        <textarea style="margin: 0;" rows="2" name="commentContent" placeholder="Comment as <?php echo $_SESSION['username']; ?>" rows="1" required class="text-base comment-textarea"></textarea>
                        <input type="submit" class="submit-comment" id="submit" value="Submit Comment"></input>
                    </form>
                </div>
                <div class="flex flex-col">
                <?php while ($comment = $comments_result->fetch_assoc()): ?>
                <?php
                    $comment_date = new DateTime($comment['created_at']);
                    $formatted_comment_date = $comment_date->format('M d, Y, h:i A');     

                    $profilePicture = !empty($comment['profile_picture']) ? "../uploads/profile_pictures/" . $comment['profile_picture'] : $defaultProfilePicture;
                ?>
                    <div class="comment-item">
                        <div class="flex flex-col">
                            <div class="flex flex-row align-center gap-4 ">
                                <div class="flex flex-row align-center">
                                    <img class="header-account-picture" src="<?php echo $profilePicture; ?>" alt="Pfp"/>
                                    <div class="flex flex-col pl-3">
                                        <div class="flex flex-row gap-4 align-center">
                                            <h2 class="text-base gradient-text inter-700"><?php echo htmlspecialchars($comment['full_name']); ?></h2> 
                                            <h1 class="flex justify-end text-xs inter-400" style="color:rgb(150, 150, 150);">
                                                <?php echo $formatted_comment_date; ?>
                                            </h1>
                                        </div>
                                        <a href="profile.php?id=<?php echo $comment['user_id']; ?>" class="text-xs inter-400 decoration-none text-white">@<?php echo htmlspecialchars($comment['username']); ?></a>
                                    </div>
                                </div>
                                <?php if ($_SESSION['id'] == $comment['user_id'] || $_SESSION['role'] == 'admin'): ?>
                                    <form action="../validation/delete-comment.php" method="POST" style="width: fit-content;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                                        <button type="submit" class="text-black action-button"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <p class="text-base py-2 inter-300 post-content text-white"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>
            </div>
        </div>
        <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <h1 class="gradient-text text-2xl inter-700">Are you sure you want to delete this post?</h1>
                    <form action="../validation/delete-post.php" method="POST">
                        <div class="flex flex-row pt-4 justify-center gap-4">
                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            <button type="submit" class="action-button">Yes</button>
                            <button type="button" onclick="closeDeleteModal()" class="action-button">No</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
<?php else: ?>
    <?php header("Location: ../index.php"); ?>
<?php endif; ?>
    <script>
        function showDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".like-button").forEach(button => {
                button.addEventListener("click", function() {
                    const postId = this.dataset.id;
                    const likeButton = this;
                    const likeCountEl = this.querySelector(".likes-count");
                    const icon = this.querySelector("i");

                    fetch("../validation/like-post.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `post_id=${postId}`
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.text(); // Get raw text instead of JSON
                    })
                    .then(text => {
                        console.log("Raw response:", text); // Log the exact response
                        return JSON.parse(text); // Attempt to parse it
                    })
                    .then(data => {
                        if (data.status === "success") {
                            likeCountEl.textContent = data.like_count;
                            icon.classList.toggle("liked", data.action === "liked");
                            likeButton.classList.toggle("active");
                        }
                    })
                    .catch(error => console.error("Error:", error));
                });
            });
        });

    </script>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
</body>
</html>
