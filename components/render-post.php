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

        $group_name = "Global"; // Default for global posts
        if (!empty($post['group_id'])) {
            $group_stmt = $conn->prepare("SELECT name FROM member_groups WHERE id = ?");
            $group_stmt->bind_param("i", $post['group_id']);
            $group_stmt->execute();
            $group_result = $group_stmt->get_result();
            if ($group_row = $group_result->fetch_assoc()) {
                $group_name = htmlspecialchars($group_row['name']);
            }
            $group_stmt->close();
        }
    ?>
    <a class="post-link" href="post.php?id=<?php echo htmlspecialchars($post['id']); ?>">
    <div class="recent-discussion rounded-2xl">
        <div class="flex flex-col gap-4">
            <div class="flex flex-row gap-4 align-center">
                    <div class="flex flex-col w-10 h-10">
                    <img class="rounded-full" src="<?php echo $profilePicture; ?>" />
                    </div>
                    <div class="flex flex-col">
                        <div class="flex flex-row align-center" style="gap: 0.4rem;">
                        <h1 class="inter-700 text-base flex flex-row align-center" style="gap: 0.4rem;">
                            <span class="gradient-text">
                                <?= $post['full_name'] ?>
                            </span> 
                        </h1>
                        <div class="text-xs inter-300 post-date" data-timestamp="<?php echo $post['created_at']; ?>" style="color:var(--text-light-muted);"></div>
                        </div>
                        <h1 class="inter-300 text-xs text-white">@<?= $post['username'] ?> in <span class="text-muted"><?php echo $group_name; ?></span></h1>
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