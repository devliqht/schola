<?php 
require_once '../api/config.php';
require_once '../api/db_connection.php';
require_once '../components/render-header.php';
require_once '../components/render-sidebar.php';
require_once '../components/render-fetch-post.php';
require_once '../components/get-breadcrumbs.php';
require_once '../validation/leveling-system.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}
$conn = establish_connection();

$post_id = intval($_GET['id']);
$posts_query = "SELECT posts.*, users.username, users.full_name, users.profile_picture FROM posts 
          JOIN users ON posts.author_id = users.id
          WHERE posts.id = ?";

$posts_stmt = $conn->prepare($posts_query);
$posts_stmt->bind_param("i", $post_id);
$posts_stmt->execute();
$posts_result = $posts_stmt->get_result();
$post = $posts_result->fetch_assoc();

$comments_query = "SELECT comments.id, comments.user_id, comments.content, comments.created_at, comments.parent_id, comments.pinned, users.username, users.full_name, users.role, users.profile_picture
      FROM comments
      JOIN users ON comments.user_id = users.id
      WHERE comments.post_id = ?
      ORDER BY comments.created_at ASC";

$comments_stmt = $conn->prepare($comments_query);
$comments_stmt->bind_param("i", $post_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();

$comments = [];
while ($comment = $comments_result->fetch_assoc()) {
    $comments[] = $comment;
}

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

$defaultProfilePicture = "../uploads/profile_pictures/default.svg";
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

function buildCommentTree($comments, $parent_id = null) {
    $tree = [];
    foreach ($comments as $comment) {
        if ($comment['parent_id'] == $parent_id) {
            $children = buildCommentTree($comments, $comment['id']);
            $comment['children'] = $children;
            $tree[] = $comment;
        }
    }
    return $tree;
}

$comment_tree = buildCommentTree($comments);

$pinned_comment_tree = [];
$unpinned_comment_tree = [];

foreach ($comment_tree as $comment) {
    if ($comment['pinned'] == 1) {
        $pinned_comment_tree[] = $comment;
    } else {
        $unpinned_comment_tree[] = $comment;
    }
}
?>
<!DOCTYPE html>
<html data-theme="<?= htmlspecialchars($theme); ?>">
<head>
    <title><?php echo htmlspecialchars($post['title']); ?> | <?= $post['username']; ?></title>
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
    <link rel="icon" type="image/png" href="../assets/logo.png">
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
        <div class="main-content w-bg">
            <nav class="breadcrumb">
                <?php echo get_breadcrumbs(); ?>
            </nav>
            <div class="flex flex-row align-center justify-between pb-2">
                <div class="flex flex-row align-center">
                    <img class="header-account-picture " src="<?php echo $authorProfilePicture; ?>" alt="Author Picture"/>
                    <div class="flex flex-col pl-3 py-2">
                        <h2 class="text-base inter-700 gradient-text"><?php echo htmlspecialchars($post['full_name']); ?></h2>
                        <div class="flex flex-row">
                            <a class="text-xs decoration-none text-white inter-300 post-author-name" href="profile.php?id=<?php echo $post['author_id']; ?>">@<?php echo htmlspecialchars($post['username']); ?>  </a>
                            <div class="text-xs inter-300 post-date" data-timestamp="<?php echo $post['created_at']; ?>" style="color:var(--text-light-muted);"></div>
                        </div>
                    </div>
                </div>
                <div class="tooltip-container">
                    <button class="text-black clear-button"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                    <div class="tooltip">
                        <?php if ($_SESSION['id'] == $post['author_id'] || $_SESSION['role'] == 'admin'): ?>
                        <button class="tooltip-option edit-comment">Edit Post</button>
                        <form action="../validation/delete-comment.php" method="POST" class="tooltip-form">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            <button type="submit" class="tooltip-option delete-comment">Delete Post</button>
                        </form>
                        <?php else: ?>
                        <p class="text-xs text-white">No actions available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="flex flex-col" style="color: var(--text-light);">
                <h1 class="text-2xl inter-600 pb-2"><?= $post['title']; ?></h1>
                <div class="text-base inter-400 rounded-lg text-muted post-content-container">
                    <?php echo $post['content']; ?>
                </div>
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
                <div class="text-sm text-muted inter-700"><?php echo $formatted_post_date; ?></div>
            </div>
            <div class="comments-section pt-2" id="comments-section">
                <div class="comment-container" style="margin-top: 1rem; position: relative;">
                    <div class="comment-input-wrapper flex flex-row align-start">
                        <form action="../validation/add-comment.php" method="POST" class="add-comment-form flex-grow">
                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            <textarea 
                                style="margin: 0;" 
                                name="commentContent" 
                                placeholder="Comment as <?php echo $_SESSION['username']; ?>" 
                                required 
                                class="text-base comment-textarea"
                            ></textarea>
                            <div class="comment-buttons">
                                <input type="submit" class="submit-comment-button" value="Submit" />
                            </div>
                        </form>
                    </div>
                </div>
                <h3 class="text-2xl inter-700 tracking-tight gradient-text pt-4"><?php echo $comment_count; ?> Comments</h3>
                <div class="flex flex-col">
                <?php
                function renderComment($comment, $post_id, $defaultProfilePicture, $post_author_id, $level = 0) {
                    $comment_date = new DateTime($comment['created_at']);
                    $formatted_comment_date = $comment_date->format('M d, Y, h:i A');
                    $profilePicture = !empty($comment['profile_picture']) ? "../uploads/profile_pictures/" . $comment['profile_picture'] : $defaultProfilePicture;
                    $isReply = $level > 0;
                ?>
                    <div class="comment-item <?php echo $isReply ? 'reply' : ''; ?> <?php echo $comment['pinned'] ? 'pinned-comment' : ''; ?>" data-comment-id="<?php echo $comment['id']; ?>">
                        <?php if ($isReply): ?>
                            <div class="comment-connector"></div>
                        <?php endif; ?>
                        <div class="flex flex-col">
                            <div class="flex flex-row align-center gap-4 justify-between">
                                <div class="flex flex-row align-center">
                                    <img class="header-account-picture" src="<?php echo $profilePicture; ?>" alt="Pfp"/>
                                    <div class="flex flex-col pl-3">
                                        <div class="flex flex-row align-center" style="gap: 0.4rem;">
                                            <h2 class="text-base gradient-text inter-700"><?php echo htmlspecialchars($comment['full_name']); ?></h2> 
                                            <div class="text-xs inter-300 post-date" data-timestamp="<?php echo $comment['created_at']; ?>" style="color:rgb(97, 97, 97);"></div>
                                            <?php if ($comment['pinned'] == 1 && $level == 0): ?>
                                                <i class="fa-solid fa-thumbtack pinned-icon"></i>
                                            <?php endif; ?>
                                        </div>
                                        <a href="profile.php?id=<?php echo $comment['user_id']; ?>" class="text-xs inter-400 decoration-none text-white comment-author">@<?php echo htmlspecialchars($comment['username']); ?></a>
                                    </div>
                                </div>
                                <div class="tooltip-container">
                                    <button class="text-black clear-button"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                    <div class="tooltip">
                                        <?php if ($_SESSION['id'] == $comment['user_id'] || $_SESSION['role'] == 'admin'): ?>
                                            <button class="tooltip-option edit-comment" onclick="editComment(<?php echo $comment['id']; ?>)">Edit Comment</button>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['id'] == $post_author_id): ?>
                                            <button class="tooltip-option pin-comment" onclick="pinComment(<?php echo $comment['id']; ?>, '<?php echo $comment['pinned'] ? 'unpin' : 'pin'; ?>')">
                                                <?php echo $comment['pinned'] ? 'Unpin Comment' : 'Pin Comment'; ?>
                                            </button>
                                        <?php endif; ?>
                                        <form action="../validation/delete-comment.php" method="POST" class="tooltip-form">
                                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                                            <button type="submit" class="tooltip-option delete-comment">Delete Comment</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <p class="text-base py-2 inter-300 post-content text-white comment-content"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                            <button class="interaction w-fit" onclick="showReplyForm(<?php echo $comment['id']; ?>)">Reply</button>
                            <form class="reply-form" id="reply-form-<?php echo $comment['id']; ?>" onsubmit="submitReply(event, <?php echo $comment['id']; ?>)">
                                <div class="reply-form-connector"></div>
                                <textarea class="text-base comment-textarea reply-textarea" name="content" placeholder="Reply as <?php echo $_SESSION['username']; ?>" required></textarea>
                                <div class="reply-buttons">
                                    <button type="submit">Submit</button>
                                    <button type="button" onclick="hideReplyForm(<?php echo $comment['id']; ?>)">Cancel</button>
                                </div>
                            </form>
                            <form class="edit-comment-form" onsubmit="updateComment(event, <?php echo $comment['id']; ?>)">
                                <textarea class="text-base text-white comment-textarea edit-comment-textarea" name="content"><?php echo htmlspecialchars($comment['content']); ?></textarea>
                                <div class="edit-comment-buttons">
                                    <button type="submit">Save</button>
                                    <button type="button" onclick="cancelEdit(<?php echo $comment['id']; ?>)">Cancel</button>
                                </div>
                            </form>
                            <?php if (!empty($comment['children'])): ?>
                                <div class="replies">
                                    <?php foreach ($comment['children'] as $child): ?>
                                        <?php renderComment($child, $post_id, $defaultProfilePicture, $post_author_id, $level + 1); ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($pinned_comment_tree)): ?>
                    <h4 class="text-lg inter-600 pt-2 text-white">Pinned Comments</h4>
                    <?php foreach ($pinned_comment_tree as $comment): ?>
                        <?php renderComment($comment, $post_id, $defaultProfilePicture, $post['author_id']); ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($unpinned_comment_tree)): ?>
                    <?php if (!empty($pinned_comment_tree)): ?>
                        <h4 class="text-lg inter-600 pt-2 text-white">Other Comments</h4>
                    <?php endif; ?>
                    <?php foreach ($unpinned_comment_tree as $comment): ?>
                        <?php renderComment($comment, $post_id, $defaultProfilePicture, $post['author_id']); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
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
        <div class="pup-wrapper" id="post-user-profile">
            <?php
                $fetch_user_posts_query = $conn->prepare("SELECT p.id, p.title, p.content, p.created_at, u.profile_picture, u.username, u.full_name
                FROM posts p
                JOIN users u ON p.author_id = u.id
                WHERE p.author_id = ? AND p.id != ?
                ORDER BY p.created_at DESC"
                );
                $fetch_user_posts_query->bind_param("ii", $post['author_id'], $post['id']);
                $fetch_user_posts_query->execute();
                $fetch_user_posts_result = $fetch_user_posts_query->get_result();
            ?>
            <div class="post-user-profile">
                <h2 class="text-lg gradient-text inter-700 pb-2">More by <?= $post['username']; ?></h2>
                <?php if ($fetch_user_posts_result->num_rows > 0): ?>
                    <div class="flex flex-col" style="gap: 0.4rem;">
                        <?php while ($fetched_post = $fetch_user_posts_result->fetch_assoc()): ?>
                            <?php 
                                render_fetch_post($fetched_post, 0); 
                            ?>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-base inter-300 text-white">No posts found for this user.</p>
                <?php endif; ?>
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

        function showReplyForm(commentId) {
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            replyForm.style.display = 'block';
            const textarea = replyForm.querySelector('.reply-textarea');
            textarea.focus();
            setTimeout(() => adjustHeight(textarea), 0);
        }

        function hideReplyForm(commentId) {
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            replyForm.style.display = 'none';
            const textarea = replyForm.querySelector('.reply-textarea');
            textarea.value = '';
        }

        function submitReply(event, commentId) {
            event.preventDefault();
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            const content = replyForm.querySelector('.reply-textarea').value;

            fetch('../validation/add-reply.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=<?php echo $post_id; ?>&parent_id=${commentId}&content=${encodeURIComponent(content)}`
            })
            .then(response => response.text())
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the reply.');
            });
        }

        function editComment(commentId) {
            const commentItem = document.querySelector(`.comment-item[data-comment-id="${commentId}"]`);
            commentItem.querySelector('.comment-content').style.display = 'none';
            const editForm = commentItem.querySelector('.edit-comment-form');
            editForm.style.display = 'block';
            const textarea = editForm.querySelector('.edit-comment-textarea');
            setTimeout(() => adjustHeight(textarea), 0);
            textarea.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    updateComment(event, commentId);
                }
            });
        }

        function cancelEdit(commentId) {
            const commentItem = document.querySelector(`.comment-item[data-comment-id="${commentId}"]`);
            commentItem.querySelector('.comment-content').style.display = 'block';
            commentItem.querySelector('.edit-comment-form').style.display = 'none';
            commentItem.querySelector('textarea[name="content"]').value = commentItem.querySelector('.comment-content').textContent.trim();
        }

        function updateComment(event, commentId) {
            event.preventDefault();
            const commentItem = document.querySelector(`.comment-item[data-comment-id="${commentId}"]`);
            const form = commentItem.querySelector('.edit-comment-form');
            const content = form.querySelector('textarea[name="content"]').value;

            fetch('../validation/update-comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `comment_id=${commentId}&content=${encodeURIComponent(content)}&post_id=<?php echo $post_id; ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    commentItem.querySelector('.comment-content').textContent = content;
                    commentItem.querySelector('.comment-content').style.display = 'block';
                    form.style.display = 'none';
                } else {
                    alert('Failed to update comment: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the comment.');
            });
        }

        function pinComment(commentId, action) {
            fetch('../validation/pin-comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `comment_id=${commentId}&post_id=<?php echo $post_id; ?>&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Failed to ' + (action === 'pin' ? 'pin' : 'unpin') + ' comment: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while ' + (action === 'pin' ? 'pinning' : 'unpinning') + ' the comment.');
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            const mainCommentTextarea = document.querySelector('.add-comment-form .comment-textarea');
            if (mainCommentTextarea) {
                const mainForm = mainCommentTextarea.closest('form');
                mainCommentTextarea.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        mainForm.submit();
                    }
                });
                function adjustHeight(textarea) {
                    textarea.style.height = 'auto';
                    textarea.style.height = `${textarea.scrollHeight}px`;
                }
                mainCommentTextarea.addEventListener('input', () => adjustHeight(mainCommentTextarea));
                adjustHeight(mainCommentTextarea);
            }

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
                        return response.text(); 
                    })
                    .then(text => {
                        console.log("Raw response:", text); 
                        return JSON.parse(text); 
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

            document.querySelectorAll('.reply-textarea').forEach(textarea => {
                textarea.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        const form = textarea.closest('form');
                        const commentId = form.id.replace('reply-form-', '');
                        submitReply(event, commentId);
                    }
                });
                textarea.addEventListener('input', () => adjustHeight(textarea));
            });
        });

        function adjustHeight(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = `${textarea.scrollHeight}px`;
        }
    </script>
    <script src="../js/search.js"></script>
    <script src="../js/formatTime.js"></script>
    <script src="../js/sidebar.js"></script>
</body>
</html>