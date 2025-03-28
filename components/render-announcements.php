<?php
require_once '../api/db_connection.php';

function render_announcement($post, $from) {
        $post_date = new DateTime($post['created_at']);
        $formatted_post_date = $post_date->format('M d Y');     
        // Fetch only announcement posts
        // $defaultProfilePicture = "../uploads/profile_pictures/default.svg"; 
        // $profilePicture = !empty($post['profile_picture']) ? "../uploads/profile_pictures/" . $post['profile_picture'] : $defaultProfilePicture;
    ?>
    <?php if ($from === 'university'): ?>
        <div class="latest-post">
            <img class="latest-img" src="../assets/university.png" alt="Latest Image"/>
            <div class="post-details">
                <a href="post.php?id=<?php echo $post['id']; ?>" class="text-lg announcement-title gradient-text inter-700 decoration-none"><?php echo $post['title']; ?></a>
                <div class="text-base inter-300 announcement-content"><?php echo $post['content']; ?></div>
                <p class="text-sm inter-600 pt-1 text-muted"><?php echo $formatted_post_date; ?></p>
            </div>
        </div>
    <?php elseif ($from === 'ssg'): ?>
        <div class="latest-post">
            <img class="latest-img" src="../assets/ssc.png" alt="Latest Image"/>
            <div class="post-details">
                <a href="post.php?id=<?php echo $post['id']; ?>" class="text-lg announcement-title gradient-text inter-700 decoration-none"><?php echo $post['title']; ?></a>
                <div class="text-base inter-300 announcement-content"><?php echo $post['content']; ?></div>
                <p class="text-sm inter-600 pt-1 text-muted"><?php echo $formatted_post_date; ?></p>
            </div>
        </div>
    <?php endif; ?>
    <?php
}
?>
