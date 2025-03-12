<?php 
function formatRelativeTime($interval) {
    if ($interval->y > 0) {
        return $interval->y . 'y ago';
    } elseif ($interval->m > 0) {
        return $interval->m . 'm ago';
    } elseif ($interval->d > 0) {
        return $interval->d . 'd ago';
    } elseif ($interval->h > 0) {
        return $interval->h . 'hr ago';
    } elseif ($interval->i > 0) {
        return $interval->i . ' min ago';
    } elseif ($interval->s > 0) {
        return $interval->s . ' sec ago';
    } else {
        return 'just now';
    }
}
?>