<?php
function formatRelativeTime($interval) {
    // Handle NULL or invalid input
    if ($interval === null) {
        return 'never active';
    }

    // Validate that $interval is a DateInterval object
    if (!$interval instanceof DateInterval) {
        return 'invalid interval';
    }

    // Check if the date is in the future
    $isFuture = $interval->invert === 1;
    $prefix = $isFuture ? 'in ' : '';
    $suffix = $isFuture ? '' : ' ago';

    // Use absolute values for the time units (since invert handles direction)
    if ($interval->y > 0) {
        $unit = $interval->y === 1 ? 'year' : 'years';
        return $prefix . $interval->y . ' ' . $unit . $suffix;
    } elseif ($interval->m > 0) {
        $unit = $interval->m === 1 ? 'month' : 'months';
        return $prefix . $interval->m . ' ' . $unit . $suffix;
    } elseif ($interval->d > 0) {
        $unit = $interval->d === 1 ? 'day' : 'days';
        return $prefix . $interval->d . ' ' . $unit . $suffix;
    } elseif ($interval->h > 0) {
        $unit = $interval->h === 1 ? 'hour' : 'hours';
        return $prefix . $interval->h . ' ' . $unit . $suffix;
    } elseif ($interval->i > 0) {
        $unit = $interval->i === 1 ? 'min' : 'mins';
        return $prefix . $interval->i . ' ' . $unit . $suffix;
    } elseif ($interval->s > 0) {
        $unit = $interval->s === 1 ? 'sec' : 'secs';
        return $prefix . $interval->s . ' ' . $unit . $suffix;
    } else {
        return $isFuture ? 'in a moment' : 'just now';
    }
}
?>