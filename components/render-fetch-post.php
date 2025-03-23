<?php
require_once '../api/db_connection.php';

function render_fetch_post($post, $likeCount) {
        if (!$post) {
            echo "<p>Post not found.</p>";
            return;
        } 

        $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; 
        $profilePicture = !empty($post['profile_picture']) ? "../uploads/profile_pictures/" . $post['profile_picture'] : $defaultProfilePicture;
    ?>
    <a class="post-link" href="post.php?id=<?php echo htmlspecialchars($post['id']); ?>">
    <div class="fetched-user-post p-4">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col">
                <div class="flex flex-row align-center">
                    <h2 class="text-lg inter-700 pr-2 gradient-text"><?php echo htmlspecialchars($post['title']); ?></h2>
                </div>
                    <div class="text-sm inter-400 fetched-content"><?php echo $post['content']; ?></div>
            </div>
        </div>
        <div class="post-interactions text-white">
            <div class="flex flex-wrap" style="gap: 0.3rem;">
                <div class="flex flex-row post-interaction">
                    <i class="fa-regular fa-thumbs-up pr-1"></i>
                    <?= $likeCount ?>
                </div>
                <div class="flex flex-row post-interaction">
                    <i class="fa-regular fa-comments pr-1"></i>
                </div>
                <div class="flex flex-row post-interaction">
                    <i class="fa-regular fa-share-from-square pr-1"></i>
                </div>
                <div class="flex flex-row post-interaction">
                    <i class="fa-solid fa-ellipsis pr-1"></i>
                </div>
            </div>
        </div>
    </div>
    </a>

    <?php
}
?>