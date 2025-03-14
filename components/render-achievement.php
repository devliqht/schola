<?php
function render_achievement($achievement) {
    $achievements = [
        'Creator' => 2,
        'post' => 10,
        'comment' => 5
    ];
?>
    <h1 class="achievement <?php echo $achievement; ?>">
        <?= $achievement; ?>
    </h1>
<?php
    }
?>