<?php
require_once 'format-date.php';
require_once '../api/db_connection.php';

function render_post($post) {
        $conn = establish_connection();
        if (!$post) {
            echo "<p>Post not found.</p>";
            return;
        } 

        $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; 
        $profilePicture = !empty($post['profile_picture']) ? "../uploads/profile_pictures/" . $post['profile_picture'] : $defaultProfilePicture;
    ?>
    <a class="post-link" href="post.php?id=<?php echo htmlspecialchars($post['id']); ?>">
    <div class="recent-discussion py-4">
        <div class="flex flex-col gap-4">
            <div class="flex flex-row gap-4 align-center">
                    <div class="flex flex-col w-10 h-10">
                    <img class="rounded-full" src="<?php echo $profilePicture; ?>" />
                    </div>
                    <div class="flex flex-col">
                        <h1 class="inter-700 text-base flex flex-row align-center" style="gap: 0.4rem;">
                            <span class="gradient-text">
                                <?= $post['full_name'] ?>
                            </span> 
                        <div class="text-xs inter-300 post-date" data-timestamp="<?php echo $post['created_at']; ?>" style="color:var(--text-light-muted);"></div>
                        <h1 class="inter-300 text-xs text-white">@<?= $post['username'] ?></h1>
                    </div>
            </div>
            <div class="flex flex-col">
                <div class="flex flex-row align-center">
                    <h2 class="text-xl inter-700 pb-2 text-white"><?php echo htmlspecialchars($post['title']); ?></h2>
                </div>
                    <div class="text-sm inter-400 fetched-content text-white"><?php echo $post['content']; ?></div>
            </div>
        </div>
        <div class="post-interactions text-white">
            <div class="flex flex-wrap" style="gap: 0.3rem;">
                <div class="flex flex-row post-interaction">
                    <i class="fa-regular fa-thumbs-up pr-1"></i>
                    <?= $post['like_count']; ?>
                </div>
                <div class="flex flex-row post-interaction">
                    <i class="fa-regular fa-comments pr-1"></i>
                    <?= $post['comment_count']; ?>

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