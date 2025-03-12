<?php
function renderPost_min($title, $author, $date, $id) {
    echo '
    <a class="post-link" href="post.php?id='. $id .'">
    <div class="post">
        <div class="flex flex-row align-center gap-4">
            <i class="fa-solid fa-newspaper fa-xl"></i>
            <div class="flex flex-col min-post-container">
                <div class="post-title gradient-text text-base inter-700 min-title">' . htmlspecialchars($title) . '</div>
                <div class="flex">
                    <p class="text-xs inter-300"><span class="inter-700">' . htmlspecialchars($author) . '</span> ' .'<span class="post-date" data-timestamp="' . $date .'"</p>
                </div>
            </div>
        </div>
    </div>
    </a>';
}
?>