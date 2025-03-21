<?php
function renderPost($title, $content, $author, $date, $id, $profile_picture, $pinned) {
    require_once '../api/db_connection.php';

    $conn = establish_connection();
    $comment_count_stmt = $conn->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
    $comment_count_stmt->bind_param("i", $id);
    $comment_count_stmt->execute();
    $comment_count_result = $comment_count_stmt->get_result()->fetch_row();
    $comment_count = $comment_count_result[0] ?? 0;
    $comment_count_stmt->close();

    $view_count_stmt = $conn->prepare("SELECT views FROM post_views WHERE post_id = ?");
    $view_count_stmt->bind_param("i", $id);
    $view_count_stmt->execute();
    $view_count_result = $view_count_stmt->get_result()->fetch_assoc();
    $view_count = $view_count_result['views'] ?? 0;
    $view_count_stmt->close();

    $recent_comment_stmt = $conn->prepare("
        SELECT users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? 
        ORDER BY comments.created_at DESC 
        LIMIT 1
    ");
    $recent_comment_stmt->bind_param("i", $id);
    $recent_comment_stmt->execute();
    $recent_comment_result = $recent_comment_stmt->get_result()->fetch_assoc();
    $recent_commenter = $recent_comment_result['username'] ?? 'No replies yet';
    $recent_comment_stmt->close();

    $title = htmlspecialchars($title);
    $author = htmlspecialchars($author);
    $date = htmlspecialchars($date);
    $recent_commenter = htmlspecialchars($recent_commenter);

    $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; 
    $profilePicture = !empty($profile_picture) ? "../uploads/profile_pictures/" . $profile_picture : $defaultProfilePicture;

?>
    <a class="post-link" href="post.php?id=<?php echo $id; ?>">
        <div class="post">
            <div class="renderPost-content">
                <div class="flex flex-row align-center gap-4 renderPost-metadata">
                <img class="header-account-picture" src="<?php echo $profilePicture; ?>" alt="Pfp"/>
                    <div class="renderPost-title">
                        <div class="post-title gradient-text text-base inter-700 flex flex-row align-center"><?php echo $title; ?>
                            <p class="text-xs inter-300 pl-2" style="color: black !important;"> <?= $pinned ? "<i class='fa-solid fa-thumbtack'></i>".' Pinned' : '' ?></p>
                        </div>
                        <div class="flex">
                            <p class="text-sm inter-300">
                                by <span class="inter-700"><?php echo $author; ?></span> | <?php echo $date; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="renderPost-statistics">
                    <p class="text-sm text-white"><i class="fa-solid fa-eye"></i> <?php echo $view_count; ?> <i class="fa-solid fa-comment-dots"></i> <?php echo $comment_count; ?></p>
                    <p class="text-sm inter-300 text-light-muted renderPost-recent-commenter"><?php echo $recent_commenter; ?></p>
                </div>
            </div>
        </div>
    </a>
 <?php
}
?>